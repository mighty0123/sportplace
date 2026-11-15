<?php

namespace Tests\Feature\Admin;

use App\Services\Admin\TransactionMonitor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\Concerns\CreatesFixtures;
use Tests\TestCase;

class TransactionMonitorTest extends TestCase
{
    use RefreshDatabase, CreatesFixtures;

    private TransactionMonitor $monitor;
    private int $fieldId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->monitor = app(TransactionMonitor::class);

        [$city, $sport] = $this->cityAndSport();
        $mitra = $this->mitraProfile($this->makeUser('mitra'), ['status' => 'approved']);
        $this->fieldId = $this->field($this->venue($mitra, $city, ['status_vanue' => 'aktif']), $sport, ['status_lapangan' => 'aktif']);
    }

    /** Baris hasil monitoring menurut kode booking */
    private function rowFor(int $headerId, array $filters = []): object
    {
        $f = $this->monitor->filters($filters + ['period' => 'semua']);
        $code = DB::table('booking_headers')->where('id', $headerId)->value('kode_booking');

        return $this->monitor->rows($f, 50)->getCollection()->firstWhere('kode_booking', $code);
    }

    private function flagsOf(int $headerId): string
    {
        return implode(' ', $this->monitor->flagsFor($this->rowFor($headerId)));
    }

    public function test_a_consistent_paid_booking_has_no_flags(): void
    {
        $id = $this->paidBooking($this->makeUser(), $this->fieldId, 150000);

        $this->assertSame([], $this->monitor->flagsFor($this->rowFor($id)));
    }

    public function test_paid_status_without_a_successful_payment_is_flagged(): void
    {
        $id = $this->booking($this->makeUser(), ['status' => 'payment']);
        $this->bookingDetail($id, $this->fieldId);

        $this->assertStringContainsString('tidak ada pembayaran berhasil', $this->flagsOf($id));
    }

    public function test_money_received_on_an_expired_booking_is_flagged(): void
    {
        $id = $this->paidBooking($this->makeUser(), $this->fieldId, 100000, ['status' => 'expired']);

        $this->assertStringContainsString('Uang sudah masuk', $this->flagsOf($id));
    }

    public function test_double_payment_is_flagged_as_amount_mismatch(): void
    {
        $id = $this->paidBooking($this->makeUser(), $this->fieldId, 80000);
        $this->payment($id, ['nominal_bayar' => 80000]);

        $this->assertStringContainsString('berbeda dari total tagihan', $this->flagsOf($id));
    }

    public function test_refund_above_the_paid_amount_is_flagged(): void
    {
        $id = $this->paidBooking($this->makeUser(), $this->fieldId, 100000);
        $detail = DB::table('booking_details')->where('header_id', $id)->value('id');
        $payment = DB::table('payments')->where('header_id', $id)->value('id');
        $this->refund($id, $detail, $payment, ['nominal_refund' => 150000]);

        $this->assertStringContainsString('melebihi', $this->flagsOf($id));
    }

    public function test_booking_without_slot_details_is_flagged(): void
    {
        $id = $this->booking($this->makeUser(), ['status' => 'payment']);
        $this->payment($id);

        $this->assertStringContainsString('rincian slot', $this->flagsOf($id));
    }

    public function test_pending_booking_past_its_deadline_is_flagged_even_without_expires_at(): void
    {
        $customer = $this->makeUser();
        $late = $this->booking($customer, ['created_at' => now()->subHour(), 'expires_at' => null]);
        $fresh = $this->booking($customer, ['created_at' => now(), 'expires_at' => null]);
        $this->bookingDetail($late, $this->fieldId);
        $this->bookingDetail($fresh, $this->fieldId);

        $this->assertStringContainsString('batas bayar', mb_strtolower($this->flagsOf($late)));
        $this->assertSame([], $this->monitor->flagsFor($this->rowFor($fresh)));
    }

    public function test_hostile_filter_values_fall_back_to_defaults(): void
    {
        $f = $this->monitor->filters([
            'period' => "'; DROP TABLE users;--", 'status' => 'x', 'method' => 'zzz', 'sort' => 'x', 'flagged' => 1, 'search' => str_repeat('a', 500),
        ]);

        $this->assertSame('30', $f['period']);
        $this->assertSame('', $f['status']);
        $this->assertSame('', $f['method']);
        $this->assertSame('terbaru', $f['sort']);
        $this->assertSame(100, mb_strlen($f['search']));
        $this->assertTrue(DB::getSchemaBuilder()->hasTable('users'));
    }

    public function test_invalid_custom_range_reports_an_error_instead_of_widening_the_query(): void
    {
        $f = $this->monitor->filters(['period' => 'kustom', 'from' => '2026-12-01', 'to' => '2026-11-01']);

        $this->assertNotNull($this->monitor->range($f)[2]);
    }

    public function test_search_finds_a_booking_by_code_and_by_number(): void
    {
        $customer = $this->makeUser();
        $id = $this->paidBooking($customer, $this->fieldId, 100000, ['kode_booking' => 'SP-ABC234']);
        $this->paidBooking($customer, $this->fieldId);

        $byCode = $this->monitor->rows($this->monitor->filters(['period' => 'semua', 'search' => 'sp-abc234']), 50);
        $byId = $this->monitor->rows($this->monitor->filters(['period' => 'semua', 'search' => (string) $id]), 50);

        $this->assertSame(1, $byCode->total());
        $this->assertSame($id, (int) $byId->first()->id);
    }

    public function test_summary_matches_the_list_for_the_same_filters(): void
    {
        $customer = $this->makeUser();
        $this->paidBooking($customer, $this->fieldId, 100000);
        $this->paidBooking($customer, $this->fieldId, 50000);
        $f = $this->monitor->filters(['period' => 'semua']);

        $summary = $this->monitor->summary($f);

        $this->assertSame($this->monitor->rows($f, 50)->total(), $summary['count']);
        $this->assertEqualsWithDelta(150000, $summary['paid'], 0.01);
    }
}