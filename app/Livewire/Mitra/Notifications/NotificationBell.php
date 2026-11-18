<?php

namespace App\Livewire\Mitra\Notifications;

use App\Livewire\Mitra\MitraComponent;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;

class NotificationBell extends MitraComponent
{
    public string $variant = 'header'; // 'header' or 'sidebar'

    #[Computed]
    public function unreadCount(): int
    {
        return $this->mitraUser()->unreadNotifications()->count();
    }

    #[Computed]
    public function recentNotifications(): Collection
    {
        return $this->mitraUser()
            ->notifications()
            ->latest()
            ->take(5)
            ->get();
    }

    #[On('notification-updated')]
    public function refreshNotifications(): void
    {
        unset($this->unreadCount);
        unset($this->recentNotifications);
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

    public function markAsRead(string $id)
    {
        $notification = $this->mitraUser()->notifications()->where('id', $id)->first();

        if ($notification) {
            if ($notification->unread()) {
                $notification->markAsRead();
            }
            $this->dispatch('notification-updated');

            $targetUrl = $notification->data['url'] ?? null;
            if ($targetUrl && $targetUrl !== '#') {
                return $this->redirect($targetUrl, navigate: true);
            }
        }
    }

    public function render(): View
    {
        return view('livewire.mitra.notifications.notification-bell');
    }
}
