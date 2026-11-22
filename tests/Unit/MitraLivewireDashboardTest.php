<?php

namespace Tests\Unit;

use App\Livewire\Mitra\Dashboard\DashboardOverview;
use App\Models\Field;
use App\Models\MitraProfile;
use App\Models\User;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class MitraLivewireDashboardTest extends TestCase
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

    private function createMockDashboard(): DashboardOverview
    {
        $profile = new MitraProfile;
        $profile->id = 10;
        $profile->status = 'approved';

        $user = new User;
        $user->id = 10;
        $user->role = 'mitra';
        $user->setRelation('mitraProfile', $profile);
        $user->setRelation('wallet', null);

        $venue = new Venue;
        $venue->id = 1;
        $venue->mitra_id = 10;
        $venue->nama_venue = 'Arena Champion';
        $venue->status_vanue = 'aktif';

        $field = new Field([
            'nama_lapangan' => 'Lapangan A',
            'venue_id' => 1,
            'harga_perjam' => 150000,
        ]);
        $field->id = 101;
        $field->status_lapangan = 'aktif';
        $field->setRelation('venue', $venue);

        $venue->setRelation('fields', collect([$field]));

        $dashboard = new class extends DashboardOverview
        {
            public ?User $currentUser = null;

            public Collection $mockVenues;

            public Collection $mockFields;

            public Collection $mockTodayBookings;

            public array $dispatchedEvents = [];

            public function profile(): MitraProfile
            {
                return $this->currentUser->mitraProfile;
            }

            public function venues(): Collection
            {
                return $this->mockVenues;
            }

            public function fields(): Collection
            {
                return $this->mockFields;
            }

            public function todayBookings(): Collection
            {
                return $this->mockTodayBookings;
            }

            public function calculateTodayRevenue(Collection $fieldIds, string $today): float
            {
                return 0.0;
            }

            public function calculateTotalRevenue(Collection $fieldIds): float
            {
                return 0.0;
            }

            public function countPendingBookings(Collection $fieldIds): int
            {
                return 0;
            }

            public function countReadyToCompleteBookings(Collection $fieldIds, string $today, string $nowTime): int
            {
                return 0;
            }

            public function dispatch($event, ...$params): void
            {
                $this->dispatchedEvents[] = ['event' => $event, 'params' => $params];
            }
        };

        $dashboard->currentUser = $user;
        $dashboard->mockVenues = collect([$venue]);
        $dashboard->mockFields = collect([$field]);
        $dashboard->mockTodayBookings = collect();

        return $dashboard;
    }

    public function test_dashboard_metrics_with_empty_state(): void
    {
        $dashboard = $this->createMockDashboard();
        $metrics = $dashboard->metrics;

        $this->assertEquals(1, $metrics['venues_count']);
        $this->assertEquals(1, $metrics['fields_count']);
        $this->assertEquals(0, $metrics['today_bookings_count']);
        $this->assertEquals(0.0, $metrics['today_revenue']);
        $this->assertEquals(0.0, $metrics['total_revenue']);
        $this->assertEquals(0.0, $metrics['saldo_aktif']);
    }

    public function test_action_shortcuts_computation_with_clean_state(): void
    {
        $dashboard = $this->createMockDashboard();
        $shortcuts = $dashboard->actionShortcuts;

        $this->assertEquals(0, $shortcuts['pending_bookings_count']);
        $this->assertEquals(0, $shortcuts['ready_to_complete_count']);
        $this->assertCount(0, $shortcuts['rejected_venues']);
        $this->assertCount(0, $shortcuts['rejected_fields']);
    }

    public function test_action_shortcuts_detects_rejected_facilities(): void
    {
        $dashboard = $this->createMockDashboard();

        $rejectedVenue = new Venue;
        $rejectedVenue->id = 2;
        $rejectedVenue->mitra_id = 10;
        $rejectedVenue->nama_venue = 'Venue Bermasalah';
        $rejectedVenue->status_vanue = 'rejected';
        $rejectedVenue->alasan_penolakan = 'Foto KTP buram';

        $rejectedField = new Field;
        $rejectedField->id = 102;
        $rejectedField->nama_lapangan = 'Lapangan Ditolak';
        $rejectedField->status_lapangan = 'rejected';
        $rejectedField->alasan_penolakan = 'Jadwal belum diisi';

        $dashboard->mockVenues = collect([$dashboard->mockVenues->first(), $rejectedVenue]);
        $dashboard->mockFields = collect([$dashboard->mockFields->first(), $rejectedField]);

        $shortcuts = $dashboard->actionShortcuts;

        $this->assertCount(1, $shortcuts['rejected_venues']);
        $this->assertEquals('Venue Bermasalah', $shortcuts['rejected_venues']->first()->nama_venue);

        $this->assertCount(1, $shortcuts['rejected_fields']);
        $this->assertEquals('Lapangan Ditolak', $shortcuts['rejected_fields']->first()->nama_lapangan);
    }

    public function test_complete_order_confirmation_modal_lifecycle(): void
    {
        $dashboard = $this->createMockDashboard();
        $this->assertFalse($dashboard->showCompleteConfirmModal);
        $this->assertNull($dashboard->completingOrderId);

        $dashboard->confirmCompleteOrder(99);
        $this->assertTrue($dashboard->showCompleteConfirmModal);
        $this->assertEquals(99, $dashboard->completingOrderId);

        $dashboard->cancelCompleteOrder();
        $this->assertFalse($dashboard->showCompleteConfirmModal);
        $this->assertNull($dashboard->completingOrderId);
    }
}
