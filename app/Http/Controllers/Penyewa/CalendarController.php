<?php

namespace App\Http\Controllers\Penyewa;

use App\Http\Controllers\Controller;
use App\Models\BookingDetail;
use App\Models\Field;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalendarController extends Controller
{
    /**
     * Tampilkan halaman Jadwal & Kalender Pemesanan Lapangan per tanggal.
     * Pengguna memilih tanggal, lalu slot jam untuk hari tersebut ditampilkan dalam grid kartu.
     */
    public function index(Request $request, ?int $id = null): View|RedirectResponse|\Illuminate\Http\JsonResponse
    {
        // 1. Tentukan Lapangan yang dipilih
        $fieldId = $id ?? (int) $request->query('field', 0);
        $field = null;

        if ($fieldId > 0) {
            $field = Field::with(['venue.city', 'venue.mitra', 'sport', 'facilities', 'photos'])->find($fieldId);
        }

        // Jika tidak ditemukan atau belum pilih, ambil lapangan pertama yang aktif
        if (!$field) {
            $field = Field::with(['venue.city', 'venue.mitra', 'sport', 'facilities', 'photos'])
                ->where('status_lapangan', 'aktif')
                ->first();
        }

        // Fallback darurat jika database sama sekali kosong
        if (!$field) {
            return redirect()->route('penyewa.lapangan')->with('error', 'Belum ada lapangan aktif yang tersedia.');
        }

        // Simpan ID lapangan aktif ke session agar review & pembayaran konsisten
        session(['selected_field_id' => $field->id, 'last_field_id' => $field->id]);

        // 2. Foto Lapangan & Venue Banner
        $sportSlug = strtolower($field->sport?->slug ?? 'futsal');
        $defaultImages = [
            'futsal'      => asset('images/courts/futsal.jpg'),
            'badminton'   => asset('images/courts/badminton.jpg'),
            'tenis'       => asset('images/courts/tennis.jpg'),
            'basket'      => asset('images/courts/basketball.jpg'),
            'mini-soccer' => asset('images/courts/futsal.jpg'),
        ];
        $primaryPhoto = $field->photos->firstWhere('is_primary', 1) ?? $field->photos->first();
        $photoUrl = $primaryPhoto ? asset($primaryPhoto->foto_url) : ($defaultImages[$sportSlug] ?? asset('images/courts/futsal.jpg'));

        $venue = [
            'id'            => $field->id,
            'field_id'      => $field->id,
            'sport'         => strtoupper($field->sport?->nama_olahraga ?? 'OLAHRAGA'),
            'name'          => ($field->venue?->nama_venue ?? 'Sport Arena') . ' — ' . $field->nama_lapangan,
            'court_type'    => $field->nama_lapangan,
            'rating'        => 4.8,
            'reviews_count' => 120 + ($field->id * 5),
            'location'      => $field->venue?->alamat_lengkap ?? 'Indonesia',
            'base_price'    => (int) $field->harga_perjam,
            'image_src'     => $photoUrl,
        ];

        // 3. Tanggal yang Dipilih (Default: Hari Ini)
        $tz = config('app.timezone', 'Asia/Makassar');
        $today = Carbon::today($tz);
        $dateParam = $request->query('date', $today->format('Y-m-d'));

        try {
            $selectedCarbon = Carbon::parse($dateParam, $tz)->startOfDay();
        } catch (\Exception $e) {
            $selectedCarbon = $today->copy();
        }

        $selectedDateStr = $selectedCarbon->format('Y-m-d');
        $selectedDateShort = $selectedCarbon->format('d M'); // e.g. "01 Nov"
        $selectedDayName = $selectedCarbon->format('l');      // e.g. "Sunday"

        $dayNamesIndo = [
            1 => 'Sen', 2 => 'Sel', 3 => 'Rab', 4 => 'Kam', 5 => 'Jum', 6 => 'Sab', 7 => 'Min'
        ];
        $fullDayNamesIndo = [
            1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'
        ];
        $monthNamesIndo = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
            7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];

        // 4. Daftar Tanggal Carousel (10 Hari ke Depan)
        $dates = [];
        for ($i = 0; $i < 10; $i++) {
            $d = $today->copy()->addDays($i);
            $fullDateStr = $d->format('Y-m-d');
            $dates[] = [
                'day'       => $dayNamesIndo[$d->dayOfWeekIso] ?? $d->format('D'),
                'day_full'  => $fullDayNamesIndo[$d->dayOfWeekIso] ?? $d->format('l'),
                'date'      => $d->format('d') . ' ' . ($monthNamesIndo[$d->month] ?? $d->format('M')),
                'date_short'=> $d->format('d M'),
                'full_date' => $fullDateStr,
                'active'    => $fullDateStr === $selectedDateStr,
            ];
        }

        $formattedSelectedDate = ($fullDayNamesIndo[$selectedCarbon->dayOfWeekIso] ?? $selectedCarbon->format('l')) . ', ' .
            $selectedCarbon->format('d') . ' ' .
            ($monthNamesIndo[$selectedCarbon->month] ?? $selectedCarbon->format('M')) . ' ' .
            $selectedCarbon->format('Y');

        // 5. Booking yang ada pada hari yang dipilih untuk lapangan ini
        $dayBookings = BookingDetail::with('header')
            ->where('field_id', $field->id)
            ->whereDate('tanggal_sewa', $selectedDateStr)
            ->get();

        // 6. Jam Operasional Venue
        $jamBuka = $field->venue?->jam_buka ? (int) substr($field->venue->jam_buka, 0, 2) : 8;
        $jamTutup = $field->venue?->jam_tutup ? (int) substr($field->venue->jam_tutup, 0, 2) : 23;
        if ($jamTutup <= $jamBuka) {
            $jamBuka = 8;
            $jamTutup = 23;
        }

        // 7. Siapkan Slot Jam untuk SATU Hari Tersebut
        $now = Carbon::now($tz);

        $timeSlots = [];
        for ($hour = $jamBuka; $hour < $jamTutup; $hour++) {
            $startStr = sprintf('%02d:00', $hour);
            $endStr   = sprintf('%02d:00', $hour + 1);
            $timeLabel = "{$startStr} - {$endStr}";
            $slotId = "slot_{$field->id}_{$selectedCarbon->format('Ymd')}_{$hour}";

            // 1. Cek apakah jam mulai slot sudah lewat dari waktu saat ini.
            // Begitu waktu sekarang melewati jam_mulai ($startStr), slot LANGSUNG dikunci (lock), tidak menunggu jam_selesai.
            $slotStartDateTime = Carbon::parse("{$selectedDateStr} {$startStr}", $tz);
            $isPast = $selectedCarbon->lt($today) || ($selectedCarbon->isSameDay($today) && $slotStartDateTime->lte($now));

            // 2. Cek semua booking yang overlapping pada jam slot ini
            $slotBookings = $dayBookings->filter(function ($b) use ($selectedDateStr, $startStr) {
                $rawDate = $b->getRawOriginal('tanggal_sewa') ?? $b->tanggal_sewa;
                $bDate = substr((string)$rawDate, 0, 10);
                if ($bDate !== $selectedDateStr) {
                    return false;
                }
                $bStart = substr((string)$b->jam_mulai, 0, 5);
                $bEnd   = substr((string)$b->jam_selesai, 0, 5);
                return ($startStr >= $bStart && $startStr < $bEnd);
            });

            // Status konfirmasi: booking sudah terbayar / confirmed / completed
            $isConfirmed = $slotBookings->contains(function ($b) {
                $status = strtolower(trim($b->header?->status ?? ''));
                return in_array($status, ['payment', 'completed', 'confirmed']) ||
                    (!$status && in_array($b->status_item, ['ongoing', 'completed']));
            });

            // Status pending: ada orang yang sedang checkout/bayar slot ini (< 15 menit)
            $isPending = false;
            $paymentWindowMinutes = (int) config('booking.payment_window_minutes', 15);
            foreach ($slotBookings as $b) {
                $header = $b->header;
                if ($header && strtolower(trim($header->status ?? '')) === 'pending') {
                    $rawExpires = $header->getRawOriginal('expires_at');
                    $rawCreated = $header->getRawOriginal('created_at');
                    $bExpires = $rawExpires
                        ? Carbon::parse($rawExpires, $tz)
                        : ($rawCreated ? Carbon::parse($rawCreated, $tz)->addMinutes($paymentWindowMinutes) : $now);

                    if ($bExpires->gt($now)) {
                        $isPending = true; // Masih aktif dalam batas waktu 15 menit -> tampil sebagai Booked
                    } else {
                        // Batas bayar habis (lewat 15 menit): update status menjadi expired -> balik ke Available
                        $header->update(['status' => 'expired']);
                    }
                }
            }

            // Indikator hanya 3 status: Available, Dipilih, dan Booked.
            // - Jika sudah terbayar, atau sedang dibooking orang (pending < 15 menit), atau jam slot sudah lewat:
            //   Maka status = 'booked', label = 'Booked'.
            // - Jika masa tahan lewat (expired) dan belum lewat jam mulai:
            //   Maka otomatis balik ke status = 'available', label = 'Available'.
            if ($isConfirmed || $isPending || $isPast) {
                $status = 'booked';
                $label  = 'Booked';
            } else {
                $status = 'available';
                $label  = 'Available';
            }

            // Hitung harga slot (cek meta harga siang/malam jika ada)
            $slotPrice = (int) $field->harga_perjam;
            if (!empty($field->meta_harga) && is_array($field->meta_harga)) {
                if ($hour < 17 && isset($field->meta_harga['siang'])) {
                    $slotPrice = (int) $field->meta_harga['siang'];
                } elseif ($hour >= 17 && isset($field->meta_harga['malam'])) {
                    $slotPrice = (int) $field->meta_harga['malam'];
                }
            }

            $timeSlots[] = [
                'id'          => $slotId,
                'date_short'  => $selectedDateShort,
                'full_date'   => $selectedDateStr,
                'day_name'    => $selectedDayName,
                'time_start'  => $startStr,
                'time_end'    => $endStr,
                'time_label'  => $timeLabel,
                'price'       => $slotPrice,
                'status'      => $status,
                'label'       => $label,
                'court_name'  => $field->nama_lapangan,
            ];
        }

        $serviceFee = 2500;

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status'     => 'success',
                'timeSlots'  => $timeSlots,
                'serviceFee' => $serviceFee,
            ]);
        }

        return view('penyewa.kalender', [
            'field'                 => $field,
            'venue'                 => $venue,
            'dates'                 => $dates,
            'selectedDate'          => $selectedDateStr,
            'selectedDateStr'       => $selectedDateStr,
            'selectedDateShort'     => $selectedDateShort,
            'selectedDayName'       => $selectedDayName,
            'formattedSelectedDate' => $formattedSelectedDate,
            'timeSlots'             => $timeSlots,
            'serviceFee'            => $serviceFee,
        ]);
    }
}
