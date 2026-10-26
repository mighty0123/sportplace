@props(['item'])
@php
    $route  = $item['route'] ?? null;
    $exists = $route && \Illuminate\Support\Facades\Route::has($route);
    $active = $exists && request()->routeIs($route);
@endphp

<a href="{{ $exists ? route($route) : '#' }}"
    @if ($active) aria-current="page" @endif
    @class([
        'flex min-h-11 items-center gap-2.5 rounded-lg px-2.5 font-medium transition-colors',
        'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-200',
        'md:w-12 md:justify-center md:px-0 lg:w-auto lg:justify-start lg:px-2.5',
        'bg-primary-500 text-white' => $active,
        'hover:bg-primary-800' => ! $active,
    ])>
    <x-ui.icon :name="$item['icon']" />
    <span class="md:sr-only lg:not-sr-only">{{ $item['label'] }}</span>
</a>