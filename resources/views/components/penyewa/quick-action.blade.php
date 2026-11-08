@props([
    'href'  => '#',
    'label' => 'Aksi',
    'icon'  => 'search', // search | history | star | receipt | calendar
])

@php
    $icons = [
        'search'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>',
        'history' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>',
        'receipt' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
        'star'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>',
    ];
    $iconPath = $icons[$icon] ?? $icons['search'];
@endphp

<a href="{{ $href }}"
   class="group flex items-center justify-between gap-3 px-4 py-3.5 rounded-xl bg-slate-50/70 hover:bg-[#E8F5F1]/60 border border-slate-200/80 hover:border-emerald-300 transition-all duration-150 cursor-pointer">
    <div class="flex items-center gap-3 min-w-0">
        <!-- Mint Icon Badge -->
        <div class="w-9 h-9 rounded-xl bg-[#E8F5F1] text-[#0B7B69] group-hover:bg-[#0B7B69] group-hover:text-white flex items-center justify-center transition-colors duration-150 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                {!! $iconPath !!}
            </svg>
        </div>
        <span class="text-sm font-semibold text-slate-800 group-hover:text-slate-900 truncate">{{ $label }}</span>
    </div>
    <!-- Teal Right Arrow -->
    <svg class="w-4 h-4 text-[#0B7B69] group-hover:translate-x-0.5 transition-transform shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
    </svg>
</a>
