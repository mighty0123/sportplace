@props(['label', 'value', 'icon', 'caption' => null, 'delta' => null, 'trend' => 'up'])
@php
    $deltaColor = match ($trend) {
        'up' => 'text-success-800',
        'down' => 'text-danger-700',
        default => 'text-ink-muted',
    };
@endphp

<div {{ $attributes->class('md:rounded-md md:border md:border-line md:bg-surface md:p-4 lg:p-5') }}>

    {{-- Mobile: satu baris ringkas --}}
    <div class="flex items-center gap-3 px-3.5 py-3 md:hidden">
        <span class="grid size-9 shrink-0 place-items-center rounded-md bg-brand-soft text-primary-700">
            <x-ui.icon :name="$icon" />
        </span>
        <div class="min-w-0 flex-1">
            <p class="text-label-md text-ink-strong">{{ $label }}</p>
            @if ($delta)
                <p class="text-caption {{ $deltaColor }}">{{ $delta }}</p>
            @endif
        </div>
        <p class="shrink-0 text-metric text-ink">{{ $value }}</p>
    </div>

    {{-- md ke atas: bertumpuk --}}
    <div class="hidden md:block">
        <div class="flex items-start justify-between gap-3">
            <p class="text-label-md text-ink-strong">{{ $label }}</p>
            <span class="grid size-9 shrink-0 place-items-center rounded-md bg-brand-soft text-primary-700">
                <x-ui.icon :name="$icon" />
            </span>
        </div>
        <p class="mt-2 text-metric text-ink">{{ $value }}</p>
        @if ($caption)
            <p class="text-caption text-ink-muted">{{ $caption }}</p>
        @endif
        @if ($delta)
            <p class="mt-3 text-label-sm {{ $deltaColor }}">{{ $delta }}</p>
        @endif
    </div>
</div>