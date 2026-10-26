@props(['title' => 'Admin'])
<!DOCTYPE html>
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title }} · {{ config('app.name') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>

    <body class="bg-neutral-0 font-sans text-sm text-neutral-700 antialiased">
        <div x-data="{ menuOpen: false }"
                x-on:keydown.escape.window="menuOpen = false"
                class="min-h-screen md:pl-rail lg:pl-sidebar">

            <x-admin.mobile-bar />
            <x-admin.sidebar />

            <div x-show="menuOpen" x-cloak x-transition.opacity @click="menuOpen = false"
                    class="fixed inset-0 z-30 bg-primary-950/50 md:hidden"></div>

            <main class="mx-auto max-w-page px-4 py-5 md:p-6 lg:px-10 lg:py-8">
                {{ $slot }}
            </main>
        </div>
        @livewireScripts
    </body>
</html>