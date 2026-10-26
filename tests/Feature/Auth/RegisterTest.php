<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * FR-AUTH-001 / PUB-05. AC-FR-AUTH-001-01..04.
 */
class RegisterTest extends TestCase
{
    use RefreshDatabase;

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'no_hp' => '081234567890',
            'role' => 'customer',
        ], $overrides);
    }

    public function test_customer_dapat_mendaftar_dengan_data_valid(): void
    {
        // AC-FR-AUTH-001-01
        $response = $this->post('/register', $this->validPayload());

        $response->assertRedirect(route('otp.verify.show'));

        $this->assertDatabaseCount('users', 1);
        $user = User::first();
        $this->assertSame('customer', $user->role);
        $this->assertNotSame('password123', $user->password);
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    public function test_email_duplikat_ditolak_tanpa_membuat_baris_baru(): void
    {
        // AC-FR-AUTH-001-02
        User::factory()->create(['email' => 'budi@example.com']);

        $response = $this->post('/register', $this->validPayload());

        $response->assertSessionHasErrors('email');
        $this->assertDatabaseCount('users', 1);
    }

    public function test_mitra_mendapat_role_mitra(): void
    {
        // AC-FR-AUTH-001-03
        $response = $this->post('/register', $this->validPayload(['role' => 'mitra', 'email' => 'mitra@example.com']));

        $response->assertRedirect(route('otp.verify.show'));
        $this->assertDatabaseHas('users', ['email' => 'mitra@example.com', 'role' => 'mitra']);
    }

    public function test_role_admin_dari_input_publik_ditolak(): void
    {
        // AC-FR-AUTH-001-04
        $response = $this->post('/register', $this->validPayload(['role' => 'admin', 'email' => 'hacker@example.com']));

        $response->assertSessionHasErrors('role');
        $this->assertDatabaseMissing('users', ['email' => 'hacker@example.com']);
        $this->assertDatabaseCount('users', 0);
    }

    public function test_field_wajib_divalidasi(): void
    {
        $response = $this->post('/register', $this->validPayload(['name' => '']));

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseCount('users', 0);
    }

    public function test_password_dan_konfirmasi_harus_cocok(): void
    {
        $response = $this->post('/register', $this->validPayload(['password_confirmation' => 'beda123456']));

        $response->assertSessionHasErrors('password');
        $this->assertDatabaseCount('users', 0);
    }

    public function test_register_memulai_otp_dan_membuat_baris_otp_codes(): void
    {
        $this->post('/register', $this->validPayload());

        $user = User::first();
        $this->assertDatabaseHas('otp_codes', [
            'user_id' => $user->id,
            'tujuan' => 'email',
        ]);
    }
}
