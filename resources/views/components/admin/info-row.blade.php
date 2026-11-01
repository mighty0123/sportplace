@props(['label', 'value' => null])

<div class="grid grid-cols-[6.5rem_minmax(0,1fr)] items-baseline gap-x-3 py-1.5">
    <dt class="text-caption text-ink-muted">{{ $label }}</dt>
    <dd class="min-w-0 wrap-break-word text-body-sm text-ink">
        @if ($slot->isNotEmpty()) {{ $slot }} @else {{ $value ?: '—' }} @endif
    </dd>
</div>