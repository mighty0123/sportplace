<?php

namespace App\Services\Mitra;

use App\Models\BookingDetail;
use App\Models\Field;
use App\Models\User;

/**
 * Service resolusi tarif komisi platform (T9.2, GAP-07 / OQ-004 / BR-016 / BR-017).
 * Menyediakan satu antarmuka terpusat untuk kalkulasi komisi tanpa hardcoding rumus tersebar.
 */
class CommissionResolver
{
    /**
     * Inisialisasi resolver dengan opsi override nilai (berguna untuk pengujian unit).
     */
    public function __construct(
        protected ?float $customRate = null,
        protected ?bool $customIsConfigured = null
    ) {
    }

    /**
     * Dapatkan persentase tarif komisi yang berlaku.
     * Mengambil dari konfigurasi pusat (default: 0.0 jika belum ditetapkan).
     */
    public function resolveRate(?Field $field = null, ?User $mitra = null): float
    {
        if ($this->customRate !== null) {
            return max(0.0, min(100.0, $this->customRate));
        }

        $rate = (float) (function_exists('config') ? config('commission.rate_percent', 0.0) : 0.0);

        return max(0.0, min(100.0, $rate));
    }

    /**
     * Periksa apakah tarif komisi telah dikonfigurasi secara definitif.
     */
    public function isConfigured(): bool
    {
        if ($this->customIsConfigured !== null) {
            return $this->customIsConfigured && $this->resolveRate() > 0;
        }

        $configured = function_exists('config') ? (bool) config('commission.is_configured', false) : false;

        return $configured && $this->resolveRate() > 0;
    }

    /**
     * Hitung rincian pendapatan kotor, potongan komisi, dan pendapatan bersih.
     *
     * @return array{
     *     gross: float,
     *     rate_percent: float,
     *     commission: float,
     *     net: float,
     *     is_configured: bool
     * }
     */
    public function calculate(float $grossAmount, ?Field $field = null, ?User $mitra = null): array
    {
        $rate = $this->resolveRate($field, $mitra);
        $commission = round($grossAmount * ($rate / 100), 2);
        $net = max(0.0, round($grossAmount - $commission, 2));

        return [
            'gross' => $grossAmount,
            'rate_percent' => $rate,
            'commission' => $commission,
            'net' => $net,
            'is_configured' => $this->isConfigured(),
        ];
    }

    /**
     * Hitung komisi untuk satu item BookingDetail.
     */
    public function calculateForDetail(BookingDetail $detail): array
    {
        $gross = (float) ($detail->harga_slot ?? 0);
        $field = $detail->relationLoaded('field') ? $detail->field : null;
        $mitraUser = ($field && $field->relationLoaded('venue') && $field->venue?->relationLoaded('mitra'))
            ? $field->venue->mitra?->user
            : null;

        return $this->calculate($gross, $field, $mitraUser);
    }
}
