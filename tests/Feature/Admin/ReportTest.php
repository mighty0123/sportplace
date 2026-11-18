<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\Concerns\CreatesFixtures;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase, CreatesFixtures;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function request(array $data = [])
    {
        return $this->actingAs($this->makeUser('admin'))->post(route('admin.laporan.download'), $data + ['type' => 'pengguna', 'format' => 'xlsx', 'period' => 'semua']);
    }

    public function test_only_admins_can_export(): void
    {
        $this->actingAs($this->makeUser('customer'))->post(route('admin.laporan.download'), ['type' => 'pengguna', 'format' => 'xlsx'])->assertForbidden();
    }

    public function test_every_export_is_recorded_in_the_audit_log(): void
    {
        $response = $this->request();
        $response->assertOk();
        $response->streamedContent(); // menjalankan pembuatan berkas

        $this->assertDatabaseHas('audit_logs', ['entitas_type' => 'Laporan', 'aksi' => 'ekspor']);
    }

    public function test_excel_cells_are_plain_text_never_formulas(): void
    {
        $admin = $this->makeUser('admin');
        $this->makeUser('customer', ['name' => '=1+1']);

        $response = $this->actingAs($admin)->post(route('admin.laporan.download'), ['type' => 'pengguna', 'format' => 'xlsx', 'period' => 'semua']);
        $path = tempnam(sys_get_temp_dir(), 'xl');
        file_put_contents($path, $response->streamedContent());

        $sheet = IOFactory::load($path)->getSheetByName('Data');
        @unlink($path);

        // Urutan: terlama dulu, jadi admin di baris 2 dan pengguna uji di baris 3.
        $this->assertSame('=1+1', $sheet->getCell('A3')->getValue());
        $this->assertSame(DataType::TYPE_STRING, $sheet->getCell('A3')->getDataType());
    }

    public function test_export_above_the_row_limit_is_refused(): void
    {
        config(['reports.max_rows.xlsx' => 1]);
        $this->makeUser('customer');
        $this->makeUser('customer');

        $this->request()->assertSessionHas('error');
        $this->assertDatabaseMissing('audit_logs', ['aksi' => 'ekspor']);
    }

    public function test_invalid_date_range_is_refused(): void
    {
        $this->request(['period' => 'kustom', 'from' => '2026-12-01', 'to' => '2026-11-01'])->assertSessionHasErrors('to');
    }

    public function test_report_with_no_data_is_refused(): void
    {
        $this->request(['type' => 'transaksi', 'period' => 'bulan-ini'])->assertSessionHas('error');
    }

    public function test_sensitive_columns_are_never_part_of_the_user_report(): void
    {
        $columns = collect(app(\App\Services\Admin\Reports\ReportService::class)
            ->data('pengguna', app(\App\Services\Admin\Reports\ReportService::class)->normalize('pengguna', []))->columns)->pluck('key');

        $this->assertFalse($columns->contains('no_hp'));
        $this->assertFalse($columns->contains('password'));
    }
}