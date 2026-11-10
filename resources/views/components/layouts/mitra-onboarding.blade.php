@props(['title' => 'Onboarding Mitra — SportPlace'])
<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F4F7F5]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>

    <!-- Google Fonts Poppins (DS II §1) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-full font-sans text-slate-800 antialiased bg-[#F4F7F5] flex flex-col justify-between">

    <!-- Global Toast Manager -->
    <x-mitra.toast-manager />

    <!-- Top Navbar (#06231B) -->
    <header class="bg-[#06231B] text-white py-3.5 px-4 sm:px-8 border-b border-[#0A3025] sticky top-0 z-30 shadow-xs">
        <div class="max-w-5xl mx-auto flex items-center justify-between">
            <x-application-logo subtitle="MitraHub" :dark="true" />
            <div class="flex items-center gap-4 text-xs">
                @auth
                    <span class="text-emerald-100/70 hidden sm:inline">{{ auth()->user()->email }}</span>
                @else
                    <span class="text-emerald-100/70 hidden sm:inline">Pusat Bantuan: +62 811-9876-5432</span>
                @endauth
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="px-4 py-1.5 rounded-full bg-[#07261F] hover:bg-rose-700 text-emerald-100 hover:text-white transition font-semibold cursor-pointer min-h-[36px]">
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 w-full">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 px-4 text-center text-xs text-slate-500 mt-12">
        <p>&copy; {{ date('Y') }} SportPlace Mitra Indonesia. Seluruh hak cipta dilindungi undang-undang.</p>
    </footer>

    @livewireScripts
</body>
</html>
