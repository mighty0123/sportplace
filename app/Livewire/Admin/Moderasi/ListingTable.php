<?php

namespace App\Livewire\Admin\Moderasi;

use App\Services\Admin\ListingModerationService as Moderation;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ListingTable extends Component
{
    use WithPagination;

    public const STATUSES = ['pending_review' => 'Menunggu', 'aktif' => 'Aktif', 'rejected' => 'Ditolak'];
    public const PER_PAGE_OPTIONS = [15, 30, 50];

    // Keadaan tampilan disimpan di URL
    #[Url(history: true)]
    public string $jenis = 'venue';

    #[Url(history: true)]
    public string $status = 'pending_review';

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'urut')]
    public string $sort = '';

    #[Url(as: 'per')]
    public int $perPage = 15;

    public function mount(): void
    {
        Gate::authorize('moderate-listings');
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function setJenis(string $jenis): void
    {
        $this->jenis = array_key_exists($jenis, Moderation::TYPES) ? $jenis : 'venue';
        $this->sort = '';
        $this->resetPage();
    }

    public function setStatus(string $status): void
    {
        $this->status = array_key_exists($status, self::STATUSES) ? $status : 'pending_review';
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

    /** Jumlah per status untuk kedua jenis, dua query. */
    #[Computed]
    public function counts(): array
    {
        $out = [];
        foreach (Moderation::TYPES as $key => $t) {
            $out[$key] = DB::table($t['table'])->selectRaw("{$t['status']} as s, COUNT(*) as n")
                ->groupBy($t['status'])->pluck('n', 's')->all();
        }

        return $out;
    }

    /** Angka untuk strip ringkasan, gabungan venue dan lapangan. */
    #[Computed]
    public function summary(): array
    {
        $limit = now()->subDays($this->sla());
        $week  = now()->subDays(7);
        $since = 'COALESCE(submitted_at, updated_at, created_at)';
        $pending = $late = $decided = 0;
        $oldest = null;

        foreach (Moderation::TYPES as $t) {
            $table = fn () => DB::table($t['table']);
            $pending += $table()->where($t['status'], 'pending_review')->count();
            $late    += $table()->where($t['status'], 'pending_review')->whereRaw("{$since} <= ?", [$limit])->count();
            $decided += $table()->whereIn($t['status'], ['aktif', 'rejected'])->where('reviewed_at', '>=', $week)->count();
            $min = $table()->where($t['status'], 'pending_review')->selectRaw("MIN({$since}) as t")->value('t');
            if ($min && (! $oldest || $min < $oldest)) {
                $oldest = $min;
            }
        }

        return [
            'pending' => $pending,
            'late'    => $late,
            'decided' => $decided,
            'oldest'  => $oldest ? (int) floor(CarbonImmutable::parse($oldest)->diffInDays(now())) : null,
        ];
    }

    #[Computed]
    public function rows(): LengthAwarePaginator
    {
        $term = mb_substr(trim($this->search), 0, 100);
        $like = '%' . addcslashes($term, '%_\\') . '%';
        $dir  = $this->direction();

        if ($this->currentJenis() === 'venue') {
            $query = DB::table('venues as v')
                ->join('mitra_profiles as m', 'm.id', '=', 'v.mitra_id')
                ->join('users as u', 'u.id', '=', 'm.user_id')
                ->join('master_cities as c', 'c.id', '=', 'v.city_id')
                ->leftJoin('users as r', 'r.id', '=', 'v.reviewed_by')
                ->where('v.status_vanue', $this->currentStatus())
                ->selectRaw('v.id, v.nama_venue, v.alasan_penolakan, v.reviewed_at,
                    COALESCE(v.submitted_at, v.updated_at, v.created_at) as waiting_since,
                    m.nama_usaha, u.name as owner_name, c.nama_kota, r.name as reviewer_name,
                    (SELECT COUNT(*) FROM fields fx WHERE fx.venue_id = v.id) as field_count')
                ->when($term !== '', fn ($q) => $q->where(fn ($w) => $w
                    ->where('v.nama_venue', 'like', $like)->orWhere('m.nama_usaha', 'like', $like)->orWhere('u.name', 'like', $like)))
                ->orderBy('waiting_since', $dir)->orderBy('v.id', $dir);
        } else {
            $query = DB::table('fields as f')
                ->join('venues as v', 'v.id', '=', 'f.venue_id')
                ->join('mitra_profiles as m', 'm.id', '=', 'v.mitra_id')
                ->join('users as u', 'u.id', '=', 'm.user_id')
                ->join('master_sports as s', 's.id', '=', 'f.sport_id')
                ->leftJoin('users as r', 'r.id', '=', 'f.reviewed_by')
                ->where('f.status_lapangan', $this->currentStatus())
                ->selectRaw('f.id, f.nama_lapangan, f.harga_perjam, f.alasan_penolakan, f.reviewed_at,
                    COALESCE(f.submitted_at, f.updated_at, f.created_at) as waiting_since,
                    v.nama_venue, v.status_vanue as venue_status, m.nama_usaha, u.name as owner_name,
                    s.nama_olahraga, r.name as reviewer_name,
                    (SELECT COUNT(*) FROM field_photo p WHERE p.field_id = f.id) as photo_count')
                ->when($term !== '', fn ($q) => $q->where(fn ($w) => $w
                    ->where('f.nama_lapangan', 'like', $like)->orWhere('v.nama_venue', 'like', $like)
                    ->orWhere('m.nama_usaha', 'like', $like)->orWhere('u.name', 'like', $like)))
                ->orderBy('waiting_since', $dir)->orderBy('f.id', $dir);
        }

        return $query->paginate($this->currentPerPage());
    }

    /** Nomor halaman: 1 … 4 5 6 … 20 */
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
        // Setiap permintaan Livewire diotorisasi sendiri
        Gate::authorize('moderate-listings');

        return view('livewire.admin.moderasi.listing-table', [
            'statuses'       => self::STATUSES,
            'jenis'          => $this->currentJenis(),
            'status'         => $this->currentStatus(),
            'direction'      => $this->direction(),
            'sla'            => $this->sla(),
            'perPageOptions' => self::PER_PAGE_OPTIONS,
            'types'          => Moderation::TYPES,
        ]);
    }

    // ---- helpers
    // Nilai properti publik bisa diubah dari browser, jadi selalu dicocokkan dengan daftar yang diizinkan.

    private function currentJenis(): string
    {
        return array_key_exists($this->jenis, Moderation::TYPES) ? $this->jenis : 'venue';
    }

    private function currentStatus(): string
    {
        return array_key_exists($this->status, self::STATUSES) ? $this->status : 'pending_review';
    }

    private function currentPerPage(): int
    {
        return in_array($this->perPage, self::PER_PAGE_OPTIONS, true) ? $this->perPage : self::PER_PAGE_OPTIONS[0];
    }

    private function direction(): string
    {
        $auto = $this->currentStatus() === 'pending_review' ? 'terlama' : 'terbaru';
        $mode = in_array($this->sort, ['terlama', 'terbaru'], true) ? $this->sort : $auto;

        return $mode === 'terlama' ? 'asc' : 'desc';
    }

    private function sla(): int
    {
        return (int) config('admin.verification_sla_days', 2);
    }
}