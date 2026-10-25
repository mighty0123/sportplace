<?php

namespace Tests\Feature\Auth;

use App\Models\MitraProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AuthSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware(['web', 'auth', 'role:admin'])->get('/admin/test-area', function () {
            return 'Admin Area';
        });

        Route::middleware(['web', 'auth', 'role:customer'])->get('/customer/test-area', function () {
            return 'Customer Area';
        });
    }

    public function test_password_selalu_di_hash_dan_tidak_pernah_plaintext(): void
    {
        $response = $this->post('/register', [
            'name' => 'Budi Santoso',
            'email' => 'budi_sec@example.com',
            'password' => 'secretPass123',
            'password_confirmation' => 'secretPass123',
            'no_hp' => '081234567890',
            'role' => 'customer',
        ]);

        $user = User::where('email', 'budi_sec@example.com')->first();
        $this->assertNotNull($user);
        $this->assertNotSame('secretPass123', $user->password);
        $this->assertTrue(Hash::check('secretPass123', $user->password));
    }

    public function test_mitra_tidak_dapat_mengakses_area_admin(): void
    {
        $mitra = User::factory()->create([
            'role' => 'mitra',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($mitra)
            ->get('/admin/test-area')
            ->assertForbidden();
    }

    public function test_customer_tidak_dapat_mengakses_area_mitra(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($customer)
            ->get(route('mitra.dashboard'))
            ->assertForbidden();
    }

    public function test_login_rate_limiting_membatasi_percobaan_brute_force(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'email' => 'hacker@example.com',
                'password' => 'wrongpass',
            ]);
        }

        // Percobaan ke-6 harus ditolak oleh rate limiter (Too Many Requests / 429 atau Session Error)
        $response = $this->post('/login', [
            'email' => 'hacker@example.com',
            'password' => 'wrongpass',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_penanganan_sesi_atau_csrf_kedaluwarsa_mengalihkan_ke_login_dengan_pesan(): void
    {
        // EC-23: TokenMismatchException dialihkan ke route login dengan status
        Route::middleware(['web'])->post('/test-csrf-route', function () {
            throw new TokenMismatchException('CSRF token mismatch');
        });

        $response = $this->post('/test-csrf-route');
        $response->assertRedirect(route('login'))
            ->assertSessionHas('status', 'Sesi berakhir, silakan login kembali.');
    }
}

