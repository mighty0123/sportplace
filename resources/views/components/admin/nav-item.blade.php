@props(['item', 'badge' => null])
@php
    $disabled = $item['disabled'] ?? false;
    $route  = $item['route'] ?? null;
    $exists = $route && \Illuminate\Support\Facades\Route::has($route);
    $active = $exists && request()->routeIs($item['active'] ?? $route);
@endphp

@if ($disabled)
    <span aria-disabled="true" title="Segera hadir"
        class="flex min-h-11 items-center gap-2.5 rounded-lg px-2.5 text-label-md text-neutral-400
            md:w-12 md:justify-center md:px-0 lg:w-auto lg:justify-start lg:px-2.5">
        <x-ui.icon :name="$item['icon']" />
        <span class="leading-tight md:sr-only lg:not-sr-only">
            {{ $item['label'] }}
            <span class="block text-label-sm">Segera hadir</span>
        </span>
    </span>
@else
    <a href="{{ $exists ? route($route) : '#' }}"
        @if ($active) aria-current="page" @endif
        @class([
            'relative flex min-h-11 items-center gap-2.5 rounded-lg px-2.5 text-label-md transition-colors',
            'md:w-12 md:justify-center md:px-0 lg:w-auto lg:justify-start lg:px-2.5',
            'bg-primary-500 text-white' => $active,
            'text-neutral-200 hover:bg-primary-800' => ! $active,
        ])>
        <x-ui.icon :name="$item['icon']" />
        <span class="md:sr-only lg:not-sr-only">{{ $item['label'] }}</span>

        @if ($badge)
            <span class="ml-auto text-label-sm text-primary-200 md:sr-only lg:not-sr-only">{{ $badge }}</span>
            <span aria-hidden="true"
                class="absolute right-2 top-2 hidden size-2 rounded-full border-2 border-primary-950 bg-warning-500 md:block lg:hidden"></span>
        @endif
    </a>
@endif