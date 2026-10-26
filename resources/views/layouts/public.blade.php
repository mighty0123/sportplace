<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="SportPlace - Platform reservasi dan booking lapangan olahraga terpercaya di Indonesia. Temukan lapangan futsal, badminton, basket, tenis, dan lainnya dengan konfirmasi instan.">
    <meta name="theme-color" content="#06231D">

    <title>{{ $title ?? 'SportPlace — Cari Lapangan Olahraga. Main Tanpa Ribet.' }}</title>

    <!-- Google Fonts Poppins (DS II §1) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])
</head>
<body class="font-sans bg-[#FCFBF5] text-[#425248] antialiased selection:bg-[#DEEF25] selection:text-[#06231D] min-h-screen flex flex-col">

    <!-- Header / Navbar Component -->
    <x-public.navbar />

    <!-- Main Content -->
    <main class="flex-grow">
        {{ $slot }}
    </main>

    <!-- Footer Component -->
    <x-public.footer />

</body>
</html>
