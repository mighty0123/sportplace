<?php

namespace App\Services\Auth;

use App\Contracts\OtpSender;
use App\Mail\OtpMail;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MailOtpSender implements OtpSender
{
    /**
     * Kirim kode OTP ke email user melalui Laravel Mailer.
     * Juga mencatat ke log sebagai fallback pencatatan dev.
     */
    public function send(User $user, string $plainCode, string $tujuan): void
    {
        // Tetap catat ke log lokal agar selalu bisa ditinjau saat dev
        Log::info('OTP dikirim', [
            'user_id' => $user->id,
            'email' => $user->email,
            'tujuan' => $tujuan,
            'kode' => $plainCode,
        ]);

        // Kirim email jika tujuan adalah email
        if ($tujuan === 'email' && ! empty($user->email)) {
            try {
                Mail::to($user->email)->send(new OtpMail($user, $plainCode));
            } catch (Exception $e) {
                Log::error('Gagal mengirim email OTP: ' . $e->getMessage(), [
                    'user_id' => $user->id,
                    'email' => $user->email,
                ]);
            }
        }
    }
}

