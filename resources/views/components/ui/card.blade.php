@props(['tag' => 'div', 'flush' => false])

<{{ $tag }} {{ $attributes->class([
    'rounded-md border border-line bg-surface',
    'p-4 lg:p-5' => ! $flush,
]) }}>
    {{ $slot }}
</{{ $tag }}>