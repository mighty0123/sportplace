<?php

namespace Tests\Feature\Mitra;

use App\Models\BookingDetail;
use App\Models\BookingHeader;
use App\Models\Field;
use App\Models\MasterCity;
use App\Models\MasterSport;
use App\Models\MitraProfile;
use App\Models\User;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * FASE 3: Dashboard Mitra (MTR-03 / FR-REPORT-001)
 * Menguji kerangka, metrik booking hari ini, estimasi pendapatan,
 * isolasi data antar-mitra (BR-023), dan pintasan tindakan operasional.
 */
class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private function makeMitraUser(string $status = 'approved'): User
    {
        $user = User::factory()->create([
            'role' => 'mitra',
            'status_akun' => 'aktif',
            'email_verified_at' => now(),
        ]);

        MitraProfile::create([
            'user_id' => $user->id,
            'nama_usaha' => 'Mitra Futsal Sejahtera',
            'bentuk_badan_usaha' => 'Perorangan',
            'kota_operasional' => 'Jakarta Selatan',
            'no_ktp' => '3170000000000001',
            'npwp' => '01.000.000.0-000.000',
            'status' => $status,
        ]);

        return $user;
    }

    private function makeVenueAndField(User $mitraUser): array
    {
        $city = MasterCity::firstOrCreate(
            ['nama_kota' => 'Jakarta Selatan'],
            ['provinsi' => 'DKI Jakarta', 'status_kota' => 'aktif']
        );

        $sport = MasterSport::firstOrCreate(
            ['slug' => 'futsal'],
            ['nama_olahraga' => 'Futsal', 'icon_url' => '/icons/futsal.png', 'status_olahraga' => 'aktif']
        );

        $venue = Venue::create([
            'mitra_id' => $mitraUser->mitraProfile->id,
            'city_id' => $city->id,
            'nama_venue' => 'Arena Sport Futsal',
            'alamat_lengkap' => 'Jl. Sudirman No. 10',
            'jam_buka' => '08:00:00',
            'jam_tutup' => '23:00:00',
            'status_vanue' => 'aktif',
        ]);

        $field = Field::create([
            'venue_id' => $venue->id,
            'sport_id' => $sport->id,
            'nama_lapangan' => 'Lapangan Vinyl 1',
            'harga_perjam' => 150000,
            'status_lapangan' => 'aktif',
        ]);

        return [$venue, $field];
    }

    public function test_tamu_tidak_bisa_mengakses_dashboard_mitra(): void
    {
        $response = $this->get('/mitra/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_customer_tidak_bisa_mengakses_dashboard_mitra(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $response = $this->actingAs($customer)->get('/mitra/dashboard');
        $response->assertStatus(403);
    }

    public function test_mitra_belum_approved_diarahkan_ke_halaman_status(): void
    {
        $mitraPending = $this->makeMitraUser('pending');
        $response = $this->actingAs($mitraPending)->get('/mitra/dashboard');
        $response->assertRedirect(route('mitra.status'));
    }

    public function test_mitra_approved_bisa_melihat_dashboard_dengan_metrik_awal(): void
    {
        $mitra = $this->makeMitraUser('approved');

        $response = $this->actingAs($mitra)->get('/mitra/dashboard');

        $response->assertOk();
        $response->assertViewIs('mitra.dashboard');
        $response->assertSee('SportPlace Mitra');
        $response->assertSee('Dashboard Mitra');
        $response->assertSee('Booking Hari Ini');
        $response->assertSee('Total Pendapatan');
        $response->assertSee('Belum Ada Booking Hari Ini');
    }

    public function test_dashboard_menampilkan_jumlah_booking_hari_ini_secara_akurat(): void
    {
        // AC-FR-REPORT-001-01: 3 booking hari ini di lapangan mitra -> tampil 3
        $mitra = $this->makeMitraUser('approved');
        [$venue, $field] = $this->makeVenueAndField($mitra);

        $customer = User::factory()->create(['role' => 'customer']);
        $today = Carbon::today()->toDateString();

        $header = BookingHeader::create([
            'penyewa_id' => $customer->id,
            'total_tagihan' => 450000,
            'status' => 'payment',
            'created_at' => now(),
        ]);

        // Buat 3 booking details untuk hari ini
        for ($i = 1; $i <= 3; $i++) {
            BookingDetail::create([
                'header_id' => $header->id,
                'field_id' => $field->id,
                'tanggal_sewa' => $today,
                'jam_mulai' => sprintf('%02d:00:00', 8 + $i * 2),
                'jam_selesai' => sprintf('%02d:00:00', 9 + $i * 2),
                'harga_slot' => 150000,
                'status_item' => 'ongoing',
                'created_at' => now(),
            ]);
        }

        // Buat 1 booking detail untuk hari besok (tidak boleh dihitung hari ini)
        BookingDetail::create([
            'header_id' => $header->id,
            'field_id' => $field->id,
            'tanggal_sewa' => Carbon::tomorrow()->toDateString(),
            'jam_mulai' => '10:00:00',
            'jam_selesai' => '11:00:00',
            'harga_slot' => 150000,
            'status_item' => 'ongoing',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($mitra)->get('/mitra/dashboard');

        $response->assertOk();
        $response->assertViewHas('todayBookingsCount', 3);
        $response->assertViewHas('totalRevenue', 600000);
        $response->assertSee('3');
        $response->assertSee('Lapangan Vinyl 1');
    }

    public function test_dashboard_mengisolasi_data_antar_mitra_secara_ketat(): void
    {
        // BR-023: Mitra A tidak boleh melihat atau menghitung booking milik Mitra B
        $mitraA = $this->makeMitraUser('approved');
        [$venueA, $fieldA] = $this->makeVenueAndField($mitraA);

        $mitraB = User::factory()->create([
            'role' => 'mitra',
            'status_akun' => 'aktif',
            'email_verified_at' => now(),
        ]);
        MitraProfile::create([
            'user_id' => $mitraB->id,
            'nama_usaha' => 'Mitra B Lapangan',
            'status' => 'approved',
        ]);
        [$venueB, $fieldB] = $this->makeVenueAndField($mitraB);

        $customer = User::factory()->create(['role' => 'customer']);
        $today = Carbon::today()->toDateString();

        // Buat booking di Lapangan Mitra B
        $headerB = BookingHeader::create([
            'penyewa_id' => $customer->id,
            'total_tagihan' => 300000,
            'status' => 'payment',
            'created_at' => now(),
        ]);

        BookingDetail::create([
            'header_id' => $headerB->id,
            'field_id' => $fieldB->id,
            'tanggal_sewa' => $today,
            'jam_mulai' => '14:00:00',
            'jam_selesai' => '16:00:00',
            'harga_slot' => 300000,
            'status_item' => 'ongoing',
            'created_at' => now(),
        ]);

        // Cek dashboard Mitra A -> harus tetap 0 booking
        $responseA = $this->actingAs($mitraA)->get('/mitra/dashboard');
        $responseA->assertOk();
        $responseA->assertViewHas('todayBookingsCount', 0);
        $responseA->assertViewHas('totalRevenue', 0);

        // Cek dashboard Mitra B -> harus 1 booking
        $responseB = $this->actingAs($mitraB)->get('/mitra/dashboard');
        $responseB->assertOk();
        $responseB->assertViewHas('todayBookingsCount', 1);
        $responseB->assertViewHas('totalRevenue', 300000);
    }

    public function test_pintasan_tindakan_muncul_saat_venue_atau_lapangan_ditolak(): void
    {
        $mitra = $this->makeMitraUser('approved');
        [$venue, $field] = $this->makeVenueAndField($mitra);

        // Ubah venue menjadi rejected
        $venue->update([
            'status_vanue' => 'rejected',
            'alasan_penolakan' => 'Foto dokumen denah lapangan kurang jelas.',
        ]);

        $response = $this->actingAs($mitra)->get('/mitra/dashboard');

        $response->assertOk();
        $response->assertSee('Perhatian: Terdapat Fasilitas yang Memerlukan Revisi');
        $response->assertSee('Foto dokumen denah lapangan kurang jelas.');
    }
}

