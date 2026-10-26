@props(['name'])
@php
    $icons = [
        'layout-grid' => '<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/>',
        'user-check'  => '<circle cx="9" cy="8" r="4"/><path d="M2 21v-1a6 6 0 0 1 6-6h2"/><path d="m16 18 2 2 4-4"/>',
        'file-check'  => '<path d="M5 3h10l4 4v14H5z"/><path d="m9 14 2 2 4-4"/>',
        'database'    => '<ellipse cx="12" cy="5" rx="8" ry="3"/><path d="M4 5v14c0 1.7 3.6 3 8 3s8-1.3 8-3V5"/><path d="M4 12c0 1.7 3.6 3 8 3s8-1.3 8-3"/>',
        'receipt'     => '<path d="M4 4h16v17l-4-2-4 2-4-2-4 2z"/><path d="M8 9h8M8 13h5"/>',
        'percent'     => '<path d="M19 5 5 19"/><circle cx="7" cy="7" r="2.5"/><circle cx="17" cy="17" r="2.5"/>',
        'users'       => '<circle cx="9" cy="8" r="4"/><path d="M2 21a7 7 0 0 1 14 0"/><path d="M17 4a4 4 0 0 1 0 8M22 21a7 7 0 0 0-4-6"/>',
        'menu'        => '<path d="M4 7h16M4 12h16M4 17h16"/>',
        'x'           => '<path d="M6 6l12 12M18 6 6 18"/>',
    ];
@endphp
<svg {{ $attributes->merge(['class' => 'size-[18px] shrink-0']) }}
        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $icons[$name] ?? '' !!}</svg>