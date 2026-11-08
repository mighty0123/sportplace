<?php

namespace Tests\Feature\Mitra;

use App\Models\MitraProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MitraCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_mitra_approve_command_updates_mitra_status_to_approved(): void
    {
        $user = User::factory()->create([
            'email' => 'mitra_test@sportplace.com',
            'role' => 'mitra',
        ]);
        MitraProfile::create([
            'user_id' => $user->id,
            'no_ktp' => '1234567890123456',
            'npwp' => '12.345.678.9-012.345',
            'status' => 'pending',
        ]);

        $this->artisan('mitra:approve mitra_test@sportplace.com')
            ->expectsOutputToContain('berhasil disetujui')
            ->assertSuccessful();

        $this->assertSame('approved', $user->fresh()->mitraProfile->status);
        $this->assertNull($user->fresh()->mitraProfile->alasan_penolakan);
    }

    public function test_mitra_reject_command_updates_status_to_rejected_with_reason(): void
    {
        $user = User::factory()->create([
            'email' => 'mitra_test@sportplace.com',
            'role' => 'mitra',
        ]);
        MitraProfile::create([
            'user_id' => $user->id,
            'no_ktp' => '1234567890123456',
            'npwp' => '12.345.678.9-012.345',
            'status' => 'pending',
        ]);

        $this->artisan('mitra:reject mitra_test@sportplace.com "KTP buram"')
            ->expectsOutputToContain('berhasil ditolak')
            ->assertSuccessful();

        $profile = $user->fresh()->mitraProfile;
        $this->assertSame('rejected', $profile->status);
        $this->assertSame('KTP buram', $profile->alasan_penolakan);
    }
}
