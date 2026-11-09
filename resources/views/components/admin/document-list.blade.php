@props(['documents', 'mitra'])

<div x-data="{
        docs: @js(collect($documents)->keyBy('key')),
        current: null,
        zoom: 1,
        get doc() { return this.current ? this.docs[this.current] : null },
        open(key) { this.pick(key); this.$refs.sheet.showModal() },
        pick(key) { this.current = key; this.zoom = 1 },
        setZoom(v) { this.zoom = Math.min(3, Math.max(0.5, Math.round(v * 100) / 100)) },
    }">

    <ul class="divide-y divide-line">
        @foreach ($documents as $d)
            <li class="flex items-center gap-3 py-3">
                <x-ui.icon name="file-text" class="{{ $d['available'] ? 'text-ink-muted' : ($d['required'] ? 'text-danger-700' : 'text-ink-muted') }}" />
                <div class="min-w-0 flex-1">
                    <p class="text-body-sm text-ink">
                        {{ $d['label'] }}
                        <span class="text-caption text-ink-muted">{{ $d['required'] ? 'wajib' : 'opsional' }}</span>
                    </p>
                    @if ($d['available'])
                        <p class="text-caption text-ink-muted">{{ $d['type'] === 'pdf' ? 'PDF' : 'Gambar' }}, {{ $d['size'] }}</p>
                    @elseif ($d['required'])
                        <p class="text-caption font-medium text-danger-700">Belum diunggah atau berkas tidak ditemukan</p>
                    @else
                        <p class="text-caption text-ink-muted">Tidak diunggah</p>
                    @endif
                </div>
                @if ($d['available'])
                    <x-ui.button variant="outline" size="sm" type="button" x-on:click="open('{{ $d['key'] }}')"
                                aria-label="Lihat berkas {{ $d['label'] }}">Lihat</x-ui.button>
                @endif
            </li>
        @endforeach
    </ul>

    {{-- Ponsel: lembar dari bawah (88% layar, sisanya menunjukkan halaman). md ke atas: panel samping --}}
    <dialog x-ref="sheet" aria-labelledby="doc-sheet-title"
            x-on:click="$event.target === $refs.sheet && $refs.sheet.close()"
            class="fixed inset-x-0 bottom-0 top-auto m-0 h-[88dvh] max-h-none w-full max-w-none overflow-hidden rounded-t-xl border-0 bg-surface p-0 text-ink-body shadow-modal backdrop:bg-primary-950/50
                md:inset-y-0 md:left-auto md:right-0 md:h-dvh md:w-176 md:max-w-[calc(100vw-4rem)] md:rounded-none">
        <div class="flex h-full flex-col">
            <div class="mx-auto mt-2 h-1 w-10 shrink-0 rounded-full bg-line-strong md:hidden" aria-hidden="true"></div>

            <div class="flex shrink-0 items-center justify-between gap-3 border-b border-line px-5 py-2.5">
                <h2 id="doc-sheet-title" class="text-h5 text-ink">Berkas legalitas</h2>
                <button type="button" aria-label="Tutup" x-on:click="$refs.sheet.close()"
                        class="grid size-10 place-items-center rounded-md hover:bg-surface-muted">
                    <x-ui.icon name="x" />
                </button>
            </div>

            {{-- Data pembanding: admin tidak perlu menutup panel untuk mencocokkan. --}}
            <div class="shrink-0 border-b border-line bg-surface-muted px-5 py-2" x-show="doc" x-cloak>
                <p class="text-caption font-medium text-ink-muted">Cocokkan dengan data formulir</p>
                <dl>
                    <div x-show="current === 'ktp'">
                        <x-admin.info-row label="Nama" :value="$mitra->user?->name" />
                        <x-admin.sensitive-value :mitra="$mitra" field="nik" label="NIK" />
                    </div>
                    <div x-show="current === 'npwp'" x-cloak>
                        <x-admin.sensitive-value :mitra="$mitra" field="npwp" label="NPWP" />
                        <x-admin.info-row label="Nama" :value="$mitra->nama_usaha ?: $mitra->user?->name" />
                    </div>
                    <div x-show="current === 'nib' || current === 'sewa'" x-cloak>
                        <x-admin.info-row label="Usaha" :value="$mitra->nama_usaha" />
                        <x-admin.info-row label="Kota" :value="$mitra->kota_operasional" />
                    </div>
                </dl>
            </div>

            <div role="tablist" aria-label="Jenis berkas" class="flex shrink-0 flex-wrap gap-2 px-5 pt-3">
                @foreach ($documents as $d)
                    @if ($d['available'])
                        <button type="button" role="tab" x-on:click="pick('{{ $d['key'] }}')"
                                :aria-selected="current === '{{ $d['key'] }}' ? 'true' : 'false'"
                                :class="current === '{{ $d['key'] }}' ? 'border-brand bg-brand-soft text-primary-700' : 'border-line-strong text-ink-body hover:bg-surface-muted'"
                                class="min-h-9 rounded-md border px-3 text-label-md">{{ $d['label'] }}</button>
                    @endif
                @endforeach
            </div>

            <div class="flex shrink-0 items-center justify-between gap-3 px-5 py-2.5">
                <div class="flex items-center gap-1.5" x-show="doc && doc.type === 'image'">
                    <button type="button" aria-label="Perkecil" x-on:click="setZoom(zoom - 0.25)"
                            class="grid size-9 place-items-center rounded-md border border-line-strong hover:bg-surface-muted">−</button>
                    <span class="w-12 text-center text-label-md text-ink" x-text="Math.round(zoom * 100) + '%'"></span>
                    <button type="button" aria-label="Perbesar" x-on:click="setZoom(zoom + 0.25)"
                            class="grid size-9 place-items-center rounded-md border border-line-strong hover:bg-surface-muted">+</button>
                    <button type="button" x-on:click="setZoom(1)" class="px-2 text-caption text-brand-link underline">Setel ulang</button>
                </div>
                <p class="text-caption text-ink-muted" x-show="doc && doc.type === 'pdf'" x-cloak>Gunakan kontrol penampil PDF untuk memperbesar.</p>
                <a :href="doc ? doc.url : '#'" target="_blank" rel="noopener" class="ml-auto text-label-md text-brand-link underline">Buka di tab baru</a>
            </div>

            <div class="min-h-0 flex-1 overflow-auto bg-surface-sunken" tabindex="0" aria-label="Pratinjau berkas">
                <template x-if="doc && doc.type === 'image'">
                    <img :src="doc.url" :alt="'Pratinjau berkas ' + doc.label" :style="`width: ${zoom * 100}%`"
                        class="mx-auto block max-w-none" draggable="false">
                </template>
                <template x-if="doc && doc.type === 'pdf'">
                    <iframe :src="doc.url" :title="'Pratinjau berkas ' + doc.label" class="h-full w-full"></iframe>
                </template>
            </div>

            <p class="shrink-0 border-t border-line px-5 py-2 text-caption text-ink-muted" x-text="doc ? doc.label + ', ' + doc.size : ''"></p>
        </div>
    </dialog>
</div>