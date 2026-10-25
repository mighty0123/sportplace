<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F4F7F5]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'SportPlace' }}</title>

    <!-- Google Fonts Poppins (DS II §1) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])
</head>
<body class="min-h-full font-sans text-slate-800 antialiased bg-[#F4F7F5] flex flex-col justify-between py-8 px-4 sm:px-6 lg:px-8">

    <!-- Top Navigation Simple Header -->
    <header class="w-full max-w-md sm:max-w-xl mx-auto flex items-center justify-between pb-6">
        <a href="/" class="transition hover:opacity-90">
            <x-application-logo subtitle="SportPlace" />
        </a>
        <a href="/" class="text-xs font-semibold text-slate-500 hover:text-[#0B7B69] transition flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Beranda</span>
        </a>
    </header>

    <!-- Main Auth Card Container -->
    <main class="w-full max-w-md sm:max-w-lg mx-auto">
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200/90 p-6 sm:p-9 transition-all">
            @if(isset($badge))
                <div class="mb-3">
                    <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-[#0B7B69] border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#0B7B69]"></span>
                        {{ $badge }}
                    </span>
                </div>
            @endif

            @if(isset($heading))
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 mb-1.5">{{ $heading }}</h1>
            @endif

            @if(isset($subheading))
                <p class="text-sm text-slate-500 mb-6 leading-relaxed">{{ $subheading }}</p>
            @endif

            <!-- Global Status Alert (Success) -->
            @if (session('status'))
                <div class="mb-5 rounded-xl bg-emerald-50 border border-emerald-200 p-4 text-sm text-emerald-800 flex items-start gap-2.5">
                    <svg class="w-5 h-5 text-[#0B7B69] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="leading-relaxed">{{ session('status') }}</div>
                </div>
            @endif

            <!-- Global Error Banner if needed -->
            @if (session('error'))
                <div class="mb-5 rounded-xl bg-rose-50 border border-rose-200 p-4 text-sm text-rose-800 flex items-start gap-2.5">
                    <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="leading-relaxed">{{ session('error') }}</div>
                </div>
            @endif

            {{ $slot }}
        </div>
    </main>

    <!-- Footer Simple -->
    <footer class="w-full max-w-md sm:max-w-xl mx-auto pt-8 text-center text-xs text-slate-400">
        <p>&copy; {{ date('Y') }} SportPlace Indonesia. Seluruh hak cipta dilindungi undang-undang.</p>
    </footer>

</body>
</html>
