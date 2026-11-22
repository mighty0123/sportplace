<?php

namespace Tests\Feature\Payment;

use App\Services\MidtransService;
use App\Services\Payment\PaymentSettlementService;
use App\Services\Payment\SettlementResult as R;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\Concerns\CreatesFixtures;
use Tests\TestCase;
use App\Services\Booking\SlotReservationService;

class PaymentSettlementTest extends TestCase
{
    use RefreshDatabase, CreatesFixtures;

    private int $fieldId;

    protected function setUp(): void
    {
        parent::setUp();
        config(['booking.payment_window_minutes' => 15, 'booking.reconcile_interval_seconds' => 10, 'booking.poll_reconcile' => true]);

        [$city, $sport] = $this->cityAndSport();
        $mitra = $this->mitraProfile($this->makeUser('mitra'), ['status' => 'approved']);
        $this->fieldId = $this->field($this->venue($mitra, $city, ['status_vanue' => 'aktif']), $sport, ['status_lapangan' => 'aktif']);
    }

    private function service(): PaymentSettlementService
    {
        return app(PaymentSettlementService::class);
    }

    /** Status gateway yang sudah terverifikasi. */
    private function gw(string $status = 'settlement', string $gross = '152500.00', array $extra = []): array
    {
        return array_merge(['transaction_status' => $status, 'fraud_status' => 'accept', 'payment_type' => 'qris', 'gross_amount' => $gross, 'status_code' => '200'], $extra);
    }

    private function slotDate(): string
    {
        return now()->addDays(3)->format('Y-m-d');
    }

    /** Booking menunggu dengan satu slot (19.00 sampai 20.00, tiga hari lagi) dan satu percobaan bayar. @return array{0:int,1:string} */
    private function pendingBooking(array $header = [], array $payment = []): array
    {
        $id = $this->booking($this->makeUser(), array_merge([
            'total_tagihan' => 152500, 'status' => 'pending', 'created_at' => now()->subMinutes(2), 'expires_at' => now()->addMinutes(13),
        ], $header));
        $this->bookingDetail($id, $this->fieldId, [
            'harga_slot' => 150000, 'tanggal_sewa' => $this->slotDate() . ' 00:00:00', 'jam_mulai' => '19:00:00', 'jam_selesai' => '20:00:00',
        ]);
        $order = "SP-{$id}-" . random_int(1000, 9999);
        $this->payment($id, array_merge([
            'pg_transaction_id' => $order, 'nominal_bayar' => 152500, 'status_pembayaran' => 'pending', 'paid_at' => null,
        ], $payment));

        return [$id, $order];
    }

    /** Booking lain yang memegang slot yang sama (lewat reservasi, bukan hanya baris rincian). */
    private function occupySlot(string $status = 'payment'): int
    {
        $other = $this->booking($this->makeUser(), ['status' => $status, 'expires_at' => now()->addMinutes(10)]);
        $this->bookingDetail($other, $this->fieldId, ['tanggal_sewa' => $this->slotDate() . ' 00:00:00', 'jam_mulai' => '19:00:00', 'jam_selesai' => '20:00:00']);
        app(SlotReservationService::class)->reserve($other);

        return $other;
    }

    public function test_a_successful_payment_makes_the_booking_hold_its_slot(): void
    {
        [$id, $order] = $this->pendingBooking();

        $this->service()->apply($order, $this->gw(), 'webhook');

        $this->assertSame(2, DB::table('slot_reservations')->where('header_id', $id)->where('holds', 1)->count());
    }

    public function test_payment_on_a_still_pending_booking_whose_slot_is_held_by_another_is_refunded_and_closed(): void
    {
        [$id, $order] = $this->pendingBooking();
        $this->occupySlot('payment');

        $r = $this->service()->apply($order, $this->gw(), 'webhook');

        $this->assertSame(R::REFUNDED, $r->outcome);
        $this->assertDatabaseHas('booking_headers', ['id' => $id, 'status' => 'canceled']);
        $this->assertDatabaseCount('refunds', 1);
        $this->assertDatabaseMissing('audit_logs', ['entitas_id' => $id, 'aksi' => 'dibayar']);
    }

    public function test_a_failed_payment_releases_the_slot(): void
    {
        [$id, $order] = $this->pendingBooking();
        app(SlotReservationService::class)->reserve($id);

        $this->service()->apply($order, $this->gw('expire'), 'webhook');

        $this->assertSame(0, DB::table('slot_reservations')->where('header_id', $id)->where('holds', 1)->count());
    }

    public function test_successful_payment_marks_payment_booking_and_slots_and_is_audited(): void
    {
        [$id, $order] = $this->pendingBooking();

        $r = $this->service()->apply($order, $this->gw(), 'webhook');

        $this->assertSame(R::SETTLED, $r->outcome);
        $this->assertTrue($r->paid);
        $this->assertDatabaseHas('payments', ['pg_transaction_id' => $order, 'status_pembayaran' => 'succes', 'metode_pembayaran' => 'Midtrans (qris)']);
        $this->assertDatabaseHas('booking_headers', ['id' => $id, 'status' => 'payment']);
        $this->assertDatabaseHas('booking_details', ['header_id' => $id, 'status_item' => 'ongoing']);
        $this->assertDatabaseHas('audit_logs', ['entitas_type' => 'BookingHeader', 'entitas_id' => $id, 'aksi' => 'dibayar']);
    }

