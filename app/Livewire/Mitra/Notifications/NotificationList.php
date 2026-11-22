<?php

namespace App\Livewire\Mitra\Notifications;

use App\Livewire\Mitra\MitraComponent;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class NotificationList extends MitraComponent
{
    use WithPagination;

    #[Url(as: 'filter', history: true)]
    public string $filter = 'all';

    public function setFilter(string $filter): void
    {
        if (in_array($filter, ['all', 'unread'], true)) {
            $this->filter = $filter;
            $this->resetPage();
        }
    }

    #[Computed]
    public function unreadCount(): int
    {
        return $this->mitraUser()->unreadNotifications()->count();
    }

    #[Computed]
    public function notifications(): LengthAwarePaginator
    {
        $user = $this->mitraUser();

        $query = $this->filter === 'unread'
            ? $user->unreadNotifications()
            : $user->notifications();

        return $query->latest()->paginate(15);
    }

    #[On('notification-updated')]
    public function refreshList(): void
    {
        unset($this->unreadCount);
        unset($this->notifications);
    }

    public function markAsRead(string $id)
    {
        $notification = $this->mitraUser()->notifications()->where('id', $id)->firstOrFail();

        if ($notification->unread()) {
            $notification->markAsRead();
        }

        $this->dispatch('notification-updated');
        $this->notify('Notifikasi ditandai telah dibaca.');

        $targetUrl = $notification->data['url'] ?? null;
        if ($targetUrl && $targetUrl !== '#') {
            return $this->redirect($targetUrl, navigate: true);
        }
    }

    public function markAllAsRead(): void
    {
        $user = $this->mitraUser();
        $unread = $user->unreadNotifications;

        if ($unread->isNotEmpty()) {
            $unread->markAsRead();
            $this->dispatch('notification-updated');
            $this->notify('Seluruh notifikasi berhasil ditandai sebagai telah dibaca.');
        }
    }

    public function render(): View
    {
        return view('livewire.mitra.notifications.notification-list', [
            'notificationsList' => $this->notifications,
            'unreadTotal' => $this->unreadCount,
        ]);
    }
}
