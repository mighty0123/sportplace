@props(['count', 'title', 'cta', 'href' => '#', 'hint' => null])

<div {{ $attributes->class(
    'py-4 first:pt-0 last:pb-0 '
    . 'md:px-6 md:py-1 md:first:pl-0 md:last:pr-0 '
    . 'lg:px-0 lg:py-4 lg:first:pt-0 lg:last:pb-0'
) }}>
    <p class="text-metric text-warning-800">{{ $count }}</p>
    <h3 class="mt-1 text-h6 text-ink">{{ $title }}</h3>
    @if ($hint)
        <p class="text-caption text-ink-muted">{{ $hint }}</p>
    @endif
    <x-ui.button variant="outline" :href="$href" trailing="arrow-right" class="mt-3 w-full md:w-auto">
        {{ $cta }}
    </x-ui.button>
</div>