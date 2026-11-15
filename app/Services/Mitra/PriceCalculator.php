<?php

namespace App\Services\Mitra;

use App\Models\Field;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class PriceCalculator
{
    /**
     * Hitung tarif sewa per jam untuk lapangan pada tanggal dan jam tertentu (FR-FIELD-006 / BR-008).
     *
     * @param  string  $startTime  Format 'H:i' (contoh: '08:00' atau '19:00')
     * @param  string|null  $endTime  Format 'H:i' (opsional)
     * @return array{
     *     price: float,
     *     rate_type: 'regular'|'off_peak'|'peak',
     *     formatted_price: string,
     *     is_peak: bool,
     *     rule_description: ?string
     * }
     */
    public function calculateSlotPrice(Field $field, CarbonInterface|string $date, string $startTime, ?string $endTime = null): array
    {
        $carbonDate = $date instanceof CarbonInterface ? Carbon::instance($date) : Carbon::parse($date);
        $isoDay = $carbonDate->dayOfWeekIso; // 1 = Senin ... 7 = Minggu
        $time = substr($startTime, 0, 5);

        $meta = $field->meta_harga;

        // Jika tidak ada meta_harga atau tipe skema flat -> Gunakan harga_perjam reguler
        if (empty($meta) || ! is_array($meta) || ($meta['tipe_skema'] ?? '') === 'flat') {
            $price = (float) ($field->harga_perjam ?? 0);

            return [
                'price' => $price,
                'rate_type' => 'regular',
                'formatted_price' => 'Rp '.number_format($price, 0, ',', '.'),
                'is_peak' => false,
                'rule_description' => 'Tarif Flat Reguler',
            ];
        }

        $hargaOffPeak = (float) ($meta['harga_off_peak'] ?? $field->harga_perjam ?? 0);
        $hargaPeak = (float) ($meta['harga_peak'] ?? $field->harga_perjam ?? 0);
        $peakRules = $meta['peak_rules'] ?? [];

        // Periksa apakah slot ini masuk dalam salah satu aturan jam peak
        foreach ($peakRules as $rule) {
            $days = $rule['hari'] ?? [];
            $jamMulai = substr($rule['jam_mulai'] ?? '00:00', 0, 5);
            $jamSelesai = substr($rule['jam_selesai'] ?? '24:00', 0, 5);

            // Jika jam_selesai adalah '24:00', samakan dengan '23:59:59' atau bandingkan <= 24:00
            if (in_array($isoDay, $days, false)) {
                // Rentang jam: slot jam_mulai >= rule jam_mulai && slot jam_mulai < rule jam_selesai
                $isMatch = false;
                if ($jamSelesai === '24:00' || $jamSelesai === '00:00' && $jamMulai !== '00:00') {
                    $isMatch = $time >= $jamMulai;
                } else {
                    $isMatch = ($time >= $jamMulai && $time < $jamSelesai);
                }

                if ($isMatch) {
                    return [
                        'price' => $hargaPeak,
                        'rate_type' => 'peak',
                        'formatted_price' => 'Rp '.number_format($hargaPeak, 0, ',', '.'),
                        'is_peak' => true,
                        'rule_description' => 'Tarif Jam Sibuk (Peak Hour)',
                    ];
                }
            }
        }

        // Di luar jam peak -> Berlaku tarif Off-Peak
        return [
            'price' => $hargaOffPeak,
            'rate_type' => 'off_peak',
            'formatted_price' => 'Rp '.number_format($hargaOffPeak, 0, ',', '.'),
            'is_peak' => false,
            'rule_description' => 'Tarif Standar (Off-Peak)',
        ];
    }
}
