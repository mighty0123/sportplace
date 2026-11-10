<?php

namespace Tests\Unit;

use App\Livewire\Mitra\Onboarding\RegistrationForm;
use App\Livewire\Mitra\Onboarding\VerificationStatus;
use App\Models\MitraProfile;
use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\TestCase;

class MitraLivewireOnboardingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $app = require dirname(__DIR__, 2) . '/bootstrap/app.php';
        $app->make(Kernel::class)->bootstrap();
    }

    private function createMitraUser(string $status = 'pending', ?MitraProfile $profile = null): User
    {
        $user = new User();
        $user->id = 1;
        $user->name = 'Budi Santoso';
        $user->email = 'budi@sportplace.id';
        $user->no_hp = '081234567890';
        $user->role = 'mitra';

        if ($profile) {
            $user->setRelation('mitraProfile', $profile);
        }

        return $user;
    }

    public function test_registration_form_nik_live_validation_rejects_short_or_alphabetic_nik(): void
    {
        $user = $this->createMitraUser();

        $form = new class extends RegistrationForm {
            public ?User $currentUser = null;

            public function testValidateField(string $field): void
            {
                $this->validateOnly($field);
            }
        };

        $form->currentUser = $user;

        // NIK terlalu pendek
        $form->no_ktp = '12345';
        try {
            $form->testValidateField('no_ktp');
            $this->fail('Seharusnya gagal validasi NIK kurang dari 16 digit.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('no_ktp', $e->errors());
        }

        // NIK mengandung huruf
        $form->no_ktp = '1234567890ABCDEF';
        try {
            $form->testValidateField('no_ktp');
            $this->fail('Seharusnya gagal validasi NIK non-numerik.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('no_ktp', $e->errors());
        }

        // NIK valid 16 digit angka
        $form->no_ktp = '5171012903840002';
        $form->testValidateField('no_ktp');
        $this->assertTrue(true);
    }

    public function test_registration_form_remove_file_clears_property(): void
    {
        $form = new class extends RegistrationForm {
            public function resetValidation($field = null)
            {
                // No-op in unit test without error bag hydration
            }
        };

        $form->file_ktp = 'dummy_uploaded_file';
        $form->removeFile('file_ktp');

        $this->assertNull($form->file_ktp);
    }

    public function test_verification_status_modal_opens_only_when_rejected_and_prefills_data(): void
    {
        $profile = new MitraProfile();
        $profile->id = 55;
        $profile->status = 'rejected';
        $profile->nama_usaha = 'GOR Nusantara';
        $profile->bentuk_badan_usaha = 'CV / Badan Usaha';
        $profile->kota_operasional = 'Bandung';
        $profile->no_ktp = '3273012903840001';
        $profile->npwp = '01.234.567.8-901.000';
        $profile->bank_nama = 'BCA';
        $profile->nomor_rekening = '1122334455';
        $profile->nama_rekening = 'GOR Nusantara';
        $profile->alasan_penolakan = 'KTP buram';

        $user = $this->createMitraUser('rejected', $profile);

        $statusComp = new class extends VerificationStatus {
            public ?User $currentUser = null;
            public ?MitraProfile $mockProfile = null;

            public function profile(): ?MitraProfile
            {
                return $this->mockProfile;
            }

            public function resetValidation($field = null)
            {
                // No-op in isolated unit test
            }
        };

        $statusComp->currentUser = $user;
        $statusComp->mockProfile = $profile;

        $statusComp->openResubmitModal();

        $this->assertTrue($statusComp->showResubmitModal);
        $this->assertEquals('GOR Nusantara', $statusComp->nama_usaha);
        $this->assertEquals('3273012903840001', $statusComp->no_ktp);
        $this->assertEquals('Bandung', $statusComp->kota_operasional);
        $this->assertEquals('1122334455', $statusComp->nomor_rekening);

        // Status pending tidak boleh membuka modal
        $pendingProfile = clone $profile;
        $pendingProfile->status = 'pending';
        $statusComp->mockProfile = $pendingProfile;
        $statusComp->showResubmitModal = false;
        $statusComp->openResubmitModal();
        $this->assertFalse($statusComp->showResubmitModal);
    }

    public function test_verification_status_refresh_dispatches_info_toast(): void
    {
        $user = $this->createMitraUser();

        $statusComp = new class extends VerificationStatus {
            public ?User $currentUser = null;
            public array $dispatchedEvents = [];

            public function dispatch($event, ...$params): void
            {
                $this->dispatchedEvents[] = ['event' => $event, 'params' => $params];
            }
        };

        $statusComp->currentUser = $user;
        $statusComp->refreshStatus(true);

        $this->assertCount(1, $statusComp->dispatchedEvents);
        $this->assertEquals('notify', $statusComp->dispatchedEvents[0]['event']);
        $this->assertEquals('info', $statusComp->dispatchedEvents[0]['params'][0]['type']);
    }
}
