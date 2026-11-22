<?php

namespace Database\Seeders;

use DateTimeImmutable;
use DateTimeZone;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/** Booking, rincian slot, pembayaran, webhook, reservasi slot, dan refund. */
class DemoBookingSeeder extends DemoBase
{
    public function run(): void
    {
        $this->ensureStarted();
        $this->seedBookings();
    }

    private function seedBookings(): void
    {
        $plan = array_merge(
            array_fill(0, 430, 'paid'),
            array_fill(0, 55, 'expired'),
            array_fill(0, 22, 'canceled'),
            array_fill(0, 10, 'refund_completed'),
            array_fill(0, 2, 'refund_processing'),
            array_fill(0, 3, 'refund_pending'),
            array_fill(0, 4, 'refund_rejected'),
            array_fill(0, 2, 'fresh_pending'),
            array_fill(0, 14, 'today'),
            array_fill(0, 8, 'yesterday'),
            array_fill(0, 6, 'upcoming_budi'),
            array_fill(0, 6, 'upcoming_dimas'),
        );
        $plan = $this->shuffled($plan);
        // Booking baru yang masih menunggu pembayaran dibuat paling akhir supaya slotnya tidak bentrok.
        usort($plan, fn ($a, $b) => (int) ($a === 'fresh_pending') <=> (int) ($b === 'fresh_pending'));

        $built = 0;
        foreach ($plan as $kind) {
            for ($try = 0; $try < 60; $try++) {
                if ($this->buildBooking($kind)) {
                    $built++;
                    break;
                }
            }
        }
        $this->report['booking'] = $built;

        $this->bulk('booking_headers', $this->headers);
        $this->bulk('booking_details', $this->details);
        $this->bulk('payments', $this->payments);
        $this->bulk('payment_webhook_events', $this->events);
        $this->bulk('slot_reservations', $this->reservations, 200);
        $this->bulk('refunds', $this->refunds);
    }

