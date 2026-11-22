@props(['name' => '', 'src' => null, 'size' => 'size-9'])
@php
    $initials = \Illuminate\Support\Str::of($name)->explode(' ')->filter()->take(2)
        ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
@endphp

<span x-data="{ failed: false }"
    {{ $attributes->class(['relative grid shrink-0 place-items-center overflow-hidden rounded-full bg-primary-100 text-label-md text-primary-700', $size]) }}>
    @if ($src)
        <img src="{{ $src }}" alt="" x-show="! failed" x-on:error="failed = true" loading="lazy"
            class="absolute inset-0 size-full object-cover">
    @endif
    <span aria-hidden="true">{{ $initials }}</span>
</span>