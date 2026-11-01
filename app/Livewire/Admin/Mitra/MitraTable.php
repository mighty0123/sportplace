<?php

namespace App\Livewire\Admin\Mitra;

use App\Models\MitraProfile;
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

    private const PER_PAGE = 15;

    // Keadaan tampilan disimpan di URL
    #[Url(history: true)]
    public string $status = 'pending';

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'urut')]
    public string $sort = ''; // '' = otomatis (Menunggu: terlama dulu, lainnya: terbaru dulu)

    public function mount(): void
    {
        $this->authorizeAccess();
    }

    public function updatedSearch(): void
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

    /** Jumlah per status, satu query untuk ketiga tab. */
    #[Computed]
    public function counts(): array
    {
        return MitraProfile::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->all();
    }

    #[Computed]
    public function rows(): LengthAwarePaginator
    {
        $term = $this->term();
        $dir  = $this->direction();

        return MitraProfile::query()
            ->with(['user:id,name,email', 'reviewer:id,name'])
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
            ->paginate(self::PER_PAGE);
    }

    public function render(): View
    {
        // Setiap permintaan Livewire diotorisasi sendiri
        $this->authorizeAccess();

        return view('livewire.admin.mitra.mitra-table', [
            'statuses'  => self::STATUSES,
            'status'    => $this->currentStatus(),
            'direction' => $this->direction(),
            'sla'       => (int) config('admin.verification_sla_days', 2),
        ]);
    }

    // helpers

    private function authorizeAccess(): void
    {
        Gate::authorize('viewAny', MitraProfile::class);
    }

    /** Nilai properti publik bisa diubah dari browser, jadi selalu dicocokkan dengan daftar yang diizinkan. */
    private function currentStatus(): string
    {
        return array_key_exists($this->status, self::STATUSES) ? $this->status : 'pending';
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