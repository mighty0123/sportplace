<?php

namespace Tests\Feature\Booking;

use App\Exceptions\Booking\SlotUnavailableException;
use App\Services\Booking\SlotReservationService;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Tests\Concerns\CreatesFixtures;
use Tests\TestCase;

class SlotReservationServiceTest extends TestCase
{
    use RefreshDatabase, CreatesFixtures;

    private int $fieldId;
    private string $date;

    protected function setUp(): void
    {
        parent::setUp();
        config(['booking.slot_minutes' => 30, 'booking.payment_window_minutes' => 15]);
        $this->date = now()->addDays(3)->format('Y-m-d');

        [$city, $sport] = $this->cityAndSport();
        $mitra = $this->mitraProfile($this->makeUser('mitra'), ['status' => 'approved']);
        $this->fieldId = $this->field($this->venue($mitra, $city, ['status_vanue' => 'aktif']), $sport, ['status_lapangan' => 'aktif']);
    }

    private function svc(): SlotReservationService
    {
        return app(SlotReservationService::class);
    }

    private function slotBooking(string $start = '19:00:00', string $end = '20:00:00', string $status = 'pending', array $header = [], ?int $field = null, ?string $date = null): int
    {
        $id = $this->booking($this->makeUser(), array_merge(['status' => $status, 'expires_at' => now()->addMinutes(10)], $header));
        $this->bookingDetail($id, $field ?? $this->fieldId, ['tanggal_sewa' => ($date ?? $this->date) . ' 00:00:00', 'jam_mulai' => $start, 'jam_selesai' => $end]);

        return $id;
    }

    public function test_reserving_creates_one_active_row_per_unit(): void
    {
        $id = $this->slotBooking('19:00:00', '20:00:00');

        $this->svc()->reserve($id);

        $this->assertSame(['19:00:00', '19:30:00'], DB::table('slot_reservations')->where('header_id', $id)->where('holds', 1)->orderBy('slot_start')->pluck('slot_start')->all());
    }

    public function test_a_second_booking_for_the_same_slot_is_refused_and_leaves_no_partial_rows(): void
    {
        $first = $this->slotBooking();
        $second = $this->slotBooking();
        $this->svc()->reserve($first);

        try {
            $this->svc()->reserve($second);
            $this->fail('Seharusnya bentrok.');
        } catch (SlotUnavailableException $e) {
            $this->assertSame($this->fieldId, $e->fieldId);
            $this->assertNotNull($e->holderCode);
        }

        $this->assertDatabaseMissing('slot_reservations', ['header_id' => $second]);
    }

    public function test_partial_overlap_conflicts_but_adjacent_slots_do_not(): void
    {
        $a = $this->slotBooking('19:00:00', '21:00:00');
        $this->svc()->reserve($a);

        $this->expectException(SlotUnavailableException::class);
        $this->svc()->reserve($this->slotBooking('20:00:00', '21:00:00'));
    }

    public function test_adjacent_slots_can_both_be_reserved(): void
    {
        $this->svc()->reserve($this->slotBooking('19:00:00', '20:00:00'));
        $this->svc()->reserve($this->slotBooking('20:00:00', '21:00:00'));

        $this->assertSame(4, DB::table('slot_reservations')->where('holds', 1)->count());
    }

    public function test_other_fields_and_other_dates_do_not_conflict(): void
    {
        $this->svc()->reserve($this->slotBooking());

        [$city, $sport] = $this->cityAndSport();
        $mitra = $this->mitraProfile($this->makeUser('mitra'), ['status' => 'approved']);
        $other = $this->field($this->venue($mitra, $city, ['status_vanue' => 'aktif']), $sport, ['status_lapangan' => 'aktif']);

        $this->svc()->reserve($this->slotBooking('19:00:00', '20:00:00', 'pending', [], $other));
        $this->svc()->reserve($this->slotBooking('19:00:00', '20:00:00', 'pending', [], null, now()->addDays(4)->format('Y-m-d')));

        $this->assertSame(6, DB::table('slot_reservations')->where('holds', 1)->count());
    }

