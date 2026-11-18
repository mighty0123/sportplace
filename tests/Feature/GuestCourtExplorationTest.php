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

class GuestCourtExplorationTest extends TestCase
{
    use RefreshDatabase;

    private function createSampleField(): Field
    {
        $city = MasterCity::create([
            'nama_kota' => 'Denpasar',
            'provinsi' => 'Bali',
            'status_kota' => 'aktif',
        ]);

        $sport = MasterSport::create([
            'nama_olahraga' => 'Badminton',
            'slug' => 'badminton',
            'icon_url' => '/icons/badminton.png',
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
            'nama_venue' => 'Arena Sport Denpasar',
            'alamat_lengkap' => 'Jl. Teuku Umar No. 10',
            'map_url' => 'https://maps.google.com',
            'jam_buka' => '08:00:00',
            'jam_tutup' => '22:00:00',
            'status_vanue' => 'aktif',
        ]);

        return Field::create([
            'venue_id' => $venue->id,
            'sport_id' => $sport->id,
            'nama_lapangan' => 'Court Badminton A',
            'harga_perjam' => 75000,
            'status_lapangan' => 'aktif',
        ]);
    }

    public function test_guest_can_access_court_catalog_without_login(): void
    {
        $this->createSampleField();

        $response = $this->get('/penyewa/lapangan');

        $response->assertStatus(200);
        $response->assertSee('Daftar Lapangan Olahraga');
        $response->assertSee('Court Badminton A');
        $response->assertSee('Login');
        $response->assertSee('Daftar');
    }

    public function test_guest_can_view_field_detail_without_login(): void
    {
        $field = $this->createSampleField();

        $response = $this->get("/penyewa/lapangan/{$field->id}");

        $response->assertStatus(200);
        $response->assertSee('Court Badminton A');
        $response->assertSee('Arena Sport Denpasar');
        $response->assertSee('Rp 75.000');
        $response->assertSee('Pilih Jadwal di Kalender');
    }

    public function test_guest_can_view_field_calendar_and_realtime_slots_without_login(): void
    {
        $field = $this->createSampleField();

        $response = $this->get("/penyewa/kalender/{$field->id}");

        $response->assertStatus(200);
        $response->assertSee('Court Badminton A');
        $response->assertSee('Lanjut ke Pembayaran');
        $response->assertSee('Available');
    }

    public function test_guest_is_redirected_to_login_when_accessing_checkout(): void
    {
        $field = $this->createSampleField();

        $response = $this->get("/penyewa/checkout/{$field->id}");

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_guest_is_redirected_to_login_when_accessing_dashboard(): void
    {
        $response = $this->get('/penyewa/dashboard');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }
}
