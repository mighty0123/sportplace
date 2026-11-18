<?php

namespace App\Livewire\Admin\Komisi;

use App\Exceptions\Commission\NoCommissionRateException;
use App\Services\Commission\CommissionService;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Component;

/** Pratinjau memakai layanan yang sama dengan penilaian sungguhan, jadi angkanya tidak bisa berbeda. */
class CommissionPreview extends Component
{
    public string $price = '150000';
    public string $slotCount = '1';
    public string $mitraId = '';
    public string $at = '';

    public function mount(): void
    {
        Gate::authorize('manage-commission');
    }

    #[Computed]
    public function mitraOptions()
    {
        return app(CommissionService::class)->mitraOptions();
    }

    #[Computed]
    public function result(): array
    {
        // Harga ditulis bebas ("150.000", "Rp 150000"): hanya angka, dengan koma sebagai pemisah desimal.
        $clean = str_replace(',', '.', preg_replace('/[^\d,]/', '', $this->price) ?? '');
        if ($clean === '' || ! is_numeric($clean) || (float) $clean > 100_000_000) {
            return ['error' => 'Masukkan harga slot yang valid (0 sampai 100.000.000).'];
        }

        $slots = ctype_digit($this->slotCount) ? max(1, min(24, (int) $this->slotCount)) : 1;
        $mitra = ctype_digit($this->mitraId) && DB::table('mitra_profiles')->where('id', (int) $this->mitraId)->where('status', 'approved')->exists()
            ? (int) $this->mitraId : null;

        $at = null;
        if ($this->at !== '') {
            $at = CarbonImmutable::createFromFormat('Y-m-d\TH:i', $this->at, config('app.timezone')) ?: false;
            if ($at === false) {
                return ['error' => 'Waktu tidak valid.'];
            }
        }

        try {
            return ['data' => app(CommissionService::class)->preview((float) $clean, $slots, $mitra, null, $at)];
        } catch (NoCommissionRateException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function render(): View
    {
        // Setiap permintaan Livewire diotorisasi sendiri: endpoint komponen bisa dipanggil langsung.
        Gate::authorize('manage-commission');

        return view('livewire.admin.komisi.commission-preview');
    }
}