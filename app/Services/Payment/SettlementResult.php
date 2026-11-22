<?php

namespace App\Services\Payment;

/** Hasil satu upaya pelunasan. Dipakai pemanggil untuk menentukan balasan HTTP dan catatan kiriman. */
final class SettlementResult
{
    public const SETTLED   = 'settled';           // lunas, booking dibayar
    public const REVIVED   = 'settled_revived';   // lunas terlambat, slot masih kosong, booking dihidupkan kembali
    public const REFUNDED  = 'settled_refunded';  // uang masuk tetapi booking tidak bisa dipakai; refund penuh dibuat
    public const ALREADY   = 'already_settled';   // sudah tercatat berhasil sebelumnya (kiriman ganda)
    public const FAILED    = 'failed';
    public const PENDING   = 'pending';
    public const IGNORED   = 'ignored';
    public const MISMATCH  = 'amount_mismatch';   // nominal tidak cocok: tidak ditandai lunas, perlu ditinjau
    public const UNKNOWN   = 'unknown_order';
    public const DEFERRED  = 'deferred';          // status gateway tidak bisa diverifikasi, coba lagi nanti
    public const REVIEW    = 'needs_review';      // uang masuk, tetapi tidak bisa diproses otomatis

    public function __construct(
        public readonly string $outcome,
        public readonly ?int $bookingId = null,
        public readonly string $message = '',
        public readonly bool $paid = false,
    ) {
    }

    /** Hanya "belum bisa diverifikasi" yang pantas dikirim ulang oleh gateway. */
    public function shouldRetry(): bool
    {
        return $this->outcome === self::DEFERRED;
    }

    /** Hasil yang perlu dilihat */
    public function needsAttention(): bool
    {
        return in_array($this->outcome, [self::MISMATCH, self::REVIEW, self::DEFERRED], true);
    }
}