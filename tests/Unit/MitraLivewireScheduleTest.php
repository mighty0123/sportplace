<?php

namespace Tests\Unit;

use App\Livewire\Mitra\Schedules\ScheduleManager;
use App\Models\Field;
use App\Models\MitraProfile;
use App\Models\User;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class MitraLivewireScheduleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $app = require dirname(__DIR__, 2) . '/bootstrap/app.php';
        $app->make(Kernel::class)->bootstrap();
    }

    private function createMockManager(): ScheduleManager
    {
        $profile = new MitraProfile();
        $profile->id = 10;
        $profile->status = 'approved';

        $user = new User();
        $user->id = 10;
        $user->role = 'mitra';
        $user->setRelation('mitraProfile', $profile);

        $venue1 = new Venue();
        $venue1->id = 1;
        $venue1->mitra_id = 10;
        $venue1->nama_venue = 'Arena Satu';
        $venue1->jam_buka = '08:00:00';
        $venue1->jam_tutup = '22:00:00';

        $venue2 = new Venue();
        $venue2->id = 2;
        $venue2->mitra_id = 10;
        $venue2->nama_venue = 'Arena Dua';
        $venue2->jam_buka = '07:00:00';
        $venue2->jam_tutup = '23:00:00';

        $field1 = new Field(['nama_lapangan' => 'Lapangan A', 'venue_id' => 1]);
        $field1->id = 101;
        $field1->setRelation('venue', $venue1);
        $field1->setRelation('schedules', collect());

        $field2 = new Field(['nama_lapangan' => 'Lapangan B', 'venue_id' => 2]);
        $field2->id = 102;
        $field2->setRelation('venue', $venue2);
        $field2->setRelation('schedules', collect());

        $venue1->setRelation('fields', collect([$field1]));
        $venue2->setRelation('fields', collect([$field2]));

        $manager = new class extends ScheduleManager {
            public ?User $currentUser = null;
            public Collection $mockVenues;
            public array $dispatchedEvents = [];

            public function venues(): Collection
            {
                return $this->mockVenues;
            }

            public function selectedField(): ?Field
            {
                return $this->allFields()->firstWhere('id', $this->fieldId);
            }

            public function dispatch($event, ...$params): void
            {
                $this->dispatchedEvents[] = ['event' => $event, 'params' => $params];
            }

            public function resetValidation($field = null)
            {
                // No-op in isolated unit test
            }
        };

        $manager->currentUser = $user;
        $manager->mockVenues = collect([$venue1, $venue2]);
        $manager->fieldId = 101;
        $manager->venueId = 1;

        return $manager;
    }

    public function test_copy_monday_to_all_days_copies_configuration(): void
    {
        $manager = $this->createMockManager();

        $manager->schedules = [
            1 => ['hari' => 1, 'nama_hari' => 'Senin', 'is_open' => true, 'jam_buka' => '09:00', 'jam_tutup' => '21:00', 'durasi' => '01:30'],
            2 => ['hari' => 2, 'nama_hari' => 'Selasa', 'is_open' => false, 'jam_buka' => '08:00', 'jam_tutup' => '22:00', 'durasi' => '01:00'],
            3 => ['hari' => 3, 'nama_hari' => 'Rabu', 'is_open' => true, 'jam_buka' => '08:00', 'jam_tutup' => '22:00', 'durasi' => '01:00'],
            4 => ['hari' => 4, 'nama_hari' => 'Kamis', 'is_open' => true, 'jam_buka' => '08:00', 'jam_tutup' => '22:00', 'durasi' => '01:00'],
            5 => ['hari' => 5, 'nama_hari' => 'Jumat', 'is_open' => true, 'jam_buka' => '08:00', 'jam_tutup' => '22:00', 'durasi' => '01:00'],
            6 => ['hari' => 6, 'nama_hari' => 'Sabtu', 'is_open' => true, 'jam_buka' => '08:00', 'jam_tutup' => '22:00', 'durasi' => '01:00'],
            7 => ['hari' => 7, 'nama_hari' => 'Minggu', 'is_open' => true, 'jam_buka' => '08:00', 'jam_tutup' => '22:00', 'durasi' => '01:00'],
        ];

        $manager->copyMondayToAll();

        for ($day = 2; $day <= 7; $day++) {
            $this->assertTrue($manager->schedules[$day]['is_open']);
            $this->assertEquals('09:00', $manager->schedules[$day]['jam_buka']);
            $this->assertEquals('21:00', $manager->schedules[$day]['jam_tutup']);
            $this->assertEquals('01:30', $manager->schedules[$day]['durasi']);
        }

        $this->assertCount(1, $manager->dispatchedEvents);
        $this->assertEquals('notify', $manager->dispatchedEvents[0]['event']);
    }

    public function test_date_navigation_previous_next_and_today(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-11 10:00:00'));

        $manager = $this->createMockManager();
        $manager->selectedDate = '2026-11-11';

        $manager->nextDate();
        $this->assertEquals('2026-11-12', $manager->selectedDate);

        $manager->previousDate();
        $manager->previousDate();
        $this->assertEquals('2026-11-10', $manager->selectedDate);

        $manager->todayDate();
        $this->assertEquals('2026-11-11', $manager->selectedDate);

        Carbon::setTestNow();
    }

    public function test_dual_dropdown_venue_and_field_reactivity(): void
    {
        $manager = $this->createMockManager();

        // Ganti ke venue 2 -> fieldId harus otomatis berubah ke 102
        $manager->updatedVenueId(2);
        $this->assertEquals(2, $manager->venueId);
        $this->assertEquals(102, $manager->fieldId);

        // Ganti field ke 101 -> venueId harus otomatis sinkron kembali ke 1
        $manager->updatedFieldId(101);
        $this->assertEquals(101, $manager->fieldId);
        $this->assertEquals(1, $manager->venueId);
    }

    public function test_save_schedules_rejects_hours_outside_venue_hours(): void
    {
        $manager = $this->createMockManager();

        // Venue 1 buka 08:00 s/d 22:00. Jika hari Senin diatur 06:00 s/d 23:00 -> harus ditolak (KS-14)
        $manager->schedules = [
            1 => ['hari' => 1, 'nama_hari' => 'Senin', 'is_open' => true, 'jam_buka' => '06:00', 'jam_tutup' => '23:00', 'durasi' => '01:00'],
        ];

        $manager->saveSchedules();

        $errors = $manager->getErrorBag()->getMessages();
        $this->assertArrayHasKey('schedules.1.jam_buka', $errors);
        $this->assertEquals('error', $manager->dispatchedEvents[0]['params'][0]['type']);
    }
}
