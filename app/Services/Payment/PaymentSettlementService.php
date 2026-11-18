<?php

namespace App\Services\Payment;

use App\Models\AuditLog;
use App\Services\MidtransService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Exceptions\Booking\SlotUnavailableException;
use App\Services\Booking\SlotReservationService;

/**
 * Satu-satunya tempat yang mengubah booking menjadi "dibayar". Webhook dan pemeriksaan status memanggil layanan
 * ini, dan halaman detail hanya membaca. Semua tulis lewat query builder dengan binding.
 */
class PaymentSettlementService
{
    public function __construct(private MidtransService $gateway, private SlotReservationService $slots)
    {
    }

    // ---- pintu masuk

    /** Menanyakan status ke Midtrans (server ke server), lalu menerapkannya. Jaringan di luar transaksi database. */
    public function settleFromGateway(string $orderId, string $source): SettlementResult
    {
        if ($orderId === '' || ! DB::table('payments')->where('pg_transaction_id', $orderId)->exists()) {
            return new SettlementResult(SettlementResult::UNKNOWN, null, 'Pesanan tidak dikenal.');
        }

        $status = $this->gateway->isConfigured() ? $this->gateway->getStatus($orderId) : null;

        // Pesanan ada di sistem kita tetapi belum pernah dibuka di Snap: Midtrans menjawab 404. Itu bukan galat.
        if (is_array($status) && (string) ($status['status_code'] ?? '') === '404') {
            return new SettlementResult(SettlementResult::PENDING, null, 'Belum ada transaksi di gateway.');
        }
        if (! is_array($status) || empty($status['transaction_status'])) {
            Log::warning("Pelunasan {$orderId}: status gateway tidak dapat diverifikasi (sumber: {$source}).");

            return new SettlementResult(SettlementResult::DEFERRED, null, 'Status gateway tidak dapat diverifikasi.');
        }

        return $this->apply($orderId, $status, $source);
    }

    /**
     * Menerapkan status gateway yang SUDAH terverifikasi. Idempoten: memanggilnya berulang tidak mengubah apa pun
     * setelah hasil akhir tercapai. Dipanggil langsung oleh tes, dan jangan pernah lewat input pengguna.
     *
     * @param array{transaction_status?:string,fraud_status?:string,payment_type?:string,gross_amount?:string|int|float} $gw
     */
    public function apply(string $orderId, array $gw, string $source): SettlementResult
    {
        $kind = $this->classify((string) ($gw['transaction_status'] ?? ''), (string) ($gw['fraud_status'] ?? ''));

        return DB::transaction(function () use ($orderId, $gw, $source, $kind) {
            // Urutan kunci selalu: pembayaran, lalu booking. Dua proses bersamaan tidak akan saling mengunci.
            $payment = DB::table('payments')->where('pg_transaction_id', $orderId)->lockForUpdate()->first();
            if (! $payment) {
                return new SettlementResult(SettlementResult::UNKNOWN, null, 'Pesanan tidak dikenal.');
            }
            $header = DB::table('booking_headers')->where('id', $payment->header_id)->lockForUpdate()->first();
            if (! $header) {
                return new SettlementResult(SettlementResult::UNKNOWN, null, 'Booking tidak ditemukan.');
            }

            return match ($kind) {
                'success' => $this->applySuccess($payment, $header, $gw, $source),
                'expired', 'cancelled', 'denied' => $this->applyFailure($payment, $header, $kind),
                'pending' => $this->applyPending($payment, $header, $gw),
                default   => new SettlementResult(SettlementResult::IGNORED, $header->id, 'Status ini tidak diproses di sini (misalnya refund).'),
            };
        }, 3);
    }

    /**
     * Cadangan bila webhook terlambat: menanyakan Midtrans paling sering sekali per interval per pesanan, dibagi oleh
     * semua tab dan pengunjung lewat kunci cache. Mengembalikan null bila dimatikan atau sedang dibatasi.
     */
    public function reconcileHeader(int $headerId): ?SettlementResult
    {
        if (! config('booking.poll_reconcile', true)) {
            return null;
        }

        $interval = max(1, (int) config('booking.reconcile_interval_seconds', 10));
        $orders = DB::table('payments')->where('header_id', $headerId)->where('status_pembayaran', 'pending')
            ->orderByDesc('id')->limit(3)->pluck('pg_transaction_id');

        $last = null;
        foreach ($orders as $orderId) {
            if (! Cache::add("payment-reconcile:{$orderId}", 1, $interval)) {
                continue;
            }
            $last = $this->settleFromGateway((string) $orderId, 'poll');
            if ($last->paid) {
                break;
            }
        }

        return $last;
    }