    private function buildBooking(string $kind): bool
    {
        $window = null;
        $forced = null;
        $fixedDay = null;
        if ($kind === 'today' || $kind === 'yesterday') {
            $fixedDay = $this->today->modify($kind === 'today' ? '+0 days' : '-1 day');
            $kind = 'paid';
        }
        if (str_starts_with($kind, 'upcoming_')) {
            $forced = $this->customers[$kind === 'upcoming_budi' ? 0 : 1];
            $kind = 'paid';
            $window = [0, 10];
        }
        $fid = $this->pickField();
        $f = $this->fields[$fid];
        $cust = $forced ?? $this->pickCustomer();
        $dur = (int) $this->weighted([1 => 55, 2 => 38, 3 => 7]);
        if ($f['sport'] === 'tenis-meja' || $f['sport'] === 'badminton') {
            $dur = (int) $this->weighted([1 => 70, 2 => 28, 3 => 2]);
        }
        $live = $f['live']->modify('+1 day');
        $cancelAt = null;
        $refundStatus = null;

        switch ($kind) {
            case 'paid':
                $day = $fixedDay ?? ($window ? $this->sampleDay($window[0], $window[1]) : $this->sampleDay(-self::HISTORY_DAYS, self::FUTURE_DAYS));
                $slot = $this->findSlot($f, $day, $dur, $window ? $this->now->modify('+90 minutes') : null);
                if (! $slot) {
                    return false;
                }
                $paidAt = $this->paidBefore($slot['start']);
                break;
            case 'expired':
            case 'canceled':
                $day = $this->sampleDay(-self::HISTORY_DAYS, 0);
                $slot = $this->findSlot($f, $day, $dur, null, $this->now->modify('-3 hours'));
                if (! $slot) {
                    return false;
                }
                $paidAt = null;
                $createdAt = $this->minDate($this->paidBefore($slot['start']), $this->now->modify('-40 minutes'));
                break;
            case 'refund_completed':
                $cancelAt = $this->now->modify('-' . mt_rand(4 * 24, 100 * 24) . ' hours');
                $target = $cancelAt->modify('+' . mt_rand(27, 9 * 24) . ' hours');
                $slot = $this->findSlot($f, $target->setTime(0, 0, 0), $dur, $cancelAt->modify('+25 hours'), $cancelAt->modify('+10 days'));
                if (! $slot) {
                    return false;
                }
                $paidAt = $cancelAt->modify('-' . mt_rand(40, 4 * 24 * 60) . ' minutes');
                $refundStatus = 'completed';
                break;
            case 'refund_processing':
            case 'refund_pending':
                $cancelAt = $this->now->modify('-' . mt_rand(3, 40) . ' hours');
                $target = $cancelAt->modify('+' . mt_rand(30, 11 * 24) . ' hours');
                $slot = $this->findSlot($f, $target->setTime(0, 0, 0), $dur, $cancelAt->modify('+25 hours'), $cancelAt->modify('+12 days'));
                if (! $slot) {
                    return false;
                }
                $paidAt = $cancelAt->modify('-' . mt_rand(60, 5 * 24 * 60) . ' minutes');
                $refundStatus = $kind === 'refund_pending' ? 'pending' : 'processing';
                break;
            case 'refund_rejected':
                $cancelAt = $this->now->modify('-' . mt_rand(30, 60 * 24) . ' hours');
                $slot = $this->findSlot($f, $cancelAt->setTime(0, 0, 0)->modify('+' . mt_rand(0, 1) . ' days'), $dur, $cancelAt->modify('+2 hours'), $cancelAt->modify('+22 hours'));
                if (! $slot) {
                    return false;
                }
                $paidAt = $cancelAt->modify('-' . mt_rand(60, 3 * 24 * 60) . ' minutes');
                $refundStatus = 'rejected';
                break;
            default: // fresh_pending
                $day = $this->today->modify('+' . mt_rand(0, 1) . ' days');
                $slot = $this->findSlot($f, $day, $dur, $this->now->modify('+3 hours'), null);
                if (! $slot) {
                    return false;
                }
                $paidAt = null;
                $createdAt = $this->now->modify('-' . mt_rand(2, 9) . ' minutes');
        }

        // Waktu pembuatan booking untuk yang dibayar: beberapa menit sebelum pelunasan.
        $retry = $paidAt !== null && $this->chance(0.14);
        if ($paidAt !== null) {
            $createdAt = $paidAt->modify('-' . ($retry ? mt_rand(6, 11) : mt_rand(1, 8)) . ' minutes');
        }
        if ($createdAt < $live || $createdAt < $cust['created']->modify('+1 hour')) {
            return false;
        }

        // Booking dengan beberapa slot: tambahan 1 jam di jam lain pada lapangan dan hari yang sama.
        $slots = [[$slot['startMin'], $slot['endMin']]];
        $this->occupy($fid, $slot['day'], $slot['startMin'], $slot['endMin']);
        if (! str_starts_with($kind, 'refund') && $this->chance(0.22)) {
            $extra = $this->findSlot($f, $slot['day'], 1, null, null);
            if ($extra && $this->bookingTimeOk($createdAt, $extra['start'], $kind)) {
                $slots[] = [$extra['startMin'], $extra['endMin']];
                $this->occupy($fid, $extra['day'], $extra['startMin'], $extra['endMin']);
            }
        }
        if (str_starts_with($kind, 'refund')) {
            $slots = [$slots[0]];
        }

        $this->commitBooking($kind, $f, $cust, $slot['day'], $slots, $createdAt, $paidAt, $cancelAt, $refundStatus, $retry);

        return true;
    }

    private function bookingTimeOk(DateTimeImmutable $createdAt, DateTimeImmutable $start, string $kind): bool
    {
        return $kind === 'fresh_pending' ? $start > $this->now : $start > $createdAt;
    }

