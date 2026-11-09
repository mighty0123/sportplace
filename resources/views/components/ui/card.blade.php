@props(['tag' => 'div', 'flush' => false])

<{{ $tag }} {{ $attributes->class([
    'rounded-3xl border border-line bg-surface',
    'p-5 sm:p-6 lg:p-7' => ! $flush,
]) }}>
    {{ $slot }}
</{{ $tag }}>