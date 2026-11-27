<?php

namespace Tests\Unit;

use App\Livewire\Mitra\Profile\ProfileManager;
use App\Models\MitraProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Console\Kernel;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\HttpException;

class MitraLivewireProfileTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $app = require dirname(__DIR__, 2).'/bootstrap/app.php';
        $app->make(Kernel::class)->bootstrap();
        Carbon::setTestNow('2026-11-23 12:00:00');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function createMockProfileManager(?string $status = 'approved', ?string $role = 'mitra'): ProfileManager
    {
        $profile = new MitraProfile;
        $profile->id = 7;
        $profile->nama_usaha = 'Champion Sport Center';
        $profile->bentuk_badan_usaha = 'CV / Badan Usaha';
        $profile->kota_operasional = 'Bandung';
        $profile->deskripsi_usaha = 'Pusat sewa lapangan futsal dan badminton.';
        $profile->bank_nama = 'BCA';
        $profile->nomor_rekening = '1234567890';
        $profile->nama_rekening = 'Budi Santoso';
        $profile->no_ktp = '3201012345678901';
        $profile->npwp = '012345678901234';
        $profile->file_ktp = 'mitra_documents/7/ktp.jpg';
        $profile->file_npwp = 'mitra_documents/7/npwp.jpg';
        $profile->status = $status ?? 'approved';
        $profile->reviewed_at = Carbon::parse('2026-11-01 10:00:00');

        $user = new User;
        $user->id = 7;
        $user->name = 'Budi Santoso';
        $user->email = 'budi@example.com';
        $user->phone = '081234567890';
        $user->role = $role ?? 'mitra';
        $user->setRelation('mitraProfile', $profile);

        return new class($user, $profile) extends ProfileManager {
            public array $dispatchedEvents = [];

            public function __construct(private User $mockUser, private MitraProfile $mockProfile)
            {
                $this->currentUser = $mockUser;
            }

            public function dispatch($event, ...$params): self
            {
                $this->dispatchedEvents[] = ['event' => $event, 'params' => $params];

                return $this;
            }
        };
    }

    public function test_approved_mitra_mounts_profile_data_correctly(): void
    {
        $manager = $this->createMockProfileManager('approved');
        $manager->mount();

        $this->assertSame('Champion Sport Center', $manager->nama_usaha);
        $this->assertSame('CV / Badan Usaha', $manager->bentuk_badan_usaha);
        $this->assertSame('Bandung', $manager->kota_operasional);
        $this->assertSame('Budi Santoso', $manager->user_name);
        $this->assertSame('budi@example.com', $manager->user_email);
        $this->assertSame('BCA', $manager->bank_nama);
        $this->assertSame('1234567890', $manager->nomor_rekening);
    }

    public function test_unapproved_mitra_cannot_access_profile_page(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Akun mitra Anda belum disetujui');

        $manager = $this->createMockProfileManager('pending');
        $manager->mount();
    }

    public function test_non_mitra_role_is_denied(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Akses terbatas untuk akun Mitra.');

        $manager = $this->createMockProfileManager('approved', 'customer');
        $manager->mount();
    }

    public function test_documents_list_contains_uploaded_and_empty_files(): void
    {
        $manager = $this->createMockProfileManager('approved');
        $manager->mount();

        $docs = $manager->documentsList();
        $this->assertCount(4, $docs);

        $ktp = $docs[0];
        $this->assertSame('ktp', $ktp['type']);
        $this->assertSame('Terverifikasi', $ktp['status']);
        $this->assertNotNull($ktp['download_url']);

        $nib = $docs[2];
        $this->assertSame('nib', $nib['type']);
        $this->assertSame('Tidak Dilampirkan', $nib['status']);
        $this->assertNull($nib['download_url']);
    }

    public function test_modal_open_and_close(): void
    {
        $manager = $this->createMockProfileManager('approved');
        $manager->mount();

        $this->assertFalse($manager->showEditModal);

        $manager->openEditModal();
        $this->assertTrue($manager->showEditModal);

        $manager->closeEditModal();
        $this->assertFalse($manager->showEditModal);
    }
}
