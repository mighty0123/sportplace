<?php

namespace Tests\Unit;

use App\Models\BookingDetail;
use App\Services\Mitra\CommissionResolver;
use PHPUnit\Framework\TestCase;

class CommissionResolverTest extends TestCase
{
    /**
     * Test T9.2: Default commission rate is 0.0 when unconfigured.
     */
    public function test_default_commission_rate(): void
    {
        $resolver = new CommissionResolver(0.0, false);
        $this->assertEquals(0.0, $resolver->resolveRate());
        $this->assertFalse($resolver->isConfigured());
    }

    /**
     * Test AC-FR-REPORT-002-01 / T9.3: Net revenue = Gross revenue - Commission amount.
     */
    public function test_net_revenue_calculation_with_custom_rate(): void
    {
        $resolver = new CommissionResolver(10.0, true);
        $gross = 150000.0;
        $result = $resolver->calculate($gross);

        // Komisi 10% dari 150.000 = 15.000
        $this->assertEquals(15000.0, $result['commission']);

        // AC: REPORT-002-01: Bersih = Kotor - Komisi = 135.000
        $this->assertEquals(135000.0, $result['net']);
        $this->assertEquals($gross - $result['commission'], $result['net']);
        $this->assertTrue($result['is_configured']);
    }

    /**
     * Test AC-FR-REPORT-002-01: When commission is 0%, Net revenue equals Gross revenue.
     */
    public function test_net_revenue_with_zero_commission(): void
    {
        $resolver = new CommissionResolver(0.0, false);
        $gross = 200000.0;
        $result = $resolver->calculate($gross);

        $this->assertEquals(0.0, $result['commission']);
        $this->assertEquals($gross, $result['net']);
        $this->assertFalse($result['is_configured']);
    }

    /**
     * Test rate clamping prevents negative or >100% values.
     */
    public function test_rate_is_clamped_safely(): void
    {
        $resolverNegative = new CommissionResolver(-5.0);
        $this->assertEquals(0.0, $resolverNegative->resolveRate());

        $resolverHigh = new CommissionResolver(150.0);
        $this->assertEquals(100.0, $resolverHigh->resolveRate());
    }

    /**
     * Test calculation directly from a BookingDetail model.
     */
    public function test_calculate_for_booking_detail(): void
    {
        $resolver = new CommissionResolver(5.0, true);
        $detail = new BookingDetail();
        $detail->setRawAttributes(['harga_slot' => 100000.0]);

        $calc = $resolver->calculateForDetail($detail);

        $this->assertEquals(100000.0, $calc['gross']);
        $this->assertEquals(5000.0, $calc['commission']);
        $this->assertEquals(95000.0, $calc['net']);
    }
}
