<?php

namespace Tests\Feature\Mitra;

use App\Models\Field;
use App\Models\MasterCity;
use App\Models\MitraProfile;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class PolicyAndMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    private function createMitra(string $status = 'approved'): array
    {
        $user = User::factory()->create([
            'role' => 'mitra',
            'email_verified_at' => now(),
        ]);

        $profile = MitraProfile::create([
            'user_id' => $user->id,
            'no_ktp' => '1234567890123456',
            'npwp' => '12.345.678.9-012.345',
            'status' => $status,
        ]);

        return [$user, $profile];
    }

    public function test_ensure_mitra_approved_redirects_pending_mitra(): void
    {
        // EC-17: mitra pending dialihkan ke status
        [$user] = $this->createMitra('pending');

        $this->actingAs($user)
            ->get('/mitra/test-protected')
            ->assertRedirect(route('mitra.status'))
            ->assertSessionHas('warning', 'Akun mitra Anda masih menunggu verifikasi.');
    }

    public function test_ensure_role_middleware_blocks_unauthorized_roles(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($customer)
            ->get('/mitra/test-protected')
            ->assertForbidden();
    }

    public function test_venue_policy_prevents_mitra_from_updating_other_mitra_venue(): void
    {
        // BR-023: kepemilikan data
        [$mitraA, $profileA] = $this->createMitra('approved');
        [$mitraB, $profileB] = $this->createMitra('approved');

        $city = MasterCity::create([
            'nama_kota' => 'Jakarta Selatan',
            'provinsi' => 'DKI Jakarta',
            'status_kota' => 'aktif',
        ]);

        $venueB = Venue::create([
            'mitra_id' => $profileB->id,
            'city_id' => $city->id,
            'nama_venue' => 'Venue Mitra B',
            'alamat_lengkap' => 'Jl. B No. 1',
            'jam_buka' => '08:00',
            'jam_tutup' => '22:00',
            'status_vanue' => 'aktif',
        ]);

        $this->assertFalse(Gate::forUser($mitraA)->allows('update', $venueB));
        $this->assertTrue(Gate::forUser($mitraB)->allows('update', $venueB));
    }

    public function test_field_policy_requires_approved_mitra_to_create_field(): void
    {
        // BR-002: mitra bukan approved tidak bisa buat lapangan
        [$mitraPending] = $this->createMitra('pending');
        [$mitraApproved] = $this->createMitra('approved');

        $this->assertFalse(Gate::forUser($mitraPending)->allows('create', Field::class));
        $this->assertTrue(Gate::forUser($mitraApproved)->allows('create', Field::class));
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Daftarkan route testing ber-middleware
        Route::middleware(['web', 'auth', 'role:mitra', 'mitra.approved'])
            ->get('/mitra/test-protected', function () {
                return 'OK';
            });
    }
}
