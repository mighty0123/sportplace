<?php

namespace Tests\Unit;

use App\Livewire\Mitra\Reports\ReportManager;
use App\Models\Field;
use App\Models\MitraProfile;
use App\Models\User;
use App\Models\Venue;
use App\Services\Mitra\CommissionResolver;
use Carbon\Carbon;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MitraLivewireReportTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $app = require dirname(__DIR__, 2).'/bootstrap/app.php';
        $app->make(Kernel::class)->bootstrap();
        Carbon::setTestNow('2026-11-17 14:00:00');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function createMockReportManager(): ReportManager
    {
        $profile = new MitraProfile;
        $profile->id = 10;
        $profile->status = 'approved';

        $user = new User;
        $user->id = 10;
        $user->role = 'mitra';
        $user->setRelation('mitraProfile', $profile);

        $venue1 = new Venue;
        $venue1->id = 1;
        $venue1->mitra_id = 10;
        $venue1->nama_venue = 'Arena Futsal A';

        $venue2 = new Venue;
        $venue2->id = 2;
        $venue2->mitra_id = 10;
        $venue2->nama_venue = 'Arena Badminton B';

        $field1 = new Field([
            'nama_lapangan' => 'Lapangan Rumput',
            'venue_id' => 1,
            'harga_perjam' => 100000,
        ]);
        $field1->id = 101;
        $field1->setRelation('venue', $venue1);

        $field2 = new Field([
            'nama_lapangan' => 'Lapangan Karpet',
            'venue_id' => 2,
            'harga_perjam' => 80000,
        ]);
        $field2->id = 102;
        $field2->setRelation('venue', $venue2);

        $venue1->setRelation('fields', collect([$field1]));
        $venue2->setRelation('fields', collect([$field2]));

        $manager = new class extends ReportManager
        {
            public ?User $currentUser = null;

            public Collection $mockVenues;

            public array $dispatchedEvents = [];

            public function venues(): Collection
            {
                return $this->mockVenues;
            }

            public function allFields(): Collection
            {
                return $this->mockVenues->flatMap->fields;
            }

            public function dispatch($event, ...$params): void
            {
                $this->dispatchedEvents[] = ['event' => $event, 'params' => $params];
            }

            public function resetPage($pageName = 'page'): void
            {
                // No-op for unit test
            }
        };

        $manager->currentUser = $user;
        $manager->mockVenues = collect([$venue1, $venue2]);

        return $manager;
    }

    public function test_initial_state_defaults_to_current_month(): void
    {
        $manager = $this->createMockReportManager();
        $manager->mount();

        $this->assertEquals('month', $manager->periodMode);
        $this->assertEquals('completed', $manager->statusFilter);
        $this->assertEquals('2026-11', $manager->month);
        $this->assertEquals('2026-11-01', $manager->startDate);
        $this->assertEquals('2026-11-30', $manager->endDate);
        $this->assertNull($manager->fieldId);
        $this->assertNull($manager->venueId);
    }

    public function test_period_mode_toggle_and_resolved_date_range(): void
    {
        $manager = $this->createMockReportManager();
        $manager->mount();

        // Month mode
        $manager->month = '2026-08';
        [$startMonth, $endMonth] = $manager->resolvedDateRange();
        $this->assertEquals('2026-08-01', $startMonth->format('Y-m-d'));
        $this->assertEquals('2026-08-31', $endMonth->format('Y-m-d'));

        // Switch to custom range mode
        $manager->setPeriodMode('range');
        $manager->startDate = '2026-08-10';
        $manager->endDate = '2026-08-25';

        [$startRange, $endRange] = $manager->resolvedDateRange();
        $this->assertEquals('2026-08-10', $startRange->format('Y-m-d'));
        $this->assertEquals('2026-08-25', $endRange->format('Y-m-d'));
    }

    public function test_month_update_adjusts_start_and_end_date(): void
    {
        $manager = $this->createMockReportManager();
        $manager->mount();

        $manager->month = '2026-12';
        $manager->updatedMonth();

        $this->assertEquals('2026-12-01', $manager->startDate);
        $this->assertEquals('2026-12-31', $manager->endDate);
    }

    public function test_reset_filters(): void
    {
        $manager = $this->createMockReportManager();
        $manager->mount();

        $manager->periodMode = 'range';
        $manager->startDate = '2026-01-01';
        $manager->endDate = '2026-01-10';
        $manager->fieldId = 101;
        $manager->venueId = 1;
        $manager->statusFilter = 'all_paid';

        $manager->resetFilters();

        $this->assertEquals('month', $manager->periodMode);
        $this->assertEquals('2026-11', $manager->month);
        $this->assertEquals('2026-11-01', $manager->startDate);
        $this->assertEquals('2026-11-30', $manager->endDate);
        $this->assertNull($manager->fieldId);
        $this->assertNull($manager->venueId);
        $this->assertEquals('completed', $manager->statusFilter);
    }

    public function test_venue_change_clears_mismatched_field_id(): void
    {
        $manager = $this->createMockReportManager();
        $manager->mount();

        $manager->venueId = 1;
        $manager->fieldId = 101; // belongs to venue 1

        // Switch to venue 2 -> field 101 doesn't belong to venue 2 -> reset to null
        $manager->venueId = 2;
        $manager->updatedVenueId();

        $this->assertNull($manager->fieldId);
    }

    public function test_export_csv_returns_streamed_response(): void
    {
        $manager = $this->createMockReportManager();
        $manager->mount();

        $resolver = new CommissionResolver(10.0, true);

        // We can test that exportCsv returns a StreamedResponse with proper headers
        $response = $manager->exportCsv($resolver);

        $this->assertInstanceOf(StreamedResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('text/csv', (string) $response->headers->get('Content-Type'));
        $this->assertStringContainsString('laporan-pendapatan-mitra-2026-11.csv', (string) $response->headers->get('Content-Disposition'));
    }
}
