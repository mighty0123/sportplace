<?php

namespace App\Livewire\Mitra\Reports;

use App\Livewire\Mitra\MitraComponent;
use App\Models\BookingDetail;
use App\Models\Venue;
use App\Services\Mitra\CommissionResolver;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportManager extends MitraComponent
{
    use WithPagination;

    #[Url(as: 'period_mode', history: true)]
    public string $periodMode = 'month';

    #[Url(history: true)]
    public string $month = '';

    #[Url(as: 'start_date', history: true)]
    public string $startDate = '';

    #[Url(as: 'end_date', history: true)]
    public string $endDate = '';

    #[Url(as: 'field_id', history: true)]
    public ?int $fieldId = null;

    #[Url(as: 'venue_id', history: true)]
    public ?int $venueId = null;

    #[Url(as: 'status_filter', history: true)]
    public string $statusFilter = 'completed';

    public int $perPage = 15;

    public function mount(): void
    {
        $this->authorizeMitraApproved();

        if ($this->month === '') {
            $this->month = Carbon::now()->format('Y-m');
        }

        if ($this->startDate === '' || $this->endDate === '') {
            $monthDate = Carbon::createFromFormat('Y-m', $this->month) ?: Carbon::now();
            $this->startDate = $monthDate->copy()->startOfMonth()->format('Y-m-d');
            $this->endDate = $monthDate->copy()->endOfMonth()->format('Y-m-d');
        }
    }

    public function updatedPeriodMode(): void
    {
        $this->resetPage();
    }

    public function updatedMonth(): void
    {
        if ($this->month !== '' && preg_match('/^\d{4}-\d{2}$/', $this->month)) {
            $monthDate = Carbon::createFromFormat('Y-m', $this->month) ?: Carbon::now();
            $this->startDate = $monthDate->copy()->startOfMonth()->format('Y-m-d');
            $this->endDate = $monthDate->copy()->endOfMonth()->format('Y-m-d');
        }
        $this->resetPage();
    }

    public function updatedStartDate(): void
    {
        $this->resetPage();
    }

    public function updatedEndDate(): void
    {
        $this->resetPage();
    }

    public function updatedFieldId(): void
    {
        $this->resetPage();
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
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function setPeriodMode(string $mode): void
    {
        if (in_array($mode, ['month', 'range'], true)) {
            $this->periodMode = $mode;
            $this->resetPage();
        }
    }

    public function resetFilters(): void
    {
        $this->periodMode = 'month';
        $this->month = Carbon::now()->format('Y-m');
        $monthDate = Carbon::createFromFormat('Y-m', $this->month) ?: Carbon::now();
        $this->startDate = $monthDate->copy()->startOfMonth()->format('Y-m-d');
        $this->endDate = $monthDate->copy()->endOfMonth()->format('Y-m-d');
        $this->fieldId = null;
        $this->venueId = null;
        $this->statusFilter = 'completed';
        $this->resetPage();
    }

    public function resolvedDateRange(): array
    {
        if ($this->periodMode === 'range' && $this->startDate !== '' && $this->endDate !== '') {
            $start = Carbon::parse($this->startDate)->startOfDay();
            $end = Carbon::parse($this->endDate)->endOfDay();
        } else {
            $monthDate = $this->month !== '' && preg_match('/^\d{4}-\d{2}$/', $this->month)
                ? (Carbon::createFromFormat('Y-m', $this->month) ?: Carbon::now())
                : Carbon::now();
            $start = $monthDate->copy()->startOfMonth()->startOfDay();
            $end = $monthDate->copy()->endOfMonth()->endOfDay();
        }

        return [$start, $end];
    }

    public function getFilteredQuery(): Builder
    {
        $profile = $this->authorizeMitraApproved();
        [$start, $end] = $this->resolvedDateRange();

        $query = BookingDetail::forMitra($profile->id)
            ->whereBetween('tanggal_sewa', [$start->toDateString(), $end->toDateString()])
            ->whereHas('header', function ($q) {
                $q->where('status', 'payment')
                    ->orWhereHas('payment', fn ($p) => $p->where('status_pembayaran', 'succes'));
            });

        if ($this->statusFilter === 'completed') {
            $query->where('status_item', 'completed');
        }

        if ($this->venueId) {
            $query->whereHas('field', fn ($f) => $f->where('venue_id', $this->venueId));
        }

        if ($this->fieldId) {
            $query->where('field_id', $this->fieldId);
        }

        return $query;
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

    public function calculateSummary(CommissionResolver $commissionResolver): array
    {
        $base = $this->getFilteredQuery();

        $totalBookings = (clone $base)->count();
        $grossRevenue = (float) (clone $base)->sum('harga_slot');

        $commissionData = $commissionResolver->calculate($grossRevenue);
        $averagePerBooking = $totalBookings > 0 ? round($grossRevenue / $totalBookings, 2) : 0;

        return [
            'total_bookings' => $totalBookings,
            'gross_revenue' => $grossRevenue,
            'rate_percent' => $commissionData['rate_percent'],
            'commission_amount' => $commissionData['commission'],
            'net_revenue' => $commissionData['net'],
            'is_configured' => $commissionData['is_configured'],
            'average_per_booking' => $averagePerBooking,
        ];
    }

    public function exportCsv(CommissionResolver $commissionResolver): StreamedResponse
    {
        [$start, $end] = $this->resolvedDateRange();

        $fileLabel = $this->periodMode === 'range'
            ? $start->format('Ymd').'-'.$end->format('Ymd')
            : $start->format('Y-m');

        $filename = "laporan-pendapatan-mitra-{$fileLabel}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $query = (clone $this->getFilteredQuery())
            ->with(['header.user', 'header.payment', 'field.venue', 'field.sport'])
            ->orderBy('tanggal_sewa', 'asc')
            ->orderBy('jam_mulai', 'asc');

        return response()->streamDownload(function () use ($query, $commissionResolver) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM untuk kompatibilitas Excel
            fwrite($handle, "\xEF\xBB\xBF");

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

            fputcsv($handle, []);
            fputcsv($handle, ['RINGKASAN TOTAL PENDAPATAN LAPORAN']);
            fputcsv($handle, ['Total Sesi Transaksi', ($iteration - 1).' Sesi']);
            fputcsv($handle, ['Total Pendapatan Kotor (Rp)', $totalGross]);
            fputcsv($handle, ['Total Komisi Platform (Rp)', $totalCommission]);
            fputcsv($handle, ['Total Pendapatan Bersih (Rp)', $totalNet]);

            fclose($handle);
        }, $filename, $headers);
    }

    public function render(CommissionResolver $commissionResolver): View
    {
        [$startDate, $endDate] = $this->resolvedDateRange();

        $transactions = (clone $this->getFilteredQuery())
            ->with(['header.user', 'header.payment', 'field.venue', 'field.sport'])
            ->orderBy('tanggal_sewa', 'desc')
            ->orderBy('jam_mulai', 'desc')
            ->paginate($this->perPage);

        return view('livewire.mitra.reports.report-manager', [
            'venues' => $this->venues,
            'allFields' => $this->allFields,
            'summary' => $this->calculateSummary($commissionResolver),
            'startDate' => $startDate,
            'endDate' => $endDate,
            'transactions' => $transactions,
            'commissionResolver' => $commissionResolver,
        ]);
    }
}
