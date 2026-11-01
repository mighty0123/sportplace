<?php

namespace App\Livewire\Admin\Mitra;

use App\Models\MitraProfile;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class MitraTable extends Component
{
    use WithPagination;

    public const STATUSES = ['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'];
    public const PER_PAGE_OPTIONS = [10, 15, 30, 50];

    // Keadaan tampilan disimpan di URL
    #[Url(history: true)]
    public string $status = 'pending';

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'urut')]
    public string $sort = '';

    #[Url(as: 'per')]
    public int $perPage = 10;

    public function mount(): void
    {
        $this->authorizeAccess();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function setStatus(string $status): void
    {
        $this->status = array_key_exists($status, self::STATUSES) ? $status : 'pending';
        $this->sort = '';
        $this->resetPage();
    }

    public function toggleSort(): void
    {
        $this->sort = $this->direction() === 'asc' ? 'terbaru' : 'terlama';
        $this->resetPage();
    }

    public function clearSearch(): void
    {
        $this->search = '';
        $this->resetPage();
    }

    /** Jumlah per status */
    #[Computed]
    public function counts(): array
    {
        return MitraProfile::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->all();
    }

    /** Angka untuk strip ringkasan di atas daftar. */
    #[Computed]
    public function summary(): array
    {
        $sla = (int) config('admin.verification_sla_days', 2);
        $pending = fn () => MitraProfile::query()->where('status', 'pending');
        $oldest = $pending()->min('submitted_at');

        return [
            'pending' => $pending()->count(),
            // submitted_at <= sekarang - SLA hari sama dengan "sudah menunggu SLA hari atau lebih".
            'late'    => $pending()->where('submitted_at', '<=', now()->subDays($sla))->count(),
            'oldest'  => $oldest ? (int) floor(CarbonImmutable::parse($oldest)->diffInDays(now())) : null,
            'decided' => MitraProfile::query()
                ->whereIn('status', ['approved', 'rejected'])
                ->where('reviewed_at', '>=', now()->subDays(7))
                ->count(),
        ];
    }

    #[Computed]
    public function rows(): LengthAwarePaginator
    {
        $term = $this->term();
        $dir  = $this->direction();

        return MitraProfile::query()
            ->with(['user:id,name,email', 'reviewer:id,name']) // eager load
            ->where('status', $this->currentStatus())
            ->when($term !== '', function ($query) use ($term) {
                // Karakter khusus LIKE (% _ \) di-escape supaya dicari apa adanya.
                $like = '%' . addcslashes($term, '%_\\') . '%';
                $query->where(fn ($q) => $q
                    ->where('nama_usaha', 'like', $like)
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', $like)->orWhere('email', 'like', $like)));
            })
            ->orderBy('submitted_at', $dir)
            ->orderBy('id', $dir)
            ->paginate($this->currentPerPage());
    }

    public function pageWindow(): array
    {
        $current = $this->rows->currentPage();
        $last    = $this->rows->lastPage();

        $pages = collect([1, $last])
            ->merge(range(max(1, $current - 1), min($last, $current + 1)))
            ->unique()->sort()->values();

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
        $this->authorizeAccess();

        return view('livewire.admin.mitra.mitra-table', [
            'statuses'       => self::STATUSES,
            'status'         => $this->currentStatus(),
            'direction'      => $this->direction(),
            'sla'            => (int) config('admin.verification_sla_days', 2),
            'perPageOptions' => self::PER_PAGE_OPTIONS,
        ]);
    }

    // ---- helpers

    private function authorizeAccess(): void
    {
        Gate::authorize('viewAny', MitraProfile::class);
    }

    /** Nilai properti publik bisa diubah dari browser, jadi selalu dicocokkan dengan daftar yang diizinkan. */
    private function currentStatus(): string
    {
        return array_key_exists($this->status, self::STATUSES) ? $this->status : 'pending';
    }

    private function currentPerPage(): int
    {
        return in_array($this->perPage, self::PER_PAGE_OPTIONS, true) ? $this->perPage : self::PER_PAGE_OPTIONS[0];
    }

    private function direction(): string
    {
        $auto = $this->currentStatus() === 'pending' ? 'terlama' : 'terbaru';
        $mode = in_array($this->sort, ['terlama', 'terbaru'], true) ? $this->sort : $auto;

        return $mode === 'terlama' ? 'asc' : 'desc';
    }

    private function term(): string
    {
        return mb_substr(trim($this->search), 0, 100);
    }
}