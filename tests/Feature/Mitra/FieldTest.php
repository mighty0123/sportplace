<?php

namespace Tests\Feature\Mitra;

use App\Models\BookingDetail;
use App\Models\BookingHeader;
use App\Models\Field;
use App\Models\MasterCity;
use App\Models\MasterFacility;
use App\Models\MasterSport;
use App\Models\MitraProfile;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * FASE 5: Manajemen Lapangan (MTR-04, MTR-05 / FR-FIELD-001..004)
 * Menguji daftar lapangan, pembuatan draft (FR-FIELD-001), unggah foto & cover (T5.3),
 * pengajuan review bersyarat venue aktif (FR-FIELD-002, OQ-010), edit spesifikasi & sinkronisasi
 * fasilitas (FR-FIELD-003), serta safeguard nonaktifkan lapangan ber-booking aktif (FR-FIELD-004, OQ-012).
 */
class FieldTest extends TestCase
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
            'nama_usaha' => 'Mitra Arena Sport',
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

    private function makeSport(string $name = 'Futsal'): MasterSport
    {
        return MasterSport::firstOrCreate(
            ['nama_olahraga' => $name],
            ['slug' => strtolower($name), 'status_olahraga' => 'aktif']
        );
    }

    private function makeFacility(string $name = 'Lampu LED Malam'): MasterFacility
    {
        return MasterFacility::firstOrCreate(
            ['nama' => $name],
            ['kategori' => 'Fasilitas Umum', 'status_fasilitas' => 'aktif']
        );
    }

    private function makeVenue(User $mitra, string $status = 'aktif'): Venue
    {
        $city = $this->makeCity();

        return Venue::create([
            'mitra_id' => $mitra->mitraProfile->id,
            'city_id' => $city->id,
            'nama_venue' => 'GOR Senayan Sport',
            'alamat_lengkap' => 'Jl. Pintu Satu Senayan No. 10',
            'jam_buka' => '08:00:00',
            'jam_tutup' => '23:00:00',
            'status_vanue' => $status,
        ]);
    }

    public function test_tamu_tidak_bisa_mengakses_daftar_lapangan(): void
    {
        $mitra = $this->makeMitraUser();
        $venue = $this->makeVenue($mitra);

        $response = $this->get(route('mitra.venues.fields.index', $venue));

        $response->assertRedirect(route('login'));
    }

    public function test_customer_tidak_bisa_mengakses_daftar_lapangan(): void
    {
        $mitra = $this->makeMitraUser();
        $venue = $this->makeVenue($mitra);
        $customer = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($customer)->get(route('mitra.venues.fields.index', $venue));

        $response->assertForbidden();
    }

    public function test_mitra_pending_dialihkan_ke_halaman_status(): void
    {
        $mitra = $this->makeMitraUser(status: 'pending');
        $venue = $this->makeVenue($mitra);

        $response = $this->actingAs($mitra)->get(route('mitra.venues.fields.index', $venue));

        $response->assertRedirect(route('mitra.status'));
        $response->assertSessionHas('warning', 'Akun mitra Anda masih menunggu verifikasi.');
    }

    public function test_mitra_approved_bisa_melihat_daftar_lapangan_venue_miliknya(): void
    {
        $mitra = $this->makeMitraUser();
        $venue = $this->makeVenue($mitra);
        $sport = $this->makeSport('Badminton');

        $field = Field::create([
            'venue_id' => $venue->id,
            'sport_id' => $sport->id,
            'nama_lapangan' => 'Lapangan Badminton 1 (Karpet BWF)',
            'harga_perjam' => 80000,
            'tipe_bangunan' => 'indoor',
            'jenis_permukaan' => 'Karpet Vinyl BWF',
            'panjang' => 13.4,
            'lebar' => 6.1,
            'status_lapangan' => 'aktif',
        ]);

        $response = $this->actingAs($mitra)->get(route('mitra.venues.fields.index', $venue));

        $response->assertOk();
        $response->assertSee('Lapangan Badminton 1 (Karpet BWF)');
        $response->assertSee('Badminton');
        $response->assertSee('Rp 80.000', false);
    }

    public function test_mitra_tidak_bisa_melihat_lapangan_venue_milik_mitra_lain(): void
    {
        $mitraA = $this->makeMitraUser();
        $venueA = $this->makeVenue($mitraA);

        $mitraB = $this->makeMitraUser();

        $response = $this->actingAs($mitraB)->get(route('mitra.venues.fields.index', $venueA));

        $response->assertForbidden();
    }

    public function test_mitra_bisa_membuat_lapangan_draft_dengan_foto_dan_fasilitas(): void
    {
        Storage::fake('public');

        $mitra = $this->makeMitraUser();
        $venue = $this->makeVenue($mitra);
        $sport = $this->makeSport('Futsal');
        $facility = $this->makeFacility('Papan Skor Digital');

        $photo1 = UploadedFile::fake()->image('lapangan1.jpg', 800, 600);
        $photo2 = UploadedFile::fake()->image('lapangan2.png', 800, 600);

        $response = $this->actingAs($mitra)->post(route('mitra.venues.fields.store', $venue), [
            'nama_lapangan' => 'Lapangan Futsal A (Vinyl)',
            'sport_id' => $sport->id,
            'harga_perjam' => 150000,
            'tipe_bangunan' => 'indoor',
            'jenis_permukaan' => 'Vinyl Taraflex',
            'panjang' => 25,
            'lebar' => 15,
            'kapasitas' => '10-12 orang',
            'aturan_lapangan' => 'Dilarang menggunakan sepatu sol hitam.',
            'facility_ids' => [$facility->id],
            'photos' => [$photo1, $photo2],
            'primary_photo_index' => 0,
            'action' => 'draft',
        ]);

        $response->assertRedirect(route('mitra.venues.fields.index', $venue));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('fields', [
            'venue_id' => $venue->id,
            'nama_lapangan' => 'Lapangan Futsal A (Vinyl)',
            'status_lapangan' => 'draft',
            'harga_perjam' => 150000,
        ]);

        $field = Field::where('nama_lapangan', 'Lapangan Futsal A (Vinyl)')->first();
        $this->assertNotNull($field);

        // Pastikan fasilitas tersambung di pivot
        $this->assertTrue($field->facilities->contains($facility->id));

        // Pastikan 2 foto tersimpan dan foto pertama adalah foto utama
        $this->assertCount(2, $field->photos);
        $this->assertTrue($field->photos->first()->is_primary);
    }

    public function test_pengajuan_lapangan_memerlukan_venue_berstatus_aktif(): void
    {
        Storage::fake('public');

        $mitra = $this->makeMitraUser();
        // Venue masih berstatus draft
        $venueDraft = $this->makeVenue($mitra, status: 'draft');
        $sport = $this->makeSport('Futsal');

        $photo = UploadedFile::fake()->image('field.jpg', 800, 600);

        // Coba ajukan lapangan pada venue draft (OQ-010)
        $response = $this->actingAs($mitra)->post(route('mitra.venues.fields.store', $venueDraft), [
            'nama_lapangan' => 'Lapangan Futsal Draft Venue',
            'sport_id' => $sport->id,
            'harga_perjam' => 120000,
            'tipe_bangunan' => 'indoor',
            'jenis_permukaan' => 'Rumput Sintetis',
            'photos' => [$photo],
            'action' => 'submit',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('fields', [
            'nama_lapangan' => 'Lapangan Futsal Draft Venue',
            'status_lapangan' => 'pending_review',
        ]);

        // Sekarang ubah venue menjadi aktif
        $venueDraft->update(['status_vanue' => 'aktif']);

        $responseSuccess = $this->actingAs($mitra)->post(route('mitra.venues.fields.store', $venueDraft), [
            'nama_lapangan' => 'Lapangan Futsal Sukses Review',
            'sport_id' => $sport->id,
            'harga_perjam' => 120000,
            'tipe_bangunan' => 'indoor',
            'jenis_permukaan' => 'Rumput Sintetis',
            'photos' => [$photo],
            'action' => 'submit',
        ]);

        $responseSuccess->assertRedirect(route('mitra.venues.fields.index', $venueDraft));
        $this->assertDatabaseHas('fields', [
            'nama_lapangan' => 'Lapangan Futsal Sukses Review',
            'status_lapangan' => 'pending_review',
        ]);
    }

    public function test_mitra_bisa_mengedit_lapangan_dan_sinkronisasi_fasilitas(): void
    {
        $mitra = $this->makeMitraUser();
        $venue = $this->makeVenue($mitra);
        $sport = $this->makeSport('Basket');
        $facilityA = $this->makeFacility('Ring Per');
        $facilityB = $this->makeFacility('Papan Skor Digital');

        $field = Field::create([
            'venue_id' => $venue->id,
            'sport_id' => $sport->id,
            'nama_lapangan' => 'Lapangan Basket A',
            'harga_perjam' => 200000,
            'tipe_bangunan' => 'indoor',
            'jenis_permukaan' => 'Kayu Jati',
            'status_lapangan' => 'aktif',
        ]);
        $field->facilities()->sync([$facilityA->id]);

        $response = $this->actingAs($mitra)->put(route('mitra.fields.update', $field), [
            'nama_lapangan' => 'Lapangan Basket A (Renovated)',
            'sport_id' => $sport->id,
            'harga_perjam' => 250000,
            'tipe_bangunan' => 'indoor',
            'jenis_permukaan' => 'Kayu Jati Grade A',
            'facility_ids' => [$facilityB->id],
            'action' => 'save',
        ]);

        $response->assertRedirect(route('mitra.venues.fields.index', $venue));

        $field->refresh();
        $this->assertEquals('Lapangan Basket A (Renovated)', $field->nama_lapangan);
        $this->assertEquals(250000, $field->harga_perjam);
        $this->assertFalse($field->facilities->contains($facilityA->id));
        $this->assertTrue($field->facilities->contains($facilityB->id));
    }

    public function test_mitra_tidak_bisa_mengedit_lapangan_mitra_lain(): void
    {
        $mitraA = $this->makeMitraUser();
        $venueA = $this->makeVenue($mitraA);
        $sport = $this->makeSport('Futsal');

        $fieldA = Field::create([
            'venue_id' => $venueA->id,
            'sport_id' => $sport->id,
            'nama_lapangan' => 'Lapangan Mitra A',
            'harga_perjam' => 100000,
            'tipe_bangunan' => 'indoor',
            'jenis_permukaan' => 'Vinyl',
            'status_lapangan' => 'aktif',
        ]);

        $mitraB = $this->makeMitraUser();

        $response = $this->actingAs($mitraB)->put(route('mitra.fields.update', $fieldA), [
            'nama_lapangan' => 'Hacked Lapangan',
            'sport_id' => $sport->id,
            'harga_perjam' => 50000,
            'tipe_bangunan' => 'indoor',
            'jenis_permukaan' => 'Vinyl',
        ]);

        $response->assertForbidden();
    }

    public function test_nonaktifkan_lapangan_berhasil_jika_tidak_ada_booking_aktif_mendatang(): void
    {
        $mitra = $this->makeMitraUser();
        $venue = $this->makeVenue($mitra);
        $sport = $this->makeSport('Futsal');

        $field = Field::create([
            'venue_id' => $venue->id,
            'sport_id' => $sport->id,
            'nama_lapangan' => 'Lapangan Siap Nonaktif',
            'harga_perjam' => 100000,
            'tipe_bangunan' => 'indoor',
            'jenis_permukaan' => 'Vinyl',
            'status_lapangan' => 'aktif',
        ]);

        $response = $this->actingAs($mitra)->post(route('mitra.fields.toggle-status', $field));

        $response->assertSessionHas('success');
        $field->refresh();
        $this->assertEquals('nonaktif', $field->status_lapangan);

        // Toggle kembali ke aktif
        $responseAktif = $this->actingAs($mitra)->post(route('mitra.fields.toggle-status', $field));
        $responseAktif->assertSessionHas('success');
        $field->refresh();
        $this->assertEquals('aktif', $field->status_lapangan);
    }

    public function test_nonaktifkan_lapangan_ditolak_jika_ada_booking_aktif_mendatang(): void
    {
        $mitra = $this->makeMitraUser();
        $venue = $this->makeVenue($mitra);
        $sport = $this->makeSport('Futsal');

        $field = Field::create([
            'venue_id' => $venue->id,
            'sport_id' => $sport->id,
            'nama_lapangan' => 'Lapangan Ada Booking',
            'harga_perjam' => 100000,
            'tipe_bangunan' => 'indoor',
            'jenis_permukaan' => 'Vinyl',
            'status_lapangan' => 'aktif',
        ]);

        $penyewa = User::factory()->create(['role' => 'customer']);

        $header = BookingHeader::create([
            'penyewa_id' => $penyewa->id,
            'total_tagihan' => 100000,
            'status' => 'confirmed',
            'created_at' => now(),
        ]);

        // Buat booking besok hari berstatus confirmed
        BookingDetail::create([
            'header_id' => $header->id,
            'field_id' => $field->id,
            'tanggal_sewa' => now()->addDays(2)->toDateString(),
            'jam_mulai' => '19:00:00',
            'jam_selesai' => '20:00:00',
            'harga_slot' => 100000,
            'status_item' => 'confirmed',
            'created_at' => now(),
        ]);

        // Coba nonaktifkan lapangan (OQ-012)
        $response = $this->actingAs($mitra)->post(route('mitra.fields.toggle-status', $field));

        $response->assertSessionHas('error');
        $field->refresh();
        $this->assertEquals('aktif', $field->status_lapangan);
    }

    public function test_hapus_lapangan_hanya_bisa_untuk_status_draft(): void
    {
        $mitra = $this->makeMitraUser();
        $venue = $this->makeVenue($mitra);
        $sport = $this->makeSport('Futsal');

        $fieldDraft = Field::create([
            'venue_id' => $venue->id,
            'sport_id' => $sport->id,
            'nama_lapangan' => 'Lapangan Draft Dihapus',
            'harga_perjam' => 100000,
            'tipe_bangunan' => 'indoor',
            'jenis_permukaan' => 'Vinyl',
            'status_lapangan' => 'draft',
        ]);

        $response = $this->actingAs($mitra)->delete(route('mitra.fields.destroy', $fieldDraft));

        $response->assertRedirect(route('mitra.venues.fields.index', $venue));
        $this->assertDatabaseMissing('fields', ['id' => $fieldDraft->id]);
    }
}
