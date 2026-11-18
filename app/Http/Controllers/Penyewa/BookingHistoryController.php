<?php

namespace App\Http\Controllers\Penyewa;

use App\Http\Controllers\Controller;
use App\Models\BookingHeader;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingHistoryController extends Controller
{
    /**
     * Tampilkan halaman Riwayat Pemesanan / Booking Penyewa berbasis data riil database.
     */
    public function index(Request $request): View
    {
        Carbon::setLocale('id');

        $currentStatus = strtolower($request->query('status', 'semua'));
        $search = trim($request->query('search', ''));
        $selectedMonth = $request->query('bulan', '');

        // 1. Dapatkan pengguna aktif (auth) atau fallback customer pertama untuk sesi lokal/testing
        $user = auth()->user() ?? User::where('role', 'customer')->first();
        $userId = $user?->id;

        // 2. Ambil data transaksi riil dari tabel booking_headers milik user
        $headers = $userId
            ? BookingHeader::with([
                'details.field.venue.city',
                'details.field.sport',
                'details.field.photos',
                'payment',
            ])->where('penyewa_id', $userId)
              ->orderByDesc('id')
              ->get()
            : collect();

        $now = Carbon::now();

        // 3. Map relasi database ke format tampilan riwayat
        $allBookings = $headers->map(function ($booking) use ($now) {
            $details = $booking->details;
            $detailFirst = $details->first();
            $field = $detailFirst?->field;
            $venue = $field?->venue;
            $sport = $field?->sport;

            $headerStatus = strtolower(trim($booking->status ?? ''));

            // Tentukan status UI, aksi, dan sub-teks harga
            if ($headerStatus === 'pending') {
                $status = 'pending';
                $actionType = 'pay';
                $priceSub = 'Batas bayar: 45 mnt';
                $priceSubType = 'amber';
            } elseif (in_array($headerStatus, ['canceled', 'cancelled', 'expired'])) {
                $status = 'cancelled';
                $actionType = 'detail';
                $priceSub = ($headerStatus === 'expired') ? 'Waktu bayar habis' : 'Pesanan dibatalkan';
                $priceSubType = 'rose';
            } else {
                // Status 'payment' atau sejenisnya
                $isCompleted = $details->isNotEmpty() && $details->every(function ($d) use ($now) {
                    if (in_array($d->status_item, ['completed', 'no_show'])) {
                        return true;
                    }
                    $tgl = Carbon::parse($d->tanggal_sewa)->format('Y-m-d');
                    $jam = substr($d->jam_selesai ?? '23:59:00', 0, 8);
                    return Carbon::parse("{$tgl} {$jam}")->isPast();
                });

                if ($isCompleted) {
                    $status = 'completed';
                    $actionType = 'review';
                    $priceSub = 'Selesai dimainkan';
                    $priceSubType = 'slate';
                } else {
                    $status = 'confirmed';
                    $actionType = 'detail';
                    $paymentMethod = $booking->payment?->metode_pembayaran ?? 'Online';
                    $priceSub = 'Lunas via ' . $paymentMethod;
                    $priceSubType = 'emerald';
                }
            }

            // Format tanggal & jam
            $tanggalSewaObj = Carbon::parse($detailFirst?->tanggal_sewa ?? $booking->created_at);
            $dateFormatted = $tanggalSewaObj->translatedFormat('l, d M Y');

            $minJamMulai = $details->min('jam_mulai') ?? '08:00:00';
            $maxJamSelesai = $details->max('jam_selesai') ?? '10:00:00';
            $jamMulaiFormatted = substr($minJamMulai, 0, 5);
            $jamSelesaiFormatted = substr($maxJamSelesai, 0, 5);
            $totalJam = $details->count() > 0 ? $details->count() : 1;
            $timeFormatted = "{$jamMulaiFormatted} - {$jamSelesaiFormatted} WIB ({$totalJam} Jam)";

            // Kode booking & identitas arena
            $bookingCode = '#SP-' . str_pad($booking->id, 5, '0', STR_PAD_LEFT);
            $sportUpper = strtoupper($sport?->nama_olahraga ?? $sport?->slug ?? 'FUTSAL');

            // Foto arena
            $primaryPhoto = $field?->photos?->firstWhere('is_primary', 1) ?? $field?->photos?->first();
            $photoPath = $primaryPhoto?->foto_url;
            $imageSrc = null;
            if ($photoPath) {
                if (str_starts_with($photoPath, 'http://') || str_starts_with($photoPath, 'https://')) {
                    $imageSrc = $photoPath;
                } elseif (str_starts_with($photoPath, 'images/')) {
                    $imageSrc = asset($photoPath);
                } else {
                    $imageSrc = asset('storage/' . $photoPath);
                }
            }

            $venueName = $venue?->nama_venue ?? 'Sport Arena';
            $fieldName = $field?->nama_lapangan ?? 'Lapangan';
            $location = $venue?->alamat_lengkap ?? ($venue?->city?->nama_kota ?? 'Denpasar');

            $totalPrice = (float) ($booking->total_tagihan > 0 ? $booking->total_tagihan : $details->sum('harga_slot'));

            return [
                'id'             => $booking->id,
                'sport'          => $sportUpper,
                'venue_name'     => $venueName,
                'field_name'     => $fieldName,
                'booking_code'   => $bookingCode,
                'location'       => $location,
                'date'           => $dateFormatted,
                'time'           => $timeFormatted,
                'price'          => $totalPrice,
                'price_sub'      => $priceSub,
                'price_sub_type' => $priceSubType,
                'status'         => $status,
                'action_type'    => $actionType,
                'detail_url'     => route('penyewa.booking.detail', ['id' => $booking->id]),
                'pay_url'        => route('penyewa.pembayaran.review', ['id' => $booking->id]),
                'review_url'     => url('/penyewa/booking/' . $booking->id . '/ulasan'),
                'image_src'      => $imageSrc,
                'month_key'      => $tanggalSewaObj->format('Y-m'),
            ];
        });

        // 4. Hitung jumlah booking per status (dari seluruh data user sebelum filter tab)
        $counts = [
            'semua'     => $allBookings->count(),
            'pending'   => $allBookings->where('status', 'pending')->count(),
            'confirmed' => $allBookings->where('status', 'confirmed')->count(),
            'completed' => $allBookings->where('status', 'completed')->count(),
            'cancelled' => $allBookings->where('status', 'cancelled')->count(),
        ];

        // 5. Filter berdasarkan status tab
        $filtered = $allBookings;
        if ($currentStatus !== 'semua' && array_key_exists($currentStatus, $counts)) {
            $filtered = $filtered->where('status', $currentStatus);
        }

        // 6. Filter berdasarkan pilihan periode bulan
        $currentMonthKey = Carbon::now()->format('Y-m');
        $allMonthKeys = $allBookings->pluck('month_key')->filter();

        $uniqueMonths = collect([$currentMonthKey])
            ->merge($allMonthKeys)
            ->unique()
            ->sortDesc()
            ->values();

        $monthOptions = [];
        foreach ($uniqueMonths as $ym) {
            $dt = Carbon::createFromFormat('Y-m', $ym);
            $isCurrent = ($ym === $currentMonthKey);
            $label = $dt->translatedFormat('F Y');
            if ($isCurrent) {
                $label = 'Bulan Ini (' . $dt->translatedFormat('M Y') . ')';
            }
            $monthOptions[$ym] = $label;
        }

        $hasMonthFilter = false;
        if (!empty($selectedMonth) && $selectedMonth !== 'semua') {
            $filtered = $filtered->where('month_key', $selectedMonth);
            $hasMonthFilter = true;
            try {
                $dtSelected = Carbon::createFromFormat('Y-m', $selectedMonth);
                $monthLabel = 'Filter: ' . $dtSelected->translatedFormat('M Y');
            } catch (\Exception $e) {
                $monthLabel = 'Filter: ' . $selectedMonth;
            }
        } else {
            $selectedMonth = 'semua';
            $monthLabel = 'Filter';
        }

        // 7. Filter pencarian teks
        if (!empty($search)) {
            $searchTerm = strtolower($search);
            $filtered = $filtered->filter(function ($item) use ($searchTerm) {
                return str_contains(strtolower($item['venue_name']), $searchTerm)
                    || str_contains(strtolower($item['field_name']), $searchTerm)
                    || str_contains(strtolower($item['booking_code']), $searchTerm)
                    || str_contains(strtolower($item['sport']), $searchTerm);
            });
        }

        // 8. Pagination (6 item per halaman sesuai desain layout)
        $perPage = 6;
        $total = $filtered->count();
        $lastPage = max(1, (int) ceil($total / $perPage));
        $page = max(1, min((int) $request->query('page', 1), $lastPage));

        $currentPageItems = $filtered->slice(($page - 1) * $perPage, $perPage)->values();
        $from = $total === 0 ? 0 : (($page - 1) * $perPage) + 1;
        $to = min($page * $perPage, $total);

        return view('penyewa.riwayat', [
            'bookings'       => $currentPageItems,
            'currentStatus'  => $currentStatus,
            'selectedMonth'  => $selectedMonth,
            'hasMonthFilter' => $hasMonthFilter,
            'monthLabel'     => $monthLabel,
            'monthOptions'   => $monthOptions,
            'counts'         => $counts,
            'search'         => $search,
            'from'           => $from,
            'to'             => $to,
            'total'          => $total,
            'currentPage'    => $page,
            'lastPage'       => $lastPage,
        ]);
    }
}
