<?php

namespace Tests\Feature\Auth;

use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * FR-AUTH-002 / PUB-05 lanjutan. AC-FR-AUTH-002-01/02.
 */
class OtpVerificationTest extends TestCase
{
    use RefreshDatabase;

    private function userWithOtp(string $plainCode = '123456', bool $expired = false): array
    {
        $user = User::factory()->create(['email_verified_at' => null]);

        $otp = OtpCode::create([
            'user_id' => $user->id,
            'kode' => Hash::make($plainCode),
            'tujuan' => 'email',
            'attempts' => 0,
            'expires_at' => $expired ? now()->subMinute() : now()->addMinutes(10),
        ]);

        return [$user, $otp];
    }

    public function test_kode_otp_benar_dan_belum_kedaluwarsa_menandai_akun_terverifikasi(): void
    {
        // AC-FR-AUTH-002-01
        [$user] = $this->userWithOtp('123456');

        $response = $this->withSession(['otp_user_id' => $user->id])
            ->post('/otp/verify', ['kode' => '123456']);

        $response->assertRedirect(route('login'));
        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_kode_otp_salah_akun_tetap_belum_terverifikasi(): void
    {
        // AC-FR-AUTH-002-02
        [$user] = $this->userWithOtp('123456');

        $response = $this->withSession(['otp_user_id' => $user->id])
            ->post('/otp/verify', ['kode' => '999999']);

        $response->assertSessionHasErrors('kode');
        $this->assertNull($user->refresh()->email_verified_at);
    }

    public function test_kode_otp_kedaluwarsa_ditolak(): void
    {
        [$user] = $this->userWithOtp('123456', expired: true);

        $response = $this->withSession(['otp_user_id' => $user->id])
            ->post('/otp/verify', ['kode' => '123456']);

        $response->assertSessionHasErrors('kode');
        $this->assertNull($user->refresh()->email_verified_at);
    }

    public function test_percobaan_melebihi_batas_ditolak(): void
    {
        [$user, $otp] = $this->userWithOtp('123456');
        $otp->update(['attempts' => 5]);

        $response = $this->withSession(['otp_user_id' => $user->id])
            ->post('/otp/verify', ['kode' => '123456']);

        $response->assertSessionHasErrors('kode');
        $this->assertNull($user->refresh()->email_verified_at);
    }

    public function test_kirim_ulang_dalam_cooldown_ditolak(): void
    {
        [$user] = $this->userWithOtp('123456');

        $response = $this->withSession(['otp_user_id' => $user->id])
            ->post('/otp/resend');

        $response->assertSessionHasErrors('kode');
    }

    public function test_kirim_ulang_setelah_cooldown_membuat_kode_baru(): void
    {
        [$user, $otp] = $this->userWithOtp('123456');
        $otp->forceFill(['created_at' => now()->subMinutes(5)])->save();

        $response = $this->withSession(['otp_user_id' => $user->id])
            ->post('/otp/resend');

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseCount('otp_codes', 1);
        $this->assertFalse(Hash::check('123456', $user->otpCodes()->first()->kode));
    }

    public function test_tanpa_sesi_otp_diarahkan_ke_register(): void
    {
        $response = $this->post('/otp/verify', ['kode' => '123456']);

        $response->assertRedirect(route('register'));
    }
}
