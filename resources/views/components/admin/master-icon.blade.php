@props(['src' => null, 'name' => '?', 'size' => 'size-10'])
@php
    $initial = mb_strtoupper(mb_substr(trim((string) $name) ?: '?', 0, 1));
@endphp

<span {{ $attributes->class(["relative grid shrink-0 place-items-center overflow-hidden rounded-md bg-primary-100 text-label-lg text-primary-700 {$size}"]) }} aria-hidden="true">
    {{ $initial }}
    @if ($src)
        <img src="{{ $src }}" alt="" loading="lazy" referrerpolicy="no-referrer"
            class="absolute inset-0 size-full bg-surface object-contain p-1" onerror="this.remove()">
    @endif
</span>