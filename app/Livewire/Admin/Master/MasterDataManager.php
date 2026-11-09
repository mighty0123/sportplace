<?php

namespace App\Livewire\Admin\Master;

use App\Services\Admin\MasterDataService as Master;
use Illuminate\Contracts\View\View;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class MasterDataManager extends Component
{
    use WithFileUploads, WithPagination;

    public const PER_PAGE_OPTIONS = [15, 30, 50];

    // Keadaan tampilan disimpan di URL
    #[Url(history: true)]
    public string $tipe = 'olahraga';

    #[Url(history: true)]
    public string $status = 'aktif';

    #[Url(as: 'kat')]
    public string $kategori = ''; // filter khusus fasilitas

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'per')]
    public int $perPage = 15;

    // Formulir tambah/ubah
    public ?int $editingId = null;
    public string $nama = '';
    public string $provinsi = '';
    public string $formKategori = 'umum';
    public $icon = null;
    public ?string $currentIcon = null;

    // Dialog nonaktifkan
    public ?int $toggleId = null;

    public ?string $notice = null;

    public function mount(): void
    {
        $this->authorizeAccess();
    }

    // ---- keperluan ui

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function setTipe(string $tipe): void
    {
        $this->tipe = array_key_exists($tipe, Master::TYPES) ? $tipe : 'olahraga';
        $this->status = 'aktif';
        $this->kategori = '';
        $this->search = '';
        $this->toggleId = null;
        $this->resetPage();
    }

    public function setStatus(string $status): void
    {
        $this->status = in_array($status, ['aktif', 'nonaktif'], true) ? $status : 'aktif';
        $this->resetPage();
    }

    public function setKategori(string $kategori): void
    {
        $this->kategori = array_key_exists($kategori, Master::CATEGORIES) ? $kategori : '';
        $this->resetPage();
    }

    public function clearSearch(): void
    {
        $this->search = '';
        $this->resetPage();
    }

    // ---- formulir

    public function startCreate(): void
    {
        $this->authorizeAccess();
        $this->resetForm();
        $this->dispatch('open-master-form');
    }

    public function startEdit(int $id): void
    {
        $this->authorizeAccess();
        $row = app(Master::class)->find($this->currentTipe(), $id);
        if (! $row) {
            return;
        }

        $t = Master::type($this->currentTipe());
        $this->resetForm();
        $this->editingId = $id;
        $this->nama = (string) $row->{$t['name']};
        $this->provinsi = (string) ($row->provinsi ?? '');
        $this->formKategori = (string) ($row->kategori ?? 'umum');
        $this->currentIcon = $row->icon_url ?? null;
        $this->dispatch('open-master-form');
    }

    /** Ikon divalidasi begitu dipilih, sebelum pratinjau dibuat */
    public function updatedIcon(): void
    {
        $this->authorizeAccess();
        $svc = app(Master::class);

        try {
            $this->validateOnly('icon', ['icon' => $svc->iconRules($this->editingId !== null)], $svc->messages($this->currentTipe()));
        } catch (ValidationException $e) {
            $this->reset('icon');
            throw $e;
        }
    }

    public function save(): void
    {
        $this->authorizeAccess();
        $tipe = $this->currentTipe();
        $svc = app(Master::class);

        $this->nama = trim((string) preg_replace('/\s+/u', ' ', $this->nama));
        $this->provinsi = trim((string) preg_replace('/\s+/u', ' ', $this->provinsi));

        $this->validate($svc->rules($tipe, $this->editingId, ['provinsi' => $this->provinsi]), $svc->messages($tipe));

        try {
            $this->notice = $svc->save($tipe, $this->editingId, [
                'nama' => $this->nama, 'provinsi' => $this->provinsi, 'kategori' => $this->formKategori,
            ], $this->icon, auth()->user());
        } catch (UniqueConstraintViolationException) {
            // Balapan: data yang sama baru saja disimpan
            $this->addError('nama', 'Data yang sama baru saja ditambahkan. Muat ulang daftar.');

            return;
        }

        $this->resetForm();
        $this->dispatch('close-master-form');
    }

    // ---- status

    public function confirmDeactivate(int $id): void
    {
        $this->authorizeAccess();
        if (! app(Master::class)->find($this->currentTipe(), $id)) {
            return;
        }
        $this->toggleId = $id;
        $this->dispatch('open-master-toggle');
    }

    public function deactivate(): void
    {
        $this->authorizeAccess();
        if ($this->toggleId) {
            $this->notice = app(Master::class)->setStatus($this->currentTipe(), $this->toggleId, 'nonaktif', auth()->user());
        }
        $this->toggleId = null;
        $this->dispatch('close-master-toggle');
    }

    public function activate(int $id): void
    {
        $this->authorizeAccess();
        $this->notice = app(Master::class)->setStatus($this->currentTipe(), $id, 'aktif', auth()->user());
    }

    // ---- data

    /** Jumlah aktif per jenis untuk kontrol jenis */
    #[Computed]
    public function totals(): array
    {
        $out = [];
        foreach (Master::TYPES as $key => $t) {
            $out[$key] = DB::table($t['table'])->where($t['status'], 'aktif')->count();
        }

        return $out;
    }

    /** Jumlah aktif dan nonaktif untuk jenis yang sedang dibuka */
    #[Computed]
    public function counts(): array
    {
        $t = Master::type($this->currentTipe());

        return DB::table($t['table'])->selectRaw("{$t['status']} as s, COUNT(*) as n")
            ->groupBy($t['status'])->pluck('n', 's')->all();
    }

    #[Computed]
    public function rows(): LengthAwarePaginator
    {
        $t = Master::type($this->currentTipe());
        $term = mb_substr(trim($this->search), 0, 100);
        $like = '%' . addcslashes($term, '%_\\') . '%';

        return DB::table("{$t['table']} as x")
            ->selectRaw("x.*, ({$t['usage_sql']}) as usage_count")
            ->where("x.{$t['status']}", $this->currentStatus())
            ->when($this->currentTipe() === 'fasilitas' && $this->currentKategori() !== '',
                fn ($q) => $q->where('x.kategori', $this->currentKategori()))
            ->when($term !== '', fn ($q) => $q->where(function ($w) use ($t, $like) {
                $w->where("x.{$t['name']}", 'like', $like);
                if ($this->currentTipe() === 'kota') {
                    $w->orWhere('x.provinsi', 'like', $like);
                } elseif ($this->currentTipe() === 'olahraga') {
                    $w->orWhere('x.slug', 'like', $like);
                }
            }))
            ->orderBy("x.{$t['name']}")->orderBy('x.id')
            ->paginate($this->currentPerPage());
    }

    #[Computed]
    public function toggleItem(): ?object
    {
        return $this->toggleId ? app(Master::class)->find($this->currentTipe(), $this->toggleId) : null;
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
        $this->authorizeAccess();

        $notice = $this->notice;
        $this->notice = null; // pesan tampil sekali

        return view('livewire.admin.master.master-data-manager', [
            'types'          => Master::TYPES,
            'config'         => Master::type($this->currentTipe()),
            'tipe'           => $this->currentTipe(),
            'status'         => $this->currentStatus(),
            'kategori'       => $this->currentKategori(),
            'categories'     => Master::CATEGORIES,
            'perPageOptions' => self::PER_PAGE_OPTIONS,
            'notice'         => $notice,
        ]);
    }

    // ---- helpers

    private function authorizeAccess(): void
    {
        Gate::authorize('manage-master-data');
    }

    private function currentTipe(): string
    {
        return array_key_exists($this->tipe, Master::TYPES) ? $this->tipe : 'olahraga';
    }

    private function currentStatus(): string
    {
        return in_array($this->status, ['aktif', 'nonaktif'], true) ? $this->status : 'aktif';
    }

    private function currentKategori(): string
    {
        return array_key_exists($this->kategori, Master::CATEGORIES) ? $this->kategori : '';
    }

    private function currentPerPage(): int
    {
        return in_array($this->perPage, self::PER_PAGE_OPTIONS, true) ? $this->perPage : self::PER_PAGE_OPTIONS[0];
    }

    private function resetForm(): void
    {
        $this->resetValidation();
        $this->reset('editingId', 'nama', 'provinsi', 'icon', 'currentIcon');
        $this->formKategori = 'umum';
    }
}