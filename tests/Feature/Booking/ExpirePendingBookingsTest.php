<?php

namespace Tests\Feature\Booking;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesFixtures;
use Tests\TestCase;

class ExpirePendingBookingsTest extends TestCase
{
    use RefreshDatabase, CreatesFixtures;

    protected function setUp(): void
    {
        parent::setUp();
        config(['booking.payment_window_minutes' => 15]);
    }

    public function test_unpaid_booking_past_its_deadline_expires_and_is_audited(): void
    {
        $id = $this->booking($this->makeUser(), ['created_at' => now()->subHour(), 'expires_at' => now()->subMinutes(45)]);

        $this->artisan('bookings:expire')->assertSuccessful();

        $this->assertDatabaseHas('booking_headers', ['id' => $id, 'status' => 'expired']);
        $this->assertDatabaseHas('audit_logs', ['entitas_type' => 'BookingHeader', 'entitas_id' => $id, 'aksi' => 'kedaluwarsa', 'aktor_role' => 'system']);
    }

    public function test_deadline_falls_back_to_creation_time_when_expires_at_is_empty(): void
    {
        $old = $this->booking($this->makeUser(), ['created_at' => now()->subHour(), 'expires_at' => null]);
        $recent = $this->booking($this->makeUser(), ['created_at' => now()->subMinutes(5), 'expires_at' => null]);

        $this->artisan('bookings:expire')->assertSuccessful();

        $this->assertDatabaseHas('booking_headers', ['id' => $old, 'status' => 'expired']);
        $this->assertDatabaseHas('booking_headers', ['id' => $recent, 'status' => 'pending']);
    }

    public function test_booking_that_is_still_within_its_deadline_is_untouched(): void
    {
        $id = $this->booking($this->makeUser(), ['created_at' => now(), 'expires_at' => now()->addMinutes(10)]);

        $this->artisan('bookings:expire')->assertSuccessful();

        $this->assertDatabaseHas('booking_headers', ['id' => $id, 'status' => 'pending']);
    }

    public function test_a_late_successful_payment_is_never_overwritten(): void
    {
        $id = $this->booking($this->makeUser(), ['created_at' => now()->subHour(), 'expires_at' => now()->subMinutes(45)]);
        $this->payment($id, ['status_pembayaran' => 'succes']);

        $this->artisan('bookings:expire')->assertSuccessful();

        $this->assertDatabaseHas('booking_headers', ['id' => $id, 'status' => 'pending']);
    }

    public function test_running_twice_does_not_process_the_same_booking_again(): void
    {
        $this->booking($this->makeUser(), ['created_at' => now()->subHour(), 'expires_at' => now()->subMinutes(45)]);

        $this->artisan('bookings:expire')->expectsOutputToContain('1 booking')->assertSuccessful();
        $this->artisan('bookings:expire')->expectsOutputToContain('0 booking')->assertSuccessful();

        $this->assertDatabaseCount('audit_logs', 1);
    }
}