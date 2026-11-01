@props(['tone' => 'neutral', 'icon' => null])
@php
    $tones = [
        'neutral' => 'bg-surface-sunken text-ink-body',
        'success' => 'bg-success-100 text-success-800',
        'warning' => 'bg-warning-100 text-warning-800',
        'danger'  => 'bg-danger-100 text-danger-700',
        'info'    => 'bg-info-100 text-info-700',
    ];
@endphp

<span {{ $attributes->class([
    'inline-flex items-center gap-1.5 whitespace-nowrap rounded-full px-2.5 py-1 text-label-sm [&>svg]:size-3.5',
    $tones[$tone] ?? $tones['neutral'],
]) }}>
    @if ($icon)
        <x-ui.icon :name="$icon" />
    @endif
    {{ $slot }}
</span>