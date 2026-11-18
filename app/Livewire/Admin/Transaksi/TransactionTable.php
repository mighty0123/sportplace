<?php

namespace App\Livewire\Admin\Transaksi;

use App\Services\Admin\TransactionMonitor as Monitor;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class TransactionTable extends Component
{
    use WithPagination;

    public const PER_PAGE_OPTIONS = [15, 30, 50];

    // Keadaan tampilan disimpan di URL
    #[Url(as: 'p', history: true)]
    public string $period = '30';

    #[Url(as: 'dari')]
    public string $from = '';

    #[Url(as: 'sampai')]
    public string $to = '';

    #[Url(history: true)]
    public string $status = '';

    #[Url(as: 'metode')]
    public string $method = '';

    #[Url(as: 'cek')]
    public bool $flagged = false;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'urut')]
    public string $sort = 'terbaru';

    #[Url(as: 'per')]
    public int $perPage = 15;

    public function mount(): void
    {
        Gate::authorize('view-transactions');
    }

    /** Setiap perubahan filter kembali ke halaman pertama */
    public function updated(string $name): void
    {
        if (in_array($name, ['period', 'from', 'to', 'method', 'flagged', 'search', 'sort', 'perPage'], true)) {
            $this->resetPage();
        }
    }

    public function setStatus(string $status): void
    {
        $this->status = array_key_exists($status, Monitor::BOOKING_STATUS) ? $status : '';
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset('period', 'from', 'to', 'status', 'method', 'flagged', 'search', 'sort');
        $this->resetPage();
    }

    public function clearSearch(): void
    {
        $this->search = '';
        $this->resetPage();
    }

    #[Computed]
    public function f(): array
    {
        return app(Monitor::class)->filters([
            'period' => $this->period, 'from' => $this->from, 'to' => $this->to, 'status' => $this->status,
            'method' => $this->method, 'flagged' => $this->flagged,
            'search' => $this->search, 'sort' => $this->sort,
        ]);
    }

    #[Computed]
    public function rows(): LengthAwarePaginator
    {
        return app(Monitor::class)->rows($this->f, $this->currentPerPage());
    }

    #[Computed]
    public function counts(): array
    {
        return app(Monitor::class)->statusCounts($this->f);
    }

    #[Computed]
    public function summary(): array
    {
        return app(Monitor::class)->summary($this->f);
    }

    #[Computed]
    public function methods(): array
    {
        return app(Monitor::class)->methods();
    }

    public function hasActiveFilters(): bool
    {
        $f = $this->f;

        return $f['period'] !== '30' || $f['status'] !== '' || $f['method'] !== ''
            || $f['flagged'] || $f['search'] !== '' || $f['sort'] !== 'terbaru';
    }

    public function pageWindow(): array
    {
        $current = $this->rows->currentPage();
        $last    = $this->rows->lastPage();
        $pages   = collect([1, $last])->merge(range(max(1, $current - 1), min($last, $current + 1)))->unique()->sort()->values();

        $out = [];
        $prev = null;
        foreach ($pages as $p) {
            if ($prev !== null && $p - $prev > 1) {
                $out[] = null;
            }
            $out[] = $p;
            $prev = $p;
        }

        return $out;
    }

    public function render(): View
    {
        Gate::authorize('view-transactions');

        return view('livewire.admin.transaksi.transaction-table', [
            'monitor'        => app(Monitor::class),
            'rangeError'     => app(Monitor::class)->range($this->f)[2],
            'perPageOptions' => self::PER_PAGE_OPTIONS,
        ]);
    }

    private function currentPerPage(): int
    {
        return in_array($this->perPage, self::PER_PAGE_OPTIONS, true) ? $this->perPage : self::PER_PAGE_OPTIONS[0];
    }
}