<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mitra\ReportFilterRequest;
use App\Models\BookingDetail;
use App\Models\Venue;
use App\Services\Mitra\CommissionResolver;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * MTR-11 / T9.1: Laporan Pendapatan dan Riwayat Transaksi Mitra (FR-REPORT-002).
     */
    public function index(ReportFilterRequest $request, CommissionResolver $commissionResolver): View
    {
        $user = Auth::user();
        $mitra = $user->mitraProfile;

        // Ambil venue dan lapangan milik mitra untuk dropdown filter
        $venues = Venue::with(['fields' => fn ($q) => $q->orderBy('nama_lapangan')])
            ->where('mitra_id', $mitra->id)
            ->get();

        $allFields = $venues->flatMap->fields;

        // Resolusi Periode Tanggal
        $periodMode = $request->query('period_mode', 'month');
        $selectedMonth = $request->query('month', Carbon::now()->format('Y-m'));
        $fieldId = $request->query('field_id');
        $statusFilter = $request->query('status_filter', 'completed'); // 'completed' (default) or 'all_paid'

        if ($periodMode === 'range' && $request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->query('start_date'))->startOfDay();
            $endDate = Carbon::parse($request->query('end_date'))->endOfDay();
        } else {
            $periodMode = 'month';
            $monthDate = Carbon::createFromFormat('Y-m', $selectedMonth) ?: Carbon::now();
            $startDate = $monthDate->copy()->startOfMonth()->startOfDay();
            $endDate = $monthDate->copy()->endOfMonth()->endOfDay();
        }

        // Query Dasar: Terisolasi hanya untuk lapangan mitra login (BR-023, FR-REPORT-002)
        $baseQuery = BookingDetail::forMitra($mitra->id)
            ->whereBetween('tanggal_sewa', [$startDate->toDateString(), $endDate->toDateString()])
            ->whereHas('header', function ($q) {
                $q->where('status', 'payment')
                    ->orWhereHas('payment', fn ($p) => $p->where('status_pembayaran', 'succes'));
            });

        // Filter status pesanan (default: hanya sesi yang telah selesai / completed)
        if ($statusFilter === 'completed') {
            $baseQuery->where('status_item', 'completed');
        }

        // Filter per Lapangan
        if ($fieldId && $allFields->contains('id', (int) $fieldId)) {
            $baseQuery->where('field_id', (int) $fieldId);
        }

        // Kalkulasi Ringkasan Finansial (T9.3 & AC: REPORT-002-01)
        $totalBookings = (clone $baseQuery)->count();
        $grossRevenue = (float) (clone $baseQuery)->sum('harga_slot');

        $commissionData = $commissionResolver->calculate($grossRevenue);
        $ratePercent = $commissionData['rate_percent'];
        $commissionAmount = $commissionData['commission'];
        $netRevenue = $commissionData['net']; // Bersih = Kotor - Komisi
        $isCommissionConfigured = $commissionData['is_configured'];

        $averagePerBooking = $totalBookings > 0 ? round($grossRevenue / $totalBookings, 2) : 0;

        // Informasi Wallet (T3.3 / FR-FIN-002)
        $wallet = $user->wallet;
        $saldoAktif = $wallet ? (float) $wallet->saldo_aktif : 0;

        // Ambil Daftar Transaksi dengan Paginasi
        $transactions = (clone $baseQuery)
            ->with(['header.user', 'header.payment', 'field.venue', 'field.sport'])
            ->orderBy('tanggal_sewa', 'desc')
            ->orderBy('jam_mulai', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('mitra.reports.index', [
            'periodMode' => $periodMode,
            'selectedMonth' => $selectedMonth,
            'startDate' => $startDate->toDateString(),
            'endDate' => $endDate->toDateString(),
            'selectedFieldId' => $fieldId,
            'statusFilter' => $statusFilter,
            'venues' => $venues,
            'allFields' => $allFields,
            'totalBookings' => $totalBookings,
            'grossRevenue' => $grossRevenue,
            'ratePercent' => $ratePercent,
            'commissionAmount' => $commissionAmount,
            'netRevenue' => $netRevenue,
            'isCommissionConfigured' => $isCommissionConfigured,
            'averagePerBooking' => $averagePerBooking,
            'saldoAktif' => $saldoAktif,
            'transactions' => $transactions,
            'commissionResolver' => $commissionResolver,
        ]);
    }

    /**
     * MTR-11 / T9.4: Ekspor Laporan Pendapatan Format CSV (FR-REPORT-002, AC: REPORT-002-01).
     */
    public function exportCsv(ReportFilterRequest $request, CommissionResolver $commissionResolver): StreamedResponse
    {
        $user = Auth::user();
        $mitra = $user->mitraProfile;

        // Resolusi Rentang Tanggal
        $periodMode = $request->query('period_mode', 'month');
        $selectedMonth = $request->query('month', Carbon::now()->format('Y-m'));
        $fieldId = $request->query('field_id');
        $statusFilter = $request->query('status_filter', 'completed');

        if ($periodMode === 'range' && $request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->query('start_date'))->startOfDay();
            $endDate = Carbon::parse($request->query('end_date'))->endOfDay();
            $fileLabel = $startDate->format('Ymd').'-'.$endDate->format('Ymd');
        } else {
            $periodMode = 'month';
            $monthDate = Carbon::createFromFormat('Y-m', $selectedMonth) ?: Carbon::now();
            $startDate = $monthDate->copy()->startOfMonth()->startOfDay();
            $endDate = $monthDate->copy()->endOfMonth()->endOfDay();
            $fileLabel = $monthDate->format('Y-m');
        }

        // Query Data Terisolasi
        $query = BookingDetail::forMitra($mitra->id)
            ->whereBetween('tanggal_sewa', [$startDate->toDateString(), $endDate->toDateString()])
            ->whereHas('header', function ($q) {
                $q->where('status', 'payment')
                    ->orWhereHas('payment', fn ($p) => $p->where('status_pembayaran', 'succes'));
            })
            ->with(['header.user', 'header.payment', 'field.venue', 'field.sport'])
            ->orderBy('tanggal_sewa', 'asc')
            ->orderBy('jam_mulai', 'asc');

        if ($statusFilter === 'completed') {
            $query->where('status_item', 'completed');
        }

        if ($fieldId) {
            $query->where('field_id', (int) $fieldId);
        }

        $filename = "laporan-pendapatan-mitra-{$fileLabel}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($query, $commissionResolver) {
            $handle = fopen('php://output', 'w');

            // Tambahkan UTF-8 BOM untuk kompatibilitas sempurna di Microsoft Excel
            fputs($handle, "\xEF\xBB\xBF");

            // Header Kolom CSV
            fputcsv($handle, [
                'No.',
                'No. Booking',
                'ID Detail',
                'Tanggal Sesi',
                'Jam Mulai',
                'Jam Selesai',
                'Venue',
                'Lapangan',
                'Cabang Olahraga',
                'Penyewa',
                'Metode Pembayaran',
                'Kode Transaksi PG',
                'Tarif Slot / Pendapatan Kotor (Rp)',
                'Komisi Platform (Rp)',
                'Pendapatan Bersih (Rp)',
                'Status Sesi',
            ]);

            $iteration = 1;
            $totalGross = 0.0;
            $totalCommission = 0.0;
            $totalNet = 0.0;

            // Stream tiap baris data
            foreach ($query->cursor() as $detail) {
                $calc = $commissionResolver->calculateForDetail($detail);
                $gross = $calc['gross'];
                $commission = $calc['commission'];
                $net = $calc['net'];

                $totalGross += $gross;
                $totalCommission += $commission;
                $totalNet += $net;

                fputcsv($handle, [
                    $iteration++,
                    "#{$detail->header_id}",
                    "#{$detail->id}",
                    $detail->tanggal_sewa?->format('Y-m-d') ?? '-',
                    substr($detail->jam_mulai, 0, 5),
                    substr($detail->jam_selesai, 0, 5),
                    $detail->field?->venue?->nama_venue ?? '-',
                    $detail->field?->nama_lapangan ?? '-',
                    $detail->field?->sport?->nama_olahraga ?? '-',
                    $detail->header?->user?->name ?? 'Penyewa',
                    $detail->header?->payment?->metode_pembayaran ?? 'Online Payment',
                    $detail->header?->payment?->pg_transaction_id ?? '-',
                    $gross,
                    $commission,
                    $net,
                    strtoupper($detail->status_item),
                ]);
            }

            // Baris Pemisah dan Ringkasan Total (AC: REPORT-002-01)
            fputcsv($handle, []);
            fputcsv($handle, ['RINGKASAN TOTAL PENDAPATAN LAPORAN']);
            fputcsv($handle, ['Total Sesi Transaksi', ($iteration - 1).' Sesi']);
            fputcsv($handle, ['Total Pendapatan Kotor (Rp)', $totalGross]);
            fputcsv($handle, ['Total Komisi Platform (Rp)', $totalCommission]);
            fputcsv($handle, ['Total Pendapatan Bersih (Rp)', $totalNet]); // AC: REPORT-002-01 (Bersih = Kotor - Komisi)

            fclose($handle);
        }, 200, $headers);
    }
}