    /** Pembacaan murni untuk polling: tidak menulis apa pun, termasuk saat batas bayar sudah lewat. */
    public function snapshot(int $headerId): ?array
    {
        $h = DB::table('booking_headers')->where('id', $headerId)->first(['id', 'status', 'created_at', 'expires_at']);
        if (! $h) {
            return null;
        }

        $deadline = $this->deadline($h);
        $overdue = $h->status === 'pending' && $deadline->lte(now());

        return [
            'status'               => $h->status,
            'is_paid'              => $h->status === 'payment',
            'is_expired'           => $h->status === 'expired' || $overdue,
            'remaining_seconds'    => $h->status === 'pending' ? max(0, $deadline->timestamp - now()->timestamp) : 0,
            'expires_at_timestamp' => $deadline->timestamp,
        ];
    }

    // ---------- hasil

    private function applySuccess(object $payment, object $header, array $gw, string $source): SettlementResult
    {
        if ($payment->status_pembayaran === 'succes') {
            return new SettlementResult(SettlementResult::ALREADY, $header->id, 'Pembayaran ini sudah tercatat berhasil.', $header->status === 'payment');
        }

        $gross  = is_numeric($gw['gross_amount'] ?? null) ? (int) round((float) $gw['gross_amount']) : null;
        $paid   = (int) round((float) $payment->nominal_bayar);
        $billed = (int) round((float) $header->total_tagihan);

        // Nominal harus cocok di tiga tempat: yang diterima gateway, yang dicatat sebagai pembayaran, dan tagihan.
        if ($gross === null || $gross !== $paid || $paid !== $billed) {
            AuditLog::record(null, 'BookingHeader', $header->id, 'selisih_pembayaran', $header->status, $header->status,
                "Gateway menerima Rp {$this->rp($gross)}, pembayaran tercatat Rp {$this->rp($paid)}, tagihan Rp {$this->rp($billed)}. Tidak ditandai lunas, perlu ditinjau.");
            Log::warning("Pelunasan {$payment->pg_transaction_id}: nominal tidak cocok (gateway {$gross}, pembayaran {$paid}, tagihan {$billed}).");

            return new SettlementResult(SettlementResult::MISMATCH, $header->id, 'Nominal tidak cocok.');
        }

        $now = now();
        $label = $this->methodLabel((string) ($gw['payment_type'] ?? ''));
        $note = "{$label}, Rp {$this->rp($paid)}, sumber: {$source}.";

        // Uang memang sudah diterima gateway, jadi pembayaran dicatat berhasil apa adanya.
        DB::table('payments')->where('id', $payment->id)->update([
            'status_pembayaran' => 'succes', 'paid_at' => $now, 'metode_pembayaran' => $label,
        ]);

        // 1. Pembayaran kedua pada booking yang sudah lunas, atau booking yang sudah dibatalkan: kembalikan uangnya.
        if ($header->status === 'payment') {
            return $this->refundInFull($payment, $header, 'Pembayaran ganda: booking ini sudah lunas dari pembayaran lain.', $label, 'dibayar_ganda');
        }
        if ($header->status === 'canceled') {
            return $this->refundInFull($payment, $header, 'Pembayaran tiba setelah booking dibatalkan.', $label, 'dibayar_batal');
        }

        // 2. Slot: booking ini harus memegang reservasinya. Idempoten bila sudah memegang, dan gagal bila booking lain memegangnya
        $late = $header->status === 'expired' || ($header->status === 'pending' && $this->deadline($header)->lt($now));
        try {
            $this->slots->reserve($header->id);
        } catch (SlotUnavailableException) {
            if ($header->status === 'pending') {
                DB::table('booking_headers')->where('id', $header->id)->where('status', 'pending')->update(['status' => 'canceled']);
            }

            return $this->refundInFull($payment, $header,
                $late ? 'Pembayaran tiba setelah booking kedaluwarsa dan slot sudah dipesan pengguna lain.'
                        : 'Slot sudah dipegang booking lain saat pembayaran diterima.',
                $label, $late ? 'dibayar_terlambat' : 'slot_bentrok');
        }

        if ($late) {
            $this->markBookingPaid($header, $header->status, "Pembayaran terlambat diterima, slot masih kosong. {$note}", 'dibayar_terlambat');

            return new SettlementResult(SettlementResult::REVIVED, $header->id, 'Booking dihidupkan kembali.', true);
        }

        // 3. Jalur normal.
        $this->markBookingPaid($header, $header->status, $note, 'dibayar');

        return new SettlementResult(SettlementResult::SETTLED, $header->id, 'Pembayaran berhasil.', true);
    }

