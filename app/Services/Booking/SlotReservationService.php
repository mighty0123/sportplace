<?php

namespace App\Services\Booking;

use App\Exceptions\Booking\SlotUnavailableException;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class SlotReservationService
{
    /** Satuan terkecil dalam menit; hanya pembagi 60 yang sah. */
    public function unitMinutes(): int
    {
        $m = (int) config('booking.slot_minutes', 30);

        return in_array($m, [15, 20, 30, 60], true) ? $m : 30;
    }

    /**
     * Awal tiap satuan dalam rentang, mis. 19:00 sampai 20:00 pada satuan 30 menit menghasilkan 19:00:00 dan 19:30:00.
     * Selesai tepat 00:00 dibaca sebagai tengah malam hari yang sama.
     *
     * @return string[]
     */
    public function units(string $start, string $end): array
    {
        $s = $this->minutes($start);
        $e = $this->minutes($end);
        if ($e === 0 && $s > 0) {
            $e = 1440;
        }

        $u = $this->unitMinutes();
        if ($e <= $s || $s % $u !== 0 || $e % $u !== 0) {
            throw new InvalidArgumentException("Jam {$start} sampai {$end} tidak selaras dengan satuan slot {$u} menit.");
        }

        $out = [];
        for ($m = $s; $m < $e; $m += $u) {
            $out[] = sprintf('%02d:%02d:00', intdiv($m, 60), $m % 60);
        }

        return $out;
    }

    /**
     * Memegang semua slot booking ini. Idempoten: slot yang sudah dipegang booking yang sama dilewati.
     * Memakai transaksi sendiri (bersarang di transaksi pemanggil) sehingga gagal berarti tidak ada yang tersimpan.
     *
     * @throws SlotUnavailableException bila ada booking lain yang memegang salah satu slot
     * @throws InvalidArgumentException bila jam tidak selaras dengan satuan slot
     */
    public function reserve(int $headerId, bool $futureOnly = false): void
    {
        $details = DB::table('booking_details')->where('header_id', $headerId)
            ->when($futureOnly, fn ($q) => $q->where('tanggal_sewa', '>=', now()->startOfDay()))
            ->orderBy('id')->get(['id', 'field_id', 'tanggal_sewa', 'jam_mulai', 'jam_selesai']);

        DB::transaction(function () use ($headerId, $details) {
            foreach ($details as $d) {
                $fieldId = (int) $d->field_id;
                $date = substr((string) $d->tanggal_sewa, 0, 10);
                $units = $this->units((string) $d->jam_mulai, (string) $d->jam_selesai);

                // Pemegang yang basi dilepas lebih dulu: status booking tetap sumber kebenaran.
                $this->releaseStale($fieldId, $date, $units);

                $held = DB::table('slot_reservations')->where('field_id', $fieldId)->where('slot_date', $date)
                    ->whereIn('slot_start', $units)->where('holds', 1)->get(['header_id', 'slot_start']);

                if ($other = $held->first(fn ($r) => (int) $r->header_id !== $headerId)) {
                    throw $this->conflict($fieldId, $date, (string) $other->slot_start, (int) $other->header_id);
                }

                $have = $held->pluck('slot_start')->map(fn ($t) => substr((string) $t, 0, 8))->all();
                $rows = [];
                foreach ($units as $unit) {
                    if (! in_array($unit, $have, true)) {
                        $rows[] = [
                            'header_id' => $headerId, 'detail_id' => $d->id, 'field_id' => $fieldId, 'slot_date' => $date,
                            'slot_start' => $unit, 'holds' => 1, 'created_at' => now(),
                        ];
                    }
                }

                try {
                    if ($rows) {
                        DB::table('slot_reservations')->insert($rows);
                    }
                } catch (UniqueConstraintViolationException) {
                    // Kalah balapan: pemesan lain memegang slot ini di antara pemeriksaan dan penyimpanan.
                    throw $this->conflict($fieldId, $date, $rows[0]['slot_start'], null);
                }
            }
        }, 3);
    }

    /** Melepas semua slot milik booking ini. @return int jumlah baris yang dilepas */
    public function release(int $headerId): int
    {
        return DB::table('slot_reservations')->where('header_id', $headerId)->where('holds', 1)
            ->update(['holds' => null, 'released_at' => now()]);
    }

    /** Melepas pemegang basi pada slot tertentu. @return int jumlah baris yang dilepas */
    public function releaseStale(int $fieldId, string $date, array $units): int
    {
        $q = DB::table('slot_reservations as r')->join('booking_headers as h', 'h.id', '=', 'r.header_id')
            ->where('r.field_id', $fieldId)->where('r.slot_date', $date)->whereIn('r.slot_start', $units)->where('r.holds', 1);

        return $this->onlyStale($q)->update(['r.holds' => null, 'r.released_at' => now()]);
    }

    /**
     * Menyelaraskan reservasi dengan status booking: melepas yang basi, dan memegang slot mendatang milik booking aktif
     * yang belum punya reservasi (booking lama, atau dibuat oleh kode yang belum memanggil reserve). Booking yang
     * lunas didahulukan, sehingga bila ada bentrok lama, yang menunggu yang ditandai.
     *
     * @return array{released:int, reserved:int, conflicts:array<int,array<string,mixed>>, invalid:array<int,array<string,mixed>>}
     */
    public function sync(bool $dryRun = false): array
    {
        $out = ['released' => 0, 'reserved' => 0, 'conflicts' => [], 'invalid' => []];

        $stale = $this->onlyStale(DB::table('slot_reservations as r')->join('booking_headers as h', 'h.id', '=', 'r.header_id')->where('r.holds', 1));
        $out['released'] = (clone $stale)->count();
        if (! $dryRun && $out['released'] > 0) {
            $stale->update(['r.holds' => null, 'r.released_at' => now()]);
        }

        $window = max(1, (int) config('booking.payment_window_minutes', 15));
        $candidates = DB::table('booking_headers as h')
            ->where(fn ($w) => $w->where('h.status', 'payment')
                ->orWhere(fn ($p) => $p->where('h.status', 'pending')
                    ->whereRaw("COALESCE(h.expires_at, h.created_at + INTERVAL {$window} MINUTE) > ?", [now()])))
            ->whereExists(fn ($e) => $e->selectRaw('1')->from('booking_details as d')
                ->whereColumn('d.header_id', 'h.id')->where('d.tanggal_sewa', '>=', now()->startOfDay()))
            ->whereNotExists(fn ($e) => $e->selectRaw('1')->from('slot_reservations as r')
                ->whereColumn('r.header_id', 'h.id')->where('r.holds', 1))
            ->orderByRaw("h.status = 'payment' DESC")->orderBy('h.id')
            ->get(['h.id', 'h.kode_booking', 'h.status']);

        foreach ($candidates as $h) {
            DB::beginTransaction();
            try {
                $this->reserve((int) $h->id, futureOnly: true);
                $out['reserved']++;
                $dryRun ? DB::rollBack() : DB::commit();
            } catch (SlotUnavailableException $e) {
                DB::rollBack();
                $out['conflicts'][] = ['id' => (int) $h->id, 'kode' => $h->kode_booking, 'status' => $h->status,
                    'message' => $e->getMessage(), 'holder' => $e->holderCode];
            } catch (InvalidArgumentException $e) {
                DB::rollBack();
                $out['invalid'][] = ['id' => (int) $h->id, 'kode' => $h->kode_booking, 'status' => $h->status, 'message' => $e->getMessage()];
            }
        }

        return $out;
    }

    /** Membuang riwayat yang sudah tidak berguna: slot lewat lebih dari sekian hari, atau baris yang dilepas sudah lama. */
    public function prune(int $days): int
    {
        if ($days <= 0) {
            return 0;
        }

        return DB::table('slot_reservations')->where(fn ($w) => $w
            ->where('slot_date', '<', now()->subDays($days)->toDateString())
            ->orWhere(fn ($r) => $r->whereNull('holds')->where('released_at', '<', now()->subDays($days))))->delete();
    }

    // ---- helpers

    private function onlyStale(Builder $q): Builder
    {
        $window = max(1, (int) config('booking.payment_window_minutes', 15));

        return $q->where(function ($w) use ($window) {
            $w->whereIn('h.status', ['canceled', 'expired'])
                ->orWhere(fn ($p) => $p->where('h.status', 'pending')
                    ->whereRaw("COALESCE(h.expires_at, h.created_at + INTERVAL {$window} MINUTE) <= ?", [now()])
                    ->whereNotExists(fn ($e) => $e->selectRaw('1')->from('payments as p')
                        ->whereColumn('p.header_id', 'h.id')->where('p.status_pembayaran', 'succes')));
        });
    }

    private function conflict(int $fieldId, string $date, string $unit, ?int $holderHeaderId): SlotUnavailableException
    {
        $holderHeaderId ??= DB::table('slot_reservations')->where('field_id', $fieldId)->where('slot_date', $date)
            ->where('slot_start', $unit)->where('holds', 1)->value('header_id');
        $code = $holderHeaderId ? DB::table('booking_headers')->where('id', $holderHeaderId)->value('kode_booking') : null;

        return new SlotUnavailableException($fieldId, $date, $unit, $code);
    }

    private function minutes(string $time): int
    {
        if (! preg_match('/^(\d{1,2}):(\d{2})(?::\d{2})?$/', trim($time), $m)) {
            throw new InvalidArgumentException("Format jam tidak valid: {$time}");
        }

        return (int) $m[1] * 60 + (int) $m[2];
    }
}