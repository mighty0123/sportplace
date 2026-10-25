<?php

namespace Tests\Feature\Mitra;

use App\Models\AuditLog;
use App\Models\MitraProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OnboardingTest extends TestCase
{
    use RefreshDatabase;

    private function createMitraUser(): User
    {
        return User::factory()->create([
            'role' => 'mitra',
            'email_verified_at' => now(),
        ]);
    }

    public function test_mitra_baru_dapat_mengakses_halaman_pendaftaran(): void
    {
        $mitra = $this->createMitraUser();

        $this->actingAs($mitra)
            ->get(route('mitra.onboarding.create'))
            ->assertOk()
            ->assertSee('Daftarkan usaha lapanganmu')
            ->assertSee($mitra->email);
    }

    public function test_pendaftaran_mitra_tanpa_ktp_ditolak(): void
    {
        $mitra = $this->createMitraUser();

        $response = $this->actingAs($mitra)->post(route('mitra.onboarding.store'), [
            'nama_usaha' => 'GOR Futsal Mandiri',
            'bentuk_badan_usaha' => 'Perorangan',
            'kota_operasional' => 'Bandung',
            'no_ktp' => '', // Kosong -> harus ditolak
            'bank_nama' => 'BCA',
            'nomor_rekening' => '1234567890',
            'nama_rekening' => 'Budi',
            'setuju_syarat' => '1',
        ]);

        $response->assertSessionHasErrors(['no_ktp', 'file_ktp']);
        $this->assertDatabaseCount('mitra_profiles', 0);
    }

    public function test_mitra_berhasil_mendaftar_dan_berkas_disimpan_di_disk_privat(): void
    {
        Storage::fake('local');
        $mitra = $this->createMitraUser();

        $fileKtp = UploadedFile::fake()->create('ktp_budi.jpg', 500, 'image/jpeg');

        $response = $this->actingAs($mitra)->post(route('mitra.onboarding.store'), [
            'nama_usaha' => 'GOR Tirta Sport',
            'bentuk_badan_usaha' => 'CV / Badan Usaha',
            'kota_operasional' => 'Denpasar, Bali',
            'deskripsi_usaha' => 'Pusat futsal dan badminton',
            'no_ktp' => '5171012903840002',
            'npwp' => '09.254.891.2-901.000',
            'bank_nama' => 'BCA',
            'nomor_rekening' => '8205128901',
            'nama_rekening' => 'CV Tirta Sport',
            'file_ktp' => $fileKtp,
            'setuju_syarat' => '1',
        ]);

        $response->assertRedirect(route('mitra.status'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('mitra_profiles', [
            'user_id' => $mitra->id,
            'nama_usaha' => 'GOR Tirta Sport',
            'status' => 'pending',
            'no_ktp' => '5171012903840002',
        ]);

        $profile = $mitra->fresh()->mitraProfile;
        $this->assertNotNull($profile->file_ktp);
        Storage::disk('local')->assertExists($profile->file_ktp);

        // Audit Log tercatat (NFR-LOG-001)
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $mitra->id,
            'entitas_type' => 'MitraProfile',
            'entitas_id' => $profile->id,
            'aksi' => 'pendaftaran',
            'status_baru' => 'pending',
        ]);
    }

    public function test_mitra_rejected_dapat_melihat_alasan_dan_mengajukan_ulang(): void
    {
        Storage::fake('local');
        $mitra = $this->createMitraUser();

        $profile = MitraProfile::create([
            'user_id' => $mitra->id,
            'nama_usaha' => 'GOR Tirta Sport',
            'bentuk_badan_usaha' => 'Perorangan',
            'kota_operasional' => 'Denpasar',
            'no_ktp' => '5171012903840002',
            'npwp' => '01.000.000.0-000.000',
            'bank_nama' => 'BCA',
            'nomor_rekening' => '8205128901',
            'nama_rekening' => 'Wayan',
            'status' => 'rejected',
            'alasan_penolakan' => 'Foto KTP terpotong dan NIB tidak valid',
            'file_ktp' => 'mitra_documents/test.jpg',
        ]);

        // Halaman status menampilkan alasan penolakan
        $this->actingAs($mitra)->get(route('mitra.status'))
            ->assertOk()
            ->assertSee('Foto KTP terpotong dan NIB tidak valid')
            ->assertSee('Perbaiki & Ajukan Ulang', false);

        // Formulir revisi dapat diakses
        $this->actingAs($mitra)->get(route('mitra.resubmit'))
            ->assertOk()
            ->assertSee('Foto KTP terpotong dan NIB tidak valid');

        // Ajukan perbaikan
        $newKtp = UploadedFile::fake()->create('ktp_baru.jpg', 600, 'image/jpeg');

        $response = $this->actingAs($mitra)->post(route('mitra.resubmit.store'), [
            'nama_usaha' => 'GOR Tirta Sport Reborn',
            'bentuk_badan_usaha' => 'Perorangan',
            'kota_operasional' => 'Denpasar',
            'no_ktp' => '5171012903840002',
            'bank_nama' => 'BCA',
            'nomor_rekening' => '8205128901',
            'nama_rekening' => 'Wayan',
            'file_ktp' => $newKtp,
            'setuju_syarat' => '1',
        ]);

        $response->assertRedirect(route('mitra.status'));

        // Status kembali menjadi pending dan alasan penolakan di-reset
        $profile->refresh();
        $this->assertSame('pending', $profile->status);
        $this->assertNull($profile->alasan_penolakan);
        $this->assertSame('GOR Tirta Sport Reborn', $profile->nama_usaha);

        // Audit Log ajukan ulang tercatat
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $mitra->id,
            'entitas_type' => 'MitraProfile',
            'aksi' => 'ajukan_ulang',
            'status_lama' => 'rejected',
            'status_baru' => 'pending',
        ]);
    }

    public function test_mitra_approved_diarahkan_ke_dashboard_bila_buka_pendaftaran(): void
    {
        $mitra = $this->createMitraUser();
        MitraProfile::create([
            'user_id' => $mitra->id,
            'nama_usaha' => 'GOR Tirta Sport',
            'no_ktp' => '5171012903840002',
            'status' => 'approved',
        ]);

        $this->actingAs($mitra)->get(route('mitra.onboarding.create'))
            ->assertRedirect(route('mitra.dashboard'));
    }
}

