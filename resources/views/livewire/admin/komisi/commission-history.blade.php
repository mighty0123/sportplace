@php
    use App\Services\Commission\CommissionService as CS;

    $rows = $this->rows;
    $fmt  = fn ($d) => \Carbon\CarbonImmutable::parse($d)->locale('id')->translatedFormat('j M Y, H.i');
    $states = ['berlaku' => ['Berlaku', 'success'], 'terjadwal' => ['Terjadwal', 'info'], 'digantikan' => ['Digantikan', 'neutral']];
@endphp

<x-ui.card flush class="overflow-hidden">
    <div class="flex flex-col gap-3 px-4 pb-3 pt-4 lg:flex-row lg:items-center lg:justify-between lg:px-5">
        <div>
            <h2 class="text-h6 text-ink">Riwayat tarif</h2>
            <p class="text-caption text-ink-muted">Setiap perubahan menambah satu baris. Baris lama tidak pernah diubah atau dihapus.</p>
        </div>
        <div class="flex gap-2">
            <label for="lingkup" class="sr-only">Lingkup</label>
            <select id="lingkup" wire:model.live="scope"
                    class="min-h-10 rounded-md border border-line-strong bg-surface px-2 text-body-sm text-ink focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-brand">
                @foreach ($scopes as $k => $l)<option value="{{ $k }}">{{ $l }}</option>@endforeach
            </select>
            <label for="cari-tarif" class="sr-only">Cari mitra atau alasan</label>
            <input id="cari-tarif" type="search" maxlength="100" autocomplete="off" wire:model.live.debounce.300ms="search" placeholder="Cari mitra atau alasan"
                    class="min-h-10 w-full rounded-md border border-line-strong bg-surface px-3 text-body-sm text-ink placeholder:text-ink-muted focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-brand lg:w-56">
        </div>
    </div>

    <div wire:loading.class="opacity-60" wire:target="scope,search,nextPage,previousPage,gotoPage,clearFilters" class="transition-opacity motion-reduce:transition-none">
        @if ($rows->isEmpty())
            <x-ui.empty-state icon="search" title="Tidak ada tarif yang cocok" description="Longgarkan filter atau kata pencarian.">
                <x-ui.button variant="outline" type="button" wire:click="clearFilters">Reset filter</x-ui.button>
            </x-ui.empty-state>
        @else
            <div class="hidden overflow-x-auto md:block">
                <table class="w-full text-left text-body-sm">
                    <caption class="sr-only">Riwayat perubahan tarif komisi</caption>
                    <thead class="bg-surface-muted text-label-sm text-ink-body">
                        <tr>
                            <th scope="col" class="px-4 py-3 font-medium">Mulai berlaku</th>
                            <th scope="col" class="px-4 py-3 font-medium">Lingkup</th>
                            <th scope="col" class="px-4 py-3 font-medium">Tarif</th>
                            <th scope="col" class="px-4 py-3 font-medium">Status</th>
                            <th scope="col" class="px-4 py-3 font-medium">Ditetapkan oleh</th>
                            <th scope="col" class="px-4 py-3 font-medium">Alasan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @foreach ($rows as $r)
                            <tr wire:key="rate-{{ $r->id }}" class="align-top">
                                <td class="whitespace-nowrap px-4 py-3 tabular-nums text-ink-body">{{ $fmt($r->berlaku_mulai) }}</td>
                                <td class="px-4 py-3 text-ink">{{ $r->mitra_id ? ($r->nama_usaha ?: 'Mitra #' . $r->mitra_id) : 'Global' }}</td>
                                <td class="whitespace-nowrap px-4 py-3 tabular-nums text-ink">{{ $r->persen === null ? 'Ikut global' : CS::percentLabel($r->persen) }}</td>
                                <td class="px-4 py-3"><x-ui.badge :tone="$states[$r->state][1]">{{ $states[$r->state][0] }}</x-ui.badge></td>
                                <td class="px-4 py-3 text-ink-body">{{ $r->admin_name ?? 'Sistem' }}</td>
                                <td class="max-w-xs px-4 py-3 text-ink-body"><span class="line-clamp-2" title="{{ $r->catatan }}">{{ $r->catatan }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <ul class="divide-y divide-line md:hidden">
                @foreach ($rows as $r)
                    <li wire:key="rate-card-{{ $r->id }}" class="space-y-1 px-4 py-3">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-body-sm font-semibold text-ink">{{ $r->mitra_id ? ($r->nama_usaha ?: 'Mitra #' . $r->mitra_id) : 'Global' }}: {{ $r->persen === null ? 'Ikut global' : CS::percentLabel($r->persen) }}</p>
                            <x-ui.badge :tone="$states[$r->state][1]">{{ $states[$r->state][0] }}</x-ui.badge>
                        </div>
                        <p class="text-caption text-ink-muted">Mulai {{ $fmt($r->berlaku_mulai) }}, oleh {{ $r->admin_name ?? 'Sistem' }}</p>
                        <p class="text-caption text-ink-body">{{ $r->catatan }}</p>
                    </li>
                @endforeach
            </ul>

            <div class="border-t border-line px-4 py-3 lg:px-5">
                @if ($rows->hasPages())
                    <x-ui.pagination :rows="$rows" :pages="$this->pageWindow()" :per-page-options="[10]" noun="tarif" />
                @else
                    <p class="text-caption text-ink-muted">{{ $rows->total() }} baris riwayat.</p>
                @endif
            </div>
        @endif
    </div>
</x-ui.card>