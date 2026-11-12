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
    public function index(Request $request, ?int $id = null): View|RedirectResponse
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
            // Begitu waktu sekarang melewati jam_mulai ($startStr), slot LANGSUNG dikunci (lock / lewat), tidak menunggu jam_selesai.
            $slotStartDateTime = Carbon::parse("{$selectedDateStr} {$startStr}", $tz);
            $isPast = $selectedCarbon->lt($today) || ($selectedCarbon->isSameDay($today) && $slotStartDateTime->lte($now));

            // 2. Cek apakah ada booking yang overlapping pada jam slot ini
            $matchingBooking = $dayBookings->first(function ($b) use ($selectedDateStr, $startStr) {
                $bDate = Carbon::parse($b->tanggal_sewa)->format('Y-m-d');
                if ($bDate !== $selectedDateStr) {
                    return false;
                }
                $bStart = substr($b->jam_mulai, 0, 5);
                $bEnd   = substr($b->jam_selesai, 0, 5);
                return ($startStr >= $bStart && $startStr < $bEnd);
            });

            // Tentukan status booking
            $headerStatus = $matchingBooking ? strtolower(trim($matchingBooking->header?->status ?? '')) : null;

            // Prioritas penentuan status slot:
            // 1) Booked: jika ada booking yang sudah terbayar / confirmed / completed
            // 2) Lewat: jika jam slot sudah terlewati
            // 3) Ditahan: jika ada booking yang statusnya pending (sedang proses pembayaran)
            // 4) Available: slot bebas untuk dipesan
            $isConfirmed = $matchingBooking && (
                in_array($headerStatus, ['payment', 'completed', 'confirmed']) ||
                (!$headerStatus && in_array($matchingBooking->status_item, ['ongoing', 'completed']))
            );

            // 3) Ditahan: jika ada booking pending yang BELUM melewati batas 15 menit
            $isPending = false;
            if ($matchingBooking && $headerStatus === 'pending') {
                $rawExpires = $matchingBooking->header?->getRawOriginal('expires_at');
                $rawCreated = $matchingBooking->header?->getRawOriginal('created_at');
                $bExpires = $rawExpires
                    ? Carbon::parse($rawExpires, $tz)
                    : ($rawCreated ? Carbon::parse($rawCreated, $tz)->addMinutes(15) : $now);

                if ($bExpires->gt($now)) {
                    $isPending = true; // Masih dalam batas 15 menit: kunci slot (Ditahan)
                } else {
                    // Batas 15 menit habis: otomatis lepas kunci dan perbarui status expired
                    if ($matchingBooking->header && $matchingBooking->header->status === 'pending') {
                        $matchingBooking->header->update(['status' => 'expired']);
                    }
                }
            }

            if ($isConfirmed) {
                $status = 'booked';
                $label  = 'Booked';
            } elseif ($isPast) {
                $status = 'past';
                $label  = 'Lewat';
            } elseif ($isPending) {
                $status = 'pending';
                $label  = 'Ditahan';
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
