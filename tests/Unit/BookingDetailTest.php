<?php

namespace Tests\Unit;

use App\Models\BookingDetail;
use App\Models\BookingHeader;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class BookingDetailTest extends TestCase
{
    /**
     * Test NFR-PRIV-001: Phone number masking protects user privacy.
     */
    public function test_masked_phone_number(): void
    {
        $detail = new BookingDetail;
        $header = new BookingHeader;
        $user = new User;
        $user->no_hp = '081234567890';
        $header->setRelation('user', $user);
        $detail->setRelation('header', $header);

        $this->assertEquals('0812****890', $detail->masked_phone);
    }

    /**
     * Test NFR-PRIV-001: Email address masking protects user privacy.
     */
    public function test_masked_email_address(): void
    {
        $detail = new BookingDetail;
        $header = new BookingHeader;
        $user = new User;
        $user->email = 'budi.santoso@example.com';
        $header->setRelation('user', $user);
        $detail->setRelation('header', $header);

        $this->assertEquals('bu**********@example.com', $detail->masked_email);
    }

    /**
     * Test AC-FR-ORDER-003-02: isReadyToComplete returns false if session has not ended.
     */
    public function test_ready_to_complete_returns_false_when_session_not_ended(): void
    {
        $detail = new BookingDetail;
        $detail->setRawAttributes([
            'tanggal_sewa' => Carbon::today()->toDateString(),
            'jam_mulai' => Carbon::now()->addHours(1)->format('H:i:s'),
            'jam_selesai' => Carbon::now()->addHours(2)->format('H:i:s'),
            'status_item' => 'ongoing',
        ]);

        $header = new BookingHeader(['status' => 'payment']);
        $detail->setRelation('header', $header);

        $this->assertFalse($detail->isReadyToComplete());
    }

    /**
     * Test AC-FR-ORDER-003-01 / 02: isReadyToComplete returns true when session has ended and is paid.
     */
    public function test_ready_to_complete_returns_true_when_session_ended_and_paid(): void
    {
        $detail = new BookingDetail;
        $detail->setRawAttributes([
            'tanggal_sewa' => Carbon::yesterday()->toDateString(),
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '09:00:00',
            'status_item' => 'ongoing',
        ]);

        $header = new BookingHeader(['status' => 'payment']);
        $detail->setRelation('header', $header);

        $this->assertTrue($detail->isReadyToComplete());
    }

    /**
     * Test AC-FR-ORDER-002-01: canMarkNoShow returns false before session starts.
     */
    public function test_can_mark_no_show_returns_false_before_start_time(): void
    {
        $detail = new BookingDetail;
        $detail->setRawAttributes([
            'tanggal_sewa' => Carbon::today()->toDateString(),
            'jam_mulai' => Carbon::now()->addHours(2)->format('H:i:s'),
            'jam_selesai' => Carbon::now()->addHours(3)->format('H:i:s'),
            'status_item' => 'ongoing',
        ]);

        $header = new BookingHeader(['status' => 'payment']);
        $detail->setRelation('header', $header);

        $this->assertFalse($detail->canMarkNoShow());
    }

    /**
     * Test AC-FR-ORDER-002-01: canMarkNoShow returns true after session start time on today's schedule.
     */
    public function test_can_mark_no_show_returns_true_after_start_time(): void
    {
        $detail = new BookingDetail;
        $detail->setRawAttributes([
            'tanggal_sewa' => Carbon::today()->toDateString(),
            'jam_mulai' => Carbon::now()->subMinutes(30)->format('H:i:s'),
            'jam_selesai' => Carbon::now()->addMinutes(30)->format('H:i:s'),
            'status_item' => 'ongoing',
        ]);

        $header = new BookingHeader(['status' => 'payment']);
        $detail->setRelation('header', $header);

        $this->assertTrue($detail->canMarkNoShow());
    }

    /**
     * Test isPaid returns true when payment status is 'succes' or header status is 'payment'.
     */
    public function test_is_paid_recognizes_payment_gateway_success(): void
    {
        $detail = new BookingDetail;
        $header = new BookingHeader(['status' => 'pending']);
        $payment = new Payment(['status_pembayaran' => 'succes']);
        $header->setRelation('payment', $payment);
        $detail->setRelation('header', $header);

        $this->assertTrue($detail->isPaid());
    }
}
