<?php

namespace App\Services\Auth;

use App\Contracts\OtpSender;
use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * FR-AUTH-002 (GAP-09). Parameter dari config/otp.php (Perlu Konfirmasi di PRD).
 */
class OtpService
{
    public function __construct(private OtpSender $sender)
    {
    }

    /**
     * Buat kode OTP baru untuk user+tujuan, menggantikan baris aktif sebelumnya,
     * lalu kirim lewat OtpSender. Mengembalikan baris OtpCode yang dibuat.
     */
    public function issue(User $user, string $tujuan = 'email'): OtpCode
    {
        $user->otpCodes()->where('tujuan', $tujuan)->whereNull('verified_at')->delete();

        $length = (int) config('otp.length', 6);
        $plainCode = Str::padLeft((string) random_int(0, (10 ** $length) - 1), $length, '0');

        $otp = $user->otpCodes()->create([
            'kode' => Hash::make($plainCode),
            'tujuan' => $tujuan,
            'attempts' => 0,
            'expires_at' => now()->addMinutes((int) config('otp.ttl_minutes', 10)),
        ]);

        $this->sender->send($user, $plainCode, $tujuan);

        return $otp;
    }

    /**
     * Ambil OTP aktif (belum diverifikasi) terbaru untuk user+tujuan, atau null.
     */
    public function latestActive(User $user, string $tujuan = 'email'): ?OtpCode
    {
        return $user->otpCodes()
            ->where('tujuan', $tujuan)
            ->whereNull('verified_at')
            ->latest()
            ->first();
    }

    /**
     * Cek apakah user masih dalam cooldown kirim ulang OTP.
     */
    public function inResendCooldown(User $user, string $tujuan = 'email'): bool
    {
        $otp = $this->latestActive($user, $tujuan);

        if (! $otp) {
            return false;
        }

        $cooldown = (int) config('otp.resend_cooldown_seconds', 60);

        return $otp->created_at->addSeconds($cooldown)->isFuture();
    }

    /**
     * FR-AUTH-002. AC-002-01/02: kode benar & belum kedaluwarsa → terverifikasi;
     * salah/kedaluwarsa/melebihi batas percobaan → ditolak.
     *
     * @return array{success: bool, message: string}
     */
    public function verify(User $user, string $plainCode, string $tujuan = 'email'): array
    {
        $otp = $this->latestActive($user, $tujuan);

        if (! $otp) {
            return ['success' => false, 'message' => 'Kode OTP tidak ditemukan. Silakan minta kode baru.'];
        }

        $maxAttempts = (int) config('otp.max_attempts', 5);

        if ($otp->attempts >= $maxAttempts) {
            return ['success' => false, 'message' => 'Batas percobaan OTP terlampaui. Silakan minta kode baru.'];
        }

        if ($otp->isExpired()) {
            return ['success' => false, 'message' => 'Kode OTP sudah kedaluwarsa. Silakan minta kode baru.'];
        }

        if (! Hash::check($plainCode, $otp->kode)) {
            $otp->increment('attempts');

            return ['success' => false, 'message' => 'Kode OTP tidak valid.'];
        }

        $otp->forceFill(['verified_at' => now()])->save();

        $user->forceFill([
            $tujuan === 'hp' ? 'phone_verified_at' : 'email_verified_at' => now(),
        ])->save();

        return ['success' => true, 'message' => 'Akun berhasil diverifikasi.'];
    }
}
