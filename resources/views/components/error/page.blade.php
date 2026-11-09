@props(['code', 'title', 'description', 'hint' => null])
@php
    // Konteks dari alamat URL, bukan dari login
    $home = match (true) {
        request()->is('admin', 'admin/*') => ['Ke dashboard admin', url('/admin')],
        request()->is('mitra', 'mitra/*') => ['Ke dashboard mitra', url('/mitra/dashboard')],
        default                           => ['Ke beranda', url('/')],
    };

    try {
        $assets = app(\Illuminate\Foundation\Vite::class)(['resources/css/app.css']);
    } catch (\Throwable) {
        $assets = '';
    }
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>{{ $title }} · SportPlace</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo-mark.svg') }}">
    
    <style>
        body{margin:0;font-family:Poppins,system-ui,sans-serif;background:#06231d;color:#ddf4ef}
        @keyframes roll-in{from{transform:translateX(-110px) rotate(-200deg);opacity:0}to{transform:none;opacity:1}}
        .ball-roll{transform-box:fill-box;transform-origin:center;animation:roll-in 1s cubic-bezier(.2,.7,.2,1) .2s both}
        @media (prefers-reduced-motion:reduce){.ball-roll{animation:none}}
    </style>
    {!! $assets !!}
</head>

<body class="min-h-dvh bg-primary-950 font-sans antialiased">
    {{-- Sprite angka --}}
    <svg width="0" height="0" class="absolute" aria-hidden="true" focusable="false"><defs><path id="g1" d="M5.4 -114.2V-146H60.8V0H25.2V-114.2Z"/><path id="g2" d="M14 -32.4Q33.6 -48.6 44.9 -59Q56.2 -69.4 64 -80.8Q71.8 -92.2 71.8 -103Q71.8 -111.2 68 -115.8Q64.2 -120.4 56.6 -120.4Q49 -120.4 44.7 -114.7Q40.4 -109 40.4 -98.6H7.4Q7.8 -115.6 14.7 -127Q21.6 -138.4 32.9 -143.8Q44.2 -149.2 58 -149.2Q81.8 -149.2 93.9 -137Q106 -124.8 106 -105.2Q106 -83.8 91.4 -65.5Q76.8 -47.2 54.2 -29.8H108.2V-2H7.8V-27.4Q14.6 -32.8 14 -32.4Z"/><path id="g3" d="M60 -149.4Q75 -149.4 85.7 -144.2Q96.4 -139 101.9 -130Q107.4 -121 107.4 -109.8Q107.4 -96.6 100.8 -88.3Q94.2 -80 85.4 -77V-76.2Q96.8 -72.4 103.4 -63.6Q110 -54.8 110 -41Q110 -28.6 104.3 -19.1Q98.6 -9.6 87.7 -4.2Q76.8 1.2 61.8 1.2Q37.8 1.2 23.5 -10.6Q9.2 -22.4 8.4 -46.2H41.6Q41.8 -37.4 46.6 -32.3Q51.4 -27.2 60.6 -27.2Q68.4 -27.2 72.7 -31.7Q77 -36.2 77 -43.6Q77 -53.2 70.9 -57.5Q64.8 -61.8 51.4 -61.8H45V-89.6H51.4Q61.6 -89.6 67.9 -93.1Q74.2 -96.6 74.2 -105.6Q74.2 -112.8 70.2 -116.8Q66.2 -120.8 59.2 -120.8Q51.6 -120.8 47.9 -116.2Q44.2 -111.6 43.6 -104.8H10.2Q11 -126.2 24.2 -137.8Q37.4 -149.4 60 -149.4Z"/><path id="g4" d="M9.8 -26V-53.6L73 -144H111.2V-55.6H127.6V-26H111.2V0H77V-26ZM79.4 -106.4 45.8 -55.6H79.4Z"/><path id="g5" d="M111.8 -116.2H49.4V-88.6Q53.4 -93 60.6 -95.8Q67.8 -98.6 76.2 -98.6Q91.2 -98.6 101.1 -91.8Q111 -85 115.6 -74.2Q120.2 -63.4 120.2 -50.8Q120.2 -27.4 107 -13.7Q93.8 0 69.8 0Q53.8 0 42 -5.5Q30.2 -11 23.8 -20.8Q17.4 -30.6 16.8 -43.4H50.2Q51.4 -37.2 56 -33.1Q60.6 -29 68.6 -29Q78 -29 82.6 -35Q87.2 -41 87.2 -51Q87.2 -60.8 82.4 -66Q77.6 -71.2 68.4 -71.2Q61.6 -71.2 57.2 -67.9Q52.8 -64.6 51.4 -59.2H18.4V-146.2H111.8Z"/><path id="g9" d="M64.2 -27Q74.8 -27 79.2 -36.4Q83.6 -45.8 83.6 -67Q79.2 -60.8 71.6 -57.4Q64 -54 54.6 -54Q41.8 -54 31.8 -59.3Q21.8 -64.6 16.1 -75.1Q10.4 -85.6 10.4 -100.6Q10.4 -115.4 16.5 -126.4Q22.6 -137.4 33.9 -143.4Q45.2 -149.4 60.6 -149.4Q90.2 -149.4 102 -131Q113.8 -112.6 113.8 -76.8Q113.8 -50.8 109.1 -33.9Q104.4 -17 93.3 -8.2Q82.2 0.6 63 0.6Q47.8 0.6 37.1 -5.4Q26.4 -11.4 20.7 -21.2Q15 -31 14.2 -42.8H45.8Q48.8 -27 64.2 -27ZM80.8 -100Q80.8 -109.6 75.6 -114.8Q70.4 -120 61.6 -120Q52.8 -120 47.7 -114.7Q42.6 -109.4 42.6 -100.2Q42.6 -91.6 47.7 -86.3Q52.8 -81 62.4 -81Q70.8 -81 75.8 -86.1Q80.8 -91.2 80.8 -100Z"/></defs></svg>

    <div class="relative isolate flex min-h-dvh flex-col overflow-hidden bg-admin-hero">
        {{-- Cahaya di belakang ilustrasi dan garis lapangan samar --}}
        <div class="pointer-events-none absolute inset-0 -z-10" aria-hidden="true"
            style="background:radial-gradient(44rem 30rem at 72% 45%, rgb(10 118 96 / .38), transparent 70%)"></div>
        <svg class="pointer-events-none absolute -bottom-24 -left-32 -z-10 w-160 max-w-none text-primary-800/60"
            viewBox="0 0 480 320" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <circle cx="240" cy="160" r="70"/><path d="M240 0v320"/>
            <rect x="0" y="60" width="90" height="200"/><rect x="390" y="60" width="90" height="200"/>
            <circle cx="240" cy="160" r="5" fill="currentColor"/>
        </svg>

        <header class="px-5 py-5 sm:px-8">
            <a href="{{ $home[1] }}" class="inline-block rounded-md focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-white">
                <img src="{{ asset('images/logo.svg') }}" alt="SportPlace, {{ $home[0] }}" class="h-9">
            </a>
        </header>

        <main class="mx-auto grid w-full max-w-6xl flex-1 content-center items-center gap-8 px-5 pb-10 pt-2 sm:px-8 lg:grid-cols-[minmax(0,1fr)_minmax(0,1.15fr)] lg:gap-10">
            <div class="text-center lg:text-left">
                <h1 class="text-display text-balance text-white">{{ $title }}</h1>
                <p class="mx-auto mt-4 max-w-md text-balance text-body-lg text-primary-100 lg:mx-0">{{ $description }}</p>
                @if ($hint)
                    <p class="mx-auto mt-2 max-w-md text-balance text-body-md text-primary-200 lg:mx-0">{{ $hint }}</p>
                @endif

                <div class="mt-9 flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:justify-center sm:gap-4 lg:justify-start
                        *:w-full sm:*:w-auto">
                @if (isset($actions) && $actions->isNotEmpty())
                    {{ $actions }}
                @else
                    <x-error.button :href="$home[1]">{{ $home[0] }}</x-error.button>
                    <x-error.button variant="ghost" icon="arrow-left" data-home="{{ $home[1] }}"
                                    onclick="history.length > 1 ? history.back() : (location.href = this.dataset.home)">
                        Kembali
                    </x-error.button>
                @endif
            </div>
            </div>

            <div class="order-first mx-auto w-full max-w-sm sm:max-w-md lg:order-last lg:max-w-none" aria-hidden="true">
                {{ $scene ?? '' }}
            </div>
        </main>

        <footer class="px-5 pb-6 text-center text-caption text-primary-200">
            Kode galat {{ $code }}. Sebutkan kode ini bila menghubungi tim.
        </footer>
    </div>

    @stack('scripts')
</body>
</html>