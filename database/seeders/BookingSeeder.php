<?php

namespace Database\Seeders;

use App\Models\BookingDetail;
use App\Models\BookingHeader;
use App\Models\Field;
use App\Models\MasterCity;
use App\Models\MasterSport;
use App\Models\MitraProfile;
use App\Models\User;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Dapatkan atau buat pengguna Penyewa (Dimas Pratama & Customer Budi)
        $dimas = User::firstOrCreate(
            ['email' => 'dimas@sportplace.com'],
            [
                'name' => 'Dimas Pratama',
                'password' => bcrypt('password123'),
                'no_hp' => '08333333334',
                'role' => 'customer',
                'status_akun' => 'aktif',
                'email_verified_at' => now(),
            ]
        );

        $customerBudi = User::where('email', 'customer@sportplace.com')->first();
        $targetUsers = array_filter([$dimas, $customerBudi]);

        // 2. Pastikan Master Olahraga lengkap
        $sportFutsal = MasterSport::firstOrCreate(['slug' => 'futsal'], [
            'nama_olahraga' => 'Futsal', 'icon_url' => '/icons/futsal.png', 'status_olahraga' => 'aktif'
        ]);
        $sportBadminton = MasterSport::firstOrCreate(['slug' => 'badminton'], [
            'nama_olahraga' => 'Badminton', 'icon_url' => '/icons/badminton.png', 'status_olahraga' => 'aktif'
        ]);
        $sportTennis = MasterSport::firstOrCreate(['slug' => 'tenis'], [
            'nama_olahraga' => 'Tenis', 'icon_url' => '/icons/tennis.png', 'status_olahraga' => 'aktif'
        ]);
        $sportBasket = MasterSport::firstOrCreate(['slug' => 'basket'], [
            'nama_olahraga' => 'Basket', 'icon_url' => '/icons/basket.png', 'status_olahraga' => 'aktif'
        ]);

        // 3. Pastikan Kota & Mitra tersedia
        $city = MasterCity::first() ?? MasterCity::create(['nama_kota' => 'Denpasar']);
        $mitra = MitraProfile::first();
        if (!$mitra) {
            $mitraUser = User::where('role', 'mitra')->first();
            $mitra = MitraProfile::create([
                'user_id' => $mitraUser?->id ?? 2,
                'nama_operasional' => 'Mitra SportPlace',
                'alamat_operasional' => 'Jl. Bypass Ngurah Rai No. 12',
                'no_rekening' => '1234567890',
                'nama_bank' => 'BCA',
                'status' => 'approved',
            ]);
        }

        // 4. Buat Venues sesuai desain referensi
        $venueFutsal = Venue::firstOrCreate(
            ['nama_venue' => 'Viva Arena Futsal'],
            [
                'mitra_id' => $mitra->id,
                'city_id' => $city->id,
                'alamat_lengkap' => 'Renon, Denpasar',
                'map_url' => 'https://maps.google.com',
                'jam_buka' => '07:00:00',
                'jam_tutup' => '23:00:00',
                'status_vanue' => 'aktif',
            ]
        );

        $venueBadminton = Venue::firstOrCreate(
            ['nama_venue' => 'Smash Center Arena'],
            [
                'mitra_id' => $mitra->id,
                'city_id' => $city->id,
                'alamat_lengkap' => 'Goa Gong, Jimbaran',
                'map_url' => 'https://maps.google.com',
                'jam_buka' => '07:00:00',
                'jam_tutup' => '23:00:00',
                'status_vanue' => 'aktif',
            ]
        );

        $venueTennis = Venue::firstOrCreate(
            ['nama_venue' => 'Champion Tennis Club'],
            [
                'mitra_id' => $mitra->id,
                'city_id' => $city->id,
                'alamat_lengkap' => 'Kediri, Tabanan',
                'map_url' => 'https://maps.google.com',
                'jam_buka' => '06:00:00',
                'jam_tutup' => '22:00:00',
                'status_vanue' => 'aktif',
            ]
        );

        $venueBasket = Venue::firstOrCreate(
            ['nama_venue' => 'Hoops Hall Jakarta'],
            [
                'mitra_id' => $mitra->id,
                'city_id' => $city->id,
                'alamat_lengkap' => 'Kedonganan, Kuta Selatan',
                'map_url' => 'https://maps.google.com',
                'jam_buka' => '08:00:00',
                'jam_tutup' => '23:00:00',
                'status_vanue' => 'aktif',
            ]
        );

        // 5. Buat Lapangan (Fields)
        $fieldFutsal = Field::firstOrCreate(
            ['venue_id' => $venueFutsal->id, 'nama_lapangan' => 'Lapangan Vip'],
            ['sport_id' => $sportFutsal->id, 'harga_perjam' => 225000, 'status_lapangan' => 'aktif']
        );

        $fieldBadminton = Field::firstOrCreate(
            ['venue_id' => $venueBadminton->id, 'nama_lapangan' => 'Lapangan A'],
            ['sport_id' => $sportBadminton->id, 'harga_perjam' => 120000, 'status_lapangan' => 'aktif']
        );

        $fieldTennis = Field::firstOrCreate(
            ['venue_id' => $venueTennis->id, 'nama_lapangan' => 'Court 2'],
            ['sport_id' => $sportTennis->id, 'harga_perjam' => 175000, 'status_lapangan' => 'aktif']
        );

        $fieldBasket = Field::firstOrCreate(
            ['venue_id' => $venueBasket->id, 'nama_lapangan' => 'Full Court'],
            ['sport_id' => $sportBasket->id, 'harga_perjam' => 300000, 'status_lapangan' => 'aktif']
        );

        // 6. Buat Data Booking untuk target pengguna
        foreach ($targetUsers as $user) {
            // Hapus data booking lama milik user untuk idempotency
            $oldHeaders = BookingHeader::where('penyewa_id', $user->id)->get();
            foreach ($oldHeaders as $oldHeader) {
                BookingDetail::where('header_id', $oldHeader->id)->delete();
                $oldHeader->delete();
            }

            $today = Carbon::today();
            $now = Carbon::now();

            // ---- A. BOOKING AKTIF (Hari Ini - 2 item) ----
            // 1. Lapangan Futsal (Sedang Berlangsung / Hari Ini)
            $h1 = BookingHeader::create([
                'penyewa_id' => $user->id,
                'total_tagihan' => 450000,
                'status' => 'payment',
                'created_at' => $today->copy()->subDays(2),
            ]);
            BookingDetail::create([
                'header_id' => $h1->id,
                'field_id' => $fieldFutsal->id,
                'tanggal_sewa' => $today->copy()->setTime(18, 0),
                'jam_mulai' => '18:00:00',
                'jam_selesai' => '20:00:00',
                'harga_slot' => 450000,
                'status_item' => 'ongoing',
                'created_at' => $today->copy()->subDays(2),
            ]);

            // 2. Lapangan Futsal sesi kedua hari ini
            $h2 = BookingHeader::create([
                'penyewa_id' => $user->id,
                'total_tagihan' => 225000,
                'status' => 'payment',
                'created_at' => $today->copy()->subDays(1),
            ]);
            BookingDetail::create([
                'header_id' => $h2->id,
                'field_id' => $fieldFutsal->id,
                'tanggal_sewa' => $today->copy()->setTime(21, 0),
                'jam_mulai' => '21:00:00',
                'jam_selesai' => '22:00:00',
                'harga_slot' => 225000,
                'status_item' => 'ongoing',
                'created_at' => $today->copy()->subDays(1),
            ]);

            // ---- B. BOOKING MENDATANG (4 jadwal dalam 7 hari ke depan) ----
            // 3. Badminton Smash Center (Sabtu / +2 hari)
            $h3 = BookingHeader::create([
                'penyewa_id' => $user->id,
                'total_tagihan' => 240000,
                'status' => 'payment',
                'created_at' => $today->copy()->subDays(1),
            ]);
            BookingDetail::create([
                'header_id' => $h3->id,
                'field_id' => $fieldBadminton->id,
                'tanggal_sewa' => $today->copy()->addDays(2)->setTime(8, 0),
                'jam_mulai' => '08:00:00',
                'jam_selesai' => '10:00:00',
                'harga_slot' => 240000,
                'status_item' => 'ongoing',
                'created_at' => $today->copy()->subDays(1),
            ]);

            // 4. Tenis Champion Tennis Club (Pending Bayar / +3 hari)
            $h4 = BookingHeader::create([
                'penyewa_id' => $user->id,
                'total_tagihan' => 350000,
                'status' => 'pending',
                'created_at' => $now->copy()->subMinutes(15),
            ]);
            BookingDetail::create([
                'header_id' => $h4->id,
                'field_id' => $fieldTennis->id,
                'tanggal_sewa' => $today->copy()->addDays(3)->setTime(16, 0),
                'jam_mulai' => '16:00:00',
                'jam_selesai' => '18:00:00',
                'harga_slot' => 350000,
                'status_item' => 'ongoing',
                'created_at' => $now->copy()->subMinutes(15),
            ]);

            // 5. Basket Hoops Hall Jakarta (+5 hari)
            $h5 = BookingHeader::create([
                'penyewa_id' => $user->id,
                'total_tagihan' => 600000,
                'status' => 'payment',
                'created_at' => $today->copy()->subDays(2),
            ]);
            BookingDetail::create([
                'header_id' => $h5->id,
                'field_id' => $fieldBasket->id,
                'tanggal_sewa' => $today->copy()->addDays(5)->setTime(20, 0),
                'jam_mulai' => '20:00:00',
                'jam_selesai' => '22:00:00',
                'harga_slot' => 600000,
                'status_item' => 'ongoing',
                'created_at' => $today->copy()->subDays(2),
            ]);

            // 6. Badminton sesi kedua (+6 hari)
            $h6 = BookingHeader::create([
                'penyewa_id' => $user->id,
                'total_tagihan' => 240000,
                'status' => 'payment',
                'created_at' => $today->copy()->subDays(3),
            ]);
            BookingDetail::create([
                'header_id' => $h6->id,
                'field_id' => $fieldBadminton->id,
                'tanggal_sewa' => $today->copy()->addDays(6)->setTime(9, 0),
                'jam_mulai' => '09:00:00',
                'jam_selesai' => '11:00:00',
                'harga_slot' => 240000,
                'status_item' => 'ongoing',
                'created_at' => $today->copy()->subDays(3),
            ]);

            // ---- C. BOOKING SELESAI BULAN INI (5 item sehingga total bulan ini = 2 + 4 + 5 = 11 booking) ----
            for ($i = 1; $i <= 5; $i++) {
                $hPast = BookingHeader::create([
                    'penyewa_id' => $user->id,
                    'total_tagihan' => 200000,
                    'status' => 'payment',
                    'created_at' => $now->copy()->startOfMonth()->addDays($i + 1),
                ]);
                BookingDetail::create([
                    'header_id' => $hPast->id,
                    'field_id' => ($i % 2 === 0) ? $fieldFutsal->id : $fieldBadminton->id,
                    'tanggal_sewa' => $now->copy()->startOfMonth()->addDays($i + 1)->setTime(15, 0),
                    'jam_mulai' => '15:00:00',
                    'jam_selesai' => '17:00:00',
                    'harga_slot' => 200000,
                    'status_item' => 'completed',
                    'created_at' => $now->copy()->startOfMonth()->addDays($i + 1),
                ]);
            }

            // ---- D. BOOKING BULAN LALU (8 item sehingga growth = (11 - 8)/8 = +37.5% -> +37%) ----
            for ($i = 1; $i <= 8; $i++) {
                $hLastMonth = BookingHeader::create([
                    'penyewa_id' => $user->id,
                    'total_tagihan' => 200000,
                    'status' => 'payment',
                    'created_at' => $now->copy()->subMonthNoOverflow()->startOfMonth()->addDays($i * 2),
                ]);
                BookingDetail::create([
                    'header_id' => $hLastMonth->id,
                    'field_id' => ($i % 2 === 0) ? $fieldTennis->id : $fieldBasket->id,
                    'tanggal_sewa' => $now->copy()->subMonthNoOverflow()->startOfMonth()->addDays($i * 2)->setTime(16, 0),
                    'jam_mulai' => '16:00:00',
                    'jam_selesai' => '18:00:00',
                    'harga_slot' => 200000,
                    'status_item' => 'completed',
                    'created_at' => $now->copy()->subMonthNoOverflow()->startOfMonth()->addDays($i * 2),
                ]);
            }
        }
    }
}
