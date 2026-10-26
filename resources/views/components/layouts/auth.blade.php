<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Autentikasi Akun SportPlace — Solusi booking lapangan olahraga terpercaya di Indonesia.">
    <title>{{ $title ?? 'SportPlace — Autentikasi' }}</title>

    <!-- Google Fonts Poppins (DS II §1) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])
</head>
<body class="min-h-full font-sans text-[#425248] antialiased bg-[#EDF9F2] flex flex-col justify-between selection:bg-[#DEEF25] selection:text-[#06231D]">

    <!-- Top Navigation Header -->
    <x-auth.header />

    <!-- Main Container Slot -->
    <main class="w-full max-w-6xl mx-auto px-4 sm:px-6 my-auto py-4">
        {{ $slot }}
    </main>

    <!-- Bottom Footer -->
    <x-auth.footer />

</body>
</html>
