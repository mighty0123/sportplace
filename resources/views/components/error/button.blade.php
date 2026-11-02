@props(['variant' => 'primary', 'href' => null, 'type' => 'button', 'icon' => null])
@php
    $base = 'inline-flex min-h-12 items-center justify-center gap-2 rounded-md px-5 text-button transition-colors '
        . 'motion-reduce:transition-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white '
        . 'disabled:cursor-not-allowed disabled:opacity-60';
    $variants = [
        'primary' => 'bg-secondary-500 text-primary-950 hover:bg-secondary-400',
        'ghost'   => 'border border-primary-300/60 text-white hover:bg-white/10',
    ];
    $classes = $base . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
@endif
        @if ($icon)<x-ui.icon :name="$icon" />@endif
        {{ $slot }}
@if ($href) </a> @else </button> @endif