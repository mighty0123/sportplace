<?php

namespace Tests\Unit;

use App\Livewire\Mitra\Concerns\WithMitraAuthorization;
use App\Models\BookingDetail;
use App\Models\Field;
use App\Models\MitraProfile;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Contracts\Console\Kernel;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\HttpException;

class MitraLivewireAuthorizationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $app = require dirname(__DIR__, 2) . '/bootstrap/app.php';
        $app->make(Kernel::class)->bootstrap();
    }

    private function createDummyComponent(?User $user = null)
    {
        $comp = new class {
            use WithMitraAuthorization;

            public ?User $currentUser = null;

            public array $dispatchedEvents = [];

            public function dispatch($event, ...$params): void
            {
                $this->dispatchedEvents[] = ['event' => $event, 'params' => $params];
            }

            public function testMitraUser(): User
            {
                return $this->mitraUser();
            }

            public function testMitraProfile(): ?MitraProfile
            {
                return $this->mitraProfile();
            }

            public function testAuthorizeMitraApproved(): MitraProfile
            {
                return $this->authorizeMitraApproved();
            }

            public function testEnsureOwnsVenue(Venue $venue): void
            {
                $this->ensureOwnsVenue($venue);
            }

            public function testEnsureOwnsField(Field $field): void
            {
                $this->ensureOwnsField($field);
            }

            public function testEnsureOwnsBookingDetail(BookingDetail $detail): void
            {
                $this->ensureOwnsBookingDetail($detail);
            }

            public function testNotify(string $message, string $type = 'success'): void
            {
                $this->notify($message, $type);
            }
        };

        $comp->currentUser = $user;

        return $comp;
    }

    public function test_rejects_unauthenticated_user(): void
    {
        $component = $this->createDummyComponent(null);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Akses terbatas untuk akun Mitra.');
        $component->testMitraUser();
    }

    public function test_rejects_customer_role(): void
    {
        $user = new User();
        $user->role = 'customer';

        $component = $this->createDummyComponent($user);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Akses terbatas untuk akun Mitra.');
        $component->testMitraUser();
    }

    public function test_authorize_approved_mitra_succeeds(): void
    {
        $profile = new MitraProfile();
        $profile->id = 10;
        $profile->status = 'approved';

        $user = new User();
        $user->role = 'mitra';
        $user->setRelation('mitraProfile', $profile);

        $component = $this->createDummyComponent($user);

        $result = $component->testAuthorizeMitraApproved();
        $this->assertEquals($profile, $result);
    }

    public function test_authorize_pending_mitra_throws_403(): void
    {
        $profile = new MitraProfile();
        $profile->id = 10;
        $profile->status = 'pending';

        $user = new User();
        $user->role = 'mitra';
        $user->setRelation('mitraProfile', $profile);

        $component = $this->createDummyComponent($user);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Akun mitra Anda belum disetujui');
        $component->testAuthorizeMitraApproved();
    }

    public function test_venue_ownership_check(): void
    {
        $profile = new MitraProfile();
        $profile->id = 5;
        $profile->status = 'approved';

        $user = new User();
        $user->role = 'mitra';
        $user->setRelation('mitraProfile', $profile);

        $component = $this->createDummyComponent($user);

        $ownVenue = new Venue();
        $ownVenue->id = 1;
        $ownVenue->mitra_id = 5;

        // Harus berhasil tanpa exception
        $component->testEnsureOwnsVenue($ownVenue);
        $this->assertTrue(true);

        $otherVenue = new Venue();
        $otherVenue->id = 2;
        $otherVenue->mitra_id = 99;

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Anda tidak memiliki hak akses terhadap venue ini.');
        $component->testEnsureOwnsVenue($otherVenue);
    }

    public function test_field_ownership_check(): void
    {
        $profile = new MitraProfile();
        $profile->id = 5;
        $profile->status = 'approved';

        $user = new User();
        $user->role = 'mitra';
        $user->setRelation('mitraProfile', $profile);

        $component = $this->createDummyComponent($user);

        $ownVenue = new Venue();
        $ownVenue->id = 1;
        $ownVenue->mitra_id = 5;

        $ownField = new Field();
        $ownField->id = 10;
        $ownField->setRelation('venue', $ownVenue);

        // Harus berhasil tanpa exception
        $component->testEnsureOwnsField($ownField);
        $this->assertTrue(true);

        $foreignVenue = new Venue();
        $foreignVenue->id = 2;
        $foreignVenue->mitra_id = 99;

        $foreignField = new Field();
        $foreignField->id = 11;
        $foreignField->setRelation('venue', $foreignVenue);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Anda tidak memiliki hak akses terhadap lapangan ini.');
        $component->testEnsureOwnsField($foreignField);
    }

    public function test_booking_detail_ownership_check(): void
    {
        $profile = new MitraProfile;
        $profile->id = 5;
        $profile->status = 'approved';

        $user = new User;
        $user->role = 'mitra';
        $user->setRelation('mitraProfile', $profile);

        $component = $this->createDummyComponent($user);

        $ownVenue = new Venue;
        $ownVenue->id = 1;
        $ownVenue->mitra_id = 5;

        $ownField = new Field;
        $ownField->id = 10;
        $ownField->setRelation('venue', $ownVenue);

        $ownDetail = new BookingDetail;
        $ownDetail->id = 100;
        $ownDetail->setRelation('field', $ownField);

        // Own booking detail succeeds
        $component->testEnsureOwnsBookingDetail($ownDetail);
        $this->assertTrue(true);

        $foreignVenue = new Venue;
        $foreignVenue->id = 2;
        $foreignVenue->mitra_id = 99;

        $foreignField = new Field;
        $foreignField->id = 11;
        $foreignField->setRelation('venue', $foreignVenue);

        $foreignDetail = new BookingDetail;
        $foreignDetail->id = 101;
        $foreignDetail->setRelation('field', $foreignField);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Anda tidak memiliki hak akses terhadap pesanan ini.');
        $component->testEnsureOwnsBookingDetail($foreignDetail);
    }

    public function test_notify_dispatches_event(): void
    {
        $user = new User();
        $user->role = 'mitra';

        $component = $this->createDummyComponent($user);

        $component->testNotify('Data berhasil disimpan', 'success');

        $this->assertCount(1, $component->dispatchedEvents);
        $this->assertEquals('notify', $component->dispatchedEvents[0]['event']);
        $this->assertEquals('Data berhasil disimpan', $component->dispatchedEvents[0]['params'][0]['message']);
        $this->assertEquals('success', $component->dispatchedEvents[0]['params'][0]['type']);
    }
}
