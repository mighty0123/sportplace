<?php

namespace App\Livewire\Admin\Komisi;

use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class CommissionHistory extends Component
{
    use WithPagination;

    public const SCOPES = ['' => 'Semua lingkup', 'global' => 'Tarif global', 'mitra' => 'Tarif khusus mitra'];

    #[Url(as: 'lingkup')]
    public string $scope = '';

    #[Url(as: 'q')]
    public string $search = '';

    public function mount(): void
    {
        Gate::authorize('manage-commission');
    }

    public function updatedScope(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset('scope', 'search');
        $this->resetPage();
    }

    #[Computed]
    public function rows(): LengthAwarePaginator
    {
        $now = now()->format('Y-m-d H:i:s');
        $scope = array_key_exists($this->scope, self::SCOPES) ? $this->scope : '';
        $term = mb_substr(trim($this->search), 0, 100);
        $like = '%' . addcslashes($term, '%_\\') . '%'; // karakter khusus LIKE di-escape: dicari apa adanya

        return DB::table('commission_rates as r')
            ->leftJoin('mitra_profiles as m', 'm.id', '=', 'r.mitra_id')
            ->leftJoin('users as a', 'a.id', '=', 'r.dibuat_oleh')
            ->when($scope === 'global', fn ($q) => $q->whereNull('r.mitra_id')->whereNull('r.sport_id'))
            ->when($scope === 'mitra', fn ($q) => $q->whereNotNull('r.mitra_id'))
            ->when($term !== '', fn ($q) => $q->where(fn ($w) => $w->where('m.nama_usaha', 'like', $like)->orWhere('r.catatan', 'like', $like)))
            ->selectRaw("r.id, r.persen, r.berlaku_mulai, r.catatan, r.created_at, r.mitra_id, m.nama_usaha, a.name as admin_name,
                CASE
                    WHEN r.berlaku_mulai > ? THEN 'terjadwal'
                    WHEN NOT EXISTS (SELECT 1 FROM commission_rates r2 WHERE r2.scope_key = r.scope_key AND r2.berlaku_mulai <= ? AND r2.berlaku_mulai > r.berlaku_mulai) THEN 'berlaku'
                    ELSE 'digantikan'
                END AS state", [$now, $now])
            ->orderByDesc('r.berlaku_mulai')->orderByDesc('r.id')
            ->paginate(10);
    }

    /** Nomor halaman: 1 … 4 5 6 … 20. Null berarti elipsis. */
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
        Gate::authorize('manage-commission');

        return view('livewire.admin.komisi.commission-history', ['scopes' => self::SCOPES]);
    }
}