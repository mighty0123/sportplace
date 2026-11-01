<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\BookingDetail;
use App\Models\Field;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * MTR-03 / FR-REPORT-001: Tampilkan Dashboard Mitra.
     * Mengagregasi metrik booking hari ini, estimasi pendapatan,
     * status operasional, dan pintasan tindakan penting.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $mitraProfile = $user->mitraProfile;

        // Ambil ID seluruh venue milik mitra
        $venues = Venue::where('mitra_id', $mitraProfile->id)->get();
        $venueIds = $venues->pluck('id');

        // Ambil ID seluruh unit lapangan milik mitra
        $fields = Field::whereIn('venue_id', $venueIds)->get();
        $fieldIds = $fields->pluck('id');

        $today = Carbon::today()->toDateString();
        $nowTime = Carbon::now()->toTimeString();

        // T3.2: Booking hari ini (jumlah booking_details pada lapangan milik mitra)
        $todayBookingsQuery = BookingDetail::with(['field.venue', 'header.user'])
            ->whereIn('field_id', $fieldIds)
            ->whereDate('tanggal_sewa', $today);

        $todayBookingsCount = (clone $todayBookingsQuery)->count();
        $todayBookings = (clone $todayBookingsQuery)
            ->orderBy('jam_mulai', 'asc')
            ->get();

        // T3.3: Ringkasan pendapatan kotor dari pesanan yang telah dibayar (status header 'payment')
        $totalRevenue = BookingDetail::whereIn('field_id', $fieldIds)
            ->whereHas('header', function ($query) {
                $query->where('status', 'payment');
            })
            ->sum('harga_slot');

        // Pendapatan hari ini
        $todayRevenue = BookingDetail::whereIn('field_id', $fieldIds)
            ->whereDate('tanggal_sewa', $today)
            ->whereHas('header', function ($query) {
                $query->where('status', 'payment');
            })
            ->sum('harga_slot');

        $wallet = $user->wallet;
        $saldoAktif = $wallet ? (float) $wallet->saldo_aktif : 0;
        $saldoTertahan = $wallet ? (float) $wallet->saldo_tertahan : 0;

        // T3.5: Pintasan tindakan
        // 1. Pesanan baru / menunggu verifikasi (booking header berstatus 'pending')
        $pendingBookingsCount = BookingDetail::whereIn('field_id', $fieldIds)
            ->whereHas('header', function ($query) {
                $query->where('status', 'pending');
            })
            ->count();

        // 2. Booking siap diselesaikan (jam sewa sudah lewat namun status masih 'ongoing')
        $readyToCompleteCount = BookingDetail::whereIn('field_id', $fieldIds)
            ->where('status_item', 'ongoing')
            ->where(function ($query) use ($today, $nowTime) {
                $query->whereDate('tanggal_sewa', '<', $today)
                    ->orWhere(function ($q) use ($today, $nowTime) {
                        $q->whereDate('tanggal_sewa', $today)
                            ->where('jam_selesai', '<=', $nowTime);
                    });
            })
            ->count();

        // 3. Venue dan Lapangan yang ditolak (rejected)
        $rejectedVenues = $venues->where('status_vanue', 'rejected');
        $rejectedFields = $fields->where('status_lapangan', 'rejected');

        return view('mitra.dashboard', [
            'mitraProfile' => $mitraProfile,
            'venuesCount' => $venues->count(),
            'fieldsCount' => $fields->count(),
            'todayBookingsCount' => $todayBookingsCount,
            'todayBookings' => $todayBookings,
            'totalRevenue' => $totalRevenue,
            'todayRevenue' => $todayRevenue,
            'saldoAktif' => $saldoAktif,
            'saldoTertahan' => $saldoTertahan,
            'pendingBookingsCount' => $pendingBookingsCount,
            'readyToCompleteCount' => $readyToCompleteCount,
            'rejectedVenues' => $rejectedVenues,
            'rejectedFields' => $rejectedFields,
        ]);
    }
}
