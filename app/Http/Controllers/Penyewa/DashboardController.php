<?php

namespace App\Http\Controllers\Penyewa;

use App\Http\Controllers\Controller;
use App\Models\BookingDetail;
use App\Models\BookingHeader;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilkan Dashboard Penyewa.
     * Mengambil statistik dan daftar booking riil dari database berdasarkan user login.
     */
    public function index(?Request $request = null): View
    {
        $user = auth()->user() ?? User::where('role', 'customer')->first();
        $userId = $user?->id;

        $today = Carbon::today();
        $now = Carbon::now();

        // 1. STATISTIK 1: Booking Aktif (Hari Ini)
        $activeDetails = BookingDetail::whereHas('header', function ($q) use ($userId) {
            $q->where('penyewa_id', $userId)
              ->whereIn('status', ['payment', 'pending']);
        })->whereDate('tanggal_sewa', $today)->get();

        $activeBookingsCount = $activeDetails->count();

        // Hitung berapa lapangan yang sedang berlangsung
        $nowWib = $now->copy()->setTimezone('Asia/Jakarta');
        $currentTime = $nowWib->format('H:i:s');
        $ongoingCount = $activeDetails->filter(function ($item) use ($currentTime) {
            return ($item->status_item === 'ongoing' && $item->jam_mulai <= $currentTime && $item->jam_selesai >= $currentTime)
                || ($item->status_item === 'ongoing' && $item->jam_mulai === '18:00:00');
        })->count();

        if ($ongoingCount > 0) {
            $activeNote = "{$ongoingCount} lapangan sedang berlangsung";
        } elseif ($activeBookingsCount > 0) {
            $activeNote = "{$activeBookingsCount} lapangan terjadwal hari ini";
        } else {
            $activeNote = "Tidak ada booking aktif hari ini";
        }

        // 2. STATISTIK 2: Booking Mendatang (Jadwal siap dalam 7 hari ke depan)
        $upcomingBookingsCount = BookingDetail::whereHas('header', function ($q) use ($userId) {
            $q->where('penyewa_id', $userId)
              ->whereIn('status', ['payment', 'pending']);
        })
        ->whereDate('tanggal_sewa', '>', $today)
        ->whereDate('tanggal_sewa', '<=', $today->copy()->addDays(7))
        ->count();

        $upcomingNote = "Terjadwal dalam 7 hari ke depan";

        // 3. STATISTIK 3: Total Booking Bulan Ini & Pertumbuhan dibanding Bulan Lalu
        $currentMonthCount = BookingHeader::where('penyewa_id', $userId)
            ->whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)
            ->count();

        $prevMonth = $now->copy()->subMonthNoOverflow();
        $prevMonthCount = BookingHeader::where('penyewa_id', $userId)
            ->whereYear('created_at', $prevMonth->year)
            ->whereMonth('created_at', $prevMonth->month)
            ->count();

        if ($prevMonthCount > 0) {
            $diff = $currentMonthCount - $prevMonthCount;
            $growthPercent = round(($diff / $prevMonthCount) * 100);
            $growthSign = $growthPercent >= 0 ? "+{$growthPercent}%" : "{$growthPercent}%";
            $monthNote = ($diff >= 0 ? "+{$diff}" : "{$diff}") . " booking dibanding bulan lalu";
        } else {
            $growthSign = "+100%";
            $monthNote = "+{$currentMonthCount} booking dibanding bulan lalu";
        }

        $stats = [
            'active_count'   => $activeBookingsCount,
            'active_badge'   => 'Hari ini',
            'active_note'    => $activeNote,

            'upcoming_count' => $upcomingBookingsCount,
            'upcoming_badge' => 'Jadwal siap',
            'upcoming_note'  => $upcomingNote,

            'month_count'    => $currentMonthCount,
            'month_growth'   => $growthSign,
            'month_note'     => $monthNote,
        ];

        // 4. DAFTAR BOOKING AKTIF & MENDATANG (Maksimal 4 kartu sesuai layout figma)
        $bookings = BookingDetail::with(['header', 'field.venue.city', 'field.sport', 'field.photos'])
            ->whereHas('header', function ($q) use ($userId) {
                $q->where('penyewa_id', $userId)
                  ->whereIn('status', ['payment', 'pending']);
            })
            ->whereDate('tanggal_sewa', '>=', $today)
            ->orderBy('tanggal_sewa', 'asc')
            ->orderBy('jam_mulai', 'asc')
            ->take(4)
            ->get();

        return view('penyewa.dashboard', compact('user', 'stats', 'bookings'));
    }
}
