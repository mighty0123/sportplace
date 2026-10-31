@props([
    'dates'        => [],
    'selectedDate' => '',
])

<div class="space-y-2">
    <!-- Date Strip Scrollable Row with Prev/Next Navigation -->
    <div class="relative flex items-center gap-2">
        <!-- Prev Arrow Button -->
        <button
            type="button"
            id="date-strip-prev"
            class="w-10 h-16 sm:h-20 rounded-2xl bg-white border border-slate-200/90 hover:bg-slate-50 flex items-center justify-center text-slate-600 shadow-2xs shrink-0 transition cursor-pointer"
            title="Geser Tanggal Sebelumnya"
        >
            <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>

        <!-- Date Items (Row matching user mockup) -->
        <div id="date-strip-container" class="flex items-center gap-3 flex-1 overflow-x-auto no-scrollbar scroll-smooth py-1 px-0.5">
            @foreach ($dates as $d)
                @php
                    $isActive = !empty($d['active']) || ($d['full_date'] ?? '') === $selectedDate;
                    $dateShort = $d['date_short'] ?? $d['date'];
                    $dayFull = $d['day_full'] ?? $d['day'];
                @endphp
                <a
                    href="{{ request()->fullUrlWithQuery(['date' => $d['full_date'] ?? '']) }}"
                    class="flex-1 min-w-[110px] sm:min-w-[130px] p-3 sm:p-4 rounded-2xl text-center transition duration-150 shrink-0 block select-none
                           {{ $isActive
                                ? 'bg-white border-2 border-[#0B7B69] ring-2 ring-emerald-500/20 shadow-xs'
                                : 'bg-white border border-slate-200/90 hover:border-slate-300 hover:shadow-2xs' }}"
                >
                    <!-- Date (e.g. 01 Nov) -->
                    <span class="block text-sm sm:text-base font-extrabold text-slate-900 tracking-tight leading-tight">
                        {{ $dateShort }}
                    </span>

                    <!-- Day of week (e.g. Sunday) - Accent colored like mockup -->
                    <span class="block text-xs font-bold text-[#EA580C] mt-1 tracking-wide leading-tight">
                        {{ $dayFull }}
                    </span>
                </a>
            @endforeach
        </div>

        <!-- Next Arrow Button -->
        <button
            type="button"
            id="date-strip-next"
            class="w-10 h-16 sm:h-20 rounded-2xl bg-white border border-slate-200/90 hover:bg-slate-50 flex items-center justify-center text-slate-600 shadow-2xs shrink-0 transition cursor-pointer"
            title="Geser Tanggal Berikutnya"
        >
            <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('date-strip-container');
    const prevBtn = document.getElementById('date-strip-prev');
    const nextBtn = document.getElementById('date-strip-next');

    if (container && prevBtn && nextBtn) {
        prevBtn.addEventListener('click', function () {
            container.scrollBy({ left: -260, behavior: 'smooth' });
        });
        nextBtn.addEventListener('click', function () {
            container.scrollBy({ left: 260, behavior: 'smooth' });
        });
    }
});
</script>
