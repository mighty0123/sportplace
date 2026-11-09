<?php

namespace App\Services\Mitra;

use App\Models\BookingDetail;
use App\Models\Field;
use App\Models\FieldBlock;
use App\Models\FieldSchedule;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class SlotGenerator
{
    /**
     * Membentuk daftar slot waktu operasional per lapangan untuk tanggal tertentu.
     * Mengurangi jadwal dasar dengan blokir lapangan (field_blocks) dan booking aktif (booking_details).
     *
     * @return array{
     *     date: string,
     *     day_name: string,
     *     is_open: bool,
     *     reason?: string,
     *     jam_buka?: string,
     *     jam_tutup?: string,
     *     slot_duration_minutes?: int,
     *     total_slots: int,
     *     available_slots: int,
     *     slots: array<int, array{
     *         start_time: string,
     *         end_time: string,
     *         label: string,
     *         is_available: bool,
     *         status: 'available'|'booked'|'blocked'|'past',
     *         reason: ?string,
     *         price: float,
     *         block_id: ?int,
     *         booking_id: ?int,
     *         penyewa_name: ?string
     *     }>
     * }
     */
    public function generateSlots(Field $field, CarbonInterface|string $date): array
    {
        $carbonDate = $date instanceof CarbonInterface ? Carbon::instance($date) : Carbon::parse($date);
        $dateString = $carbonDate->toDateString();
        $isoDay = $carbonDate->dayOfWeekIso; // 1 = Senin, 7 = Minggu (ISO-8601)

        // 1. Ambil jadwal operasional untuk hari tersebut
        $schedule = $field->relationLoaded('schedules')
            ? $field->schedules->firstWhere('hari', $isoDay)
            : $field->schedules()->where('hari', $isoDay)->first();

        $dayName = FieldSchedule::DAYS[$isoDay] ?? 'Hari '.$isoDay;

        // Jika tidak ada jadwal atau status hari 'tutup' (AC: SCHED-001-02)
        if (! $schedule || $schedule->status_hari === 'tutup' || empty($schedule->jam_buka) || empty($schedule->jam_tutup)) {
            return [
                'date' => $dateString,
                'day_name' => $dayName,
                'is_open' => false,
                'reason' => 'Tutup (Hari Libur / Tidak Beroperasi)',
                'total_slots' => 0,
                'available_slots' => 0,
                'slots' => [],
            ];
        }

        // 2. Parse jam buka, jam tutup, dan durasi slot (default 60 menit)
        $openTime = Carbon::createFromTimeString($schedule->jam_buka);
        $closeTime = Carbon::createFromTimeString($schedule->jam_tutup);

        $durationMinutes = $this->parseDurationMinutes($schedule->durasi ?? '01:00:00');

        // Pastikan jam tutup lebih besar dari jam buka
        if ($closeTime->lte($openTime) || $durationMinutes <= 0) {
            return [
                'date' => $dateString,
                'day_name' => $dayName,
                'is_open' => false,
                'reason' => 'Konfigurasi Jam Operasional Tidak Valid',
                'total_slots' => 0,
                'available_slots' => 0,
                'slots' => [],
            ];
        }

        // 3. Ambil data blokir yang aktif pada tanggal ini (KS-15 & AC: SCHED-002-01)
        $blocks = $field->relationLoaded('blocks')
            ? $field->blocks->filter(function ($b) use ($dateString) {
                $start = Carbon::parse($b->tanggal_mulai)->toDateString();
                $end = Carbon::parse($b->tanggal_selesai)->toDateString();

                return $dateString >= $start && $dateString <= $end;
            })
            : FieldBlock::query()
                ->where('field_id', $field->id)
                ->whereDate('tanggal_mulai', '<=', $dateString)
                ->whereDate('tanggal_selesai', '>=', $dateString)
                ->get();

        // 4. Ambil booking aktif yang terkonfirmasi/sedang berlangsung pada tanggal ini
        $bookings = $field->relationLoaded('bookingDetails')
            ? $field->bookingDetails->filter(function ($b) use ($dateString) {
                $tgl = Carbon::parse($b->tanggal_sewa)->toDateString();
                $statusHeader = $b->header?->status;

                return $tgl === $dateString && ! in_array($statusHeader, ['canceled', 'expired'], true);
            })
            : BookingDetail::with('header.user')
                ->where('field_id', $field->id)
                ->whereDate('tanggal_sewa', $dateString)
                ->whereHas('header', function ($q) {
                    $q->whereNotIn('status', ['canceled', 'expired']);
                })
                ->get();

        // 5. Generate slot waktu
        $slots = [];
        $currentTime = $openTime->copy();
        $isToday = $carbonDate->isToday();
        $isPastDay = $carbonDate->isPast() && ! $isToday;
        $nowTimeString = now()->format('H:i');
        $priceCalculator = new PriceCalculator;

        while ($currentTime->copy()->addMinutes($durationMinutes)->lte($closeTime)) {
            $slotStart = $currentTime->format('H:i');
            $slotEnd = $currentTime->copy()->addMinutes($durationMinutes)->format('H:i');

            $status = 'available';
            $isAvailable = true;
            $reason = null;
            $blockId = null;
            $bookingId = null;
            $penyewaName = null;

            // Hitung tarif spesifik slot (mendukung peak/off-peak FR-FIELD-006)
            $pricing = $priceCalculator->calculateSlotPrice($field, $carbonDate, $slotStart, $slotEnd);

            // Cek bentrok dengan blokir lapangan (T6.3)
            foreach ($blocks as $block) {
                $blockStart = substr($block->jam_mulai, 0, 5);
                $blockEnd = substr($block->jam_selesai, 0, 5);

                // Slot bertabrakan jika start < blockEnd && end > blockStart
                if ($slotStart < $blockEnd && $slotEnd > $blockStart) {
                    $status = 'blocked';
                    $isAvailable = false;
                    $reason = $block->alasan_pemblokiran ?: 'Pemeliharaan Lapangan';
                    $blockId = $block->id;
                    break;
                }
            }

            // Cek bentrok dengan reservasi yang sudah ada (jika belum diblokir)
            if ($status === 'available') {
                foreach ($bookings as $booking) {
                    $bStart = substr($booking->jam_mulai, 0, 5);
                    $bEnd = substr($booking->jam_selesai, 0, 5);

                    if ($slotStart < $bEnd && $slotEnd > $bStart) {
                        $status = 'booked';
                        $isAvailable = false;
                        $reason = 'Sudah Dipesan';
                        $bookingId = $booking->id;
                        $penyewaName = $booking->header?->user?->name ?? 'Penyewa';
                        break;
                    }
                }
            }

            // Cek jika slot waktu sudah terlewat di hari ini atau hari kemarin
            if ($status === 'available') {
                if ($isPastDay || ($isToday && $slotStart < $nowTimeString)) {
                    $status = 'past';
                    $isAvailable = false;
                    $reason = 'Waktu telah berlalu';
                }
            }

            $slots[] = [
                'start_time' => $slotStart,
                'end_time' => $slotEnd,
                'label' => "{$slotStart} – {$slotEnd}",
                'is_available' => $isAvailable,
                'status' => $status,
                'reason' => $reason,
                'price' => $pricing['price'],
                'formatted_price' => $pricing['formatted_price'],
                'is_peak' => $pricing['is_peak'],
                'rate_type' => $pricing['rate_type'],
                'block_id' => $blockId,
                'booking_id' => $bookingId,
                'penyewa_name' => $penyewaName,
            ];

            $currentTime->addMinutes($durationMinutes);
        }

        return [
            'date' => $dateString,
            'day_name' => $dayName,
            'is_open' => true,
            'jam_buka' => substr($schedule->jam_buka, 0, 5),
            'jam_tutup' => substr($schedule->jam_tutup, 0, 5),
            'slot_duration_minutes' => $durationMinutes,
            'total_slots' => count($slots),
            'available_slots' => count(array_filter($slots, fn ($s) => $s['is_available'])),
            'slots' => $slots,
        ];
    }

    /**
     * Konversi durasi format TIME ('01:00:00' atau '01:00') menjadi menit integer.
     */
    private function parseDurationMinutes(?string $duration): int
    {
        if (empty($duration)) {
            return 60;
        }

        $parts = explode(':', $duration);
        $hours = (int) ($parts[0] ?? 1);
        $minutes = (int) ($parts[1] ?? 0);

        $total = ($hours * 60) + $minutes;

        return $total > 0 ? $total : 60;
    }
}