    private function applyFailure(object $payment, object $header, string $kind): SettlementResult
    {
        // Notifikasi lama yang datang terlambat tidak boleh membalik pembayaran yang sudah berhasil.
        if ($payment->status_pembayaran === 'succes') {
            return new SettlementResult(SettlementResult::IGNORED, $header->id, 'Pembayaran sudah berhasil; notifikasi gagal diabaikan.');
        }

        DB::table('payments')->where('id', $payment->id)->update(['status_pembayaran' => 'failed']);

        // "deny": penyewa boleh mencoba lagi sampai batas bayar, jadi booking dibiarkan menunggu.
        if ($kind !== 'denied' && $header->status === 'pending') {
            $alive = DB::table('payments')->where('header_id', $header->id)->where('id', '!=', $payment->id)
                ->whereIn('status_pembayaran', ['succes', 'pending'])->exists();

            if (! $alive) {
                $to = $kind === 'expired' ? 'expired' : 'canceled';
                $n = DB::table('booking_headers')->where('id', $header->id)->where('status', 'pending')->update(['status' => $to]);
                if ($n) {
                    $this->slots->release($header->id);
                    AuditLog::record(null, 'BookingHeader', $header->id, 'pembayaran_gagal', 'pending', $to,
                        $kind === 'expired' ? 'Pembayaran kedaluwarsa di gateway.' : 'Pembayaran dibatalkan di gateway.');
                }
            }
        }

        return new SettlementResult(SettlementResult::FAILED, $header->id, 'Pembayaran tidak berhasil.');
    }

    private function applyPending(object $payment, object $header, array $gw): SettlementResult
    {
        // Tidak pernah menurunkan hasil akhir. Hanya mencatat metode yang dipilih penyewa agar terlihat di admin.
        $type = (string) ($gw['payment_type'] ?? '');
        if ($payment->status_pembayaran === 'pending' && $type !== '') {
            DB::table('payments')->where('id', $payment->id)->update(['metode_pembayaran' => $this->methodLabel($type)]);
        }

        return new SettlementResult(SettlementResult::PENDING, $header->id, 'Menunggu pembayaran.');
    }

    // -------- helpers

    private function markBookingPaid(object $header, string $from, string $note, string $action): void
    {
        $n = DB::table('booking_headers')->where('id', $header->id)->whereIn('status', ['pending', 'expired'])->update(['status' => 'payment']);
        if ($n) {
            DB::table('booking_details')->where('header_id', $header->id)->update(['status_item' => 'ongoing']);
            AuditLog::record(null, 'BookingHeader', $header->id, $action, $from, 'payment', $note);
        }
    }

    /** Refund penuh sebesar uang yang benar-benar diterima. Satu refund per pembayaran, jadi aman dari pemanggilan ulang. */
    private function refundInFull(object $payment, object $header, string $reason, string $label, string $action): SettlementResult
    {
        $amount = (float) $payment->nominal_bayar;
        $detailId = DB::table('booking_details')->where('header_id', $header->id)->orderBy('id')->value('id');

        if (! $detailId) {
            AuditLog::record(null, 'BookingHeader', $header->id, 'refund_perlu_ditinjau', $header->status, $header->status,
                "{$reason} Uang Rp {$this->rp((int) round($amount))} sudah masuk, tetapi booking tidak punya rincian slot sehingga refund tidak bisa dibuat otomatis.");

            return new SettlementResult(SettlementResult::REVIEW, $header->id, 'Refund perlu dibuat manual.');
        }

        if (! DB::table('refunds')->where('payment_id', $payment->id)->exists()) {
            DB::table('refunds')->insert([
                'header_id' => $header->id, 'detail_id' => $detailId, 'payment_id' => $payment->id,
                'alasan_refund' => $reason, 'nominal_refund' => $amount, 'metode_refund' => $label,
                'rekening_tujuan' => 'Metode pembayaran asal', 'status_refund' => 'pending',
                'created_at' => now(), 'updated_at' => now(),
            ]);
            AuditLog::record(null, 'BookingHeader', $header->id, 'refund_otomatis', $header->status, $header->status,
                "{$reason} Refund penuh Rp {$this->rp((int) round($amount))} dibuat otomatis.");
        }

        return new SettlementResult(SettlementResult::REFUNDED, $header->id, 'Refund penuh dibuat.', false);
    }

    private function deadline(object $h): CarbonImmutable
    {
        $window = max(1, (int) config('booking.payment_window_minutes', 15));

        return $h->expires_at ? CarbonImmutable::parse($h->expires_at) : CarbonImmutable::parse($h->created_at)->addMinutes($window);
    }

    /** success | pending | expired | cancelled | denied | ignore */
    private function classify(string $trx, string $fraud): string
    {
        return match (true) {
            $trx === 'settlement', $trx === 'capture' && $fraud === 'accept' => 'success',
            $trx === 'capture', $trx === 'authorize', $trx === 'pending'     => 'pending', // capture + challenge: tinjau di dashboard Midtrans
            $trx === 'expire'                                               => 'expired',
            $trx === 'cancel'                                               => 'cancelled',
            $trx === 'deny', $trx === 'failure'                             => 'denied',
            default                                                         => 'ignore',
        };
    }

    /** Satu tempat untuk format metode. Data yang sudah ada memakai "Midtrans (jenis)". */
    private function methodLabel(string $type): string
    {
        return $type !== '' ? "Midtrans ({$type})" : 'Midtrans';
    }

    private function rp(?int $n): string
    {
        return number_format($n ?? 0, 0, ',', '.');
    }
}