    private function commitBooking(string $kind, array $f, array $cust, DateTimeImmutable $day, array $slots, DateTimeImmutable $createdAt, ?DateTimeImmutable $paidAt, ?DateTimeImmutable $cancelAt, ?string $refundStatus, bool $retry): void
    {
        $hid = $this->next('header');
        $code = $this->bookingCode();
        $expires = $createdAt->modify('+' . self::PAYMENT_WINDOW_MIN . ' minutes');
        $subtotal = 0;
        foreach ($slots as [$a, $b]) {
            $subtotal += (int) round($f['price'] * (($b - $a) / 60));
        }
        $total = $subtotal + self::SERVICE_FEE;

        $status = match (true) {
            $kind === 'paid', $kind === 'refund_rejected' => 'payment',
            $kind === 'expired' => 'expired',
            $kind === 'canceled', in_array($kind, ['refund_completed', 'refund_processing', 'refund_pending'], true) => 'canceled',
            default => 'pending',
        };

        $firstStart = $day->setTime(0, 0, 0)->modify('+' . $slots[0][0] . ' minutes');
        $allEnded = true;
        foreach ($slots as [, $b]) {
            if ($day->setTime(0, 0, 0)->modify('+' . $b . ' minutes') > $this->now) {
                $allEnded = false;
            }
        }
        $checkedIn = null;
        if ($status === 'payment' && $firstStart <= $this->now && $this->chance($allEnded ? 0.87 : 0.7)) {
            $checkedIn = $firstStart->modify(mt_rand(-12, 8) . ' minutes');
        }
        $this->headers[] = [
            'id' => $hid, 'kode_booking' => $code, 'penyewa_id' => $cust['id'], 'total_tagihan' => $total,
            'subtotal_slot' => $subtotal, 'biaya_layanan' => self::SERVICE_FEE, 'status' => $status,
            'expires_at' => $this->dt($expires), 'checked_in_at' => $checkedIn ? $this->dt($checkedIn) : null,
            'created_at' => $this->dt($createdAt),
        ];

        // Rincian slot + penilaian komisi (hanya yang sudah dibayar).
        $detailIds = [];
        $assessed = in_array($kind, ['paid', 'refund_rejected', 'refund_completed', 'refund_processing', 'refund_pending'], true);
        $rate = $assessed ? $this->rateFor($f['mitra_key'], $paidAt) : null;
        foreach ($slots as [$a, $b]) {
            $did = $this->next('detail');
            $detailIds[] = $did;
            $harga = (int) round($f['price'] * (($b - $a) / 60));
            $end = $day->setTime(0, 0, 0)->modify('+' . $b . ' minutes');
            $itemStatus = 'ongoing';
            if ($status === 'payment' && $end <= $this->now) {
                $itemStatus = $this->chance(0.9) ? 'completed' : 'no_show';
                // Jadwal yang baru lewat dalam 30 jam terakhir sebagian belum ditandai selesai oleh mitra ("Siap Diselesaikan").
                if ($end > $this->now->modify('-30 hours') && $this->chance(0.65)) {
                    $itemStatus = 'ongoing';
                }
            }
            $komisi = null;
            $mitraAmt = null;
            $persen = null;
            $rateId = null;
            if ($assessed) {
                $p = (int) round(((float) $rate['persen']) * 100);
                $komisi = intdiv($harga * $p + 5000, 10000);   // setengah naik, per rincian slot
                $mitraAmt = $harga - $komisi;
                $persen = $rate['persen'];
                $rateId = $rate['id'];
            }
            $reverse = $refundStatus === 'completed';
            $this->details[] = [
                'id' => $did, 'header_id' => $hid, 'field_id' => $f['id'], 'tanggal_sewa' => $this->dt($day->setTime(0, 0, 0)),
                'jam_mulai' => $this->hm($a) . ':00', 'jam_selesai' => $this->hm($b) . ':00', 'harga_slot' => $harga,
                'komisi_persen' => $persen, 'komisi_nominal' => $assessed ? ($reverse ? 0 : $komisi) : null,
                'pendapatan_mitra' => $assessed ? ($reverse ? 0 : $mitraAmt) : null, 'komisi_rate_id' => $rateId,
                'status_item' => $itemStatus, 'created_at' => $this->dt($createdAt),
            ];
            if ($assessed) {
                $this->earnings[] = [
                    'mitra' => $f['mitra_key'], 'detail' => $did, 'header' => $hid, 'code' => $code, 'field' => $f['name'],
                    'day' => $day, 'a' => $a, 'b' => $b, 'harga' => $harga, 'komisi' => $komisi, 'mitra_amt' => $mitraAmt,
                    'persen' => $persen, 'paid' => $paidAt, 'release' => $end->modify('+24 hours'), 'refund' => $refundStatus,
                    'refund_id' => null, 'refund_at' => $cancelAt, 'item' => $itemStatus,
                ];
            }

            // Reservasi slot per 30 menit. Aktif untuk booking yang masih berlaku, dilepas untuk yang gagal atau batal.
            $active = in_array($status, ['payment', 'pending'], true);
            $releasedAt = match (true) {
                $status === 'expired' => $expires,
                $status === 'canceled' && $paidAt === null => $createdAt->modify('+' . mt_rand(3, 12) . ' minutes'),
                $status === 'canceled' => $cancelAt,
                default => null,
            };
            for ($t = $a; $t < $b; $t += 30) {
                $this->reservations[] = [
                    'id' => $this->next('reservation'), 'header_id' => $hid, 'detail_id' => $did, 'field_id' => $f['id'],
                    'slot_date' => $day->format('Y-m-d'), 'slot_start' => $this->hm($t) . ':00',
                    'holds' => $active ? 1 : null, 'released_at' => $active ? null : $this->dt($releasedAt),
                    'created_at' => $this->dt($createdAt),
                ];
            }
        }

        // Pembayaran + event webhook Midtrans.
        $methods = ['qris' => 40, 'ewallet' => 25, 'va' => 20, 'bank_transfer' => 10, 'credit_card' => 5];
        $method = $this->weighted($methods);
        $paymentId = null;

        if ($retry) {
            $this->addPayment($hid, $total, $this->weighted($methods), 'failed', $createdAt, null, $this->pick(['deny', 'cancel']), $createdAt->modify('+' . mt_rand(2, 4) . ' minutes'));
        }

        if ($paidAt !== null) {
            $paymentId = $this->addPayment($hid, $total, $method, 'succes', $retry ? $createdAt->modify('+' . mt_rand(3, 5) . ' minutes') : $createdAt->modify('+' . mt_rand(0, 1) . ' minutes'), $paidAt, 'settlement', $paidAt);
        } elseif ($status === 'expired') {
            $this->addPayment($hid, $total, $method, 'failed', $createdAt, null, 'expire', $expires->modify('+' . mt_rand(5, 90) . ' seconds'));
        } elseif ($status === 'canceled') {
            $this->addPayment($hid, $total, $method, 'failed', $createdAt, null, 'cancel', $createdAt->modify('+' . mt_rand(3, 12) . ' minutes'));
        } else {
            $this->addPayment($hid, $total, $method, 'pending', $createdAt, null, 'pending', $createdAt->modify('+5 seconds'));
        }

        if ($refundStatus !== null && $paymentId !== null) {
            $rid = $this->addRefund($hid, $detailIds[0], $paymentId, $total, $refundStatus, $cust, $cancelAt);
            foreach ($this->earnings as $i => $e) {
                if ($e['detail'] === $detailIds[0]) {
                    $this->earnings[$i]['refund_id'] = $rid;
                }
            }
        }
        $this->report['kind'][$kind] = ($this->report['kind'][$kind] ?? 0) + 1;
    }

