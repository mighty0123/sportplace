<?php

namespace App\Services\Auth;

use App\Contracts\OtpSender;
use App\Models\User;
use Illuminate\Support\Facades\Log;

/**
 * Implementasi awal OtpSender: tulis ke log channel (dev) — belum ada
 * penyedia email/SMS resmi (DEP-02). Ganti via binding di AppServiceProvider
 * saat penyedia dipilih.
 */
class LogOtpSender implements OtpSender
{
    public function send(User $user, string $plainCode, string $tujuan): void
    {
        Log::info('OTP dikirim', [
            'user_id' => $user->id,
            'tujuan' => $tujuan,
            'kode' => $plainCode,
        ]);
    }
}
