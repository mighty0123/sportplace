<?php

namespace Tests\Feature\Penyewa;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesFixtures;
use Tests\TestCase;

class PaymentSecurityTest extends TestCase
{
    use RefreshDatabase, CreatesFixtures;

    private ?array $cityAndSport = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function bookableField(array $field = [], string $mitraAccount = 'aktif'): int
    {
        [$city, $sport] = $this->cityAndSport ??= $this->cityAndSport();
        $owner = $this->makeUser('mitra', ['status_akun' => $mitraAccount]);
        $mitra = $this->mitraProfile($owner, ['status' => 'approved']);
        $venue = $this->venue($mitra, $city, ['status_vanue' => 'aktif']);

        return $this->field($venue, $sport, array_merge(['status_lapangan' => 'aktif', 'harga_perjam' => 150000], $field));
    }

    private function pay(\App\Models\User $user, int $fieldId, array $extra = [])
    {
        return $this->actingAs($user)->postJson(route('penyewa.pembayaran.process', $fieldId), array_merge([
            'field_id' => $fieldId, 'tanggal_sewa' => now()->addDays(3)->format('Y-m-d'),
            'slots' => json_encode([['start' => '19:00', 'end' => '20:00']]),
        ], $extra));
    }

    public function test_visiting_the_detail_page_with_paid_query_never_marks_a_booking_paid(): void
    {
        $customer = $this->makeUser();
        $id = $this->booking($customer, ['total_tagihan' => 152500]);
        $this->bookingDetail($id, $this->bookableField(), ['harga_slot' => 150000]);
        $this->payment($id, ['status_pembayaran' => 'pending', 'paid_at' => null]);

        $this->actingAs($customer)->get(route('penyewa.booking.detail', ['id' => $id, 'paid' => 1]));

        $this->assertDatabaseHas('booking_headers', ['id' => $id, 'status' => 'pending']);
        $this->assertDatabaseMissing('payments', ['header_id' => $id, 'status_pembayaran' => 'succes']);
    }

    public function test_other_customers_cannot_view_poll_download_or_cancel_a_booking(): void
    {
        $owner = $this->makeUser();
        $intruder = $this->makeUser();
        $id = $this->paidBooking($owner, $this->bookableField(), 150000);

        $this->actingAs($intruder)->get(route('penyewa.booking.detail', $id))->assertNotFound();
        $this->actingAs($intruder)->getJson(route('penyewa.booking.check-status', $id))->assertNotFound();
        $this->actingAs($intruder)->get(route('penyewa.booking.invoice', $id))->assertNotFound();
        $this->actingAs($intruder)->postJson(route('penyewa.booking.cancel', $id))->assertForbidden();

        $this->assertDatabaseHas('booking_headers', ['id' => $id, 'status' => 'payment']);
    }

    public function test_guests_are_never_treated_as_the_first_customer(): void
    {
        $this->makeUser();
        $fieldId = $this->bookableField();

        $this->postJson(route('penyewa.pembayaran.process', $fieldId), ['slots' => '19:00-20:00'])->assertUnauthorized();

        $this->assertDatabaseCount('booking_headers', 0);
    }

    public function test_client_supplied_prices_and_totals_are_ignored(): void
    {
        $customer = $this->makeUser();
        $fieldId = $this->bookableField(['harga_perjam' => 150000]);

        $this->pay($customer, $fieldId, [
            'slots' => json_encode([['start' => '19:00', 'end' => '20:00', 'price' => 1000]]),
            'total_bayar' => 1000,
        ])->assertOk();

        $this->assertDatabaseHas('booking_details', ['field_id' => $fieldId, 'harga_slot' => 150000]);
        $this->assertDatabaseHas('booking_headers', ['penyewa_id' => $customer->id, 'total_tagihan' => 152500]);
    }

    public function test_fields_that_are_not_active_or_whose_owner_is_sanctioned_cannot_be_booked(): void
    {
        $customer = $this->makeUser();

        $pending = $this->bookableField(['status_lapangan' => 'pending_review']);
        $sanctioned = $this->bookableField([], 'suspend');

        $this->pay($customer, $pending)->assertNotFound();
        $this->pay($customer, $sanctioned)->assertNotFound();

        $this->assertDatabaseCount('booking_headers', 0);
    }
}