    private function addPayment(int $hid, int $total, string $method, string $status, DateTimeImmutable $createdAt, ?DateTimeImmutable $paidAt, string $eventType, DateTimeImmutable $eventAt): int
    {
        $pid = $this->next('payment');
        $orderId = 'SP-' . $hid . '-' . $createdAt->getTimestamp();
        $this->payments[] = [
            'id' => $pid, 'header_id' => $hid, 'pg_transaction_id' => $orderId, 'metode_pembayaran' => $method,
            'nominal_bayar' => $total, 'status_pembayaran' => $status, 'paid_at' => $paidAt ? $this->dt($paidAt) : null,
            'created_at' => $this->dt($createdAt),
        ];

        $gross = number_format($total, 2, '.', '');
        $statusCode = $eventType === 'pending' ? '201' : ($eventType === 'settlement' ? '200' : ($eventType === 'deny' ? '202' : '407'));
        $payload = [
            'transaction_time' => $this->dt($createdAt), 'transaction_status' => $eventType, 'transaction_id' => $this->uuid(),
            'status_message' => 'midtrans payment notification', 'status_code' => $statusCode,
            'signature_key' => hash('sha512', $orderId . $statusCode . $gross . self::MIDTRANS_SERVER_KEY),
            'payment_type' => $method === 'va' ? 'bank_transfer' : ($method === 'ewallet' ? 'gopay' : $method),
            'order_id' => $orderId, 'merchant_id' => 'G' . mt_rand(100000000, 999999999), 'gross_amount' => $gross,
            'fraud_status' => 'accept', 'currency' => 'IDR',
        ];
        if ($paidAt) {
            $payload['settlement_time'] = $this->dt($paidAt);
        }
        $this->events[] = [
            'id' => $this->next('event'), 'provider' => 'midtrans', 'event_id' => $orderId . ':' . $eventType,
            'pg_transaction_id' => $orderId, 'event_type' => $eventType, 'payment_id' => $pid, 'signature_valid' => 1,
            'payload' => json_encode($payload), 'process_status' => 'processed', 'error' => null,
            'received_at' => $this->dt($eventAt), 'processed_at' => $this->dt($eventAt->modify('+1 second')),
        ];

        return $pid;
    }

