<?php

namespace Tests\Feature\Booking;

use App\Services\Booking\SlotReservationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\Concerns\CreatesFixtures;
use Tests\TestCase;

class SyncSlotReservationsTest extends TestCase
{
    use RefreshDatabase, CreatesFixtures;

    private int $fieldId;

    protected function setUp(): void
    {
        parent::setUp();
        config(['booking.slot_minutes' => 30, 'booking.payment_window_minutes' => 15]);

        [$city, $sport] = $this->cityAndSport();
        $mitra = $this->mitraProfile($this->makeUser('mitra'), ['status' => 'approved']);
        $this->fieldId = $this->field($this->venue($mitra, $city, ['status_vanue' => 'aktif']), $sport, ['status_lapangan' => 'aktif']);
    }

    /** Booking lama: tidak memegang reservasi. */
    private function legacy(string $status, int $daysAhead = 3, array $header = [], string $start = '19:00:00', string $end = '20:00:00'): int
    {
        $id = $this->booking($this->makeUser(), array_merge(['status' => $status, 'expires_at' => now()->addMinutes(10)], $header));
        $this->bookingDetail($id, $this->fieldId, [
            'tanggal_sewa' => now()->addDays($daysAhead)->format('Y-m-d') . ' 00:00:00', 'jam_mulai' => $start, 'jam_selesai' => $end,
        ]);

        return $id;
    }

    private function active(int $headerId): int
    {
        return DB::table('slot_reservations')->where('header_id', $headerId)->where('holds', 1)->count();
    }

    public function test_active_future_bookings_without_reservations_get_them(): void
    {
        $paid = $this->legacy('payment');
        $waiting = $this->legacy('pending', 4);

        $this->artisan('slots:sync')->expectsOutputToContain('direservasi: 2')->assertSuccessful();

        $this->assertSame(2, $this->active($paid));
        $this->assertSame(2, $this->active($waiting));
    }

    public function test_past_stale_and_cancelled_bookings_are_skipped(): void
    {
        $past = $this->legacy('payment', -2);
        $stale = $this->legacy('pending', 3, ['expires_at' => now()->subMinutes(5), 'created_at' => now()->subHour()]);
        $cancelled = $this->legacy('canceled', 3);

        $this->artisan('slots:sync')->assertSuccessful();

        $this->assertSame(0, $this->active($past) + $this->active($stale) + $this->active($cancelled));
    }

    public function test_the_paid_booking_wins_a_legacy_overlap_and_the_waiting_one_is_reported_once(): void
    {
        $waiting = $this->legacy('pending', 3);   // id lebih kecil
        $paid = $this->legacy('payment', 3);      // slot yang sama

        $this->artisan('slots:sync')->expectsOutputToContain('Bentrok')->assertSuccessful();
        $this->artisan('slots:sync')->assertSuccessful();

        $this->assertSame(2, $this->active($paid));
        $this->assertSame(0, $this->active($waiting));
        $this->assertSame(1, DB::table('audit_logs')->where('entitas_id', $waiting)->where('aksi', 'slot_bentrok')->count());
    }

    public function test_reservations_of_cancelled_and_expired_bookings_are_released(): void
    {
        $id = $this->legacy('pending', 3);
        app(SlotReservationService::class)->reserve($id);
        DB::table('booking_headers')->where('id', $id)->update(['status' => 'expired']);

        $this->artisan('slots:sync')->expectsOutputToContain('Dilepas: 2')->assertSuccessful();

        $this->assertSame(0, $this->active($id));
    }

    public function test_dry_run_reports_but_changes_nothing(): void
    {
        $paid = $this->legacy('payment');

        $this->artisan('slots:sync --dry-run')->expectsOutputToContain('[simulasi]')->assertSuccessful();

        $this->assertSame(0, $this->active($paid));
        $this->assertDatabaseCount('slot_reservations', 0);
    }

    public function test_prune_removes_old_history_only(): void
    {
        $old = $this->legacy('payment', -200);
        $recent = $this->legacy('payment', 3);
        DB::table('slot_reservations')->insert([
            ['header_id' => $old, 'field_id' => $this->fieldId, 'slot_date' => now()->subDays(200)->toDateString(), 'slot_start' => '19:00:00', 'holds' => 1, 'created_at' => now()],
        ]);

        $this->artisan('slots:sync --prune=90')->assertSuccessful();

        $this->assertDatabaseMissing('slot_reservations', ['header_id' => $old]);
        $this->assertSame(2, $this->active($recent));
    }
}