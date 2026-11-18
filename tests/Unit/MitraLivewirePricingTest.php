<?php

namespace Tests\Unit;

use App\Livewire\Mitra\Pricing\PricingManager;
use App\Models\Field;
use App\Models\MitraProfile;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class MitraLivewirePricingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $app = require dirname(__DIR__, 2) . '/bootstrap/app.php';
        $app->make(Kernel::class)->bootstrap();
    }

    private function createMockManager(): PricingManager
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

        $venue2 = new Venue();
        $venue2->id = 2;
        $venue2->mitra_id = 10;
        $venue2->nama_venue = 'Arena Dua';

        $field1 = new Field([
            'nama_lapangan' => 'Lapangan A',
            'venue_id' => 1,
            'harga_perjam' => 120000,
            'meta_harga' => ['tipe_skema' => 'flat'],
        ]);
        $field1->id = 101;
        $field1->setRelation('venue', $venue1);

        $field2 = new Field([
            'nama_lapangan' => 'Lapangan B',
            'venue_id' => 2,
            'harga_perjam' => 100000,
            'meta_harga' => [
                'tipe_skema' => 'peak_off_peak',
                'harga_off_peak' => 100000,
                'harga_peak' => 160000,
                'peak_rules' => [
                    ['hari' => [6, 7], 'jam_mulai' => '08:00', 'jam_selesai' => '23:00'],
                ],
            ],
        ]);
        $field2->id = 102;
        $field2->setRelation('venue', $venue2);

        $venue1->setRelation('fields', collect([$field1]));
        $venue2->setRelation('fields', collect([$field2]));

        $manager = new class extends PricingManager {
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

    public function test_live_pricing_simulation_computed_for_flat_and_dynamic_schemes(): void
    {
        $manager = $this->createMockManager();

        // Uji Flat
        $manager->tipe_skema = 'flat';
        $manager->harga_perjam = 120000;
        $simFlat = $manager->simulation();

        $this->assertEquals(120000, $simFlat['weekday_day']['price']);
        $this->assertEquals('Reguler', $simFlat['weekday_day']['badge']);
        $this->assertFalse($simFlat['weekend_night']['is_peak']);

        // Uji Peak/Off-Peak dengan preset combo (Weekday Malam 18:00-23:00 & Weekend 08:00-23:00)
        $manager->harga_off_peak = 100000;
        $manager->harga_peak = 150000;
        $manager->applyPreset('combo');

        $simCombo = $manager->simulation();

        // Senin 10:00 -> Off-Peak
        $this->assertEquals(100000, $simCombo['weekday_day']['price']);
        $this->assertEquals('Off-Peak', $simCombo['weekday_day']['badge']);
        $this->assertFalse($simCombo['weekday_day']['is_peak']);

        // Senin 19:00 -> Peak Hour
        $this->assertEquals(150000, $simCombo['weekday_night']['price']);
        $this->assertEquals('Peak Hour', $simCombo['weekday_night']['badge']);
        $this->assertTrue($simCombo['weekday_night']['is_peak']);

        // Sabtu 10:00 -> Peak Hour
        $this->assertEquals(150000, $simCombo['weekend_day']['price']);
        $this->assertTrue($simCombo['weekend_day']['is_peak']);

        // Sabtu 20:00 -> Peak Hour
        $this->assertEquals(150000, $simCombo['weekend_night']['price']);
        $this->assertTrue($simCombo['weekend_night']['is_peak']);
    }

    public function test_dynamic_peak_rules_repeater_and_day_toggle(): void
    {
        $manager = $this->createMockManager();
        $manager->peak_rules = [
            ['hari' => [6], 'jam_mulai' => '08:00', 'jam_selesai' => '22:00'],
        ];

        // Tidak boleh hapus jika tinggal 1 rule
        $manager->removePeakRule(0);
        $this->assertCount(1, $manager->peak_rules);
        $this->assertEquals('warning', $manager->dispatchedEvents[0]['params'][0]['type']);

        // Tambah rule ke-2
        $manager->addPeakRule();
        $this->assertCount(2, $manager->peak_rules);

        // Toggle hari Minggu (7) di rule pertama
        $manager->toggleRuleDay(0, 7);
        $this->assertContains(7, $manager->peak_rules[0]['hari']);

        // Toggle lagi -> terhapus
        $manager->toggleRuleDay(0, 7);
        $this->assertNotContains(7, $manager->peak_rules[0]['hari']);

        // Hapus rule ke-2
        $manager->removePeakRule(1);
        $this->assertCount(1, $manager->peak_rules);
    }

    public function test_dual_dropdown_loads_field_pricing_automatically(): void
    {
        $manager = $this->createMockManager();

        // Ganti ke venue 2 -> memilih field 102 dengan konfigurasi peak_off_peak (Rp 160.000 peak)
        $manager->updatedVenueId(2);

        $this->assertEquals(2, $manager->venueId);
        $this->assertEquals(102, $manager->fieldId);
        $this->assertEquals('peak_off_peak', $manager->tipe_skema);
        $this->assertEquals(160000, $manager->harga_peak);
    }

    public function test_save_pricing_rejects_overlapping_peak_rules(): void
    {
        $manager = $this->createMockManager();
        $manager->tipe_skema = 'peak_off_peak';
        $manager->harga_perjam = 100000;
        $manager->harga_off_peak = 100000;
        $manager->harga_peak = 150000;

        // Rule 0 dan Rule 1 bertumpukan di hari Senin (hari=1) jam 18:00-20:00
        $manager->peak_rules = [
            ['hari' => [1], 'jam_mulai' => '17:00', 'jam_selesai' => '21:00'],
            ['hari' => [1], 'jam_mulai' => '19:00', 'jam_selesai' => '23:00'],
        ];

        $manager->savePricing();

        $errors = $manager->getErrorBag()->getMessages();
        $this->assertArrayHasKey('peak_rules.1.jam_mulai', $errors);
        $this->assertEquals('error', $manager->dispatchedEvents[0]['params'][0]['type']);
    }
}
