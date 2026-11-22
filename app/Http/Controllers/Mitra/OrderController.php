<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mitra\BulkCompleteOrderRequest;
use App\Models\AuditLog;
use App\Models\BookingDetail;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * MTR-08 / T8.1: Daftar dan Kalender Pesanan Masuk (FR-ORDER-001).
     */
    public function index(): View
    {
        return view('mitra.orders.index');
    }

    /**
     * MTR-09 / T8.2: Detail Pesanan Masuk.
     */
    public function show(BookingDetail $order): View
    {
        // Otorisasi Kepemilikan (BR-023, Policy: view) -> 403 jika milik orang lain
        $this->authorize('view', $order);

        $order->load([
            'header.user',
            'header.payment',
            'field.venue.city',
            'field.sport',
            'field.photos',
        ]);

        // Riwayat Audit Log untuk pesanan ini (NFR-LOG-001)
        $auditLogs = AuditLog::where('entitas_type', 'BookingDetail')
            ->where('entitas_id', $order->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('mitra.orders.show', [
            'order' => $order,
            'auditLogs' => $auditLogs,
        ]);
    }

    /**
     * MTR-09 / T8.4: Konfirmasi Kehadiran Penyewa (FR-ORDER-002).
     */
    public function confirmAttendance(BookingDetail $order): RedirectResponse
    {
        $this->authorize('updateStatus', $order);

        if (! $order->isPaid()) {
            return back()->with('error', 'Kehadiran tidak dapat dikonfirmasi karena pembayaran pesanan belum lunas.');
        }

        DB::transaction(function () use ($order) {
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

        return back()->with('success', 'Kehadiran penyewa berhasil dikonfirmasi. Sesi bermain sedang berlangsung.');
    }

    /**
     * MTR-09 / T8.4: Tandai Tidak Hadir (No-Show) (FR-ORDER-002, AC: ORDER-002-01).
     * Aturan: Hanya boleh setelah jam_mulai jadwal hari ini terlewati.
     */
    public function markNoShow(BookingDetail $order): RedirectResponse
    {
        $this->authorize('updateStatus', $order);

        if (! $order->isPaid()) {
            return back()->with('error', 'Pesanan yang belum lunas tidak dapat ditandai No-Show.');
        }

        // Guard Waktu (FR-ORDER-002): Tidak boleh menandai no_show sebelum sesi dimulai
        if (Carbon::now()->lt($order->getStartDateTime())) {
            return back()->with('error', 'Penandaan No-Show hanya dapat dilakukan setelah sesi bermain dimulai ('.$order->jam_mulai.').');
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
            return back()->with('error', $result['message']);
        }

        if ($result['status'] === 'already_processed') {
            return back()->with('info', $result['message']);
        }

        return back()->with('success', $result['message']);
    }

    /**
     * MTR-09 / T8.5: Selesaikan Transaksi Per Sesi (FR-ORDER-003, AC: ORDER-003-02).
     * Aturan: Hanya boleh jika jam_selesai sudah lewat dan booking terbayar.
     */
    public function complete(BookingDetail $order): RedirectResponse
    {
        $this->authorize('updateStatus', $order);

        if (! $order->isPaid()) {
            return back()->with('error', 'Pesanan tidak dapat diselesaikan karena belum berstatus lunas.');
        }

        // Guard Waktu (FR-ORDER-003, AC: ORDER-003-02): Jam sewa belum berakhir -> ditolak
        if (Carbon::now()->lt($order->getEndDateTime())) {
            return back()->with('error', 'Pesanan hanya dapat diselesaikan setelah jam sewa berakhir ('.$order->jam_selesai.').');
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
                'Sesi bermain berhasil diselesaikan oleh mitra. Hak ulasan penyewa aktif.'
            );

            return ['status' => 'success', 'message' => 'Sesi bermain berhasil diselesaikan. Hak ulasan penyewa kini aktif.'];
        });

        if ($result['status'] === 'already_processed') {
            return back()->with('info', $result['message']);
        }

        return back()->with('success', $result['message']);
    }

    /**
     * MTR-10 / T8.6: Bulk Completion — Penyelesaian Massal (FR-ORDER-003, AC: ORDER-003-01, EC-24).
     * Memproses item valid secara atomik per transaksi DB agar kegagalan 1 item tidak membatalkan item lain.
     */
    public function bulkComplete(BulkCompleteOrderRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $mitra = $user->mitraProfile;
        $detailIds = $request->validated('detail_ids');

        $completedCount = 0;
        $skippedCount = 0;
        $skippedReasons = [];

        foreach ($detailIds as $id) {
            try {
                $completed = DB::transaction(function () use ($id, $mitra, $user, &$skippedReasons) {
                    /** @var BookingDetail|null $locked */
                    $locked = BookingDetail::lockForUpdate()
                        ->with(['header.payment', 'field.venue'])
                        ->find($id);

                    if (! $locked) {
                        $skippedReasons[] = "#{$id}: Pesanan tidak ditemukan.";

                        return false;
                    }

                    // Guard Kepemilikan (BR-023)
                    if ($locked->field?->venue?->mitra_id !== $mitra->id) {
                        $skippedReasons[] = "#{$id}: Akses ditolak (bukan milik fasilitas Anda).";

                        return false;
                    }

                    // Guard Status Selesai
                    if ($locked->status_item === 'completed') {
                        $skippedReasons[] = "#{$id}: Sudah diselesaikan sebelumnya.";

                        return false;
                    }

                    // Guard Status Pembayaran
                    if (! $locked->isPaid()) {
                        $skippedReasons[] = "#{$id}: Pembayaran belum lunas.";

                        return false;
                    }

                    // Guard Jam Selesai (AC: ORDER-003-02)
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

        // Susun Pesan Ringkasan Feedback (EC-24)
        if ($completedCount > 0 && $skippedCount === 0) {
            return back()->with('success', "Berhasil menyelesaikan {$completedCount} pesanan secara massal.");
        }

        if ($completedCount > 0 && $skippedCount > 0) {
            $reasonText = implode(' ', array_slice($skippedReasons, 0, 3));

            return back()->with('warning', "{$completedCount} pesanan berhasil diselesaikan, namun {$skippedCount} pesanan dilewati: {$reasonText}");
        }

        $reasonText = implode(' ', array_slice($skippedReasons, 0, 3));

        return back()->with('error', "Tidak ada pesanan yang dapat diselesaikan: {$reasonText}");
    }
}
