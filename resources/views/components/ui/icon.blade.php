@props(['name'])
@php
    $icons = [
        'layout-grid'   => '<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/>',
        'user-check'    => '<circle cx="9" cy="8" r="4"/><path d="M2 21v-1a6 6 0 0 1 6-6h2"/><path d="m16 18 2 2 4-4"/>',
        'file-check'    => '<path d="M5 3h10l4 4v14H5z"/><path d="m9 14 2 2 4-4"/>',
        'file-text'     => '<path d="M5 3h10l4 4v14H5z"/><path d="M9 12h6M9 16h6M9 8h2"/>',
        'database'      => '<ellipse cx="12" cy="5" rx="8" ry="3"/><path d="M4 5v14c0 1.7 3.6 3 8 3s8-1.3 8-3V5"/><path d="M4 12c0 1.7 3.6 3 8 3s8-1.3 8-3"/>',
        'receipt'       => '<path d="M4 4h16v17l-4-2-4 2-4-2-4 2z"/><path d="M8 9h8M8 13h5"/>',
        'percent'       => '<path d="M19 5 5 19"/><circle cx="7" cy="7" r="2.5"/><circle cx="17" cy="17" r="2.5"/>',
        'users'         => '<circle cx="9" cy="8" r="4"/><path d="M2 21a7 7 0 0 1 14 0"/><path d="M17 4a4 4 0 0 1 0 8M22 21a7 7 0 0 0-4-6"/>',
        'menu'          => '<path d="M4 7h16M4 12h16M4 17h16"/>',
        'x'             => '<path d="M6 6l12 12M18 6 6 18"/>',
        'arrow-right'   => '<path d="M5 12h14M13 6l6 6-6 6"/>',
        'store'         => '<path d="M4 9l1-5h14l1 5"/><path d="M4 9a2.67 2.67 0 0 0 5.33 0 2.67 2.67 0 0 0 5.34 0A2.67 2.67 0 0 0 20 9"/><path d="M5 12v8h14v-8"/>',
        'layers'        => '<path d="M12 3 3 8l9 5 9-5z"/><path d="m3 13 9 5 9-5"/>',
        'wallet'        => '<path d="M4 7h15a1 1 0 0 1 1 1v11H5a1 1 0 0 1-1-1z"/><path d="m4 7 12-3v3"/><circle cx="16" cy="13.5" r="1"/>',
        'activity'      => '<path d="M3 12h4l3-8 4 16 3-8h4"/>',
        'check-circle'  => '<circle cx="12" cy="12" r="9"/><path d="m8 12 3 3 5-6"/>',
        'chevron-down'  => '<path d="m6 9 6 6 6-6"/>',
        'user'          => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
        'log-out'       => '<path d="M9 4H5v16h4"/><path d="m16 8 4 4-4 4M20 12H9"/>',
        'x-circle'      => '<circle cx="12" cy="12" r="9"/><path d="m9 9 6 6M15 9l-6 6"/>',
        'send'          => '<path d="M21 3 10 14"/><path d="M21 3l-7 18-4-7-7-4z"/>',
        'ban'           => '<circle cx="12" cy="12" r="9"/><path d="m5.6 5.6 12.8 12.8"/>',
        'clock'         => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
        'alert-circle'  => '<circle cx="12" cy="12" r="9"/><path d="M12 8v5M12 16h.01"/>',
        'info'          => '<circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8h.01"/>',
        'inbox'         => '<path d="M4 13 6 5h12l2 8"/><path d="M4 13v6h16v-6h-5l-1 2h-4l-1-2z"/>',
        'search'        => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
        'chevron-right' => '<path d="m9 6 6 6-6 6"/>',
        'map-pin'       => '<path d="M12 21s7-6.2 7-11a7 7 0 1 0-14 0c0 4.8 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/>',
    ];
@endphp
<svg {{ $attributes->merge(['class' => 'size-[18px] shrink-0']) }}
        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $icons[$name] ?? '' !!}</svg>