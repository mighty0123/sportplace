<?php

namespace Tests\Feature;

use App\Models\Field;
use App\Models\MasterCity;
use App\Models\MasterSport;
use App\Models\MitraProfile;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicDashboardTest extends TestCase
{
    use RefreshDatabase;
    public function test_public_dashboard_renders_successfully(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('SportPlace');
        $response->assertSee('Cari Lapangan.');
        $response->assertSee('Main Tanpa Ribet.');
    }

    public function test_public_dashboard_contains_sport_categories(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Main Olahraga Favoritmu');
        $response->assertSee('Futsal');
        $response->assertSee('Badminton');
        $response->assertSee('Basket');
        $response->assertSee('Tenis');
        $response->assertSee('Voli');
        $response->assertSee('Tenis Meja');
    }

    public function test_public_dashboard_contains_popular_fields(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Lapangan Populer');
        $response->assertSee('Arena Sport Center');
        $response->assertSee('Green Court Arena');
        $response->assertSee('Bali Basket Hub');
        $response->assertSee('Sunrise Tennis Club');
        $response->assertSee('Rp 80.000');
        $response->assertSee('Rp 120.000');
    }

    public function test_public_dashboard_contains_features_and_steps(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Booking Lapangan Lebih Mudah');
        $response->assertSee('Banyak Pilihan');
        $response->assertSee('Harga Transparan');
        $response->assertSee('Jadwal Jelas');
        $response->assertSee('Booking Lebih Praktis');

        $response->assertSee('Cara Booking');
        $response->assertSee('Cari');
        $response->assertSee('Pilih Lapangan');
        $response->assertSee('Pilih Jadwal');
        $response->assertSee('Booking');

        $response->assertSee('Lapangan Terpilih untuk Kamu');
        $response->assertSee('Siap Mulai Main?');
    }

    public function test_public_dashboard_displays_database_sports_and_fields(): void
    {
        $city = MasterCity::create([
            'nama_kota' => 'Surabaya',
            'provinsi' => 'Jawa Timur',
            'status_kota' => 'aktif',
        ]);

        $sport = MasterSport::create([
            'nama_olahraga' => 'Padel Tennis',
            'slug' => 'padel',
            'icon_url' => '/icons/padel.png',
            'status_olahraga' => 'aktif',
        ]);

        $mitraUser = User::factory()->create(['role' => 'mitra']);
        $mitra = MitraProfile::create([
            'user_id' => $mitraUser->id,
            'no_ktp' => '1234567890123456',
            'npwp' => '12.345.678.9-012.345',
            'status' => 'approved',
        ]);

        $venue = Venue::create([
            'mitra_id' => $mitra->id,
            'city_id' => $city->id,
            'nama_venue' => 'Surabaya Padel Center',
            'alamat_lengkap' => 'Jl. Padel No. 1',
            'map_url' => 'https://maps.google.com',
            'jam_buka' => '08:00:00',
            'jam_tutup' => '22:00:00',
            'status_vanue' => 'aktif',
        ]);

        Field::create([
            'venue_id' => $venue->id,
            'sport_id' => $sport->id,
            'nama_lapangan' => 'Padel Court 1',
            'harga_perjam' => 250000,
            'status_lapangan' => 'aktif',
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Padel Tennis');
        $response->assertSee('Surabaya Padel Center');
        $response->assertSee('Surabaya');
        $response->assertSee('Rp 250.000');
    }

    public function test_tentang_kami_page_renders_successfully(): void
    {
        $response = $this->get('/tentang-kami');

        $response->assertStatus(200);
        $response->assertSee('Tentang SportPlace Indonesia');
        $response->assertSee('Temukan Lapangan.');
        $response->assertSee('Mulai Permainanmu.');
        $response->assertSee('Menghubungkan Pemain');
        $response->assertSee('Arena Tanpa Friksi');
        $response->assertSee('Kenapa Memilih SportPlace?');
        $response->assertSee('Fasilitas Lengkap untuk Setiap Laga');
        $response->assertSee('Untuk Penyewa Lapangan');
        $response->assertSee('Untuk Mitra Pemilik Venue');
        $response->assertSee('Dibangun untuk Pengalaman Booking yang Jelas');
        $response->assertSee('Siap Mengatur Jadwal Main Berikutnya?');
    }

    public function test_about_redirects_to_tentang_kami(): void
    {
        $response = $this->get('/about');

        $response->assertRedirect('/tentang-kami');
    }
}
