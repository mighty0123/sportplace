<?php

namespace App\Http\Controllers\Penyewa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalendarController extends Controller
{
    /**
     * Tampilkan halaman Jadwal & Kalender Pemesanan Lapangan.
     */
    public function index(Request $request, ?int $id = null): View
    {
        $selectedDate = $request->query('date', '2024-10-24');

        // Data Venue / Lapangan sesuai referensi
        $venue = [
            'id' => $id ?? 1,
            'sport' => 'FUTSAL',
            'name' => 'Viva Arena Futsal - Lapangan Vinyl A',
            'court_type' => 'Indoor Vinyl Mat',
            'rating' => 4.8,
            'reviews_count' => 128,
            'location' => 'Renon, Denpasar, Bali',
            'base_price' => 150000,
            'image_src' => asset('images/courts/futsal.jpg'),
        ];

        // Daftar Tanggal Carousel
        $dates = [
            ['day' => 'Sen', 'date' => '21 Okt', 'full_date' => '2024-10-21', 'active' => false],
            ['day' => 'Sel', 'date' => '22 Okt', 'full_date' => '2024-10-22', 'active' => false],
            ['day' => 'Rab', 'date' => '23 Okt', 'full_date' => '2024-10-23', 'active' => false],
            ['day' => 'Kam', 'date' => '24 Okt', 'full_date' => '2024-10-24', 'active' => true],
            ['day' => 'Jum', 'date' => '25 Okt', 'full_date' => '2024-10-25', 'active' => false],
            ['day' => 'Sab', 'date' => '26 Okt', 'full_date' => '2024-10-26', 'active' => false],
            ['day' => 'Min', 'date' => '27 Okt', 'full_date' => '2024-10-27', 'active' => false],
            ['day' => 'Sen', 'date' => '28 Okt', 'full_date' => '2024-10-28', 'active' => false],
        ];

        // Kolom Lapangan
        $courts = [
            ['id' => 'c1', 'name' => 'Court 1', 'surface' => 'Vinyl Mat'],
            ['id' => 'c2', 'name' => 'Court 2', 'surface' => 'Vinyl Mat'],
            ['id' => 'c3', 'name' => 'Court 3', 'surface' => 'Interlock Pro'],
        ];

        // Baris Waktu & Slot Status
        $timeSlots = [
            [
                'time' => '08:00 - 09:00',
                'slots' => [
                    'c1' => ['id' => 'c1_0800', 'status' => 'available', 'label' => 'Tersedia', 'price' => 150000, 'court' => 'Court 1 (Vinyl)'],
                    'c2' => ['id' => 'c2_0800', 'status' => 'available', 'label' => 'Tersedia', 'price' => 150000, 'court' => 'Court 2 (Vinyl)'],
                    'c3' => ['id' => 'c3_0800', 'status' => 'booked', 'title' => 'Sparring Denpasar', 'subtitle' => 'Terisi'],
                ],
            ],
            [
                'time' => '09:00 - 10:00',
                'slots' => [
                    'c1' => ['id' => 'c1_0900', 'status' => 'available', 'label' => 'Tersedia', 'price' => 150000, 'court' => 'Court 1 (Vinyl)'],
                    'c2' => ['id' => 'c2_0900', 'status' => 'maintenance', 'title' => 'Perawatan Rutin', 'subtitle' => 'Pembersihan Mat'],
                    'c3' => ['id' => 'c3_0900', 'status' => 'available', 'label' => 'Tersedia', 'price' => 170000, 'court' => 'Court 3 (Interlock)'],
                ],
            ],
            [
                'time' => '15:00 - 16:00',
                'slots' => [
                    'c1' => ['id' => 'c1_1500', 'status' => 'booked', 'title' => 'Liga Mahasiswa', 'subtitle' => 'Terisi'],
                    'c2' => ['id' => 'c2_1500', 'status' => 'available', 'label' => 'Tersedia', 'price' => 150000, 'court' => 'Court 2 (Vinyl)'],
                    'c3' => ['id' => 'c3_1500', 'status' => 'available', 'label' => 'Tersedia', 'price' => 170000, 'court' => 'Court 3 (Interlock)'],
                ],
            ],
            [
                'time' => '16:00 - 17:00',
                'slots' => [
                    'c1' => ['id' => 'c1_1600', 'status' => 'available', 'label' => 'Tersedia', 'price' => 150000, 'court' => 'Court 1 (Vinyl)'],
                    'c2' => ['id' => 'c2_1600', 'status' => 'booked', 'title' => 'Komunitas Bali', 'subtitle' => 'Terisi'],
                    'c3' => ['id' => 'c3_1600', 'status' => 'available', 'label' => 'Tersedia', 'price' => 170000, 'court' => 'Court 3 (Interlock)'],
                ],
            ],
            [
                'time' => '18:00 - 19:00',
                'slots' => [
                    'c1' => ['id' => 'c1_1800', 'status' => 'selected', 'label' => 'Dipilih Anda', 'price' => 150000, 'court' => 'Court 1 (Vinyl)'],
                    'c2' => ['id' => 'c2_1800', 'status' => 'available', 'label' => 'Tersedia', 'price' => 150000, 'court' => 'Court 2 (Vinyl)'],
                    'c3' => ['id' => 'c3_1800', 'status' => 'available', 'label' => 'Tersedia', 'price' => 170000, 'court' => 'Court 3 (Interlock)'],
                ],
            ],
            [
                'time' => '19:00 - 20:00',
                'slots' => [
                    'c1' => ['id' => 'c1_1900', 'status' => 'selected', 'label' => 'Dipilih Anda', 'price' => 150000, 'court' => 'Court 1 (Vinyl)'],
                    'c2' => ['id' => 'c2_1900', 'status' => 'booked', 'title' => 'FC Garuda', 'subtitle' => 'Terisi'],
                    'c3' => ['id' => 'c3_1900', 'status' => 'available', 'label' => 'Tersedia', 'price' => 170000, 'court' => 'Court 3 (Interlock)'],
                ],
            ],
            [
                'time' => '20:00 - 21:00',
                'slots' => [
                    'c1' => ['id' => 'c1_2000', 'status' => 'booked', 'title' => 'Futsal Pro Club', 'subtitle' => 'Terisi'],
                    'c2' => ['id' => 'c2_2000', 'status' => 'booked', 'title' => 'Bintang Arena', 'subtitle' => 'Terisi'],
                    'c3' => ['id' => 'c3_2000', 'status' => 'available', 'label' => 'Tersedia', 'price' => 170000, 'court' => 'Court 3 (Interlock)'],
                ],
            ],
            [
                'time' => '21:00 - 22:00',
                'slots' => [
                    'c1' => ['id' => 'c1_2100', 'status' => 'available', 'label' => 'Tersedia', 'price' => 150000, 'court' => 'Court 1 (Vinyl)'],
                    'c2' => ['id' => 'c2_2100', 'status' => 'available', 'label' => 'Tersedia', 'price' => 150000, 'court' => 'Court 2 (Vinyl)'],
                    'c3' => ['id' => 'c3_2100', 'status' => 'closed', 'title' => 'Tutup', 'subtitle' => 'Jadwal Selesai'],
                ],
            ],
        ];

        // Slot yang terpilih secara default
        $selectedSlots = [
            [
                'id' => 'c1_1800',
                'court_name' => 'Court 1 (Vinyl)',
                'time' => '18:00 - 19:00 WIB',
                'price' => 150000,
            ],
            [
                'id' => 'c1_1900',
                'court_name' => 'Court 1 (Vinyl)',
                'time' => '19:00 - 20:00 WIB',
                'price' => 150000,
            ],
        ];

        $serviceFee = 2500;
        $rentalTotal = collect($selectedSlots)->sum('price');
        $grandTotal = $rentalTotal + $serviceFee;

        return view('penyewa.kalender', [
            'venue'         => $venue,
            'dates'         => $dates,
            'courts'        => $courts,
            'timeSlots'     => $timeSlots,
            'selectedSlots' => $selectedSlots,
            'selectedDate'  => 'Kamis, 24 Okt 2024',
            'serviceFee'    => $serviceFee,
            'rentalTotal'   => $rentalTotal,
            'grandTotal'    => $grandTotal,
        ]);
    }
}
