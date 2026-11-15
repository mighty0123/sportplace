<?php

namespace Tests\Unit;

use App\Services\Admin\AdminProfileService;
use App\Services\Admin\MitraDataChecks;
use App\Services\Admin\TransactionMonitor;
use App\Support\BookingCode;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class DomainHelpersTest extends TestCase
{
    public static function names(): array
    {
        return [
            'gelar dan badan usaha diabaikan' => ['H. Budi Santoso, S.Kom', 'Budi Santoso', 'exact'],
            'badan usaha diabaikan'           => ['CV Arena Bali', 'Arena Bali', 'exact'],
            'urutan kata tidak berpengaruh'   => ['Santoso Budi', 'Budi Santoso', 'exact'],
            'satu nama termuat di nama lain'  => ['Budi Santoso', 'Budi Santoso Putra', 'similar'],
            'nama berbeda'                    => ['Budi Santoso', 'Made Wirawan', 'different'],
            'kosong'                          => ['', 'Budi', 'different'],
        ];
    }

    #[DataProvider('names')]
    public function test_name_matching(string $a, string $b, string $expected): void
    {
        $this->assertSame($expected, (new MitraDataChecks())->nameMatch($a, $b));
    }

    public function test_booking_codes_are_well_formed_and_do_not_contain_confusable_characters(): void
    {
        $codes = [];
        for ($i = 0; $i < 300; $i++) {
            $code = BookingCode::make();
            $this->assertMatchesRegularExpression('/^SP-[A-HJKMNP-Z2-9]{6}$/', $code);
            $codes[$code] = true;
        }

        $this->assertGreaterThan(295, count($codes), 'Kode seharusnya hampir selalu berbeda.');
    }

    public function test_account_numbers_are_masked_except_the_last_four_digits(): void
    {
        $this->assertSame('BCA ••••••7890 a.n. Budi', TransactionMonitor::maskAccount('BCA 1234567890 a.n. Budi'));
        $this->assertSame('', TransactionMonitor::maskAccount(null));
    }

    public function test_legacy_bookings_fall_back_to_a_code_derived_from_their_number(): void
    {
        $this->assertSame('BK-000042', TransactionMonitor::code(42));
        $this->assertSame('SP-7K3M9Q', TransactionMonitor::code(42, 'SP-7K3M9Q'));
    }

    public function test_devices_are_described_without_exposing_the_full_user_agent(): void
    {
        $this->assertSame('Chrome di Windows', AdminProfileService::describeAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0 Safari/537.36'));
        $this->assertSame('Chrome di Android', AdminProfileService::describeAgent('Mozilla/5.0 (Linux; Android 14) AppleWebKit/537.36 Chrome/120.0 Mobile Safari/537.36'));
        $this->assertSame('Safari di iOS', AdminProfileService::describeAgent('Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 Version/17.0 Mobile Safari/604.1'));
    }
}