    public function test_releasing_frees_the_slot_for_the_next_booking_and_keeps_history(): void
    {
        $a = $this->slotBooking();
        $this->svc()->reserve($a);
        $this->assertSame(2, $this->svc()->release($a));

        $this->svc()->reserve($this->slotBooking());

        $this->assertSame(2, DB::table('slot_reservations')->where('header_id', $a)->whereNull('holds')->count());
        $this->assertSame(2, DB::table('slot_reservations')->where('holds', 1)->count());
    }

    public function test_an_overdue_pending_hold_is_released_automatically_when_someone_else_needs_the_slot(): void
    {
        $stale = $this->slotBooking('19:00:00', '20:00:00', 'pending', ['expires_at' => now()->subMinutes(5), 'created_at' => now()->subHour()]);
        $this->svc()->reserve($stale);

        $this->svc()->reserve($this->slotBooking());

        $this->assertSame(0, DB::table('slot_reservations')->where('header_id', $stale)->where('holds', 1)->count());
    }

    public function test_cancelled_and_expired_holds_are_released_automatically(): void
    {
        foreach (['canceled', 'expired'] as $status) {
            $old = $this->slotBooking('10:00:00', '11:00:00', $status);
            $this->svc()->reserve($old);

            $this->svc()->reserve($this->slotBooking('10:00:00', '11:00:00'));

            $this->assertSame(0, DB::table('slot_reservations')->where('header_id', $old)->where('holds', 1)->count(), $status);
            $this->svc()->release((int) DB::table('slot_reservations')->where('holds', 1)->value('header_id'));
        }
    }

    public function test_an_overdue_pending_booking_that_already_has_a_successful_payment_keeps_its_slot(): void
    {
        $paying = $this->slotBooking('19:00:00', '20:00:00', 'pending', ['expires_at' => now()->subMinutes(5), 'created_at' => now()->subHour()]);
        $this->payment($paying, ['status_pembayaran' => 'succes']);
        $this->svc()->reserve($paying);

        $this->expectException(SlotUnavailableException::class);
        $this->svc()->reserve($this->slotBooking());
    }

    public function test_reserving_twice_for_the_same_booking_is_idempotent(): void
    {
        $id = $this->slotBooking();

        $this->svc()->reserve($id);
        $this->svc()->reserve($id);

        $this->assertSame(2, DB::table('slot_reservations')->where('header_id', $id)->count());
    }

    public function test_times_that_do_not_align_with_the_slot_unit_are_rejected(): void
    {
        $id = $this->slotBooking('19:15:00', '20:15:00');

        $this->expectException(InvalidArgumentException::class);
        $this->svc()->reserve($id);
    }

    public function test_a_slot_ending_at_midnight_covers_the_last_units_of_the_day(): void
    {
        $this->assertSame(['23:00:00', '23:30:00'], $this->svc()->units('23:00:00', '00:00:00'));
    }

    public function test_the_database_itself_refuses_two_active_holders(): void
    {
        $id = $this->slotBooking();
        $this->svc()->reserve($id);
        $row = DB::table('slot_reservations')->where('header_id', $id)->first();

        $this->expectException(UniqueConstraintViolationException::class);
        DB::table('slot_reservations')->insert([
            'header_id' => $id, 'field_id' => $row->field_id, 'slot_date' => $row->slot_date,
            'slot_start' => $row->slot_start, 'holds' => 1, 'created_at' => now(),
        ]);
    }

    public function test_released_rows_never_block_each_other_in_the_unique_index(): void
    {
        $id = $this->slotBooking();
        $row = ['header_id' => $id, 'field_id' => $this->fieldId, 'slot_date' => $this->date, 'slot_start' => '19:00:00', 'holds' => null, 'created_at' => now()];

        DB::table('slot_reservations')->insert([$row, $row, $row]);

        $this->assertSame(3, DB::table('slot_reservations')->whereNull('holds')->count());
    }
}