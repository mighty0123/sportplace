@props([
    'title' => null,
    'subtitle' => 'Pantau jadwal sewa lapangan dan kelola aktivitas olahragamu di sini.',
    'actionText' => 'Booking Lapangan Baru',
    'actionUrl' => url('/penyewa/booking/baru'),
    'actionId' => 'btn-booking-baru',
])

@php
    $defaultTitle = 'Halo, ' . (auth()->user()?->name ?? 'Dimas Pratama');
    $displayTitle = $title ?? $defaultTitle;
@endphp

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
            {{ $displayTitle }}
        </h1>
        @if ($subtitle)
            <p class="text-sm text-slate-500 mt-1">
                {{ $subtitle }}
            </p>
        @endif
    </div>

    @if ($actionText && $actionUrl)
        <a href="{{ $actionUrl }}"
           id="{{ $actionId }}"
           class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-[#0B7B69] hover:bg-[#086354] active:scale-[0.98] text-white text-sm font-semibold shadow-xs hover:shadow-md transition-all duration-150 whitespace-nowrap shrink-0 min-h-[46px] group">
            <div class="w-5 h-5 rounded-full bg-white/20 flex items-center justify-center transition-transform group-hover:scale-110">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
            <span>{{ $actionText }}</span>
        </a>
    @endif
</div>