    public function test_settling_twice_has_exactly_one_effect(): void
    {
        [$id, $order] = $this->pendingBooking();

        $this->service()->apply($order, $this->gw(), 'webhook');
        $paidAt = DB::table('payments')->where('pg_transaction_id', $order)->value('paid_at');
        $second = $this->service()->apply($order, $this->gw(), 'poll');

        $this->assertSame(R::ALREADY, $second->outcome);
        $this->assertTrue($second->paid);
        $this->assertSame($paidAt, DB::table('payments')->where('pg_transaction_id', $order)->value('paid_at'));
        $this->assertSame(1, DB::table('audit_logs')->where('entitas_id', $id)->where('aksi', 'dibayar')->count());
    }

    public function test_an_amount_the_gateway_reports_differently_is_never_marked_paid(): void
    {
        [$id, $order] = $this->pendingBooking();

        $r = $this->service()->apply($order, $this->gw('settlement', '1000.00'), 'webhook');

        $this->assertSame(R::MISMATCH, $r->outcome);
        $this->assertDatabaseHas('booking_headers', ['id' => $id, 'status' => 'pending']);
        $this->assertDatabaseHas('payments', ['pg_transaction_id' => $order, 'status_pembayaran' => 'pending']);
        $this->assertDatabaseHas('audit_logs', ['entitas_id' => $id, 'aksi' => 'selisih_pembayaran']);
    }

    public function test_a_payment_that_differs_from_the_bill_is_never_marked_paid(): void
    {
        [$id, $order] = $this->pendingBooking(['total_tagihan' => 152500], ['nominal_bayar' => 1000]);

        $r = $this->service()->apply($order, $this->gw('settlement', '1000.00'), 'webhook');

        $this->assertSame(R::MISMATCH, $r->outcome);
        $this->assertDatabaseHas('booking_headers', ['id' => $id, 'status' => 'pending']);
    }

    public function test_expire_closes_the_booking_unless_another_payment_is_still_alive(): void
    {
        [$id, $order] = $this->pendingBooking();
        $this->service()->apply($order, $this->gw('expire'), 'webhook');
        $this->assertDatabaseHas('booking_headers', ['id' => $id, 'status' => 'expired']);
        $this->assertDatabaseHas('payments', ['pg_transaction_id' => $order, 'status_pembayaran' => 'failed']);

        [$id2, $order2] = $this->pendingBooking();
        $this->payment($id2, ['pg_transaction_id' => "SP-{$id2}-retry", 'nominal_bayar' => 152500, 'status_pembayaran' => 'pending', 'paid_at' => null]);
        $this->service()->apply($order2, $this->gw('expire'), 'webhook');
        $this->assertDatabaseHas('booking_headers', ['id' => $id2, 'status' => 'pending']);
    }

    public function test_deny_fails_the_payment_but_keeps_the_booking_open_for_a_retry(): void
    {
        [$id, $order] = $this->pendingBooking();

        $this->service()->apply($order, $this->gw('deny'), 'webhook');

        $this->assertDatabaseHas('payments', ['pg_transaction_id' => $order, 'status_pembayaran' => 'failed']);
        $this->assertDatabaseHas('booking_headers', ['id' => $id, 'status' => 'pending']);
    }

    public function test_late_payment_revives_the_booking_when_the_slot_is_still_free(): void
    {
        [$id, $order] = $this->pendingBooking(['status' => 'expired', 'expires_at' => now()->subMinutes(30), 'created_at' => now()->subHour()]);

        $r = $this->service()->apply($order, $this->gw(), 'webhook');

        $this->assertSame(R::REVIVED, $r->outcome);
        $this->assertDatabaseHas('booking_headers', ['id' => $id, 'status' => 'payment']);
        $this->assertDatabaseCount('refunds', 0);
    }

    public function test_late_payment_creates_exactly_one_full_refund_when_the_slot_was_taken(): void
    {
        [$id, $order] = $this->pendingBooking(['status' => 'expired', 'expires_at' => now()->subMinutes(30), 'created_at' => now()->subHour()]);
        $this->occupySlot('payment');

        $r = $this->service()->apply($order, $this->gw(), 'webhook');
        $this->service()->apply($order, $this->gw(), 'poll'); // kiriman ganda

        $this->assertSame(R::REFUNDED, $r->outcome);
        $this->assertDatabaseHas('booking_headers', ['id' => $id, 'status' => 'expired']);
        $this->assertDatabaseCount('refunds', 1);
        $this->assertDatabaseHas('refunds', ['header_id' => $id, 'nominal_refund' => 152500, 'status_refund' => 'pending']);
    }

