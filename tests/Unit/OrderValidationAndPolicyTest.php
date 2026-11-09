<?php

namespace Tests\Unit;

use App\Http\Requests\Mitra\BulkCompleteOrderRequest;
use App\Models\BookingDetail;
use App\Models\BookingHeader;
use App\Models\Field;
use App\Models\MitraProfile;
use App\Models\User;
use App\Models\Venue;
use App\Policies\BookingDetailPolicy;
use PHPUnit\Framework\TestCase;

class OrderValidationAndPolicyTest extends TestCase
{
    /**
     * Test AC-FR-ORDER-001-01 / BR-023: BookingDetailPolicy view only allows the owner mitra.
     */
    public function test_mitra_owner_can_view_own_booking(): void
    {
        $policy = new BookingDetailPolicy;

        $mitraUser = new User;
        $mitraUser->id = 10;
        $mitraUser->role = 'mitra';

        $mitraProfile = new MitraProfile;
        $mitraProfile->id = 5;
        $mitraUser->setRelation('mitraProfile', $mitraProfile);

        $venue = new Venue;
        $venue->mitra_id = 5;

        $field = new Field;
        $field->setRelation('venue', $venue);

        $detail = new BookingDetail;
        $detail->setRelation('field', $field);

        $this->assertTrue($policy->view($mitraUser, $detail));
    }

    /**
     * Test AC-FR-ORDER-001-01 / BR-023: Another mitra is rejected with 403 / false.
     */
    public function test_other_mitra_cannot_view_foreign_booking(): void
    {
        $policy = new BookingDetailPolicy;

        $mitraUserA = new User;
        $mitraUserA->id = 10;
        $mitraUserA->role = 'mitra';

        $profileA = new MitraProfile;
        $profileA->id = 5;
        $mitraUserA->setRelation('mitraProfile', $profileA);

        // Booking belongs to Mitra B (profile ID 99)
        $venueB = new Venue;
        $venueB->mitra_id = 99;

        $fieldB = new Field;
        $fieldB->setRelation('venue', $venueB);

        $detail = new BookingDetail;
        $detail->setRelation('field', $fieldB);

        $this->assertFalse($policy->view($mitraUserA, $detail));
    }

    /**
     * Test BR-023: Customer can view their own booking via header->penyewa_id.
     */
    public function test_customer_can_view_own_booking(): void
    {
        $policy = new BookingDetailPolicy;

        $customer = new User;
        $customer->id = 42;
        $customer->role = 'customer';

        $header = new BookingHeader;
        $header->penyewa_id = 42;

        $detail = new BookingDetail;
        $detail->setRelation('header', $header);

        $this->assertTrue($policy->view($customer, $detail));

        // Other customer cannot view
        $otherCustomer = new User;
        $otherCustomer->id = 99;
        $otherCustomer->role = 'customer';

        $this->assertFalse($policy->view($otherCustomer, $detail));
    }

    /**
     * Test BR-023: BookingDetailPolicy updateStatus allows owner mitra.
     */
    public function test_mitra_owner_can_update_status(): void
    {
        $policy = new BookingDetailPolicy;

        $mitraUser = new User;
        $mitraUser->role = 'mitra';

        $profile = new MitraProfile;
        $profile->id = 7;
        $mitraUser->setRelation('mitraProfile', $profile);

        $venue = new Venue;
        $venue->mitra_id = 7;

        $field = new Field;
        $field->setRelation('venue', $venue);

        $detail = new BookingDetail;
        $detail->setRelation('field', $field);

        $this->assertTrue($policy->updateStatus($mitraUser, $detail));
    }

    /**
     * Test BR-023: BookingDetailPolicy updateStatus rejects non-owner mitra.
     */
    public function test_other_mitra_cannot_update_status(): void
    {
        $policy = new BookingDetailPolicy;

        $mitraUser = new User;
        $mitraUser->role = 'mitra';

        $profile = new MitraProfile;
        $profile->id = 7;
        $mitraUser->setRelation('mitraProfile', $profile);

        $venue = new Venue;
        $venue->mitra_id = 88; // Different mitra

        $field = new Field;
        $field->setRelation('venue', $venue);

        $detail = new BookingDetail;
        $detail->setRelation('field', $field);

        $this->assertFalse($policy->updateStatus($mitraUser, $detail));
    }

    /**
     * Test BulkCompleteOrderRequest validation rules.
     */
    public function test_bulk_complete_request_rules(): void
    {
        $request = new BulkCompleteOrderRequest;
        $rules = $request->rules();

        $this->assertArrayHasKey('detail_ids', $rules);
        $this->assertContains('required', $rules['detail_ids']);
        $this->assertContains('array', $rules['detail_ids']);
        $this->assertContains('min:1', $rules['detail_ids']);
    }
}
