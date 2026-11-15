<?php

namespace App\Livewire\Admin\Laporan;

use App\Services\Admin\Reports\ReportData;
use App\Services\Admin\Reports\ReportService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

class ReportBuilder extends Component
{
    // Pilihan disimpan di URL: bisa dibagikan, dan tombol kembali browser bekerja
    #[Url(as: 'jenis', history: true)]
    public string $type = 'transaksi';

    #[Url(as: 'p')]
    public string $period = 'bulan-ini';

    #[Url(as: 'dari')]
    public string $from = '';

    #[Url(as: 'sampai')]
    public string $to = '';

    #[Url(as: 'status')]
    public string $status = '';

    #[Url(as: 'metode')]
    public string $method = '';

    #[Url(as: 'peran')]
    public string $role = '';

    #[Url(as: 'akun')]
    public string $account = '';

    public function mount(): void
    {
        Gate::authorize('view-reports');
    }

    public function setType(string $type): void
    {
        $this->type = array_key_exists($type, ReportService::TYPES) ? $type : 'transaksi';
        $this->period = app(ReportService::class)->defaultPeriod($this->type);
        $this->reset('from', 'to', 'status', 'method', 'role', 'account');
    }

    /** Filter yang sudah dinormalkan, sama persis dengan yang dipakai saat mengunduh */
    #[Computed]
    public function filters(): array
    {
        return app(ReportService::class)->normalize($this->type, [
            'period' => $this->period, 'from' => $this->from, 'to' => $this->to, 'status' => $this->status,
            'method' => $this->method, 'role' => $this->role, 'account' => $this->account,
        ]);
    }

    #[Computed]
    public function report(): ReportData
    {
        return app(ReportService::class)->data($this->type, $this->filters, (int) config('reports.preview_rows', 8));
    }

    #[Computed]
    public function methods(): array
    {
        return app(\App\Services\Admin\TransactionMonitor::class)->methods();
    }

    public function render(): View
    {
        // Setiap permintaan Livewire diotorisasi sendiri: endpoint komponen bisa dipanggil langsung
        Gate::authorize('view-reports');

        if (! array_key_exists($this->type, ReportService::TYPES)) {
            $this->type = 'transaksi';
        }

        return view('livewire.admin.laporan.report-builder', [
            'types' => ReportService::TYPES,
            'svc'   => app(ReportService::class),
        ]);
    }
}