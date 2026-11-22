@php
    use App\Services\Commission\CommissionService as CS;

    $res = $this->result;
    $d   = $res['data'] ?? null;
    $rp  = fn ($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');
    $levels = ['global' => 'tarif global', 'mitra' => 'tarif khusus mitra', 'olahraga' => 'tarif cabang olahraga', 'mitra_olahraga' => 'tarif khusus mitra dan olahraga'];

    // Opsi lanjutan terbuka dari awal hanya bila sudah ada isinya.
    $advancedOpen = $this->mitraId !== '' || $this->at !== '';
@endphp

<x-ui.card>
    <h2 class="text-h6 text-ink">Pratinjau perhitungan</h2>
    <p class="mt-0.5 text-caption text-ink-muted">Coba angka tanpa menyimpan apa pun. Aturannya sama dengan perhitungan sungguhan.</p>

    <div class="mt-3 grid grid-cols-2 gap-3">
        <x-ui.input name="price" wire:model.live.debounce.400ms="price" label="Harga per slot (Rp)" inputmode="numeric" />
        <x-ui.input name="slotCount" wire:model.live.debounce.400ms="slotCount" label="Jumlah slot" inputmode="numeric" />
    </div>

    {{-- Mitra dan waktu jarang diubah, jadi disembunyikan sampai diminta. --}}
    <div x-data="{ open: {{ $advancedOpen ? 'true' : 'false' }} }" class="mt-3">
        <button type="button" @click="open = ! open" :aria-expanded="open.toString()" aria-controls="preview-lanjutan"
                class="inline-flex items-center gap-1 text-label-md text-ink-muted hover:text-ink focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand">
            <x-ui.icon name="chevron-down" class="size-4 transition-transform motion-reduce:transition-none" x-bind:class="open ? 'rotate-180' : ''" />
            Mitra dan waktu tertentu
        </button>

        <div id="preview-lanjutan" x-show="open" @unless ($advancedOpen) style="display: none;" @endunless class="mt-3 grid grid-cols-1 gap-3">
            <div>
                <label for="preview-mitra" class="text-label-md text-ink-strong">Mitra</label>
                <div class="relative mt-1.5">
                    <select id="preview-mitra" wire:model.live="mitraId"
                            class="min-h-11 w-full appearance-none truncate rounded-md border border-line-strong bg-surface pl-3 pr-10 text-body-md text-ink focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-brand lg:min-h-10">
                        <option value="">Semua mitra (tarif global)</option>
                        @foreach ($this->mitraOptions as $m)
                            <option value="{{ $m->id }}">{{ $m->nama_usaha ?: 'Mitra #' . $m->id }}</option>
                        @endforeach
                    </select>
                    <x-ui.icon name="chevron-down" class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-ink-muted" />
                </div>
            </div>

            <x-ui.input name="at" type="datetime-local" wire:model.live="at" label="Untuk pembayaran pada" hint="Kosong berarti sekarang." />
        </div>
    </div>

    <div class="mt-4 border-t border-line pt-4" wire:loading.class="opacity-60" wire:target="price,slotCount,mitraId,at">
        @if (! $d)
            <p class="text-body-sm text-danger-700">{{ $res['error'] }}</p>
        @else
            <p class="text-caption text-ink-muted">
                Memakai <strong class="text-ink">{{ CS::percentLabel($d['rate']->persen) }}</strong>
                ({{ $levels[$d['rate']->level] ?? $d['rate']->level }}, sejak {{ \Carbon\CarbonImmutable::parse($d['rate']->berlaku_mulai)->locale('id')->translatedFormat('j M Y') }}).
            </p>

            <div class="mt-3 flex items-baseline justify-between gap-3">
                <span class="text-body-sm font-semibold text-ink">Dibayar penyewa</span>
                <span class="whitespace-nowrap text-h6 tabular-nums text-ink">{{ $rp($d['dibayar_penyewa']) }}</span>
            </div>
            <p class="mt-0.5 text-caption text-ink-muted">
                Harga slot{{ $d['slots'] > 1 ? " ({$d['slots']} slot)" : '' }} {{ $rp($d['subtotal']) }} + biaya layanan {{ $rp($d['biaya_layanan']) }} (sekali per booking).
            </p>

            <dl class="mt-3 grid grid-cols-2 gap-px overflow-hidden rounded-md border border-line bg-line">
                <div class="bg-surface-muted p-3">
                    <dt class="text-caption text-ink-muted">Komisi platform</dt>
                    <dd class="mt-0.5 whitespace-nowrap text-body-md tabular-nums text-ink">{{ $rp($d['komisi']) }}</dd>
                </div>
                <div class="bg-surface-muted p-3">
                    <dt class="text-caption text-ink-muted">Hak mitra</dt>
                    <dd class="mt-0.5 whitespace-nowrap text-body-md font-semibold tabular-nums text-ink">{{ $rp($d['mitra']) }}</dd>
                </div>
            </dl>

            <div class="mt-3 flex items-baseline justify-between gap-3 text-body-sm">
                <span class="text-ink-body">Pendapatan platform</span>
                <span class="whitespace-nowrap tabular-nums text-ink">{{ $rp($d['platform']) }}</span>
            </div>
            <p class="mt-0.5 text-caption text-ink-muted">
                Komisi ditambah biaya layanan. Komisi dibulatkan per slot ke rupiah terdekat (setengah naik): {{ $rp($d['per_slot']['komisi']) }} komisi dan {{ $rp($d['per_slot']['mitra']) }} hak mitra per slot.
            </p>
        @endif
    </div>
</x-ui.card>