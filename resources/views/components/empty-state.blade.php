@props([
    'title' => 'Belum ada data',
    'description' => 'Data yang Anda cari saat ini belum tersedia.',
    'icon' => '📂',
])

<div {{ $attributes->merge(['class' => 'text-center py-12 px-4 rounded-xl border-2 border-dashed border-slate-200 bg-white']) }}>
    <div class="text-4xl mb-3">{{ $icon }}</div>
    <h3 class="text-base font-semibold text-slate-800">{{ $title }}</h3>
    <p class="mt-1 text-sm text-slate-500 max-w-sm mx-auto leading-relaxed">{{ $description }}</p>

    @if ($slot->isNotEmpty())
        <div class="mt-6 flex justify-center">
            {{ $slot }}
        </div>
    @endif
</div>

