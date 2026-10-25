@props([
    'label'      => 'Label',
    'value'      => '0',
    'badge'      => null,
    'badgeType'  => 'pill-green', // 'pill-green' | 'text-gray' | 'trend-green'
    'note'       => null,
    'noteIcon'   => null,         // 'dot' | 'calendar' | 'check'
    'icon'       => 'calendar',   // 'flag' | 'clock' | 'trophy' | 'calendar'
])

@php
    $icons = [
        'flag' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 2H21v10l-2.5-1H13l-1-2H5a2 2 0 00-2 2z"/>',
        'clock' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        'trophy' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>',
        'calendar' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>',
    ];
    $iconPath = $icons[$icon] ?? $icons['calendar'];
@endphp

<div class="bg-white rounded-2xl border border-slate-200/90 p-5 sm:p-6 shadow-xs hover:shadow-md transition-shadow duration-200 flex flex-col justify-between">
    <!-- Top Row: Label & Icon -->
    <div class="flex items-center justify-between mb-3">
        <span class="text-xs sm:text-sm font-medium text-slate-500 tracking-normal">{{ $label }}</span>
        <div class="w-10 h-10 rounded-xl bg-[#E8F5F1] text-[#0B7B69] flex items-center justify-center shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                {!! $iconPath !!}
            </svg>
        </div>
    </div>

    <!-- Value Row + Badge -->
    <div class="flex items-baseline gap-2.5">
        <span class="text-3xl sm:text-4xl font-extrabold text-slate-900 tabular-nums leading-none tracking-tight">
            {{ $value }}
        </span>

        @if ($badge)
            @if ($badgeType === 'pill-green')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100/80 text-emerald-800">
                    {{ $badge }}
                </span>
            @elseif ($badgeType === 'trend-green')
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100/80 text-emerald-800">
                    <svg class="w-3 h-3 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    {{ $badge }}
                </span>
            @elseif ($badgeType === 'text-gray')
                <span class="text-xs font-normal text-slate-400">
                    {{ $badge }}
                </span>
            @else
                <span class="text-xs font-medium text-slate-600">
                    {{ $badge }}
                </span>
            @endif
        @endif
    </div>

    <!-- Bottom Note Row -->
    <div class="mt-4 pt-1">
        @if ($note)
            <div class="text-xs text-slate-500 flex items-center gap-1.5">
                @if ($noteIcon === 'dot')
                    <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                @elseif ($noteIcon === 'calendar')
                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                @elseif ($noteIcon === 'check')
                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                @endif
                <span class="truncate">{{ $note }}</span>
            </div>
        @endif

        {{ $slot }}
    </div>
</div>
