@props(['name'])
@php
    $initials = collect(explode(' ', $name))->take(2)->map(fn ($w) => mb_substr($w, 0, 1))->implode('');
@endphp
<div {{ $attributes->class('grid size-9 shrink-0 place-items-center rounded-full bg-primary-100 text-label-md text-primary-700') }}
        aria-hidden="true">{{ $initials }}</div>