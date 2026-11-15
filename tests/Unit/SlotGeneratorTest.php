<?php

namespace Tests\Unit;

use App\Models\BookingDetail;
use App\Models\BookingHeader;
use App\Models\Field;
use App\Models\FieldBlock;
use App\Models\FieldSchedule;
use App\Models\User;
use App\Services\Mitra\SlotGenerator;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class SlotGeneratorTest extends TestCase
{
    private SlotGenerator $generator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->generator = new SlotGenerator;
    }

    /**
     * AC: SCHED-001-01: 08:00–22:00 dengan durasi 01:00 menghasilkan tepat 14 slot.
     */
    public function test_generate_slots_produces_exact_14_slots_for_standard_operating_hours(): void
    {
        $field = new Field;
        $field->id = 1;
        $field->harga_perjam = 150000;

        // Misal target date: 2026-11-09 (Senin = hari 1 ISO)
        $date = Carbon::parse('2026-11-09');

        $schedule = new FieldSchedule([
            'field_id' => 1,
            'hari' => 1,
            'jam_buka' => '08:00:00',
            'jam_tutup' => '22:00:00',
            'durasi' => '01:00:00',
            'status_hari' => 'buka',
        ]);

        $field->setRelation('schedules', collect([$schedule]));
        $field->setRelation('blocks', collect());
        $field->setRelation('bookingDetails', collect());

        $result = $this->generator->generateSlots($field, $date);

        $this->assertTrue($result['is_open']);
        $this->assertSame('Senin', $result['day_name']);
        $this->assertSame(14, $result['total_slots']);
        $this->assertCount(14, $result['slots']);

        // Slot pertama: 08:00 – 09:00
        $this->assertSame('08:00', $result['slots'][0]['start_time']);
        $this->assertSame('09:00', $result['slots'][0]['end_time']);

        // Slot terakhir: 21:00 – 22:00
        $this->assertSame('21:00', $result['slots'][13]['start_time']);
        $this->assertSame('22:00', $result['slots'][13]['end_time']);
    }

    /**
     * AC: SCHED-001-02: Hari tutup menghasilkan 0 slot dan status is_open = false.
     */
    public function test_closed_day_returns_zero_slots_and_is_open_false(): void
    {
        $field = new Field;
        $field->id = 1;

        // Hari Minggu = hari 7 ISO
        $date = Carbon::parse('2026-11-15');

        $schedule = new FieldSchedule([
            'field_id' => 1,
            'hari' => 7,
            'status_hari' => 'tutup',
        ]);

        $field->setRelation('schedules', collect([$schedule]));

        $result = $this->generator->generateSlots($field, $date);

        $this->assertFalse($result['is_open']);
        $this->assertSame('Minggu', $result['day_name']);
        $this->assertSame(0, $result['total_slots']);
        $this->assertEmpty($result['slots']);
    }

    /**
     * AC: SCHED-002-01: Pemblokiran lapangan menandai slot yang bentrok sebagai 'blocked'.
     */
    public function test_blocked_time_range_marks_overlapping_slots_as_blocked(): void
    {
        $field = new Field;
        $field->id = 1;
        $field->harga_perjam = 100000;

        $date = Carbon::parse('2026-11-09'); // Senin

        $schedule = new FieldSchedule([
            'field_id' => 1,
            'hari' => 1,
            'jam_buka' => '08:00:00',
            'jam_tutup' => '12:00:00',
            'durasi' => '01:00:00',
            'status_hari' => 'buka',
        ]);

        // Blokir dari jam 09:00 s.d 11:00 untuk perawatan rumput
        $block = new FieldBlock;
        $block->setRawAttributes([
            'id' => 99,
            'field_id' => 1,
            'tanggal_mulai' => '2026-11-09',
            'tanggal_selesai' => '2026-11-09',
            'jam_mulai' => '09:00:00',
            'jam_selesai' => '11:00:00',
            'alasan_pemblokiran' => 'Perawatan Rumput Sintetis',
        ]);

        $field->setRelation('schedules', collect([$schedule]));
        $field->setRelation('blocks', collect([$block]));
        $field->setRelation('bookingDetails', collect());

        $result = $this->generator->generateSlots($field, $date);

        $this->assertSame(4, $result['total_slots']);

        // Slot 08:00-09:00: Available
        $this->assertSame('available', $result['slots'][0]['status']);
        $this->assertTrue($result['slots'][0]['is_available']);

        // Slot 09:00-10:00: Blocked
        $this->assertSame('blocked', $result['slots'][1]['status']);
        $this->assertFalse($result['slots'][1]['is_available']);
        $this->assertSame('Perawatan Rumput Sintetis', $result['slots'][1]['reason']);
        $this->assertSame(99, $result['slots'][1]['block_id']);

        // Slot 10:00-11:00: Blocked
        $this->assertSame('blocked', $result['slots'][2]['status']);
        $this->assertFalse($result['slots'][2]['is_available']);

        // Slot 11:00-12:00: Available
        $this->assertSame('available', $result['slots'][3]['status']);
        $this->assertTrue($result['slots'][3]['is_available']);
    }

    /**
     * Pengujian booking aktif menandai slot sebagai 'booked'.
     */
    public function test_active_booking_marks_slot_as_booked(): void
    {
        $field = new Field;
        $field->id = 1;

        $date = Carbon::parse('2026-11-09');

        $schedule = new FieldSchedule([
            'field_id' => 1,
            'hari' => 1,
            'jam_buka' => '18:00:00',
            'jam_tutup' => '20:00:00',
            'durasi' => '01:00:00',
            'status_hari' => 'buka',
        ]);

        $penyewa = new User(['name' => 'Budi Santoso']);
        $header = new BookingHeader(['status' => 'payment']);
        $header->setRelation('user', $penyewa);

        $booking = new BookingDetail;
        $booking->setRawAttributes([
            'id' => 101,
            'field_id' => 1,
            'tanggal_sewa' => '2026-11-09',
            'jam_mulai' => '18:00:00',
            'jam_selesai' => '19:00:00',
            'status_item' => 'ongoing',
        ]);
        $booking->setRelation('header', $header);

        $field->setRelation('schedules', collect([$schedule]));
        $field->setRelation('blocks', collect());
        $field->setRelation('bookingDetails', collect([$booking]));

        $result = $this->generator->generateSlots($field, $date);

        $this->assertSame(2, $result['total_slots']);

        // Slot 18:00-19:00: Booked
        $this->assertSame('booked', $result['slots'][0]['status']);
        $this->assertFalse($result['slots'][0]['is_available']);
        $this->assertSame('Budi Santoso', $result['slots'][0]['penyewa_name']);

        // Slot 19:00-20:00: Available
        $this->assertSame('available', $result['slots'][1]['status']);
        $this->assertTrue($result['slots'][1]['is_available']);
    }
}
