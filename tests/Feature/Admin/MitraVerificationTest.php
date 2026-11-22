<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\CreatesFixtures;
use Tests\TestCase;

class MitraVerificationTest extends TestCase
{
    use RefreshDatabase, CreatesFixtures;

    private const CHECKLIST = ['foto_ktp' => 1, 'nik_sesuai' => 1];

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Storage::fake('local');
    }

    /** Pendaftaran menunggu dengan berkas KTP yang benar-benar ada di penyimpanan. */
    private function pendingMitra(array $o = []): int
    {
        $owner = $this->makeUser('mitra');
        $path = "mitra_documents/{$owner->id}/ktp.jpg";
        Storage::disk('local')->put($path, 'gambar');

        return $this->mitraProfile($owner, ['file_ktp' => $path] + $o);
    }

    public function test_approve_sets_status_reviewer_and_audit_log(): void
    {
        $admin = $this->makeUser('admin');
        $id = $this->pendingMitra();

        $this->actingAs($admin)->post(route('admin.verifikasi.approve', $id), ['checklist' => self::CHECKLIST])
            ->assertRedirect(route('admin.verifikasi.index'));

        $this->assertDatabaseHas('mitra_profiles', ['id' => $id, 'status' => 'approved', 'reviewed_by' => $admin->id]);
        $this->assertDatabaseHas('audit_logs', ['entitas_type' => 'MitraProfile', 'entitas_id' => $id, 'aksi' => 'setujui', 'aktor_role' => 'admin']);
    }

    public function test_approve_requires_the_complete_manual_checklist(): void
    {
        $id = $this->pendingMitra();

        $this->actingAs($this->makeUser('admin'))->post(route('admin.verifikasi.approve', $id), ['checklist' => ['foto_ktp' => 1]])
            ->assertSessionHasErrors();

        $this->assertDatabaseHas('mitra_profiles', ['id' => $id, 'status' => 'pending']);
    }

    public function test_second_decision_on_the_same_application_is_rejected(): void
    {
        $admin = $this->makeUser('admin');
        $id = $this->pendingMitra();

        $this->actingAs($admin)->post(route('admin.verifikasi.approve', $id), ['checklist' => self::CHECKLIST]);
        $this->actingAs($admin)->post(route('admin.verifikasi.approve', $id), ['checklist' => self::CHECKLIST])->assertSessionHas('error');

        $this->assertDatabaseCount('audit_logs', 1);
    }

    public function test_duplicate_nik_blocks_approval(): void
    {
        $nik = $this->nik();
        $this->pendingMitra(['no_ktp' => $nik]);
        $id = $this->pendingMitra(['no_ktp' => $nik]);

        $this->actingAs($this->makeUser('admin'))->post(route('admin.verifikasi.approve', $id), ['checklist' => self::CHECKLIST])
            ->assertRedirect(route('admin.verifikasi.show', $id))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('mitra_profiles', ['id' => $id, 'status' => 'pending']);
    }

    public function test_missing_ktp_file_blocks_approval(): void
    {
        $id = $this->mitraProfile($this->makeUser('mitra'), ['file_ktp' => 'mitra_documents/0/tidak-ada.jpg']);

        $this->actingAs($this->makeUser('admin'))->post(route('admin.verifikasi.approve', $id), ['checklist' => self::CHECKLIST])
            ->assertSessionHas('error');

        $this->assertDatabaseHas('mitra_profiles', ['id' => $id, 'status' => 'pending']);
    }

    public function test_rejection_needs_a_reason_and_stores_it(): void
    {
        $admin = $this->makeUser('admin');
        $id = $this->pendingMitra();

        $this->actingAs($admin)->post(route('admin.verifikasi.reject', $id), ['alasan_penolakan' => 'pendek'])->assertSessionHasErrors('alasan_penolakan');
        $this->assertDatabaseHas('mitra_profiles', ['id' => $id, 'status' => 'pending']);

        $this->actingAs($admin)->post(route('admin.verifikasi.reject', $id), ['alasan_penolakan' => 'Foto KTP buram dan tidak terbaca'])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('mitra_profiles', ['id' => $id, 'status' => 'rejected', 'alasan_penolakan' => 'Foto KTP buram dan tidak terbaca']);
    }

    public function test_sensitive_documents_are_not_available_to_non_admins(): void
    {
        $id = $this->pendingMitra();

        $this->actingAs($this->makeUser('mitra'))->get(route('admin.verifikasi.document', [$id, 'ktp']))->assertForbidden();
        $this->actingAs($this->makeUser('admin'))->get(route('admin.verifikasi.document', [$id, 'ktp']))->assertOk();
        $this->assertDatabaseHas('audit_logs', ['entitas_id' => $id, 'aksi' => 'lihat_dokumen']);
    }
}