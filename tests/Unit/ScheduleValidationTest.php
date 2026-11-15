<?php

namespace Tests\Unit;

use App\Http\Requests\Mitra\StoreBlockRequest;
use App\Http\Requests\Mitra\UpdateScheduleRequest;
use App\Models\Field;
use App\Models\Venue;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ScheduleValidationTest extends TestCase
{
    /**
     * T6.1: Validasi jam operasional lapangan harus berada di dalam jam venue (KS-14).
     */
    public function test_schedule_must_be_within_venue_operating_hours(): void
    {
        $venue = new Venue;
        $venue->setRawAttributes([
            'id' => 1,
            'jam_buka' => '08:00:00',
            'jam_tutup' => '22:00:00',
        ]);

        $field = new Field;
        $field->setRawAttributes(['id' => 10, 'venue_id' => 1]);
        $field->setRelation('venue', $venue);

        // Kasus 1: Lapangan buka jam 07:00 (sebelum venue buka jam 08:00) -> Harus Ditolak
        $request1 = new UpdateScheduleRequest;
        $request1->setRouteResolver(fn () => $field);
        $data1 = [
            'schedules' => [
                [
                    'hari' => 1,
                    'status_hari' => 'buka',
                    'jam_buka' => '07:00',
                    'jam_tutup' => '21:00',
                    'durasi' => '01:00',
                ],
            ],
        ];
        $request1->merge($data1);

        $validator1 = Validator::make($data1, $request1->rules(), $request1->messages());
        $request1->withValidator($validator1);

        $this->assertTrue($validator1->fails());
        $this->assertTrue($validator1->errors()->has('schedules.0.jam_buka'));
        $this->assertStringContainsString('berada di luar jam buka venue', $validator1->errors()->first('schedules.0.jam_buka'));

        // Kasus 2: Lapangan tutup jam 23:00 (setelah venue tutup jam 22:00) -> Harus Ditolak
        $request2 = new UpdateScheduleRequest;
        $request2->setRouteResolver(fn () => $field);
        $data2 = [
            'schedules' => [
                [
                    'hari' => 1,
                    'status_hari' => 'buka',
                    'jam_buka' => '09:00',
                    'jam_tutup' => '23:00',
                    'durasi' => '01:00',
                ],
            ],
        ];
        $request2->merge($data2);

        $validator2 = Validator::make($data2, $request2->rules(), $request2->messages());
        $request2->withValidator($validator2);

        $this->assertTrue($validator2->fails());
        $this->assertTrue($validator2->errors()->has('schedules.0.jam_buka'));

        // Kasus 3: Jam lapangan di dalam rentang venue (09:00–21:00) -> Lolos
        $request3 = new UpdateScheduleRequest;
        $request3->setRouteResolver(fn () => $field);
        $data3 = [
            'schedules' => [
                [
                    'hari' => 1,
                    'status_hari' => 'buka',
                    'jam_buka' => '09:00',
                    'jam_tutup' => '21:00',
                    'durasi' => '01:00',
                ],
            ],
        ];
        $request3->merge($data3);

        $validator3 = Validator::make($data3, $request3->rules(), $request3->messages());
        $request3->withValidator($validator3);

        $this->assertFalse($validator3->fails());
    }

    /**
     * T6.1: Jam tutup lapangan harus lebih malam daripada jam buka.
     */
    public function test_schedule_close_time_must_be_after_open_time(): void
    {
        $venue = new Venue;
        $venue->setRawAttributes([
            'id' => 1,
            'jam_buka' => '06:00:00',
            'jam_tutup' => '23:00:00',
        ]);

        $field = new Field;
        $field->setRawAttributes(['id' => 10, 'venue_id' => 1]);
        $field->setRelation('venue', $venue);

        $request = new UpdateScheduleRequest;
        $request->setRouteResolver(fn () => $field);

        $data = [
            'schedules' => [
                [
                    'hari' => 2,
                    'status_hari' => 'buka',
                    'jam_buka' => '15:00',
                    'jam_tutup' => '10:00', // Salah
                    'durasi' => '01:00',
                ],
            ],
        ];
        $request->merge($data);

        $validator = Validator::make($data, $request->rules(), $request->messages());
        $request->withValidator($validator);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('schedules.0.jam_tutup'));
        $this->assertStringContainsString('lebih malam', $validator->errors()->first('schedules.0.jam_tutup'));
    }

    /**
     * T6.3: Validasi jam blokir (jam_selesai harus setelah jam_mulai).
     */
    public function test_block_time_must_be_chronologically_valid(): void
    {
        $request = new StoreBlockRequest;
        $rules = $request->rules();

        $invalidData = [
            'tanggal_mulai' => '2026-11-10',
            'tanggal_selesai' => '2026-11-10',
            'jam_mulai' => '14:00',
            'jam_selesai' => '12:00', // Sebelum jam mulai
            'alasan_pemblokiran' => 'Perbaikan Lampu',
        ];

        $validator = Validator::make($invalidData, $rules, $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('jam_selesai'));
    }
}
