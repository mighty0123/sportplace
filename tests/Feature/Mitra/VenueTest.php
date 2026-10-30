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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * FASE 4: Manajemen Venue (MTR-04, MTR-05b / FR-VENUE-001..004)
 * Menguji pembuatan draft, validasi, pengajuan review, pengeditan,
 * isolasi data antar-mitra (BR-023), serta penonaktifan venue (FR-VENUE-004 & OQ-012).
 */
class VenueTest extends TestCase
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
            'nama_usaha' => 'Mitra Sport Prima',
            'bentuk_badan_usaha' => 'Perorangan',
            'kota_operasional' => 'Jakarta Selatan',
            'no_ktp' => '3170000000000001',
            'npwp' => '01.000.000.0-000.000',
            'status' => $status,
        ]);

        return $user;
    }

    private function makeCity(): MasterCity
    {
        return MasterCity::firstOrCreate(
            ['nama_kota' => 'Jakarta Selatan'],
            ['provinsi' => 'DKI Jakarta', 'status_kota' => 'aktif']
        );
    }

    public function test_tamu_tidak_bisa_mengakses_daftar_venue(): void
    {
        $response = $this->get(route('mitra.venues.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_customer_tidak_bisa_mengakses_daftar_venue(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($customer)->get(route('mitra.venues.index'));

        $response->assertForbidden();
    }

    public function test_mitra_pending_dialihkan_ke_halaman_status(): void
    {
        $mitraPending = $this->makeMitraUser(status: 'pending');

        $response = $this->actingAs($mitraPending)->get(route('mitra.venues.index'));

        $response->assertRedirect(route('mitra.status'));
        $response->assertSessionHas('warning', 'Akun mitra Anda masih menunggu verifikasi.');
    }

    public function test_mitra_approved_bisa_melihat_daftar_venue_miliknya(): void
    {
        $mitra = $this->makeMitraUser(status: 'approved');
        $city = $this->makeCity();

        $venue = Venue::create([
            'mitra_id' => $mitra->mitraProfile->id,
            'city_id' => $city->id,
            'nama_venue' => 'GOR Tirta Sport',
            'alamat_lengkap' => 'Jl. Sudirman No. 100',
            'jam_buka' => '08:00:00',
            'jam_tutup' => '23:00:00',
            'status_vanue' => 'aktif',
        ]);

        $response = $this->actingAs($mitra)->get(route('mitra.venues.index'));

        $response->assertOk();
        $response->assertSee('Manajemen Vanue');
        $response->assertSee('GOR Tirta Sport');
        $response->assertSee('Disetujui &amp; Aktif', false);
    }

    public function test_mitra_hanya_melihat_venue_miliknya_sendiri_isolasi_data(): void
    {
        $mitraA = $this->makeMitraUser(status: 'approved');
        $mitraB = $this->makeMitraUser(status: 'approved');
        $city = $this->makeCity();

        Venue::create([
            'mitra_id' => $mitraA->mitraProfile->id,
            'city_id' => $city->id,
            'nama_venue' => 'Arena Sport Milik Mitra A',
            'alamat_lengkap' => 'Jl. A No. 1',
            'jam_buka' => '08:00:00',
            'jam_tutup' => '22:00:00',
            'status_vanue' => 'aktif',
        ]);

        Venue::create([
            'mitra_id' => $mitraB->mitraProfile->id,
            'city_id' => $city->id,
            'nama_venue' => 'Arena Sport Milik Mitra B',
            'alamat_lengkap' => 'Jl. B No. 2',
            'jam_buka' => '09:00:00',
            'jam_tutup' => '21:00:00',
            'status_vanue' => 'aktif',
        ]);

        $response = $this->actingAs($mitraA)->get(route('mitra.venues.index'));

        $response->assertOk();
        $response->assertSee('Arena Sport Milik Mitra A');
        $response->assertDontSee('Arena Sport Milik Mitra B');
    }

    public function test_mitra_bisa_membuat_venue_berstatus_draft(): void
    {
        $mitra = $this->makeMitraUser(status: 'approved');
        $city = $this->makeCity();

        $payload = [
            'nama_venue' => 'Champion Futsal Club',
            'city_id' => $city->id,
            'alamat_lengkap' => 'Jl. Kebon Jeruk No. 12',
            'map_url' => 'https://maps.google.com/?q=champion',
            'jam_buka' => '08:00',
            'jam_tutup' => '23:00',
            'deskripsi' => 'Fasilitas futsal terlengkap dengan rumput sintetis.',
            'action' => 'draft',
        ];

        $response = $this->actingAs($mitra)->post(route('mitra.venues.store'), $payload);

        $response->assertRedirect(route('mitra.venues.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('venues', [
            'mitra_id' => $mitra->mitraProfile->id,
            'nama_venue' => 'Champion Futsal Club',
            'status_vanue' => 'draft',
        ]);
    }

    public function test_mitra_bisa_membuat_dan_langsung_mengajukan_venue(): void
    {
        $mitra = $this->makeMitraUser(status: 'approved');
        $city = $this->makeCity();

        $payload = [
            'nama_venue' => 'Permata Badminton Arena',
            'city_id' => $city->id,
            'alamat_lengkap' => 'Jl. Permata Indah No. 5',
            'jam_buka' => '07:00',
            'jam_tutup' => '22:00',
            'action' => 'submit',
        ];

        $response = $this->actingAs($mitra)->post(route('mitra.venues.store'), $payload);

        $response->assertRedirect(route('mitra.venues.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('venues', [
            'mitra_id' => $mitra->mitraProfile->id,
            'nama_venue' => 'Permata Badminton Arena',
            'status_vanue' => 'pending_review',
        ]);
    }

    public function test_pembuatan_venue_gagal_jika_alamat_lengkap_kosong(): void
    {
        $mitra = $this->makeMitraUser(status: 'approved');
        $city = $this->makeCity();

        $payload = [
            'nama_venue' => 'Venue Tanpa Alamat',
            'city_id' => $city->id,
            'alamat_lengkap' => '',
            'jam_buka' => '08:00',
            'jam_tutup' => '22:00',
        ];

        $response = $this->actingAs($mitra)->post(route('mitra.venues.store'), $payload);

        $response->assertSessionHasErrors('alamat_lengkap');
        $this->assertDatabaseMissing('venues', [
            'nama_venue' => 'Venue Tanpa Alamat',
        ]);
    }

    public function test_pembuatan_venue_gagal_jika_jam_tutup_lebih_awal_dari_jam_buka(): void
    {
        $mitra = $this->makeMitraUser(status: 'approved');
        $city = $this->makeCity();

        $payload = [
            'nama_venue' => 'Venue Jam Terbalik',
            'city_id' => $city->id,
            'alamat_lengkap' => 'Jl. Merdeka No. 1',
            'jam_buka' => '22:00',
            'jam_tutup' => '08:00', // lebih awal dari jam buka
        ];

        $response = $this->actingAs($mitra)->post(route('mitra.venues.store'), $payload);

        $response->assertSessionHasErrors('jam_tutup');
    }

    public function test_mitra_bisa_mengajukan_venue_draft_atau_rejected_untuk_ditinjau(): void
    {
        $mitra = $this->makeMitraUser(status: 'approved');
        $city = $this->makeCity();

        $venue = Venue::create([
            'mitra_id' => $mitra->mitraProfile->id,
            'city_id' => $city->id,
            'nama_venue' => 'Venue Siap Review',
            'alamat_lengkap' => 'Jl. Ahmad Yani No. 9',
            'jam_buka' => '08:00:00',
            'jam_tutup' => '22:00:00',
            'status_vanue' => 'draft',
        ]);

        $response = $this->actingAs($mitra)->post(route('mitra.venues.submit', $venue));

        $response->assertRedirect(route('mitra.venues.index'));
        $this->assertDatabaseHas('venues', [
            'id' => $venue->id,
            'status_vanue' => 'pending_review',
        ]);
    }

    public function test_mitra_bisa_mengedit_venue_miliknya(): void
    {
        $mitra = $this->makeMitraUser(status: 'approved');
        $city = $this->makeCity();

        $venue = Venue::create([
            'mitra_id' => $mitra->mitraProfile->id,
            'city_id' => $city->id,
            'nama_venue' => 'Nama Venue Lama',
            'alamat_lengkap' => 'Alamat Lama',
            'jam_buka' => '08:00:00',
            'jam_tutup' => '22:00:00',
            'status_vanue' => 'aktif',
        ]);

        $updatePayload = [
            'nama_venue' => 'Nama Venue Baru Terupdate',
            'city_id' => $city->id,
            'alamat_lengkap' => 'Alamat Baru No. 99',
            'jam_buka' => '09:00',
            'jam_tutup' => '23:00',
            'action' => 'save',
        ];

        $response = $this->actingAs($mitra)->put(route('mitra.venues.update', $venue), $updatePayload);

        $response->assertRedirect(route('mitra.venues.index'));
        $this->assertDatabaseHas('venues', [
            'id' => $venue->id,
            'nama_venue' => 'Nama Venue Baru Terupdate',
            'alamat_lengkap' => 'Alamat Baru No. 99',
            'status_vanue' => 'aktif', // Tetap aktif sesuai OQ-011
        ]);
    }

    public function test_mitra_tidak_bisa_mengedit_venue_milik_mitra_lain(): void
    {
        $mitraA = $this->makeMitraUser(status: 'approved');
        $mitraB = $this->makeMitraUser(status: 'approved');
        $city = $this->makeCity();

        $venueB = Venue::create([
            'mitra_id' => $mitraB->mitraProfile->id,
            'city_id' => $city->id,
            'nama_venue' => 'Venue Milik Mitra B',
            'alamat_lengkap' => 'Alamat B',
            'jam_buka' => '08:00:00',
            'jam_tutup' => '22:00:00',
            'status_vanue' => 'aktif',
        ]);

        $updatePayload = [
            'nama_venue' => 'Coba Bajak Nama Venue',
            'city_id' => $city->id,
            'alamat_lengkap' => 'Alamat Bajakan',
            'jam_buka' => '08:00',
            'jam_tutup' => '22:00',
        ];

        $response = $this->actingAs($mitraA)->put(route('mitra.venues.update', $venueB), $updatePayload);

        $response->assertForbidden();
    }

    public function test_mitra_bisa_menonaktifkan_venue_aktif_tanpa_booking_mendatang(): void
    {
        $mitra = $this->makeMitraUser(status: 'approved');
        $city = $this->makeCity();

        $venue = Venue::create([
            'mitra_id' => $mitra->mitraProfile->id,
            'city_id' => $city->id,
            'nama_venue' => 'Venue Aktif Mau Ditutup',
            'alamat_lengkap' => 'Jl. Diponegoro No. 12',
            'jam_buka' => '08:00:00',
            'jam_tutup' => '22:00:00',
            'status_vanue' => 'aktif',
        ]);

        $response = $this->actingAs($mitra)->post(route('mitra.venues.deactivate', $venue));

        $response->assertRedirect(route('mitra.venues.index'));
        $this->assertDatabaseHas('venues', [
            'id' => $venue->id,
            'status_vanue' => 'nonaktif',
        ]);
    }

    public function test_mitra_tidak_bisa_menonaktifkan_venue_dengan_booking_aktif_mendatang(): void
    {
        $mitra = $this->makeMitraUser(status: 'approved');
        $city = $this->makeCity();

        $sport = MasterSport::firstOrCreate(
            ['slug' => 'futsal'],
            ['nama_olahraga' => 'Futsal', 'icon_url' => '/icons/futsal.png', 'status_olahraga' => 'aktif']
        );

        $venue = Venue::create([
            'mitra_id' => $mitra->mitraProfile->id,
            'city_id' => $city->id,
            'nama_venue' => 'Venue Banyak Booking',
            'alamat_lengkap' => 'Jl. Hayam Wuruk No. 8',
            'jam_buka' => '08:00:00',
            'jam_tutup' => '22:00:00',
            'status_vanue' => 'aktif',
        ]);

        $field = Field::create([
            'venue_id' => $venue->id,
            'sport_id' => $sport->id,
            'nama_lapangan' => 'Lapangan 1',
            'harga_perjam' => 100000,
            'status_lapangan' => 'aktif',
        ]);

        $customer = User::factory()->create(['role' => 'customer']);

        $header = BookingHeader::create([
            'user_id' => $customer->id,
            'nomor_invoice' => 'INV-TEST-001',
            'total_harga' => 100000,
            'status' => 'confirmed',
        ]);

        // Booking mendatang (besok)
        BookingDetail::create([
            'header_id' => $header->id,
            'field_id' => $field->id,
            'tanggal_sewa' => now()->addDay()->toDateString(),
            'jam_mulai' => '10:00:00',
            'jam_selesai' => '11:00:00',
            'harga_slot' => 100000,
            'status_item' => 'confirmed',
        ]);

        $response = $this->actingAs($mitra)->post(route('mitra.venues.deactivate', $venue));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('venues', [
            'id' => $venue->id,
            'status_vanue' => 'aktif', // Tetap aktif karena ditolak
        ]);
    }

    public function test_mitra_bisa_mengaktifkan_kembali_venue_yang_nonaktif(): void
    {
        $mitra = $this->makeMitraUser(status: 'approved');
        $city = $this->makeCity();

        $venue = Venue::create([
            'mitra_id' => $mitra->mitraProfile->id,
            'city_id' => $city->id,
            'nama_venue' => 'Venue Nonaktif Mau Dibuka Lagi',
            'alamat_lengkap' => 'Jl. Gatot Subroto No. 4',
            'jam_buka' => '08:00:00',
            'jam_tutup' => '22:00:00',
            'status_vanue' => 'nonaktif',
        ]);

        $response = $this->actingAs($mitra)->post(route('mitra.venues.activate', $venue));

        $response->assertRedirect(route('mitra.venues.index'));
        $this->assertDatabaseHas('venues', [
            'id' => $venue->id,
            'status_vanue' => 'aktif',
        ]);
    }

    public function test_mitra_bisa_menghapus_draft_venue(): void
    {
        $mitra = $this->makeMitraUser(status: 'approved');
        $city = $this->makeCity();

        $venue = Venue::create([
            'mitra_id' => $mitra->mitraProfile->id,
            'city_id' => $city->id,
            'nama_venue' => 'Draft Venue Salah Ketik',
            'alamat_lengkap' => 'Alamat Sementara',
            'jam_buka' => '08:00:00',
            'jam_tutup' => '22:00:00',
            'status_vanue' => 'draft',
        ]);

        $response = $this->actingAs($mitra)->delete(route('mitra.venues.destroy', $venue));

        $response->assertRedirect(route('mitra.venues.index'));
        $this->assertDatabaseMissing('venues', [
            'id' => $venue->id,
        ]);
    }

    public function test_mitra_tidak_bisa_menghapus_venue_aktif(): void
    {
        $mitra = $this->makeMitraUser(status: 'approved');
        $city = $this->makeCity();

        $venue = Venue::create([
            'mitra_id' => $mitra->mitraProfile->id,
            'city_id' => $city->id,
            'nama_venue' => 'Venue Aktif Tidak Boleh Dihapus',
            'alamat_lengkap' => 'Alamat Resmi',
            'jam_buka' => '08:00:00',
            'jam_tutup' => '22:00:00',
            'status_vanue' => 'aktif',
        ]);

        $response = $this->actingAs($mitra)->delete(route('mitra.venues.destroy', $venue));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('venues', [
            'id' => $venue->id,
        ]);
    }
}

