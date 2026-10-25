<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * FR-AUTH-004 / PUB-06. AC-FR-AUTH-004-01/02.
 */
class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(string $password = 'oldPassword123'): User
    {
        return User::factory()->create([
            'password' => Hash::make($password),
            'email_verified_at' => now(),
        ]);
    }

    public function test_forgot_password_form_dapat_diakses(): void
    {
        $response = $this->get(route('password.request'));

        $response->assertStatus(200);
    }

    public function test_mengirim_email_terdaftar_menampilkan_pesan_netral(): void
    {
        $user = $this->createUser();

        $response = $this->post(route('password.send-reset-link'), ['email' => $user->email]);

        $response->assertSessionHas('status');
        $this->assertDatabaseHas('password_reset_tokens', ['email' => $user->email]);
    }

    public function test_mengirim_email_tidak_terdaftar_menampilkan_pesan_netral(): void
    {
        $response = $this->post(route('password.send-reset-link'), ['email' => 'tidakada@example.com']);

        // Pesan sama dengan email terdaftar (cegah enumerasi).
        $response->assertSessionHas('status', 'Jika email terdaftar, tautan reset password telah dikirim.');
    }

    public function test_token_reset_valid_dapat_diakses(): void
    {
        $user = $this->createUser();
        $token = 'valid-token-123';

        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        $response = $this->get(route('password.reset', ['token' => $token], false).'?email='.$user->email);

        $response->assertStatus(200);
    }

    public function test_token_reset_invalid_diarahkan_ke_login(): void
    {
        $user = $this->createUser();

        $response = $this->get(route('password.reset', ['token' => 'invalid-token'], false).'?email='.$user->email);

        $response->assertRedirect(route('login'));
    }

    public function test_password_baru_berhasil_mengubah_password(): void
    {
        // AC-FR-AUTH-004-01
        $user = $this->createUser('oldPassword123');
        $token = 'reset-token-123';

        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        $response = $this->post(route('password.update'), [
            'email' => $user->email,
            'token' => $token,
            'password' => 'newPassword456',
            'password_confirmation' => 'newPassword456',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertFalse(Hash::check('oldPassword123', $user->refresh()->password));
        $this->assertTrue(Hash::check('newPassword456', $user->password));
    }

    public function test_token_tidak_valid_password_tidak_berubah(): void
    {
        // AC-FR-AUTH-004-02
        $user = $this->createUser('oldPassword123');
        $passwordHash = $user->password;

        $response = $this->post(route('password.update'), [
            'email' => $user->email,
            'token' => 'invalid-token',
            'password' => 'newPassword456',
            'password_confirmation' => 'newPassword456',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertEquals($passwordHash, $user->refresh()->password);
    }

    public function test_password_baru_harus_min_8_karakter(): void
    {
        $user = $this->createUser();
        $token = 'reset-token-123';

        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        $response = $this->post(route('password.update'), [
            'email' => $user->email,
            'token' => $token,
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_konfirmasi_password_harus_cocok(): void
    {
        $user = $this->createUser();
        $token = 'reset-token-123';

        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        $response = $this->post(route('password.update'), [
            'email' => $user->email,
            'token' => $token,
            'password' => 'newPassword456',
            'password_confirmation' => 'berbeda123456',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_token_dihapus_setelah_reset_berhasil(): void
    {
        $user = $this->createUser();
        $token = 'reset-token-123';

        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        $this->post(route('password.update'), [
            'email' => $user->email,
            'token' => $token,
            'password' => 'newPassword456',
            'password_confirmation' => 'newPassword456',
        ]);

        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $user->email]);
    }
}
