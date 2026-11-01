@props([
    'variant'  => 'primary',
    'size'     => 'md',
    'href'     => null,     // kalau diisi, dirender sebagai <a>
    'type'     => 'button',
    'icon'     => null,     // ikon di kiri
    'trailing' => null,     // ikon di kanan
])

@php
    $variants = [
        'primary'        => 'bg-brand text-white hover:bg-brand-hover active:bg-brand-active',
        'secondary'      => 'bg-secondary-500 text-primary-900 hover:bg-secondary-600',
        'outline'        => 'border border-brand text-brand-link hover:bg-primary-50',
        'neutral'        => 'border border-line-strong text-ink-body hover:bg-surface-muted',
        'ghost'          => 'text-brand-link hover:bg-primary-50',
        'danger'         => 'bg-danger-600 text-white hover:bg-danger-700 active:bg-danger-800',
        'danger-outline' => 'border border-danger-600 text-danger-700 hover:bg-danger-50',
    ];
    $sizes = [
        'md' => 'min-h-11 px-4 lg:min-h-10',   // 44px di layar kecil, 40px di desktop
        'sm' => 'min-h-9 px-3',
    ];

    $classes = 'group inline-flex items-center justify-center gap-2 rounded-md text-button '
        . 'transition-colors duration-150 active:scale-[.98] motion-reduce:transition-none '
        . 'disabled:pointer-events-none disabled:border-transparent disabled:bg-neutral-200 disabled:text-neutral-400 '
        . $variants[$variant] . ' ' . $sizes[$size];
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
@endif
        @if ($icon)<x-ui.icon :name="$icon" />@endif
        {{ $slot }}
        @if ($trailing)
            <x-ui.icon :name="$trailing" class="transition-transform group-hover:translate-x-0.5 motion-reduce:transform-none" />
        @endif
@if ($href) </a> @else </button> @endif