    private function addRefund(int $hid, int $did, int $pid, int $total, string $status, array $cust, DateTimeImmutable $at): int
    {
        $id = $this->next('refund');
        $reasons = ['Jadwal tim bentrok dengan agenda kantor.', 'Beberapa anggota tim berhalangan hadir.', 'Salah memilih lapangan saat pemesanan.', 'Ada keperluan keluarga mendadak.', 'Cuaca diperkirakan buruk pada hari pemakaian.', 'Pertandingan dipindah ke tanggal lain.'];
        $method = $this->pick(['Transfer bank', 'Saldo e-wallet', 'QRIS (dikembalikan ke sumber dana)']);
        $acct = $cust['bank'] . ' ' . mt_rand(1000000000, 9999999999) . ' a.n. ' . $cust['name'];
        $done = $at->modify('+' . mt_rand(30, 70) . ' hours');
        if ($status === 'completed' && $done > $this->now) {
            $done = $this->now->modify('-' . mt_rand(1, 6) . ' hours');
        }
        $processed = in_array($status, ['processing', 'completed', 'rejected'], true);

        $this->refunds[] = [
            'id' => $id, 'header_id' => $hid, 'detail_id' => $did, 'payment_id' => $pid,
            'alasan_refund' => $status === 'rejected' ? 'Mohon pembatalan karena tim tidak bisa hadir.' : $this->pick($reasons),
            'alasan_penolakan' => $status === 'rejected' ? 'Pembatalan kurang dari 24 jam sebelum jadwal sehingga tidak memenuhi syarat refund.' : null,
            'nominal_refund' => $total, 'metode_refund' => $method,
            'gateway_refund_id' => in_array($status, ['processing', 'completed'], true) ? 'RFD-' . $this->code(10) : null,
            'rekening_tujuan' => $acct, 'status_refund' => $status,
            'diproses_oleh' => $processed ? $this->adminId : null,
            'complated_at' => $status === 'completed' ? $this->dt($done) : null,
            'created_at' => $this->dt($at), 'updated_at' => $this->dt($status === 'completed' ? $done : ($processed ? $at->modify('+3 hours') : $at)),
        ];

        return $id;
    }

    private function pickField(): int
    {
        $w = [];
        foreach ($this->bookable as $fid) {
            $w[$fid] = $this->fields[$fid]['weight'];
        }

        return (int) $this->weighted($w);
    }

    private function pickCustomer(): array
    {
        $w = [];
        foreach ($this->customers as $i => $c) {
            if ($c['weight'] > 0) {
                $w[$i] = $c['weight'];
            }
        }

        return $this->customers[(int) $this->weighted($w)];
    }

