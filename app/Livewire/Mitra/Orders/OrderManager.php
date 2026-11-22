<?php

namespace App\Livewire\Mitra\Orders;

use App\Livewire\Mitra\MitraComponent;
use App\Models\AuditLog;
use App\Models\BookingDetail;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class OrderManager extends MitraComponent
{
    use WithPagination;

    // Filter Query Strings
    #[Url(history: true)]
    public string $search = '';

    #[Url(as: 'field_id', history: true)]
    public ?int $fieldId = null;

    #[Url(as: 'venue_id', history: true)]
    public ?int $venueId = null;

    #[Url(history: true)]
    public string $status = '';

    #[Url(as: 'payment_status', history: true)]
    public string $paymentStatus = '';

    #[Url(history: true)]
    public string $date = '';

    #[Url(as: 'view_mode', history: true)]
    public string $viewMode = 'table';

    #[Url(history: true)]
    public string $filter = '';

    public int $perPage = 15;

    // Bulk Completion State
    public array $selectedOrderIds = [];

    public bool $selectAll = false;

    public bool $showBulkConfirmModal = false;

    // Single Complete Confirmation Modal
    public bool $showCompleteConfirmModal = false;

    public ?int $completingOrderId = null;

    // Detail Modal State (MTR-09)
    public bool $showDetailModal = false;

    public ?int $viewingOrderId = null;

    public function mount(): void
    {
        $this->authorizeMitraApproved();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->resetBulkSelection();
    }

    public function updatedFieldId(): void
    {
        $this->resetPage();
        $this->resetBulkSelection();
    }

    public function updatedVenueId(): void
    {
        if ($this->venueId && $this->fieldId) {
            $field = $this->allFields()->firstWhere('id', $this->fieldId);
            if ($field && $field->venue_id !== $this->venueId) {
                $this->fieldId = null;
            }
        }
        $this->resetPage();
        $this->resetBulkSelection();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
        $this->resetBulkSelection();
    }

    public function updatedPaymentStatus(): void
    {
        $this->resetPage();
        $this->resetBulkSelection();
    }

    public function updatedDate(): void
    {
        $this->resetPage();
        $this->resetBulkSelection();
    }

    public function updatedFilter(): void
    {
        $this->resetPage();
        $this->resetBulkSelection();
    }

    public function setViewMode(string $mode): void
    {
        if (in_array($mode, ['table', 'calendar'], true)) {
            $this->viewMode = $mode;
            $this->resetPage();
            $this->resetBulkSelection();
        }
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'fieldId', 'venueId', 'status', 'paymentStatus', 'date', 'filter']);
        $this->resetPage();
        $this->resetBulkSelection();
    }

    public function toggleReadyToCompleteFilter(): void
    {
        if ($this->filter === 'ready_to_complete') {
            $this->filter = '';
        } else {
            $this->filter = 'ready_to_complete';
        }
        $this->resetPage();
        $this->resetBulkSelection();
    }

    // Calendar Navigation
    public function prevDay(): void
    {
        $current = $this->currentCalendarDate();
        $this->date = $current->copy()->subDay()->format('Y-m-d');
    }

    public function nextDay(): void
    {
        $current = $this->currentCalendarDate();
        $this->date = $current->copy()->addDay()->format('Y-m-d');
    }

    public function goToToday(): void
    {
        $this->date = Carbon::today()->format('Y-m-d');
    }

    public function currentCalendarDate(): Carbon
    {
        if (! empty($this->date) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $this->date)) {
            return Carbon::parse($this->date);
        }

        return Carbon::today();
    }

    // Bulk Selection Helpers
    public function selectAllOnPage(array $pageIds): void
    {
        $ids = array_map('intval', $pageIds);
        $this->selectedOrderIds = array_values(array_unique(array_merge($this->selectedOrderIds, $ids)));
        $this->selectAll = true;
    }

    public function resetBulkSelection(): void
    {
        $this->selectedOrderIds = [];
        $this->selectAll = false;
        $this->showBulkConfirmModal = false;
    }

    public function openBulkModal(): void
    {
        if (empty($this->selectedOrderIds)) {
            $this->notify('Silakan pilih minimal satu pesanan untuk diselesaikan.', 'error');

            return;
        }

        $this->showBulkConfirmModal = true;
    }

    public function closeBulkModal(): void
    {
        $this->showBulkConfirmModal = false;
    }

    // Detail Modal (MTR-09)
    public function openDetail(int $orderId): void
    {
        $profile = $this->authorizeMitraApproved();
        $order = BookingDetail::with(['field.venue'])->find($orderId);

        if (! $order || $order->field?->venue?->mitra_id !== $profile->id) {
            $this->notify('Pesanan tidak ditemukan atau Anda tidak memiliki akses.', 'error');

            return;
        }

        $this->viewingOrderId = $orderId;
        $this->showDetailModal = true;
    }

    public function closeDetail(): void
    {
        $this->showDetailModal = false;
        $this->viewingOrderId = null;
    }

    // Single Actions (MTR-09 / T8.4 & T8.5)
    public function confirmAttendance(int $orderId): void
    {
        $order = BookingDetail::with(['field.venue'])->findOrFail($orderId);
        $this->ensureOwnsBookingDetail($order);

        if (! $order->isPaid()) {
            $this->notify('Kehadiran tidak dapat dikonfirmasi karena pembayaran pesanan belum lunas.', 'error');

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
                'Mitra mengonfirmasi kehadiran penyewa di lapangan.'
            );
        });

        $this->notify('Kehadiran penyewa berhasil dikonfirmasi. Sesi bermain sedang berlangsung.', 'success');
    }

    public function markNoShow(int $orderId): void
    {
        $order = BookingDetail::with(['field.venue'])->findOrFail($orderId);
        $this->ensureOwnsBookingDetail($order);

        if (! $order->isPaid()) {
            $this->notify('Pesanan yang belum lunas tidak dapat ditandai No-Show.', 'error');

            return;
        }

        if (Carbon::now()->lt($order->getStartDateTime())) {
            $this->notify('Penandaan No-Show hanya dapat dilakukan setelah sesi bermain dimulai ('.$order->jam_mulai.').', 'error');

            return;
        }

        $result = DB::transaction(function () use ($order) {
            /** @var BookingDetail $locked */
            $locked = BookingDetail::lockForUpdate()->findOrFail($order->id);

            if ($locked->status_item === 'no_show') {
                return ['status' => 'already_processed', 'message' => 'Pesanan ini sudah ditandai No-Show sebelumnya.'];
            }

            if ($locked->status_item === 'completed') {
                return ['status' => 'error', 'message' => 'Pesanan ini sudah diselesaikan dan tidak dapat diubah menjadi No-Show.'];
            }

            $oldStatus = $locked->status_item;
            $locked->update(['status_item' => 'no_show']);

            AuditLog::record(
                Auth::user(),
                'BookingDetail',
                $locked->id,
                'mark_no_show',
                $oldStatus,
                'no_show',
                'Penyewa tidak hadir pada sesi bermain yang telah dijadwalkan.'
            );

            return ['status' => 'success', 'message' => 'Pesanan berhasil ditandai sebagai No-Show (Tidak Hadir).'];
        });

        if ($result['status'] === 'error') {
            $this->notify($result['message'], 'error');
        } elseif ($result['status'] === 'already_processed') {
            $this->notify($result['message'], 'info');
        } else {
            $this->notify($result['message'], 'success');
        }
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
            $this->completeOrder($orderId);
        }
    }

    public function completeOrder(int $orderId): void
    {
        $order = BookingDetail::with(['field.venue'])->findOrFail($orderId);
        $this->ensureOwnsBookingDetail($order);

        if (! $order->isPaid()) {
            $this->notify('Pesanan tidak dapat diselesaikan karena belum berstatus lunas.', 'error');

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
                'Sesi bermain berhasil diselesaikan oleh mitra.'
            );

            return ['status' => 'success', 'message' => 'Sesi bermain berhasil diselesaikan.'];
        });

        if ($result['status'] === 'already_processed') {
            $this->notify($result['message'], 'info');
        } else {
            $this->notify($result['message'], 'success');
        }
    }

    // Bulk Completion (MTR-10 / T8.6)
    public function bulkComplete(): void
    {
        $user = Auth::user();
        $profile = $this->authorizeMitraApproved();

        if (empty($this->selectedOrderIds)) {
            $this->notify('Tidak ada pesanan yang dipilih.', 'error');
            $this->showBulkConfirmModal = false;

            return;
        }

        $completedCount = 0;
        $skippedCount = 0;
        $skippedReasons = [];

        foreach ($this->selectedOrderIds as $id) {
            try {
                $completed = DB::transaction(function () use ($id, $profile, $user, &$skippedReasons) {
                    /** @var BookingDetail|null $locked */
                    $locked = BookingDetail::lockForUpdate()
                        ->with(['header.payment', 'field.venue'])
                        ->find($id);

                    if (! $locked) {
                        $skippedReasons[] = "#{$id}: Pesanan tidak ditemukan.";

                        return false;
                    }

                    if ($locked->field?->venue?->mitra_id !== $profile->id) {
                        $skippedReasons[] = "#{$id}: Akses ditolak (bukan milik fasilitas Anda).";

                        return false;
                    }

                    if ($locked->status_item === 'completed') {
                        $skippedReasons[] = "#{$id}: Sudah diselesaikan sebelumnya.";

                        return false;
                    }

                    if (! $locked->isPaid()) {
                        $skippedReasons[] = "#{$id}: Pembayaran belum lunas.";

                        return false;
                    }

                    if (Carbon::now()->lt($locked->getEndDateTime())) {
                        $skippedReasons[] = "#{$id}: Jam sewa belum berakhir ({$locked->jam_selesai}).";

                        return false;
                    }

                    $oldStatus = $locked->status_item;
                    $locked->update(['status_item' => 'completed']);

                    AuditLog::record(
                        $user,
                        'BookingDetail',
                        $locked->id,
                        'bulk_complete_order',
                        $oldStatus,
                        'completed',
                        'Diselesaikan melalui aksi massal (Bulk Completion).'
                    );

                    return true;
                });

                if ($completed) {
                    $completedCount++;
                } else {
                    $skippedCount++;
                }
            } catch (\Throwable $e) {
                $skippedCount++;
                $skippedReasons[] = "#{$id}: Terjadi kesalahan internal ({$e->getMessage()}).";
            }
        }

        $this->resetBulkSelection();

        if ($completedCount > 0 && $skippedCount === 0) {
            $this->notify("Berhasil menyelesaikan {$completedCount} pesanan secara massal.", 'success');
        } elseif ($completedCount > 0 && $skippedCount > 0) {
            $reasonText = implode(' ', array_slice($skippedReasons, 0, 3));
            $this->notify("{$completedCount} pesanan diselesaikan, {$skippedCount} dilewati: {$reasonText}", 'warning');
        } else {
            $reasonText = implode(' ', array_slice($skippedReasons, 0, 3));
            $this->notify("Tidak ada pesanan yang dapat diselesaikan: {$reasonText}", 'error');
        }
    }

    #[Computed]
    public function venues(): Collection
    {
        $profile = $this->authorizeMitraApproved();

        return Venue::with(['fields' => function ($q) {
            $q->with('sport')->orderBy('nama_lapangan');
        }])
            ->where('mitra_id', $profile->id)
            ->orderBy('nama_venue')
            ->get();
    }

    #[Computed]
    public function allFields(): Collection
    {
        return $this->venues()->flatMap->fields;
    }

    #[Computed]
    public function metrics(): array
    {
        $profile = $this->authorizeMitraApproved();
        $query = BookingDetail::forMitra($profile->id);

        return [
            'total' => (clone $query)->count(),
            'ongoing' => (clone $query)->where('status_item', 'ongoing')->count(),
            'ready_to_complete' => (clone $query)->readyToComplete()->count(),
            'completed' => (clone $query)->where('status_item', 'completed')->count(),
            'no_show' => (clone $query)->where('status_item', 'no_show')->count(),
        ];
    }

    public function render(): View
    {
        $profile = $this->authorizeMitraApproved();

        $baseQuery = BookingDetail::forMitra($profile->id);

        if ($this->venueId) {
            $baseQuery->whereHas('field', fn ($f) => $f->where('venue_id', $this->venueId));
        }

        if ($this->fieldId) {
            $baseQuery->where('field_id', $this->fieldId);
        }

        if ($this->status !== '' && in_array($this->status, ['ongoing', 'completed', 'no_show'], true)) {
            $baseQuery->where('status_item', $this->status);
        }

        if ($this->filter === 'ready_to_complete') {
            $baseQuery->readyToComplete();
        }

        if ($this->paymentStatus !== '' && in_array($this->paymentStatus, ['payment', 'pending', 'canceled', 'expired'], true)) {
            $baseQuery->whereHas('header', fn ($q) => $q->where('status', $this->paymentStatus));
        }

        if ($this->date !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $this->date)) {
            $baseQuery->whereDate('tanggal_sewa', $this->date);
        }

        if ($this->search !== '') {
            $search = trim($this->search);
            $baseQuery->where(function ($q) use ($search) {
                if (is_numeric($search)) {
                    $q->where('id', (int) $search)
                        ->orWhere('header_id', (int) $search);
                }

                $q->orWhereHas('header.user', fn ($u) => $u->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('header.payment', fn ($p) => $p->where('pg_transaction_id', 'like', "%{$search}%"));
            });
        }

        // Calendar vs Table View
        $selectedDate = $this->currentCalendarDate();
        $calendarBookings = collect();
        $orders = null;

        if ($this->viewMode === 'calendar') {
            $calendarBookings = (clone $baseQuery)
                ->whereDate('tanggal_sewa', $selectedDate->toDateString())
                ->with(['header.user', 'header.payment', 'field.venue', 'field.sport'])
                ->orderBy('jam_mulai')
                ->get();
        } else {
            $orders = (clone $baseQuery)
                ->with(['header.user', 'header.payment', 'field.venue', 'field.sport'])
                ->orderBy('tanggal_sewa', 'desc')
                ->orderBy('jam_mulai', 'desc')
                ->paginate($this->perPage);
        }

        // Detail Modal Data
        $viewingOrder = null;
        $auditLogs = collect();

        if ($this->showDetailModal && $this->viewingOrderId) {
            $viewingOrder = BookingDetail::with([
                'header.user',
                'header.payment',
                'field.venue.city',
                'field.sport',
                'field.photos',
            ])->find($this->viewingOrderId);

            if ($viewingOrder && $viewingOrder->field?->venue?->mitra_id === $profile->id) {
                $auditLogs = AuditLog::where('entitas_type', 'BookingDetail')
                    ->where('entitas_id', $this->viewingOrderId)
                    ->orderBy('created_at', 'desc')
                    ->get();
            } else {
                $viewingOrder = null;
            }
        }

        return view('livewire.mitra.orders.order-manager', [
            'venues' => $this->venues,
            'allFields' => $this->allFields,
            'metrics' => $this->metrics,
            'selectedDate' => $selectedDate,
            'calendarBookings' => $calendarBookings,
            'orders' => $orders,
            'viewingOrder' => $viewingOrder,
            'auditLogs' => $auditLogs,
        ]);
    }
}
