<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Tampilkan daftar notifikasi milik mitra (FR-NOTIF-001, BR-027).
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $filter = $request->query('filter', 'all');

        $query = $filter === 'unread'
            ? $user->unreadNotifications()
            : $user->notifications();

        $notifications = $query->paginate(15)->withQueryString();
        $unreadCount = $user->unreadNotifications()->count();

        return view('mitra.notifications.index', [
            'notifications' => $notifications,
            'filter' => $filter,
            'unreadCount' => $unreadCount,
        ]);
    }

    /**
     * Tandai satu notifikasi sebagai telah dibaca dan arahkan ke tautan tujuan.
     */
    public function markAsRead(Request $request, string $id): RedirectResponse
    {
        $user = $request->user();
        $notification = $user->notifications()->where('id', $id)->firstOrFail();

        if ($notification->unread()) {
            $notification->markAsRead();
        }

        $targetUrl = $notification->data['url'] ?? route('mitra.notifications.index');

        return redirect()->to($targetUrl);
    }

    /**
     * Tandai seluruh notifikasi yang belum dibaca menjadi terbaca.
     */
    public function markAllAsRead(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Seluruh notifikasi berhasil ditandai sebagai telah dibaca.');
    }
}
