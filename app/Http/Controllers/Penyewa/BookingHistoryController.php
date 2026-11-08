<?php

namespace App\Http\Controllers\Penyewa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingHistoryController extends Controller
{
    /**
     * Tampilkan halaman Riwayat Pemesanan / Booking Penyewa.
     */
    public function index(Request $request): View
    {
        $currentStatus = $request->query('status', 'semua');
        $search = $request->query('search', '');

        // Data riwayat booking sesuai referensi
        $allBookings = collect([
            [
                'id' => 1,
                'sport' => 'TENIS',
                'venue_name' => 'Champion Tennis Club',
                'field_name' => 'Court 2',
                'booking_code' => '#SP-88294',
                'location' => 'Kediri, Tabanan',
                'date' => 'Kamis, 24 Okt 2024',
                'time' => '16:00 - 18:00 WIB (2 Jam)',
                'price' => 350000,
                'price_sub' => 'Batas bayar: 45 mnt',
                'price_sub_type' => 'amber',
                'status' => 'pending',
                'action_type' => 'pay',
                'detail_url' => route('penyewa.booking.detail', ['id' => 1]),
                'pay_url' => route('penyewa.pembayaran.review', ['id' => 1]),
                'image_src' => asset('images/courts/tennis.jpg'),
            ],
            [
                'id' => 2,
                'sport' => 'FUTSAL',
                'venue_name' => 'Viva Arena Futsal',
                'field_name' => 'Lapangan Vinyl A',
                'booking_code' => '#SP-88102',
                'location' => 'Renon, Denpasar',
                'date' => 'Kamis, 24 Okt 2024',
                'time' => '18:00 - 20:00 WIB (2 Jam)',
                'price' => 302500,
                'price_sub' => 'Lunas via QRIS',
                'price_sub_type' => 'emerald',
                'status' => 'confirmed',
                'action_type' => 'detail',
                'detail_url' => url('/penyewa/booking/2'),
                'image_src' => asset('images/courts/futsal.jpg'),
            ],
            [
                'id' => 3,
                'sport' => 'BADMINTON',
                'venue_name' => 'Smash Center Arena',
                'field_name' => 'Lapangan 1 & 2',
                'booking_code' => '#SP-87941',
                'location' => 'Goa Gong, Jimbaran',
                'date' => 'Sabtu, 26 Okt 2024',
                'time' => '08:00 - 10:00 WIB (2 Jam)',
                'price' => 240000,
                'price_sub' => 'Lunas via BCA VA',
                'price_sub_type' => 'emerald',
                'status' => 'confirmed',
                'action_type' => 'detail',
                'detail_url' => url('/penyewa/booking/3'),
                'image_src' => asset('images/courts/badminton.jpg'),
            ],
            [
                'id' => 4,
                'sport' => 'BASKET',
                'venue_name' => 'Hoops Hall Arena',
                'field_name' => 'Full Court Timber',
                'booking_code' => '#SP-86532',
                'location' => 'Kedonganan, Kuta Selatan',
                'date' => 'Minggu, 20 Okt 2024',
                'time' => '20:00 - 22:00 WIB (2 Jam)',
                'price' => 600000,
                'price_sub' => 'Selesai dimainkan',
                'price_sub_type' => 'slate',
                'status' => 'completed',
                'action_type' => 'review',
                'detail_url' => url('/penyewa/booking/4'),
                'review_url' => url('/penyewa/booking/4/ulasan'),
                'image_src' => asset('images/courts/basketball.jpg'),
            ],
            [
                'id' => 5,
                'sport' => 'FUTSAL',
                'venue_name' => 'Bali Futsal Center',
                'field_name' => 'Court B',
                'booking_code' => '#SP-85190',
                'location' => 'Teuku Umar Barat',
                'date' => 'Rabu, 16 Okt 2024',
                'time' => '19:00 - 21:00 WIB (2 Jam)',
                'price' => 320000,
                'price_sub' => 'Selesai dimainkan',
                'price_sub_type' => 'slate',
                'status' => 'completed',
                'action_type' => 'detail',
                'detail_url' => url('/penyewa/booking/5'),
                'image_src' => asset('images/courts/futsal.jpg'),
            ],
            [
                'id' => 6,
                'sport' => 'TENIS',
                'venue_name' => 'Sanur Grand Tennis',
                'field_name' => 'Outdoor Court',
                'booking_code' => '#SP-84912',
                'location' => 'Sanur, Denpasar',
                'date' => 'Sabtu, 12 Okt 2024',
                'time' => '07:00 - 09:00 WIB (2 Jam)',
                'price' => 250000,
                'price_sub' => 'Dana dikembalikan 100%',
                'price_sub_type' => 'rose',
                'status' => 'cancelled',
                'action_type' => 'detail',
                'detail_url' => url('/penyewa/booking/6'),
                'image_src' => asset('images/courts/tennis.jpg'),
            ],
        ]);

        // Filter status jika ada
        $filtered = $allBookings;
        if ($currentStatus !== 'semua') {
            $filtered = $filtered->where('status', strtolower($currentStatus));
        }

        if (!empty($search)) {
            $filtered = $filtered->filter(function ($item) use ($search) {
                return str_contains(strtolower($item['venue_name']), strtolower($search))
                    || str_contains(strtolower($item['field_name']), strtolower($search))
                    || str_contains(strtolower($item['booking_code']), strtolower($search));
            });
        }

        // Hitungan per status
        $counts = [
            'semua'     => 12, // total riwayat
            'pending'   => 1,
            'confirmed' => 2,
            'completed' => 8,
            'cancelled' => 1,
        ];

        return view('penyewa.riwayat', [
            'bookings'      => $filtered,
            'currentStatus' => $currentStatus,
            'counts'        => $counts,
            'search'        => $search,
            'from'          => $filtered->isEmpty() ? 0 : 1,
            'to'            => $filtered->count(),
            'total'         => $allBookings->count(),
        ]);
    }
}