    public function test_payment_on_a_cancelled_booking_is_refunded_never_revived(): void
    {
        [$id, $order] = $this->pendingBooking(['status' => 'canceled']);

        $r = $this->service()->apply($order, $this->gw(), 'webhook');

        $this->assertSame(R::REFUNDED, $r->outcome);
        $this->assertDatabaseHas('booking_headers', ['id' => $id, 'status' => 'canceled']);
        $this->assertDatabaseCount('refunds', 1);
    }

    public function test_a_second_successful_payment_on_a_paid_booking_is_refunded(): void
    {
        [$id, $first] = $this->pendingBooking();
        $this->service()->apply($first, $this->gw(), 'webhook');
        $this->payment($id, ['pg_transaction_id' => "SP-{$id}-dua", 'nominal_bayar' => 152500, 'status_pembayaran' => 'pending', 'paid_at' => null]);

        $r = $this->service()->apply("SP-{$id}-dua", $this->gw(), 'webhook');

        $this->assertSame(R::REFUNDED, $r->outcome);
        $this->assertDatabaseHas('booking_headers', ['id' => $id, 'status' => 'payment']);
        $this->assertDatabaseCount('refunds', 1);
        $this->assertSame(1, DB::table('audit_logs')->where('entitas_id', $id)->where('aksi', 'dibayar')->count(), 'Dibayar hanya dicatat sekali, jadi komisi nanti tidak ganda.');
    }

    public function test_late_notifications_never_downgrade_a_settled_payment(): void
    {
        [$id, $order] = $this->pendingBooking();
        $this->service()->apply($order, $this->gw(), 'webhook');

        $this->service()->apply($order, $this->gw('expire'), 'webhook');
        $this->service()->apply($order, $this->gw('pending'), 'webhook');

        $this->assertDatabaseHas('payments', ['pg_transaction_id' => $order, 'status_pembayaran' => 'succes']);
        $this->assertDatabaseHas('booking_headers', ['id' => $id, 'status' => 'payment']);
    }

    public function test_a_card_under_fraud_challenge_stays_pending(): void
    {
        [$id, $order] = $this->pendingBooking();

        $r = $this->service()->apply($order, $this->gw('capture', '152500.00', ['fraud_status' => 'challenge']), 'webhook');

        $this->assertSame(R::PENDING, $r->outcome);
        $this->assertDatabaseHas('booking_headers', ['id' => $id, 'status' => 'pending']);

        $this->service()->apply($order, $this->gw('capture', '152500.00', ['fraud_status' => 'accept']), 'webhook');
        $this->assertDatabaseHas('booking_headers', ['id' => $id, 'status' => 'payment']);
    }

    public function test_unknown_orders_create_nothing(): void
    {
        $r = $this->service()->apply('SP-0-0', $this->gw(), 'webhook');

        $this->assertSame(R::UNKNOWN, $r->outcome);
        $this->assertDatabaseCount('payments', 0);
        $this->assertDatabaseCount('audit_logs', 0);
    }

    public function test_an_unverifiable_gateway_status_defers_and_changes_nothing(): void
    {
        [$id, $order] = $this->pendingBooking();
        $this->mock(MidtransService::class, function ($m) {
            $m->shouldReceive('isConfigured')->andReturn(true);
            $m->shouldReceive('getStatus')->andReturn(null);
        });

        $r = $this->service()->settleFromGateway($order, 'webhook');

        $this->assertSame(R::DEFERRED, $r->outcome);
        $this->assertTrue($r->shouldRetry());
        $this->assertDatabaseHas('booking_headers', ['id' => $id, 'status' => 'pending']);
    }

    public function test_reconciliation_asks_the_gateway_at_most_once_per_interval(): void
    {
        [$id, $order] = $this->pendingBooking();
        $this->mock(MidtransService::class, function ($m) {
            $m->shouldReceive('isConfigured')->andReturn(true);
            $m->shouldReceive('getStatus')->once()->andReturn($this->gw('pending'));
        });

        $this->service()->reconcileHeader($id);
        $this->service()->reconcileHeader($id);
        $this->service()->reconcileHeader($id);

        $this->addToAssertionCount(1); // Mockery memastikan getStatus tepat satu kali
    }

    public function test_reconciliation_can_be_switched_off(): void
    {
        [$id] = $this->pendingBooking();
        config(['booking.poll_reconcile' => false]);
        $this->mock(MidtransService::class, fn ($m) => $m->shouldNotReceive('getStatus'));

        $this->assertNull($this->service()->reconcileHeader($id));
    }

    public function test_the_snapshot_never_writes_even_when_the_deadline_has_passed(): void
    {
        [$id] = $this->pendingBooking(['expires_at' => now()->subMinutes(5), 'created_at' => now()->subHour()]);

        $snap = $this->service()->snapshot($id);

        $this->assertTrue($snap['is_expired']);
        $this->assertFalse($snap['is_paid']);
        $this->assertSame(0, $snap['remaining_seconds']);
        $this->assertDatabaseHas('booking_headers', ['id' => $id, 'status' => 'pending']);
    }
}