<?php

namespace Tests\Unit;

use App\Http\Requests\Mitra\UpdatePricingRequest;
use App\Models\Field;
use App\Models\Venue;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class PricingValidationTest extends TestCase
{
    /**
     * T7.2: Harga tidak boleh bernilai negatif.
     */
    public function test_price_cannot_be_negative(): void
    {
        $venue = new Venue;
        $venue->setRawAttributes(['id' => 1, 'mitra_id' => 99]);

        $field = new Field;
        $field->setRawAttributes(['id' => 10, 'venue_id' => 1]);
        $field->setRelation('venue', $venue);

        $request = new UpdatePricingRequest;
        $request->setRouteResolver(fn () => $field);

        $invalidData = [
            'tipe_skema' => 'flat',
            'harga_perjam' => -50000,
        ];
        $request->merge($invalidData);

        $validator = Validator::make($invalidData, $request->rules(), $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('harga_perjam'));
    }

    /**
     * T7.2: Periode peak tidak boleh tumpang-tindih untuk hari yang sama.
     */
    public function test_peak_rules_cannot_overlap_on_same_day(): void
    {
        $venue = new Venue;
        $venue->setRawAttributes(['id' => 1, 'mitra_id' => 99]);

        $field = new Field;
        $field->setRawAttributes(['id' => 10, 'venue_id' => 1]);
        $field->setRelation('venue', $venue);

        $request = new UpdatePricingRequest;
        $request->setRouteResolver(fn () => $field);

        // Aturan 1: Sabtu (hari 6) 08:00–12:00
        // Aturan 2: Sabtu (hari 6) 10:00–14:00 (Tumpang tindih di jam 10:00–12:00)
        $overlappingData = [
            'tipe_skema' => 'peak_off_peak',
            'harga_perjam' => 100000,
            'harga_off_peak' => 100000,
            'harga_peak' => 150000,
            'peak_rules' => [
                [
                    'hari' => [6],
                    'jam_mulai' => '08:00',
                    'jam_selesai' => '12:00',
                ],
                [
                    'hari' => [6],
                    'jam_mulai' => '10:00',
                    'jam_selesai' => '14:00',
                ],
            ],
        ];
        $request->merge($overlappingData);

        $validator = Validator::make($overlappingData, $request->rules(), $request->messages());
        $request->withValidator($validator);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('peak_rules.1.jam_mulai'));
        $this->assertStringContainsString('bertumpukan', $validator->errors()->first('peak_rules.1.jam_mulai'));
    }

    /**
     * Periode peak yang valid dan tidak tumpang tindih harus lolos validasi.
     */
    public function test_valid_peak_rules_pass_validation(): void
    {
        $venue = new Venue;
        $venue->setRawAttributes(['id' => 1, 'mitra_id' => 99]);

        $field = new Field;
        $field->setRawAttributes(['id' => 10, 'venue_id' => 1]);
        $field->setRelation('venue', $venue);

        $request = new UpdatePricingRequest;
        $request->setRouteResolver(fn () => $field);

        // Aturan 1: Hari Kerja (1-5) 18:00–22:00
        // Aturan 2: Akhir Pekan (6-7) 08:00–22:00
        $validData = [
            'tipe_skema' => 'peak_off_peak',
            'harga_perjam' => 90000,
            'harga_off_peak' => 90000,
            'harga_peak' => 140000,
            'peak_rules' => [
                [
                    'hari' => [1, 2, 3, 4, 5],
                    'jam_mulai' => '18:00',
                    'jam_selesai' => '22:00',
                ],
                [
                    'hari' => [6, 7],
                    'jam_mulai' => '08:00',
                    'jam_selesai' => '22:00',
                ],
            ],
        ];
        $request->merge($validData);

        $validator = Validator::make($validData, $request->rules(), $request->messages());
        $request->withValidator($validator);

        $this->assertFalse($validator->fails());
    }
}
