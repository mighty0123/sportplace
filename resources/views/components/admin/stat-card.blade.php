@props(['label', 'value', 'caption' => null, 'delta' => null])

<div {{ $attributes->class(
    'flex items-start justify-between gap-3 py-3.5 '
        . 'md:flex-col md:justify-start md:gap-0 md:rounded-md md:border md:border-line md:bg-surface md:p-4 lg:p-5'
) }}>
    <div class="min-w-0 md:contents">
        <p class="text-label-md text-ink-strong md:order-1">{{ $label }}</p>
        @if ($caption)
            <p class="text-caption text-ink-muted md:order-3">{{ $caption }}</p>
        @endif
    </div>
    <div class="shrink-0 text-right md:contents">
        <p class="text-metric text-ink md:order-2 md:mb-1 md:mt-2 md:text-left">{{ $value }}</p>
        @if ($delta)
            <p class="text-label-sm text-success-800 md:order-4 md:mt-3 md:text-left">{{ $delta }}</p>
        @endif
    </div>
</div>