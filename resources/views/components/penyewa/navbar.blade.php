@props([
    'active' => 'dashboard', // dashboard | kalender | riwayat | notifikasi | profil
])

@php
    $userName = auth()->user()?->name ?? 'Dimas Pratama';
    $userEmail = auth()->user()?->email ?? 'dimas.pratama@example.com';
    $userAvatar = asset('images/avatars/dimas-pratama.jpg');
    $userRole = 'Penyewa Aktif';
@endphp

<!-- ========== TOP NAVBAR SPORTPLACE ========== -->
<header id="main-navbar" class="sticky top-0 z-40 bg-[#06231B] border-b border-[#0A3025] shadow-md transition-all duration-200 overflow-x-clip">
    <div class="max-w-screen-2xl w-full mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-18 gap-3 sm:gap-4 lg:gap-6">

            <!-- LEFT: Logo & Main Navigation Links -->
            <div class="flex items-center gap-4 lg:gap-6 xl:gap-8 shrink-0">
                <!-- Brand Logo -->
                <a href="{{ url('/penyewa/dashboard') }}" class="flex items-center gap-2.5 sm:gap-3 group focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 rounded-xl" title="SportPlace Dashboard">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-[#0B7B69] group-hover:bg-[#086354] flex items-center justify-center text-white shadow-xs transition-colors duration-150 shrink-0">
                        <!-- Court Icon -->
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="18" height="18" x="3" y="3" rx="4"/>
                            <line x1="3" x2="21" y1="12" y2="12"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </div>
                    <span class="text-lg sm:text-xl font-bold tracking-tight text-white group-hover:text-emerald-100 transition-colors">SportPlace</span>
                </a>

                <!-- Desktop Navigation Links (Converted from Sidebar) -->
                <nav class="hidden lg:flex items-center gap-1">
                    {{-- Dashboard Link --}}
                    <a href="{{ url('/penyewa/dashboard') }}"
                       class="flex items-center gap-2 px-3 py-1.5 xl:px-3.5 xl:py-2 rounded-xl text-xs xl:text-sm font-medium transition duration-150 {{ request()->is('penyewa/dashboard*') || $active === 'dashboard' ? 'bg-[#0B7B69] text-white font-semibold shadow-xs' : 'text-emerald-100/75 hover:bg-[#07261F] hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zM14 13a1 1 0 011-1h4a1 1 0 011 1v6a1 1 0 01-1 1h-4a1 1 0 01-1-1v-6z"/>
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    {{-- Kalender Link --}}
                    <a href="{{ url('/penyewa/kalender') }}"
                       class="flex items-center gap-2 px-3 py-1.5 xl:px-3.5 xl:py-2 rounded-xl text-xs xl:text-sm font-medium transition duration-150 {{ request()->is('penyewa/kalender*') || $active === 'kalender' ? 'bg-[#0B7B69] text-white font-semibold shadow-xs' : 'text-emerald-100/75 hover:bg-[#07261F] hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>Kalender</span>
                    </a>

                    {{-- Riwayat Booking Link --}}
                    <a href="{{ url('/penyewa/riwayat') }}"
                       class="flex items-center gap-2 px-3 py-1.5 xl:px-3.5 xl:py-2 rounded-xl text-xs xl:text-sm font-medium transition duration-150 {{ request()->is('penyewa/riwayat*') || $active === 'riwayat' ? 'bg-[#0B7B69] text-white font-semibold shadow-xs' : 'text-emerald-100/75 hover:bg-[#07261F] hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <span>Riwayat Booking</span>
                    </a>
                </nav>
            </div>

            <!-- RIGHT: Search Input, Notification Bell & User Capsule -->
            <div class="flex items-center gap-2.5 sm:gap-3 lg:gap-4 flex-1 justify-end min-w-0">

                <!-- Search Bar (Desktop & Tablet) -->
                <div class="hidden md:flex items-center gap-2.5 bg-[#07261F] hover:bg-[#092d24] focus-within:bg-[#092d24] border border-[#0A3025] focus-within:border-emerald-500/50 rounded-xl px-3 py-2 w-48 lg:w-60 xl:w-72 2xl:w-80 min-w-0 transition-all duration-150">
                    <svg class="w-4 h-4 text-emerald-400/70 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input
                        id="navbar-search"
                        type="text"
                        placeholder="Cari lapangan, lokasi..."
                        class="bg-transparent text-xs text-emerald-100 placeholder-emerald-400/50 outline-none w-full truncate"
                    >
                </div>

                <!-- Notification Bell Button (from Reference Header) -->
                <a href="{{ url('/penyewa/notifikasi') }}"
                   class="relative p-2 rounded-xl text-emerald-100/80 hover:text-white hover:bg-[#07261F] transition-colors focus:outline-none shrink-0"
                   title="Notifikasi"
                   aria-label="Lihat Notifikasi">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <!-- Notification indicator dot -->
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-amber-400 ring-2 ring-[#06231B]"></span>
                </a>

                <!-- User Profile Dropdown Pill -->
                <div class="relative shrink-0" id="user-profile-dropdown-container">
                    <button
                        id="user-profile-dropdown-btn"
                        type="button"
                        class="flex items-center gap-2 sm:gap-2.5 pl-1.5 pr-2.5 sm:pr-3 py-1 rounded-xl bg-[#07261F]/80 hover:bg-[#07261F] border border-[#0A3025] hover:border-emerald-500/30 transition-all cursor-pointer focus:outline-none focus:ring-2 focus:ring-emerald-400/50"
                        aria-haspopup="true"
                        aria-expanded="false"
                    >
                        <!-- Avatar Photo -->
                        <div class="w-8 h-8 rounded-full overflow-hidden ring-2 ring-emerald-500/40 shrink-0 bg-[#0B7B69] flex items-center justify-center text-white text-xs font-bold">
                            <img src="{{ $userAvatar }}" alt="{{ $userName }}" class="w-full h-full object-cover" onerror="this.style.display='none'">
                            <span class="fallback-initial">{{ strtoupper(substr($userName, 0, 1)) }}</span>
                        </div>

                        <!-- Name & Badge -->
                        <div class="hidden sm:flex flex-col text-left">
                            <span class="text-xs font-bold text-white leading-tight truncate max-w-[110px] xl:max-w-[130px]">{{ $userName }}</span>
                            <span class="text-[10px] text-emerald-400 font-medium leading-tight">{{ $userRole }}</span>
                        </div>

                        <!-- Down Chevron -->
                        <svg class="w-3.5 h-3.5 text-emerald-400/70 hidden sm:block shrink-0 transition-transform duration-150" id="profile-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div id="user-profile-menu"
                         class="absolute right-0 top-full mt-2 w-56 bg-white rounded-2xl shadow-xl border border-slate-200/80 py-2 hidden z-50 origin-top-right transition-all duration-150">
                        <div class="px-4 py-3 border-b border-slate-100 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full overflow-hidden ring-1 ring-slate-200 shrink-0 bg-[#0B7B69] flex items-center justify-center text-white font-bold text-sm">
                                <img src="{{ $userAvatar }}" alt="{{ $userName }}" class="w-full h-full object-cover" onerror="this.style.display='none'">
                                <span>{{ strtoupper(substr($userName, 0, 1)) }}</span>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-slate-900 truncate">{{ $userName }}</p>
                                <p class="text-[11px] text-slate-500 truncate">{{ $userEmail }}</p>
                                <span class="inline-block mt-0.5 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/50">
                                    {{ $userRole }}
                                </span>
                            </div>
                        </div>

                        <div class="py-1">
                            <a href="{{ url('/penyewa/profil') }}"
                               class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span>Profil Saya</span>
                            </a>
                            <a href="{{ url('/penyewa/riwayat') }}"
                               class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <span>Riwayat Pesanan</span>
                            </a>
                            <a href="{{ url('/penyewa/notifikasi') }}"
                               class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                <span>Notifikasi</span>
                            </a>
                        </div>

                        <div class="border-t border-slate-100 pt-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="w-full flex items-center gap-3 px-4 py-2 text-sm text-rose-600 hover:bg-rose-50 transition cursor-pointer">
                                    <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    <span>Keluar</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Mobile Hamburger Button -->
                <button
                    id="mobile-nav-toggle"
                    type="button"
                    class="lg:hidden p-2 rounded-xl text-emerald-100/75 hover:text-white hover:bg-[#07261F] transition focus:outline-none shrink-0"
                    aria-label="Menu Navigasi"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- MOBILE MENU DRAWER (Expands on small screens) -->
        <div id="mobile-nav-menu" class="hidden lg:hidden border-t border-[#0A3025] py-3 space-y-2">
            <!-- Mobile Search -->
            <div class="flex items-center gap-2 bg-[#07261F] border border-[#0A3025] rounded-xl px-3.5 py-2.5">
                <svg class="w-4 h-4 text-emerald-400/70 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input
                    type="text"
                    placeholder="Cari lapangan, lokasi..."
                    class="bg-transparent text-xs text-emerald-100 placeholder-emerald-400/50 outline-none w-full"
                >
            </div>

            <!-- Mobile Nav Links -->
            <nav class="space-y-1">
                <a href="{{ url('/penyewa/dashboard') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->is('penyewa/dashboard*') || $active === 'dashboard' ? 'bg-[#0B7B69] text-white font-semibold' : 'text-emerald-100/75 hover:bg-[#07261F] hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zM14 13a1 1 0 011-1h4a1 1 0 011 1v6a1 1 0 01-1 1h-4a1 1 0 01-1-1v-6z"/>
                    </svg>
                    <span>Dashboard</span>
                </a>
                <a href="{{ url('/penyewa/kalender') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->is('penyewa/kalender*') || $active === 'kalender' ? 'bg-[#0B7B69] text-white font-semibold' : 'text-emerald-100/75 hover:bg-[#07261F] hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>Kalender</span>
                </a>
                <a href="{{ url('/penyewa/riwayat') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->is('penyewa/riwayat*') || $active === 'riwayat' ? 'bg-[#0B7B69] text-white font-semibold' : 'text-emerald-100/75 hover:bg-[#07261F] hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <span>Riwayat Booking</span>
                </a>
                <a href="{{ url('/penyewa/notifikasi') }}"
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->is('penyewa/notifikasi*') || $active === 'notifikasi' ? 'bg-[#0B7B69] text-white font-semibold' : 'text-emerald-100/75 hover:bg-[#07261F] hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span>Notifikasi</span>
                    </div>
                    <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                </a>
                <a href="{{ url('/penyewa/profil') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->is('penyewa/profil*') || $active === 'profil' ? 'bg-[#0B7B69] text-white font-semibold' : 'text-emerald-100/75 hover:bg-[#07261F] hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span>Profil Saya</span>
                </a>
            </nav>

            <div class="border-t border-[#0A3025] pt-2">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-rose-300 hover:bg-rose-950/40 hover:text-rose-200 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
