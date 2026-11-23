<?php

namespace Tests\Unit;

use App\Models\Field;
use App\Services\Mitra\PriceCalculator;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class PriceCalculatorTest extends TestCase
{
    private PriceCalculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = new PriceCalculator;
    }

    /**
     * AC: FIELD-006-01: Slot pada hari Sabtu (weekend) menggunakan harga peak, slot Senin siang menggunakan harga off-peak.
     */
    public function test_saturday_slot_uses_peak_price_and_monday_uses_off_peak(): void
    {
        $field = new Field;
        $field->id = 1;
        $field->harga_perjam = 100000;
        $field->meta_harga = [
            'tipe_skema' => 'peak_off_peak',
            'harga_off_peak' => 100000,
            'harga_peak' => 150000,
            'peak_rules' => [
                [
                    'hari' => [6, 7], // Sabtu & Minggu
                    'jam_mulai' => '00:00',
                    'jam_selesai' => '24:00',
                ],
            ],
        ];

        // 1. Uji slot hari Sabtu (2026-11-14) jam 10:00 -> Harus harga peak (Rp 150.000)
        $saturday = Carbon::parse('2026-11-14');
        $saturdayResult = $this->calculator->calculateSlotPrice($field, $saturday, '10:00', '11:00');

        $this->assertSame(150000.0, $saturdayResult['price']);
        $this->assertTrue($saturdayResult['is_peak']);
        $this->assertSame('peak', $saturdayResult['rate_type']);

        // 2. Uji slot hari Senin (2026-11-09) jam 10:00 -> Harus harga off-peak (Rp 100.000)
        $monday = Carbon::parse('2026-11-09');
        $mondayResult = $this->calculator->calculateSlotPrice($field, $monday, '10:00', '11:00');

        $this->assertSame(100000.0, $mondayResult['price']);
        $this->assertFalse($mondayResult['is_peak']);
        $this->assertSame('off_peak', $mondayResult['rate_type']);
    }

    /**
     * Pengujian aturan jam malam pada hari kerja (Senin 18:00–22:00 adalah peak).
     */
    public function test_night_hours_on_weekday_uses_peak_price(): void
    {
        $field = new Field;
        $field->id = 1;
        $field->harga_perjam = 80000;
        $field->meta_harga = [
            'tipe_skema' => 'peak_off_peak',
            'harga_off_peak' => 80000,
            'harga_peak' => 120000,
            'peak_rules' => [
                [
                    'hari' => [1, 2, 3, 4, 5],
                    'jam_mulai' => '18:00',
                    'jam_selesai' => '22:00',
                ],
            ],
        ];

        $monday = Carbon::parse('2026-11-09');

        // Slot Senin 14:00 -> Off-peak (Rp 80.000)
        $afternoon = $this->calculator->calculateSlotPrice($field, $monday, '14:00', '15:00');
        $this->assertSame(80000.0, $afternoon['price']);
        $this->assertFalse($afternoon['is_peak']);

        // Slot Senin 19:00 -> Peak (Rp 120.000)
        $night = $this->calculator->calculateSlotPrice($field, $monday, '19:00', '20:00');
        $this->assertSame(120000.0, $night['price']);
        $this->assertTrue($night['is_peak']);
    }

    /**
     * Tanpa meta_harga (atau skema flat) -> harga_perjam berlaku seragam untuk semua slot.
     */
    public function test_without_meta_price_returns_base_hourly_price(): void
    {
        $field = new Field;
        $field->id = 1;
        $field->harga_perjam = 125000;
        $field->meta_harga = null;

        $date = Carbon::parse('2026-11-14'); // Sabtu
        $result = $this->calculator->calculateSlotPrice($field, $date, '20:00', '21:00');

        $this->assertSame(125000.0, $result['price']);
        $this->assertFalse($result['is_peak']);
        $this->assertSame('regular', $result['rate_type']);
    }

    /**
     * Pengujian skema tarif Siang (08:00 - 17:00) dan Malam (17:00 - 23:00) dari meta_harga.
     */
    public function test_siang_and_malam_meta_pricing(): void
    {
        $field = new Field;
        $field->id = 1;
        $field->harga_perjam = 180000;
        $field->meta_harga = [
            'siang' => 140000,
            'malam' => 180000,
        ];

        $date = Carbon::parse('2026-11-24');

        // Jam 10:00 -> Siang (Rp 140.000)
        $siang = $this->calculator->calculateSlotPrice($field, $date, '10:00', '11:00');
        $this->assertSame(140000.0, $siang['price']);
        $this->assertFalse($siang['is_peak']);
        $this->assertSame('off_peak', $siang['rate_type']);

        // Jam 19:00 -> Malam (Rp 180.000)
        $malam = $this->calculator->calculateSlotPrice($field, $date, '19:00', '20:00');
        $this->assertSame(180000.0, $malam['price']);
        $this->assertTrue($malam['is_peak']);
        $this->assertSame('peak', $malam['rate_type']);
    }
}
