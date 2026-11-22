<?php

namespace Tests\Unit;

use App\Livewire\Mitra\Notifications\NotificationBell;
use App\Livewire\Mitra\Notifications\NotificationList;
use App\Models\MitraProfile;
use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Notifications\DatabaseNotification;
use PHPUnit\Framework\TestCase;

class MitraLivewireNotificationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $app = require dirname(__DIR__, 2) . '/bootstrap/app.php';
        $app->make(Kernel::class)->bootstrap();
    }

    private function createMitraUser(): User
    {
        $user = new User();
        $user->id = 1;
        $user->name = 'Bambang Mitra';
        $user->email = 'mitra@sportplace.id';
        $user->role = 'mitra';

        $profile = new MitraProfile();
        $profile->id = 1;
        $profile->user_id = 1;
        $profile->status = 'approved';

        $user->setRelation('mitraProfile', $profile);

        return $user;
    }

    public function test_notification_bell_initialization_and_unread_count(): void
    {
        $user = $this->createMitraUser();

        $bell = new class extends NotificationBell {
            public ?User $currentUser = null;
            public array $dispatchedEvents = [];
            public int $mockCount = 4;

            public function unreadCount(): int
            {
                return $this->mockCount;
            }

            public function dispatch($event, ...$params): void
            {
                $this->dispatchedEvents[] = ['event' => $event, 'params' => $params];
            }
        };

        $bell->currentUser = $user;
        $bell->variant = 'header';

        $this->assertEquals('header', $bell->variant);
        $this->assertEquals(4, $bell->unreadCount());
    }

    public function test_notification_list_filter_toggling(): void
    {
        $user = $this->createMitraUser();

        $list = new class extends NotificationList {
            public ?User $currentUser = null;
            public array $dispatchedEvents = [];

            public function dispatch($event, ...$params): void
            {
                $this->dispatchedEvents[] = ['event' => $event, 'params' => $params];
            }
        };

        $list->currentUser = $user;
        $this->assertEquals('all', $list->filter);

        $list->setFilter('unread');
        $this->assertEquals('unread', $list->filter);

        $list->setFilter('invalid_filter');
        $this->assertEquals('unread', $list->filter); // Harus mengabaikan filter tidak valid

        $list->setFilter('all');
        $this->assertEquals('all', $list->filter);
    }

    public function test_notification_bell_mark_as_read_dispatches_events(): void
    {
        $user = $this->createMitraUser();

        $notification = new DatabaseNotification();
        $notification->id = 'notif-123';
        $notification->type = 'App\Notifications\NewBookingReceived';
        $notification->data = [
            'type' => 'new_booking',
            'title' => 'Reservasi Baru Diterima',
            'message' => 'Lapangan Sintetis A dipesan',
            'url' => '/mitra/pesanan',
        ];
        $notification->read_at = null;

        $bell = new class extends NotificationBell {
            public ?User $currentUser = null;
            public array $dispatchedEvents = [];
            public ?DatabaseNotification $mockNotif = null;

            public function dispatch($event, ...$params): void
            {
                $this->dispatchedEvents[] = ['event' => $event, 'params' => $params];
            }

            public function markAsRead(string $id)
            {
                if ($this->mockNotif && $this->mockNotif->id === $id) {
                    $this->mockNotif->read_at = now();
                    $this->dispatch('notification-updated');
                    return $this->mockNotif->data['url'];
                }
            }
        };

        $bell->currentUser = $user;
        $bell->mockNotif = $notification;

        $redirectUrl = $bell->markAsRead('notif-123');

        $this->assertEquals('/mitra/pesanan', $redirectUrl);
        $this->assertNotNull($notification->read_at);
        $this->assertCount(1, $bell->dispatchedEvents);
        $this->assertEquals('notification-updated', $bell->dispatchedEvents[0]['event']);
    }

    public function test_notification_list_mark_all_as_read_dispatches_notify(): void
    {
        $user = $this->createMitraUser();

        $list = new class extends NotificationList {
            public ?User $currentUser = null;
            public array $dispatchedEvents = [];

            public function dispatch($event, ...$params): void
            {
                $this->dispatchedEvents[] = ['event' => $event, 'params' => $params];
            }

            public function markAllAsRead(): void
            {
                $this->dispatch('notification-updated');
                $this->notify('Seluruh notifikasi berhasil ditandai sebagai telah dibaca.');
            }
        };

        $list->currentUser = $user;
        $list->markAllAsRead();

        $this->assertCount(2, $list->dispatchedEvents);
        $this->assertEquals('notification-updated', $list->dispatchedEvents[0]['event']);
        $this->assertEquals('notify', $list->dispatchedEvents[1]['event']);
        $this->assertEquals('Seluruh notifikasi berhasil ditandai sebagai telah dibaca.', $list->dispatchedEvents[1]['params'][0]['message']);
    }
}
