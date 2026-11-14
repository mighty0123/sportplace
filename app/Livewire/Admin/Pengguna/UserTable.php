<?php

namespace App\Livewire\Admin\Pengguna;

use App\Services\Admin\UserAccountService as Accounts;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class UserTable extends Component
{
    use WithPagination;

    public const PER_PAGE_OPTIONS = [15, 30, 50];
    public const SORTS = ['terbaru' => 'Terbaru dulu', 'terlama' => 'Terlama dulu', 'nama' => 'Nama A sampai Z'];

    // Keadaan tampilan disimpan di URL
    #[Url(as: 'peran', history: true)]
    public string $role = '';

    #[Url(as: 'akun', history: true)]
    public string $account = '';

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'urut')]
    public string $sort = 'terbaru';

    #[Url(as: 'per')]
    public int $perPage = 15;

    public function mount(): void
    {
        Gate::authorize('manage-users');
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSort(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function setRole(string $role): void
    {
        $this->role = array_key_exists($role, Accounts::ROLES) ? $role : '';
        $this->resetPage();
    }

    public function setAccount(string $account): void
    {
        $this->account = array_key_exists($account, Accounts::STATUS) ? $account : '';
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset('role', 'account', 'search', 'sort');
        $this->resetPage();
    }

    /** Ringkasan seluruh pengguna, tidak terpengaruh filter */
    #[Computed]
    public function summary(): array
    {
        $r = DB::table('users')->selectRaw('role as k, COUNT(*) as n')->groupBy('role')->pluck('n', 'k');
        $s = DB::table('users')->selectRaw('status_akun as k, COUNT(*) as n')->groupBy('status_akun')->pluck('n', 'k');

        return [
            'total'   => (int) $r->sum(),
            'mitra'   => (int) ($r['mitra'] ?? 0),
            'customer' => (int) ($r['customer'] ?? 0),
            'flagged' => (int) (($s['suspend'] ?? 0) + ($s['banned'] ?? 0)),
        ];
    }

    /** Jumlah per peran, mengikuti filter status. */
    #[Computed]
    public function roleCounts(): array
    {
        return $this->base(withRole: false, withSearch: false)->selectRaw('u.role as k, COUNT(*) as n')->groupBy('u.role')->pluck('n', 'k')->all();
    }

    /** Jumlah per status akun, mengikuti filter peran */
    #[Computed]
    public function accountCounts(): array
    {
        return $this->base(withAccount: false, withSearch: false)->selectRaw('u.status_akun as k, COUNT(*) as n')->groupBy('u.status_akun')->pluck('n', 'k')->all();
    }

    #[Computed]
    public function rows(): LengthAwarePaginator
    {
        $q = $this->base()->selectRaw('u.id, u.name, u.email, u.role, u.status_akun, u.email_verified_at, u.created_at,
            (SELECT m.nama_usaha FROM mitra_profiles m WHERE m.user_id = u.id ORDER BY m.id LIMIT 1) as nama_usaha');

        match ($this->currentSort()) {
            'terlama' => $q->orderBy('u.created_at')->orderBy('u.id'),
            'nama'    => $q->orderBy('u.name')->orderBy('u.id'),
            default   => $q->orderByDesc('u.created_at')->orderByDesc('u.id'),
        };

        return $q->paginate($this->currentPerPage());
    }

    /** Nomor halaman */
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
        // Setiap permintaan Livewire diotorisasi sendiri: endpoint komponen bisa dipanggil langsung
        Gate::authorize('manage-users');

        return view('livewire.admin.pengguna.user-table', [
            'role'           => $this->currentRole(),
            'account'        => $this->currentAccount(),
            'sort'           => $this->currentSort(),
            'perPageOptions' => self::PER_PAGE_OPTIONS,
        ]);
    }

    // ---- helpers

    private function base(bool $withRole = true, bool $withAccount = true, bool $withSearch = true): Builder
    {
        $term = mb_substr(trim($this->search), 0, 100);
        $like = '%' . addcslashes($term, '%_\\') . '%'; // karakter khusus LIKE di-escape: dicari apa adanya

        return DB::table('users as u')
            ->when($withRole && $this->currentRole() !== '', fn ($q) => $q->where('u.role', $this->currentRole()))
            ->when($withAccount && $this->currentAccount() !== '', fn ($q) => $q->where('u.status_akun', $this->currentAccount()))
            ->when($withSearch && $term !== '', fn ($q) => $q->where(fn ($w) => $w
                ->where('u.name', 'like', $like)->orWhere('u.email', 'like', $like)->orWhere('u.no_hp', 'like', $like)
                ->orWhereExists(fn ($e) => $e->selectRaw('1')->from('mitra_profiles as m')
                    ->whereColumn('m.user_id', 'u.id')->where('m.nama_usaha', 'like', $like))));
    }

    // Nilai properti publik bisa diubah dari browser, jadi selalu dicocokkan dengan daftar yang diizinkan
    private function currentRole(): string
    {
        return array_key_exists($this->role, Accounts::ROLES) ? $this->role : '';
    }

    private function currentAccount(): string
    {
        return array_key_exists($this->account, Accounts::STATUS) ? $this->account : '';
    }

    private function currentSort(): string
    {
        return array_key_exists($this->sort, self::SORTS) ? $this->sort : 'terbaru';
    }

    private function currentPerPage(): int
    {
        return in_array($this->perPage, self::PER_PAGE_OPTIONS, true) ? $this->perPage : self::PER_PAGE_OPTIONS[0];
    }
}