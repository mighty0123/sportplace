@props(['title' => 'Admin'])
@php $hasHero = isset($hero) && $hero->isNotEmpty(); @endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} · {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body @class([
        'font-sans text-sm text-ink-body antialiased',
        'bg-admin-canvas-flat' => $hasHero,
        'bg-admin-canvas' => ! $hasHero,
    ])>
    <div x-data="{ menuOpen: false }"
        x-on:keydown.escape.window="menuOpen = false"
        class="min-h-screen md:pl-rail lg:pl-sidebar">

        <x-admin.mobile-bar />
        <x-admin.sidebar />

        <div x-show="menuOpen" x-cloak x-transition.opacity @click="menuOpen = false"
            class="fixed inset-0 z-30 bg-primary-950/50 md:hidden"></div>

            @if ($hasHero)
                <section class="relative">
                    <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
                        <div class="absolute inset-0 bg-admin-hero"></div>

                        <svg class="absolute -right-16 -top-12 hidden h-96 text-primary-700/40 md:block"
                            viewBox="0 0 480 320" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="240" cy="160" r="70"/><path d="M240 0v320"/>
                            <rect x="0" y="60" width="90" height="200"/><rect x="390" y="60" width="90" height="200"/>
                            <circle cx="240" cy="160" r="5" fill="currentColor"/>
                        </svg>

                        <svg class="absolute inset-x-0 bottom-0 h-rail w-full md:h-24 lg:h-32"
                            viewBox="0 0 1440 120" preserveAspectRatio="none">
                            <path class="fill-admin-canvas opacity-40"
                                d="M0 50 C 200 100, 420 100, 620 60 S 1040 10, 1240 50 S 1380 80, 1440 60 L1440 120 L0 120 Z"/>
                            <path class="fill-admin-canvas"
                                d="M0 70 C 180 20, 360 20, 540 60 S 900 110, 1080 60 S 1320 20, 1440 50 L1440 120 L0 120 Z"/>
                        </svg>
                    </div>

                    <div class="relative mx-auto max-w-page px-4 pb-24 pt-5 md:px-6 md:pb-28 md:pt-6 lg:px-10 lg:pb-36 lg:pt-8">
                        {{ $hero }}
                    </div>
                </section>
            @endif

        <main @class([
            'mx-auto max-w-page',
            'relative -mt-10 px-4 pb-8 md:-mt-14 md:px-6 md:pb-10 lg:-mt-20 lg:px-10' => $hasHero,
            'px-4 py-5 md:p-6 lg:px-10 lg:py-8' => ! $hasHero,
        ])>
            <x-ui.flash />
            {{ $slot }}
        </main>

        <x-admin.logout-modal />
    </div>
    @livewireScripts
</body>
</html>