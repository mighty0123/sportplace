<?php

namespace Tests\Unit;

use App\Livewire\Mitra\Orders\OrderManager;
use App\Models\Field;
use App\Models\MitraProfile;
use App\Models\User;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class MitraLivewireOrderTest extends TestCase
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

    private function createMockOrderManager(): OrderManager
    {
        $profile = new MitraProfile;
        $profile->id = 10;
        $profile->status = 'approved';

        $user = new User;
        $user->id = 10;
        $user->role = 'mitra';
        $user->setRelation('mitraProfile', $profile);

        $venue = new Venue;
        $venue->id = 1;
        $venue->mitra_id = 10;
        $venue->nama_venue = 'Arena Futsal Kita';

        $field1 = new Field([
            'nama_lapangan' => 'Lapangan Rumput A',
            'venue_id' => 1,
            'harga_perjam' => 100000,
        ]);
        $field1->id = 101;
        $field1->setRelation('venue', $venue);

        $field2 = new Field([
            'nama_lapangan' => 'Lapangan Sintetis B',
            'venue_id' => 1,
            'harga_perjam' => 120000,
        ]);
        $field2->id = 102;
        $field2->setRelation('venue', $venue);

        $venue->setRelation('fields', collect([$field1, $field2]));

        $manager = new class extends OrderManager {
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
        $manager->mockVenues = collect([$venue]);

        return $manager;
    }

    public function test_initial_state_and_calendar_dates(): void
    {
        $manager = $this->createMockOrderManager();

        $this->assertEquals('table', $manager->viewMode);
        $this->assertEquals('', $manager->search);
        $this->assertNull($manager->fieldId);
        $this->assertNull($manager->venueId);
        $this->assertEquals('', $manager->status);
        $this->assertEquals('', $manager->paymentStatus);
        $this->assertEmpty($manager->selectedOrderIds);
        $this->assertFalse($manager->selectAll);
        $this->assertFalse($manager->showDetailModal);
        $this->assertFalse($manager->showBulkConfirmModal);

        // When date is empty, defaults to current test now
        $this->assertEquals('2026-11-17', $manager->currentCalendarDate()->format('Y-m-d'));

        // When date is set
        $manager->date = '2026-11-20';
        $this->assertEquals('2026-11-20', $manager->currentCalendarDate()->format('Y-m-d'));
    }

    public function test_calendar_date_navigation(): void
    {
        $manager = $this->createMockOrderManager();
        $manager->date = '2026-11-17';

        $manager->nextDay();
        $this->assertEquals('2026-11-18', $manager->date);

        $manager->prevDay();
        $this->assertEquals('2026-11-17', $manager->date);

        $manager->date = '2026-12-01';
        $manager->goToToday();
        $this->assertEquals('2026-11-17', $manager->date);
    }

    public function test_filter_resets_and_toggle_ready_to_complete(): void
    {
        $manager = $this->createMockOrderManager();
        $manager->search = 'Budi';
        $manager->fieldId = 101;
        $manager->venueId = 1;
        $manager->status = 'ongoing';
        $manager->paymentStatus = 'payment';
        $manager->date = '2026-11-17';
        $manager->filter = 'ready_to_complete';
        $manager->selectedOrderIds = [1, 2];

        $manager->resetFilters();

        $this->assertEquals('', $manager->search);
        $this->assertNull($manager->fieldId);
        $this->assertNull($manager->venueId);
        $this->assertEquals('', $manager->status);
        $this->assertEquals('', $manager->paymentStatus);
        $this->assertEquals('', $manager->date);
        $this->assertEquals('', $manager->filter);
        $this->assertEmpty($manager->selectedOrderIds);

        // Toggle ready to complete
        $manager->toggleReadyToCompleteFilter();
        $this->assertEquals('ready_to_complete', $manager->filter);

        $manager->toggleReadyToCompleteFilter();
        $this->assertEquals('', $manager->filter);
    }

    public function test_view_mode_toggle(): void
    {
        $manager = $this->createMockOrderManager();

        $manager->setViewMode('calendar');
        $this->assertEquals('calendar', $manager->viewMode);

        $manager->setViewMode('table');
        $this->assertEquals('table', $manager->viewMode);

        // Invalid mode does not change viewMode
        $manager->setViewMode('invalid_mode');
        $this->assertEquals('table', $manager->viewMode);
    }

    public function test_bulk_selection_helpers(): void
    {
        $manager = $this->createMockOrderManager();

        $manager->selectAllOnPage([10, 20, 30]);
        $this->assertEquals([10, 20, 30], $manager->selectedOrderIds);
        $this->assertTrue($manager->selectAll);

        // Adding more merges uniquely
        $manager->selectAllOnPage([20, 40]);
        $this->assertEquals([10, 20, 30, 40], $manager->selectedOrderIds);

        // Open bulk modal without selection
        $manager->resetBulkSelection();
        $this->assertEmpty($manager->selectedOrderIds);
        $manager->openBulkModal();
        $this->assertFalse($manager->showBulkConfirmModal);
        $this->assertCount(1, $manager->dispatchedEvents);
        $this->assertEquals('error', $manager->dispatchedEvents[0]['params'][0]['type']);

        // Open bulk modal with selection
        $manager->selectedOrderIds = [1, 2];
        $manager->openBulkModal();
        $this->assertTrue($manager->showBulkConfirmModal);

        $manager->closeBulkModal();
        $this->assertFalse($manager->showBulkConfirmModal);
    }

    public function test_bulk_complete_with_empty_selection_dispatches_error(): void
    {
        $manager = $this->createMockOrderManager();
        $manager->selectedOrderIds = [];

        $manager->bulkComplete();

        $this->assertFalse($manager->showBulkConfirmModal);
        $this->assertCount(1, $manager->dispatchedEvents);
        $this->assertEquals('error', $manager->dispatchedEvents[0]['params'][0]['type']);
    }

    public function test_detail_modal_close_resets_state(): void
    {
        $manager = $this->createMockOrderManager();
        $manager->showDetailModal = true;
        $manager->viewingOrderId = 42;

        $manager->closeDetail();

        $this->assertFalse($manager->showDetailModal);
        $this->assertNull($manager->viewingOrderId);
    }
}
