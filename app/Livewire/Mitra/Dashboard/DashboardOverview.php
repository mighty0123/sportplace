<?php

namespace App\Livewire\Mitra\Dashboard;

use App\Livewire\Mitra\MitraComponent;
use App\Models\AuditLog;
use App\Models\BookingDetail;
use App\Models\Field;
use App\Models\MitraProfile;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;

class DashboardOverview extends MitraComponent
{
    public bool $showCompleteConfirmModal = false;

    public ?int $completingOrderId = null;

    public function mount(): void
    {
        $this->authorizeMitraApproved();
    }

    #[Computed]
    public function profile(): MitraProfile
    {
        return $this->authorizeMitraApproved();
    }

    #[Computed]
    public function venues(): Collection
    {
        return Venue::where('mitra_id', $this->profile->id)->get();
    }

    #[Computed]
    public function fields(): Collection
    {
        return Field::whereIn('venue_id', $this->venues()->pluck('id'))->get();
    }

    #[Computed]
    public function todayBookings(): Collection
    {
        $fieldIds = $this->fields()->pluck('id');
        $today = Carbon::today()->toDateString();

        return BookingDetail::with(['field.venue', 'header.user'])
            ->whereIn('field_id', $fieldIds)
            ->whereDate('tanggal_sewa', $today)
            ->orderBy('jam_mulai', 'asc')
            ->get();
    }

    #[Computed]
    public function metrics(): array
    {
        $fieldIds = $this->fields()->pluck('id');
        $today = Carbon::today()->toDateString();

        $todayBookingsCount = $this->todayBookings()->count();

        $todayRevenue = $this->calculateTodayRevenue($fieldIds, $today);
        $totalRevenue = $this->calculateTotalRevenue($fieldIds);

        $user = $this->mitraUser();
        $wallet = $user->wallet;
        $saldoAktif = $wallet ? (float) $wallet->saldo_aktif : 0.0;
        $saldoTertahan = $wallet ? (float) $wallet->saldo_tertahan : 0.0;

        return [
            'venues_count' => $this->venues()->count(),
            'fields_count' => $this->fields()->count(),
            'today_bookings_count' => $todayBookingsCount,
            'today_revenue' => $todayRevenue,
            'total_revenue' => $totalRevenue,
            'saldo_aktif' => $saldoAktif,
            'saldo_tertahan' => $saldoTertahan,
        ];
    }

    #[Computed]
    public function actionShortcuts(): array
    {
        $fieldIds = $this->fields()->pluck('id');
        $today = Carbon::today()->toDateString();
        $nowTime = Carbon::now()->toTimeString();

        $pendingBookingsCount = $this->countPendingBookings($fieldIds);
        $readyToCompleteCount = $this->countReadyToCompleteBookings($fieldIds, $today, $nowTime);

        $rejectedVenues = $this->venues()->where('status_vanue', 'rejected');
        $rejectedFields = $this->fields()->where('status_lapangan', 'rejected');

        return [
            'pending_bookings_count' => $pendingBookingsCount,
            'ready_to_complete_count' => $readyToCompleteCount,
            'rejected_venues' => $rejectedVenues,
            'rejected_fields' => $rejectedFields,
        ];
    }

    public function calculateTodayRevenue(Collection $fieldIds, string $today): float
    {
        return (float) BookingDetail::whereIn('field_id', $fieldIds)
            ->whereDate('tanggal_sewa', $today)
            ->whereHas('header', fn ($q) => $q->where('status', 'payment'))
            ->sum('harga_slot');
    }

    public function calculateTotalRevenue(Collection $fieldIds): float
    {
        return (float) BookingDetail::whereIn('field_id', $fieldIds)
            ->whereHas('header', fn ($q) => $q->where('status', 'payment'))
            ->sum('harga_slot');
    }

    public function countPendingBookings(Collection $fieldIds): int
    {
        return BookingDetail::whereIn('field_id', $fieldIds)
            ->whereHas('header', fn ($q) => $q->where('status', 'pending'))
            ->count();
    }

    public function countReadyToCompleteBookings(Collection $fieldIds, string $today, string $nowTime): int
    {
        return BookingDetail::whereIn('field_id', $fieldIds)
            ->readyToComplete()
            ->count();
    }

    public function confirmAttendance(int $detailId): void
    {
        $order = BookingDetail::with(['field.venue'])->findOrFail($detailId);
        $this->ensureOwnsBookingDetail($order);

        if (! $order->isPaid()) {
            $this->notify('Kehadiran tidak dapat dikonfirmasi karena pembayaran belum lunas.', 'error');

            return;
        }

        DB::transaction(function () use ($order) {
            /** @var BookingDetail $locked */
            $locked = BookingDetail::lockForUpdate()->findOrFail($order->id);

            if ($locked->status_item !== 'ongoing') {
                return;
            }

            AuditLog::record(
                Auth::user(),
                'BookingDetail',
                $locked->id,
                'confirm_attendance',
                $locked->status_item,
                'ongoing',
                'Mitra mengonfirmasi kehadiran penyewa di lapangan via Dashboard.'
            );
        });

        $this->notify('Kehadiran penyewa berhasil dikonfirmasi.', 'success');
    }

    public function confirmCompleteOrder(int $orderId): void
    {
        $this->completingOrderId = $orderId;
        $this->showCompleteConfirmModal = true;
    }

    public function cancelCompleteOrder(): void
    {
        $this->showCompleteConfirmModal = false;
        $this->completingOrderId = null;
    }

    public function executeCompleteOrder(): void
    {
        if ($this->completingOrderId) {
            $orderId = $this->completingOrderId;
            $this->cancelCompleteOrder();
            $this->completeBooking($orderId);
        }
    }

    public function completeBooking(int $detailId): void
    {
        $order = BookingDetail::with(['field.venue'])->findOrFail($detailId);
        $this->ensureOwnsBookingDetail($order);

        if (! $order->isPaid()) {
            $this->notify('Pesanan tidak dapat diselesaikan karena belum lunas.', 'error');

            return;
        }

        if (Carbon::now()->lt($order->getEndDateTime())) {
            $this->notify('Pesanan hanya dapat diselesaikan setelah jam sewa berakhir ('.$order->jam_selesai.').', 'error');

            return;
        }

        $result = DB::transaction(function () use ($order) {
            /** @var BookingDetail $locked */
            $locked = BookingDetail::lockForUpdate()->findOrFail($order->id);

            if ($locked->status_item === 'completed') {
                return ['status' => 'already_processed', 'message' => 'Pesanan ini sudah diselesaikan sebelumnya.'];
            }

            $oldStatus = $locked->status_item;
            $locked->update(['status_item' => 'completed']);

            AuditLog::record(
                Auth::user(),
                'BookingDetail',
                $locked->id,
                'complete_order',
                $oldStatus,
                'completed',
                'Sesi bermain diselesaikan oleh mitra via Dashboard.'
            );

            return ['status' => 'success', 'message' => 'Sesi bermain berhasil diselesaikan.'];
        });

        if ($result['status'] === 'already_processed') {
            $this->notify($result['message'], 'info');
        } else {
            $this->notify($result['message'], 'success');
        }
    }

    public function render(): View
    {
        return view('livewire.mitra.dashboard.dashboard-overview', [
            'profile' => $this->profile,
            'venues' => $this->venues,
            'fields' => $this->fields,
            'todayBookings' => $this->todayBookings,
            'metrics' => $this->metrics,
            'actionShortcuts' => $this->actionShortcuts,
        ]);
    }
}
