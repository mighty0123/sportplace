<?php

namespace Tests\Feature\Auth;

use App\Models\MitraProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * FR-AUTH-005 / Logout. AC-FR-AUTH-005-01.
 */
class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_logout_menghapus_sesi_dan_arahkan_ke_login(): void
    {
        // AC-FR-AUTH-005-01
        $user = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($user)->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_halaman_terproteksi_tidak_dapat_diakses_setelah_logout(): void
    {
        $user = User::factory()->create([
            'role' => 'mitra',
            'email_verified_at' => now(),
        ]);
        MitraProfile::create([
            'user_id' => $user->id,
            'no_ktp' => '1234567890123456',
            'npwp' => '12.345.678.9-012.345',
            'status' => 'approved',
        ]);

        $this->actingAs($user)->post(route('logout'));

        $this->get(route('mitra.dashboard'))
            ->assertRedirect(route('login'));
    }
}
