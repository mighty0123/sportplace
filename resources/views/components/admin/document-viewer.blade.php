@props(['documents'])
@php $first = collect($documents)->firstWhere('available', true); @endphp

<section aria-label="Berkas legalitas" class="flex flex-col gap-3"
        x-data="{
            docs: @js(collect($documents)->keyBy('key')),
            current: @js($first['key'] ?? null),
            zoom: 1,
            get doc() { return this.current ? this.docs[this.current] : null },
            pick(key) { this.current = key; this.zoom = 1 },
            setZoom(v) { this.zoom = Math.min(3, Math.max(0.5, Math.round(v * 100) / 100)) },
        }">

    <div role="tablist" aria-label="Jenis berkas" class="flex flex-wrap gap-2">
        @foreach ($documents as $d)
            <button type="button" role="tab" @disabled(! $d['available'])
                    x-on:click="pick('{{ $d['key'] }}')"
                    :aria-selected="current === '{{ $d['key'] }}' ? 'true' : 'false'"
                    :class="current === '{{ $d['key'] }}' ? 'border-brand bg-brand-soft text-primary-700' : 'border-line-strong text-ink-body hover:bg-surface-muted'"
                    class="min-h-9 rounded-md border px-3 text-label-md disabled:cursor-not-allowed disabled:border-line disabled:text-ink-muted">
                {{ $d['label'] }}
                <span class="text-caption">
                    {{ $d['available'] ? ($d['required'] ? 'wajib' : '') : ($d['required'] ? 'wajib, belum ada' : 'tidak diunggah') }}
                </span>
            </button>
        @endforeach
    </div>

    <div class="flex items-center justify-between gap-3" x-show="doc" x-cloak>
        <div class="flex items-center gap-1.5" x-show="doc && doc.type === 'image'">
            <button type="button" aria-label="Perkecil" x-on:click="setZoom(zoom - 0.25)"
                    class="grid size-9 place-items-center rounded-md border border-line-strong text-body-md hover:bg-surface-muted">−</button>
            <span class="w-12 text-center text-label-md text-ink" x-text="Math.round(zoom * 100) + '%'"></span>
            <button type="button" aria-label="Perbesar" x-on:click="setZoom(zoom + 0.25)"
                    class="grid size-9 place-items-center rounded-md border border-line-strong text-body-md hover:bg-surface-muted">+</button>
            <button type="button" x-on:click="setZoom(1)" class="px-2 text-caption text-brand-link underline">Setel ulang</button>
        </div>
        <p class="text-caption text-ink-muted" x-show="doc && doc.type === 'pdf'">Gunakan kontrol penampil PDF untuk memperbesar.</p>
        <a :href="doc ? doc.url : '#'" target="_blank" rel="noopener" class="text-label-md text-brand-link underline">Buka di tab baru</a>
    </div>

    <div class="relative h-[28rem] overflow-auto rounded-md bg-surface-sunken lg:h-[34rem]" tabindex="0" aria-label="Pratinjau berkas">
        <template x-if="doc && doc.type === 'image'">
            <img :src="doc.url" :alt="'Pratinjau berkas ' + doc.label" :style="`width: ${zoom * 100}%`"
                class="mx-auto block max-w-none" draggable="false">
        </template>
        <template x-if="doc && doc.type === 'pdf'">
            <iframe :src="doc.url" :title="'Pratinjau berkas ' + doc.label" class="h-full w-full"></iframe>
        </template>
        <template x-if="!doc">
            <x-ui.empty-state title="Belum ada berkas yang bisa ditampilkan"
                            description="Mitra belum mengunggah berkas, atau berkasnya tidak ditemukan di penyimpanan." />
        </template>
    </div>

    <p class="text-caption text-ink-muted" x-show="doc" x-text="doc ? doc.label + ', ' + doc.size : ''"></p>
</section>