<?php

namespace Tests\Unit;

use App\Livewire\Mitra\Fields\FieldManager;
use App\Livewire\Mitra\Venues\VenueManager;
use App\Models\MitraProfile;
use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\TestCase;

class MitraLivewireVenueFieldTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $app = require dirname(__DIR__, 2) . '/bootstrap/app.php';
        $app->make(Kernel::class)->bootstrap();
    }

    private function createApprovedMitra(): User
    {
        $profile = new MitraProfile();
        $profile->id = 10;
        $profile->status = 'approved';

        $user = new User();
        $user->id = 10;
        $user->role = 'mitra';
        $user->setRelation('mitraProfile', $profile);

        return $user;
    }

    public function test_venue_manager_status_filter_and_reset(): void
    {
        $user = $this->createApprovedMitra();

        $manager = new class extends VenueManager {
            public ?User $currentUser = null;
        };
        $manager->currentUser = $user;

        $this->assertEquals('semua', $manager->status);

        $manager->setStatusFilter('aktif');
        $this->assertEquals('aktif', $manager->status);

        $manager->setStatusFilter('invalid_status');
        $this->assertEquals('aktif', $manager->status); // Mengabaikan nilai invalid

        $manager->search = 'Futsal';
        $manager->cityId = '1';
        $manager->resetFilters();

        $this->assertEquals('', $manager->search);
        $this->assertEquals('', $manager->cityId);
        $this->assertEquals('semua', $manager->status);
    }

    public function test_venue_manager_validates_closing_time_after_opening_time(): void
    {
        $user = $this->createApprovedMitra();

        $manager = new class extends VenueManager {
            public ?User $currentUser = null;

            public function testValidateClosingTime(): void
            {
                $this->validateOnly('jam_tutup');
            }
        };
        $manager->currentUser = $user;
        $manager->jam_buka = '10:00';
        $manager->jam_tutup = '08:00'; // Salah: jam tutup lebih awal

        try {
            $manager->testValidateClosingTime();
            $this->fail('Validasi seharusnya menolak jam tutup sebelum jam buka.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('jam_tutup', $e->errors());
        }

        $manager->jam_tutup = '22:00'; // Valid
        $manager->testValidateClosingTime();
        $this->assertTrue(true);
    }

    public function test_field_manager_photo_staging_helpers(): void
    {
        $user = $this->createApprovedMitra();

        $fieldManager = new class extends FieldManager {
            public ?User $currentUser = null;
        };
        $fieldManager->currentUser = $user;

        // Simulasi 3 file upload temporary
        $fieldManager->photos = ['photo_a', 'photo_b', 'photo_c'];
        $fieldManager->setPrimaryNewPhoto(2);
        $this->assertEquals(2, $fieldManager->newPrimaryIndex);

        // Hapus foto ke-2 (index 2) -> primaryIndex harus tetap valid (reset ke 0)
        $fieldManager->removeNewPhoto(2);
        $this->assertCount(2, $fieldManager->photos);
        $this->assertEquals(['photo_a', 'photo_b'], $fieldManager->photos);
        $this->assertEquals(0, $fieldManager->newPrimaryIndex);
    }

    public function test_field_manager_sport_filter_and_reset(): void
    {
        $user = $this->createApprovedMitra();

        $fieldManager = new class extends FieldManager {
            public ?User $currentUser = null;
        };
        $fieldManager->currentUser = $user;

        $fieldManager->setSportFilter('3');
        $this->assertEquals('3', $fieldManager->sportId);

        $fieldManager->search = 'Sintetis';
        $fieldManager->status = 'aktif';
        $fieldManager->resetFilters();

        $this->assertEquals('', $fieldManager->search);
        $this->assertEquals('semua', $fieldManager->status);
        $this->assertEquals('semua', $fieldManager->sportId);
    }

    public function test_venue_and_field_rating_accessors_without_database_rating(): void
    {
        $venue = new \App\Models\Venue();
        $this->assertNull($venue->average_rating);
        $this->assertEquals('Belum ada ulasan', $venue->formatted_rating);

        $field = new \App\Models\Field();
        $this->assertNull($field->average_rating);
        $this->assertEquals('Belum ada ulasan', $field->formatted_rating);
    }

    public function test_venue_and_field_rating_accessors_with_database_rating(): void
    {
        $venue = new \App\Models\Venue(['nama_venue' => 'Arena Kita']);
        $venue->rating = 4.7;
        $this->assertEquals(4.7, $venue->average_rating);
        $this->assertEquals('4.7', $venue->formatted_rating);

        $field = new \App\Models\Field(['nama_lapangan' => 'Lapangan A']);
        $field->rating = 4.9;
        $this->assertEquals(4.9, $field->average_rating);
        $this->assertEquals('4.9', $field->formatted_rating);
    }
}