    private function sampleDay(int $from, int $to): DateTimeImmutable
    {
        $dow = [1 => 0.8, 2 => 0.8, 3 => 0.85, 4 => 0.9, 5 => 1.15, 6 => 1.8, 7 => 1.6];
        $w = [];
        for ($o = $from; $o <= $to; $o++) {
            $date = $this->today->modify(($o >= 0 ? '+' : '') . $o . ' days');
            $trend = $o <= 0 ? 0.30 + 0.70 * ((self::HISTORY_DAYS + $o) / self::HISTORY_DAYS) : max(0.15, 0.9 - $o / 20);
            $w[$o] = $trend * $dow[(int) $date->format('N')];
        }
        $o = (int) $this->weighted($w);

        return $this->today->modify(($o >= 0 ? '+' : '') . $o . ' days');
    }

    /** @return null|array{day:DateTimeImmutable,start:DateTimeImmutable,startMin:int,endMin:int} */
    private function findSlot(array $f, DateTimeImmutable $day, int $dur, ?DateTimeImmutable $notBefore = null, ?DateTimeImmutable $notAfter = null): ?array
    {
        $day = $day->setTime(0, 0, 0);
        if ($day < $f['live']->setTime(0, 0, 0)->modify('+1 day')) {
            return null;
        }
        $sched = $f['sched'][(int) $day->format('N')];
        if ($sched['status'] !== 'buka') {
            return null;
        }
        $cands = [];
        for ($m = $sched['open']; $m + $dur * 60 <= $sched['close']; $m += 60) {
            $h = intdiv($m, 60);
            $w = $h < 11 ? 0.6 : ($h < 15 ? 0.5 : ($h < 17 ? 1.0 : ($h < 22 ? 2.4 : 0.8)));
            $start = $day->modify('+' . $m . ' minutes');
            if (($notBefore && $start < $notBefore) || ($notAfter && $start > $notAfter)) {
                continue;
            }
            if ($this->isTaken($f, $day, $m, $m + $dur * 60)) {
                continue;
            }
            $cands[$m] = $w;
        }
        if (! $cands) {
            return null;
        }
        $m = (int) $this->weighted($cands);

        return ['day' => $day, 'start' => $day->modify('+' . $m . ' minutes'), 'startMin' => $m, 'endMin' => $m + $dur * 60];
    }

    private function isTaken(array $f, DateTimeImmutable $day, int $a, int $b): bool
    {
        $key = $day->format('Y-m-d');
        for ($t = $a; $t < $b; $t += 30) {
            if (isset($this->occ[$f['id']][$key][intdiv($t, 30)])) {
                return true;
            }
        }
        foreach ($f['blocks'] as [$from, $to, $x, $y]) {
            if ($day >= $from && $day <= $to && $a < $y && $b > $x) {
                return true;
            }
        }

        return false;
    }

    private function occupy(int $fid, DateTimeImmutable $day, int $a, int $b): void
    {
        $key = $day->format('Y-m-d');
        for ($t = $a; $t < $b; $t += 30) {
            $this->occ[$fid][$key][intdiv($t, 30)] = true;
        }
    }

    /** Waktu pelunasan sebelum jadwal main: banyak yang H-0 sampai H-2, sebagian jauh hari. */
    private function paidBefore(DateTimeImmutable $playStart): DateTimeImmutable
    {
        $lead = match ($this->weighted(['same' => 25, 'near' => 35, 'mid' => 25, 'far' => 15])) {
            'same' => mt_rand(40, 6 * 60),
            'near' => mt_rand(20 * 60, 2 * 24 * 60),
            'mid' => mt_rand(3 * 24 * 60, 5 * 24 * 60),
            default => mt_rand(6 * 24 * 60, 13 * 24 * 60),
        };
        $paid = $playStart->modify('-' . $lead . ' minutes');
        $limit = $this->now->modify('-12 minutes');
        if ($paid > $limit) {
            // Jadwal yang belum lewat sudah dibayar lebih dulu: sebarkan ke beberapa hari terakhir (condong ke yang baru).
            $lag = (int) min(10 * 1440, 15 + (-log(max(1e-6, mt_rand() / mt_getrandmax())) * 1.6 * 1440));
            $paid = $limit->modify('-' . $lag . ' minutes');
        }

        return $paid;
    }

    private function minDate(DateTimeImmutable $a, DateTimeImmutable $b): DateTimeImmutable
    {
        return $a < $b ? $a : $b;
    }
}
