@props([
    'dates'        => [],
    'selectedDate' => '2024-10-24',
])

<div class="flex items-center gap-2 overflow-x-auto no-scrollbar py-1">
    <!-- Prev Arrow Button -->
    <button
        type="button"
        class="w-9 h-11 sm:h-12 rounded-xl bg-white border border-slate-200/90 hover:bg-slate-50 flex items-center justify-center text-slate-500 shadow-2xs shrink-0 transition"
        title="Sebelumnya"
    >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </button>

    <!-- Date Items Carousel -->
    <div class="flex items-center gap-2 flex-1 overflow-x-auto no-scrollbar">
        @foreach ($dates as $d)
            @php
                $isActive = !empty($d['active']) || ($d['full_date'] ?? '') === $selectedDate;
            @endphp
            <a
                href="?date={{ $d['full_date'] ?? '' }}"
                class="flex-1 min-w-[70px] sm:min-w-[80px] py-2 sm:py-2.5 px-3 rounded-xl text-center transition duration-150 {{ $isActive ? 'bg-white border-2 border-slate-400/80 text-slate-900 shadow-xs ring-1 ring-slate-400/20' : 'bg-slate-100/80 hover:bg-slate-200 text-slate-600 border border-transparent' }}"
            >
                <span class="block text-[11px] font-medium {{ $isActive ? 'text-slate-500' : 'text-slate-400' }}">
                    {{ $d['day'] }}
                </span>
                <span class="block text-xs sm:text-sm font-bold mt-0.5 {{ $isActive ? 'text-slate-900' : 'text-slate-700' }}">
                    {{ $d['date'] }}
                </span>
            </a>
        @endforeach
    </div>

    <!-- Next Arrow Button -->
    <button
        type="button"
        class="w-9 h-11 sm:h-12 rounded-xl bg-white border border-slate-200/90 hover:bg-slate-50 flex items-center justify-center text-slate-500 shadow-2xs shrink-0 transition"
        title="Selanjutnya"
    >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </button>
</div>
