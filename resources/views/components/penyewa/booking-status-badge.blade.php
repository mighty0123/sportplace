@props([
    'status' => 'confirmed', // pending | confirmed | completed | cancelled
])

@php
    $normalized = strtolower(trim($status));

    $configs = [
        'pending' => [
            'label' => 'Pending',
            'pill'  => 'bg-amber-50 text-amber-800 border border-amber-200/90',
            'icon'  => 'dot-amber',
        ],
        'confirmed' => [
            'label' => 'Confirmed',
            'pill'  => 'bg-emerald-50 text-emerald-800 border border-emerald-200/80',
            'icon'  => 'dot-emerald',
        ],
        'completed' => [
            'label' => 'Completed',
            'pill'  => 'bg-emerald-50 text-emerald-800 border border-emerald-200/80',
            'icon'  => 'check-emerald',
        ],
        'cancelled' => [
            'label' => 'Cancelled',
            'pill'  => 'bg-rose-50 text-rose-800 border border-rose-200/80',
            'icon'  => 'x-rose',
        ],
    ];

    $cfg = $configs[$normalized] ?? $configs['confirmed'];
@endphp

<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $cfg['pill'] }} shadow-2xs whitespace-nowrap">
    @if ($cfg['icon'] === 'dot-amber')
        <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span>
    @elseif ($cfg['icon'] === 'dot-emerald')
        <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
    @elseif ($cfg['icon'] === 'check-emerald')
        <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10" stroke-width="2"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12l2.5 2.5L16 9"/>
        </svg>
    @elseif ($cfg['icon'] === 'x-rose')
        <svg class="w-3.5 h-3.5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10" stroke-width="2"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9l6 6m0-6l-6 6"/>
        </svg>
    @endif
    <span>{{ $cfg['label'] }}</span>
</span>
