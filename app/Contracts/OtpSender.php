<?php

namespace App\Contracts;

use App\Models\User;

/**
 * Abstraksi penyedia OTP (DEP-02, INT-02 — penyedia belum ditentukan di PRD).
 * Implementasi awal: log/mail driver. Ganti implementasi tanpa menyentuh pemanggil.
 */
interface OtpSender
{
    /**
     * Kirim kode OTP mentah (belum di-hash) ke user lewat kanal tertentu.
     */
    public function send(User $user, string $plainCode, string $tujuan): void;
}
