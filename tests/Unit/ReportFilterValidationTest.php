<?php

namespace Tests\Unit;

use App\Http\Requests\Mitra\ReportFilterRequest;
use PHPUnit\Framework\TestCase;

class ReportFilterValidationTest extends TestCase
{
    /**
     * Test ReportFilterRequest rules array structure.
     */
    public function test_report_filter_rules_presence(): void
    {
        $request = new ReportFilterRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('period_mode', $rules);
        $this->assertArrayHasKey('month', $rules);
        $this->assertArrayHasKey('start_date', $rules);
        $this->assertArrayHasKey('end_date', $rules);
        $this->assertArrayHasKey('field_id', $rules);

        $this->assertContains('after_or_equal:start_date', $rules['end_date']);
    }

    /**
     * Test regex pattern for YYYY-MM month format.
     */
    public function test_month_regex_pattern(): void
    {
        $request = new ReportFilterRequest();
        $monthRules = $request->rules()['month'];

        $regexRule = null;
        foreach ($monthRules as $rule) {
            if (is_string($rule) && str_starts_with($rule, 'regex:')) {
                $regexRule = substr($rule, 6);
                break;
            }
        }

        $this->assertNotNull($regexRule);
        $this->assertEquals(1, preg_match($regexRule, '2026-11'));
        $this->assertEquals(1, preg_match($regexRule, '2025-01'));
        $this->assertEquals(0, preg_match($regexRule, '2026-1'));
        $this->assertEquals(0, preg_match($regexRule, 'invalid-date'));
    }
}
