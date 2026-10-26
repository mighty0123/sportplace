<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F4F7F5]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Portal Mitra — SportPlace' }}</title>

    <!-- Google Fonts Poppins (DS II §1) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])
</head>
<body class="h-full font-sans text-slate-800 antialiased flex flex-col md:flex-row bg-[#F4F7F5]">

    <!-- Backdrop Gelap untuk Mobile saat Sidebar Terbuka -->
    <div id="sidebar-backdrop" class="fixed inset-0 bg-slate-900/60 z-40 hidden md:hidden transition-opacity opacity-0 duration-300 backdrop-blur-xs" aria-hidden="true"></div>

    <!-- Sidebar (Desktop Selalu Tampil, Mobile Drawer Off-Canvas) -->
    <aside id="main-sidebar" class="fixed inset-y-0 left-0 w-64 bg-[#06231B] text-slate-200 z-50 flex flex-col overflow-hidden transition-transform duration-300 ease-in-out -translate-x-full md:translate-x-0">
        <div class="flex items-center justify-between px-6 h-18 border-b border-[#0A3025] shrink-0">
            <!-- Menekan logo di dalam sidebar akan menutupnya di layar kecil -->
            <button id="sidebar-logo-close" type="button" class="flex items-center text-left cursor-pointer focus:outline-none" title="Tutup Menu Sidebar">
                <x-application-logo subtitle="MitraHub" :dark="true" />
            </button>
            <button id="sidebar-close-btn" type="button" class="md:hidden p-1.5 -mr-1 rounded-lg text-emerald-400 hover:text-white hover:bg-[#07261F] transition cursor-pointer" aria-label="Tutup Menu">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto no-scrollbar [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden">
            <div class="px-3 pb-1 text-[10px] font-bold tracking-wider text-emerald-400/60 uppercase">Utama</div>
            <a href="{{ route('mitra.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('mitra.dashboard') ? 'bg-[#0B7B69] text-white shadow-xs' : 'text-emerald-100/70 hover:bg-[#07261F] hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zM14 13a1 1 0 011-1h4a1 1 0 011 1v6a1 1 0 01-1 1h-4a1 1 0 01-1-1v-6z"/>
                </svg>
                <span>Dashboard</span>
            </a>

            <div class="pt-3 px-3 pb-1 text-[10px] font-bold tracking-wider text-emerald-400/60 uppercase">Operasional</div>
            <a href="{{ url('/mitra/venues') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition {{ request()->is('mitra/venues*') ? 'bg-[#0B7B69] text-white shadow-xs' : 'text-emerald-100/70 hover:bg-[#07261F] hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <span>Venue & Lapangan</span>
            </a>
            <a href="{{ url('/mitra/jadwal') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition {{ request()->is('mitra/jadwal*') ? 'bg-[#0B7B69] text-white shadow-xs' : 'text-emerald-100/70 hover:bg-[#07261F] hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0zM8 3v4M16 3v4"/>
                </svg>
                <span>Jadwal & Slot</span>
            </a>
            <a href="{{ url('/mitra/harga') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition {{ request()->is('mitra/harga*') ? 'bg-[#0B7B69] text-white shadow-xs' : 'text-emerald-100/70 hover:bg-[#07261F] hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 7h.01M7 3h5a1.99 1.99 0 011.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V5a2 2 0 012-2z"/>
                </svg>
                <span>Pengaturan Harga</span>
            </a>
            <a href="{{ url('/mitra/pesanan') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition {{ request()->is('mitra/pesanan*') ? 'bg-[#0B7B69] text-white shadow-xs' : 'text-emerald-100/70 hover:bg-[#07261F] hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span>Kalender Booking</span>
            </a>

            <div class="pt-3 px-3 pb-1 text-[10px] font-bold tracking-wider text-emerald-400/60 uppercase">Keuangan</div>
            <a href="{{ url('/mitra/laporan') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition {{ request()->is('mitra/laporan*') ? 'bg-[#0B7B69] text-white shadow-xs' : 'text-emerald-100/70 hover:bg-[#07261F] hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <span>Laporan Pendapatan</span>
            </a>

            <div class="pt-3 px-3 pb-1 text-[10px] font-bold tracking-wider text-emerald-400/60 uppercase">Akun</div>
            <a href="{{ url('/mitra/profil') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition {{ request()->is('mitra/profil*') ? 'bg-[#0B7B69] text-white shadow-xs' : 'text-emerald-100/70 hover:bg-[#07261F] hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <span>Profil & Legalitas</span>
            </a>
        </nav>

        <div class="p-4 border-t border-[#0A3025] bg-[#051c15] shrink-0">
            <div class="flex items-center justify-between mb-3">
                <div class="truncate pr-2">
                    <p class="text-xs font-semibold text-white truncate">{{ auth()->user()?->name ?? 'Mitra SportPlace' }}</p>
                    <span class="inline-flex items-center gap-1 text-[10px] text-emerald-400 font-medium">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        Terverifikasi
                    </span>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-2 text-xs font-semibold rounded-lg bg-[#07261F] hover:bg-rose-700 text-emerald-100 hover:text-white transition cursor-pointer min-h-[36px]">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span>Keluar Akun</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 md:pl-64 flex flex-col min-h-screen">
        <!-- Topbar Khusus Mobile (Tidak Sticky / Tidak Mengikuti Scroll) -->
        <header class="md:hidden h-16 bg-white border-b border-slate-200/80 flex items-center justify-between px-4 sm:px-6">
            <div class="flex items-center gap-3">
                <!-- Mobile: Cukup Hamburger Button di Kiri Atas (tanpa logo dan teks logo) -->
                <button id="mobile-sidebar-toggle" type="button" class="p-2 -ml-2 rounded-xl text-slate-700 hover:text-emerald-700 hover:bg-slate-100 active:scale-95 transition cursor-pointer focus:outline-none" aria-label="Buka Menu Sidebar" title="Buka Menu Sidebar">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

            <div class="flex items-center gap-3">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-xs font-semibold text-rose-600 hover:underline min-h-[44px] flex items-center">Keluar</button>
                </form>
            </div>
        </header>

        <!-- Flash Messages & Main Content -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
            {{ $slot }}
        </main>
    </div>

    <!-- Script Interaktif Toggle Sidebar Mobile -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('main-sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            const toggleBtn = document.getElementById('mobile-sidebar-toggle');
            const closeBtn = document.getElementById('sidebar-close-btn');
            const logoCloseBtn = document.getElementById('sidebar-logo-close');

            let isOpen = false;

            function openSidebar() {
                isOpen = true;
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('translate-x-0');
                backdrop.classList.remove('hidden');
                requestAnimationFrame(() => {
                    backdrop.classList.remove('opacity-0');
                    backdrop.classList.add('opacity-100');
                });
                document.body.classList.add('overflow-hidden', 'md:overflow-auto');
            }

            function closeSidebar() {
                isOpen = false;
                sidebar.classList.remove('translate-x-0');
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.remove('opacity-100');
                backdrop.classList.add('opacity-0');
                setTimeout(() => {
                    if (!isOpen) {
                        backdrop.classList.add('hidden');
                    }
                }, 300);
                document.body.classList.remove('overflow-hidden', 'md:overflow-auto');
            }

            function toggleSidebar() {
                if (isOpen) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            }

            if (toggleBtn) {
                toggleBtn.addEventListener('click', toggleSidebar);
            }

            if (closeBtn) {
                closeBtn.addEventListener('click', closeSidebar);
            }

            if (logoCloseBtn) {
                logoCloseBtn.addEventListener('click', function () {
                    if (window.innerWidth < 768) {
                        closeSidebar();
                    }
                });
            }

            if (backdrop) {
                backdrop.addEventListener('click', closeSidebar);
            }

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && isOpen) {
                    closeSidebar();
                }
            });

            window.addEventListener('resize', function () {
                if (window.innerWidth >= 768 && isOpen) {
                    closeSidebar();
                }
            });
        });
    </script>
</body>
</html>
