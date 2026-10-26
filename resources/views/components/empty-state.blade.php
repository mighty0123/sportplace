@props([
    'title' => 'Belum ada data',
    'description' => 'Data yang Anda cari saat ini belum tersedia.',
])

<div {{ $attributes->merge(['class' => 'text-center py-12 px-6 rounded-2xl border-2 border-dashed border-slate-200 bg-white/70']) }}>
    <div class="mx-auto w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-4">
        @if (isset($icon))
            {{ $icon }}
        @else
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
        @endif
    </div>
    <h3 class="text-base font-semibold text-slate-800">{{ $title }}</h3>
    <p class="mt-1 text-sm text-slate-500 max-w-sm mx-auto leading-relaxed">{{ $description }}</p>

    @if ($slot->isNotEmpty())
        <div class="mt-6 flex justify-center">
            {{ $slot }}
        </div>
    @endif
</div>
