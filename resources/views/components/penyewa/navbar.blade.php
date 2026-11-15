@props([
    'active' => null, // beranda | dashboard | lapangan | riwayat | tentang-kami | profil
])

@php
    $currentUser = auth()->user();
    $userName    = $currentUser?->name ?? 'Tamu';
    $userEmail   = $currentUser?->email ?? '';
    $userRole    = $currentUser?->role === 'customer'
        ? 'Penyewa Aktif'
        : ($currentUser?->role ? ucfirst($currentUser->role) : 'Tamu');

    $userAvatar  = null;
    if (!empty($currentUser?->foto_profil)) {
        $userAvatar = str_starts_with($currentUser->foto_profil, 'http')
            ? $currentUser->foto_profil
            : asset('storage/' . ltrim($currentUser->foto_profil, '/'));
    }
    $userInitials = $currentUser ? collect(explode(' ', trim($userName)))->take(2)->map(fn ($w) => mb_substr($w, 0, 1))->implode('') : 'SP';

    // Active state helpers
    $isBerandaActive   = ($active === 'beranda') || (request()->is('/') || request()->routeIs('home') || request()->routeIs('beranda'));
    $isDashboardActive = ($active === 'dashboard') || (request()->is('penyewa/dashboard*') || request()->is('mitra/dashboard*') || request()->is('admin/dashboard*'));
    $isLapanganActive  = ($active === 'lapangan') || (request()->is('penyewa/lapangan*') || request()->is('penyewa/kalender*') || request()->routeIs('penyewa.lapangan*') || request()->routeIs('penyewa.kalender*'));
    $isRiwayatActive   = ($active === 'riwayat') || (request()->is('penyewa/riwayat*') || request()->routeIs('penyewa.riwayat*'));
    $isTentangActive   = ($active === 'tentang-kami') || (request()->is('tentang-kami*') || request()->routeIs('tentang-kami*'));
@endphp

<!-- ========== UNIFIED SPORTPLACE NAVBAR ========== -->
<header id="main-navbar" class="sticky top-0 z-40 bg-[#06231D] border-b border-[#0B322A] shadow-md transition-all duration-200">
    <div class="max-w-screen-2xl w-full mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-18 gap-3 sm:gap-4 lg:gap-6">

            <!-- LEFT: Brand Logo & Navigation Links -->
            <div class="flex items-center gap-4 lg:gap-6 xl:gap-8 shrink-0">
                <!-- Brand Logo -->
                <a href="{{ url('/penyewa/dashboard') }}" class="flex items-center group focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 rounded-xl" title="SportPlace Dashboard">
                    <img src="{{ asset('images/logo.svg') }}" alt="SportPlace" class="h-8 sm:h-9 w-auto">
                </a>

                <!-- Desktop Navigation Links -->
                <nav class="hidden lg:flex items-center gap-1.5">
                    {{-- Beranda Link --}}
                    <a href="{{ url('/') }}"
                       class="flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs xl:text-sm transition duration-150 {{ $isBerandaActive ? 'bg-[#0B7B69] text-white font-bold shadow-xs' : 'text-emerald-100/75 hover:bg-[#082B24] hover:text-white font-medium' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span>Beranda</span>
                    </a>

                    {{-- Dashboard Link (Hanya untuk pengguna login) --}}
                    @auth
                        <a href="{{ auth()->user()->role === 'mitra' ? route('mitra.dashboard') : (auth()->user()->role === 'admin' ? route('admin.dashboard') : url('/penyewa/dashboard')) }}"
                           class="flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs xl:text-sm transition duration-150 {{ $isDashboardActive ? 'bg-[#0B7B69] text-white font-bold shadow-xs' : 'text-emerald-100/75 hover:bg-[#082B24] hover:text-white font-medium' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zM14 13a1 1 0 011-1h4a1 1 0 011 1v6a1 1 0 01-1 1h-4a1 1 0 01-1-1v-6z"/>
                            </svg>
                            <span>Dashboard</span>
                        </a>
                    @endauth

                    {{-- Lapangan Link --}}
                    <a href="{{ route('penyewa.lapangan') }}"
                       class="flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs xl:text-sm transition duration-150 {{ $isLapanganActive ? 'bg-[#0B7B69] text-white font-bold shadow-xs' : 'text-emerald-100/75 hover:bg-[#082B24] hover:text-white font-medium' }}">
                        <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="18" height="18" x="3" y="3" rx="3"/>
                            <line x1="3" x2="21" y1="12" z2="12"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                        <span>Lapangan</span>
                    </a>

                    {{-- Riwayat Booking Link (Hanya untuk pengguna login) --}}
                    @auth
                        <a href="{{ url('/penyewa/riwayat') }}"
                           class="flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs xl:text-sm transition duration-150 {{ $isRiwayatActive ? 'bg-[#0B7B69] text-white font-bold shadow-xs' : 'text-emerald-100/75 hover:bg-[#082B24] hover:text-white font-medium' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <span>Riwayat Booking</span>
                        </a>
                    @endauth
                </nav>
            </div>

            <!-- RIGHT: Tentang Kami, Bantuan & User Profile / Auth Area -->
            <div class="flex items-center gap-2 sm:gap-3 lg:gap-4 shrink-0">

                <!-- Desktop Menu: Tentang Kami & Bantuan (Pindah ke posisi search bar sebelumnya) -->
                <div class="hidden md:flex items-center gap-1.5 lg:gap-2">
                    {{-- Tentang Kami Link --}}
                    <a href="{{ route('tentang-kami') }}"
                       class="flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs xl:text-sm transition duration-150 {{ $isTentangActive ? 'bg-[#0B7B69] text-white font-bold shadow-xs' : 'text-emerald-100/75 hover:bg-[#082B24] hover:text-white font-medium' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Tentang Kami</span>
                    </a>

                    {{-- Bantuan Pop-up Trigger --}}
                    <button type="button" onclick="openHelpModal('tutorial')" data-open-help-modal="tutorial"
                       class="flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs xl:text-sm font-medium transition duration-150 text-emerald-100/75 hover:bg-[#082B24] hover:text-white cursor-pointer">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Bantuan</span>
                    </button>
                </div>

                @auth
                    <!-- User Profile Dropdown Pill (Rounded Full) -->
                    <div class="relative shrink-0" id="user-profile-dropdown-container">
                        <button
                            id="user-profile-dropdown-btn"
                            type="button"
                            class="flex items-center gap-2 sm:gap-2.5 pl-1.5 pr-3 py-1.5 rounded-full bg-[#082B24] hover:bg-[#0B342C] border border-[#0F4A3F] hover:border-emerald-500/40 transition-all cursor-pointer focus:outline-none focus:ring-2 focus:ring-emerald-400/50"
                            aria-haspopup="true"
                            aria-expanded="false"
                        >
                            <!-- Avatar Photo with Initials fallback -->
                            <div class="w-8 h-8 rounded-full overflow-hidden ring-2 ring-emerald-500/40 shrink-0 bg-[#0B7B69] flex items-center justify-center text-white text-xs font-bold select-none">
                                @if(!empty($userAvatar))
                                    <img
                                        src="{{ $userAvatar }}"
                                        alt="{{ $userName }}"
                                        class="w-full h-full object-cover"
                                        loading="lazy"
                                        onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('hidden');"
                                    >
                                    <span class="tracking-wide hidden">{{ $userInitials }}</span>
                                @else
                                    <span class="tracking-wide">{{ $userInitials }}</span>
                                @endif
                            </div>

                            <!-- Name & Badge (Data dinamis dari database) -->
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
                                <div class="w-10 h-10 rounded-full overflow-hidden ring-1 ring-slate-200 shrink-0 bg-[#0B7B69] flex items-center justify-center text-white font-bold text-sm select-none">
                                    @if(!empty($userAvatar))
                                        <img
                                            src="{{ $userAvatar }}"
                                            alt="{{ $userName }}"
                                            class="w-full h-full object-cover"
                                            loading="lazy"
                                            onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('hidden');"
                                        >
                                        <span class="tracking-wide hidden">{{ $userInitials }}</span>
                                    @else
                                        <span class="tracking-wide">{{ $userInitials }}</span>
                                    @endif
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
                                @if(auth()->user()->role === 'mitra')
                                    <a href="{{ route('mitra.dashboard') }}"
                                       class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zM14 13a1 1 0 011-1h4a1 1 0 011 1v6a1 1 0 01-1 1h-4a1 1 0 01-1-1v-6z"/>
                                        </svg>
                                        <span>Dashboard Mitra</span>
                                    </a>
                                @elseif(auth()->user()->role === 'admin')
                                    <a href="{{ route('admin.dashboard') }}"
                                       class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zM14 13a1 1 0 011-1h4a1 1 0 011 1v6a1 1 0 01-1 1h-4a1 1 0 01-1-1v-6z"/>
                                        </svg>
                                        <span>Dashboard Admin</span>
                                    </a>
                                @endif

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
                                    <span>Riwayat Booking</span>
                                </a>
                                <button type="button" onclick="openHelpModal('tutorial');"
                                   class="w-full flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition text-left cursor-pointer">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>Bantuan & FAQ</span>
                                </button>
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
                @else
                    <!-- Guest Login / Register Action Buttons -->
                    <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                        <a href="{{ route('login') }}" class="text-xs sm:text-sm font-medium text-emerald-100/90 hover:text-white transition px-2">
                            Login
                        </a>
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-4 py-2 text-xs sm:text-sm font-semibold text-white bg-[#0A7660] hover:bg-[#0B604F] active:bg-[#0C4E40] rounded-full transition shadow-xs">
                            Daftar
                        </a>
                    </div>
                @endauth

                <!-- Mobile Hamburger Button -->
                <button
                    id="mobile-nav-toggle"
                    type="button"
                    class="lg:hidden p-2 rounded-xl text-emerald-100/75 hover:text-white hover:bg-[#07261F] transition focus:outline-none shrink-0 cursor-pointer"
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
            <!-- Mobile Nav Links -->
            <nav class="space-y-1">
                {{-- Beranda --}}
                <a href="{{ url('/') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition {{ $isBerandaActive ? 'bg-[#0B7B69] text-white font-bold' : 'text-emerald-100/75 hover:bg-[#07261F] hover:text-white font-medium' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span>Beranda</span>
                </a>

                {{-- Dashboard (Auth) --}}
                @auth
                    <a href="{{ auth()->user()->role === 'mitra' ? route('mitra.dashboard') : (auth()->user()->role === 'admin' ? route('admin.dashboard') : url('/penyewa/dashboard')) }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition {{ $isDashboardActive ? 'bg-[#0B7B69] text-white font-bold' : 'text-emerald-100/75 hover:bg-[#07261F] hover:text-white font-medium' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zM14 13a1 1 0 011-1h4a1 1 0 011 1v6a1 1 0 01-1 1h-4a1 1 0 01-1-1v-6z"/>
                        </svg>
                        <span>Dashboard</span>
                    </a>
                @endauth

                {{-- Lapangan --}}
                <a href="{{ route('penyewa.lapangan') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition {{ $isLapanganActive ? 'bg-[#0B7B69] text-white font-bold' : 'text-emerald-100/75 hover:bg-[#07261F] hover:text-white font-medium' }}">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="18" height="18" x="3" y="3" rx="3"/>
                        <line x1="3" x2="21" y1="12" y2="12"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                    <span>Lapangan</span>
                </a>

                {{-- Riwayat Booking (Auth) --}}
                @auth
                    <a href="{{ url('/penyewa/riwayat') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition {{ $isRiwayatActive ? 'bg-[#0B7B69] text-white font-bold' : 'text-emerald-100/75 hover:bg-[#07261F] hover:text-white font-medium' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <span>Riwayat Booking</span>
                    </a>

                    <a href="{{ url('/penyewa/profil') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition text-emerald-100/75 hover:bg-[#07261F] hover:text-white font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span>Profil Saya</span>
                    </a>
                @endauth

                {{-- Tentang Kami --}}
                <a href="{{ route('tentang-kami') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition {{ $isTentangActive ? 'bg-[#0B7B69] text-white font-bold' : 'text-emerald-100/75 hover:bg-[#07261F] hover:text-white font-medium' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Tentang Kami</span>
                </a>

                {{-- Mobile Bantuan Pop-up Trigger --}}
                <button type="button" onclick="openHelpModal('tutorial')" data-open-help-modal="tutorial"
                   class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition text-emerald-100/75 hover:bg-[#07261F] hover:text-white cursor-pointer text-left">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Bantuan & Panduan</span>
                </button>
            </nav>

            @auth
                <div class="border-t border-[#0A3025] pt-2">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-rose-300 hover:bg-rose-950/40 hover:text-rose-200 transition cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            <span>Keluar</span>
                        </button>
                    </form>
                </div>
            @else
                <div class="border-t border-[#0A3025] pt-3 flex flex-col gap-2">
                    <a href="{{ route('login') }}" class="w-full text-center py-2 text-sm font-medium text-emerald-100 hover:text-white transition">
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="w-full text-center py-2.5 text-sm font-semibold text-white bg-[#0A7660] hover:bg-[#0B604F] rounded-xl transition shadow-xs">
                        Daftar Akun Baru
                    </a>
                </div>
            @endauth
        </div>
    </div>
</header>

{{-- Idempotent, self-contained interaction script for navbar dropdowns and mobile toggles --}}
<script>
    (function () {
        function setupSportPlaceNavbar() {
            // Mobile navigation toggle
            const mobileToggle = document.getElementById('mobile-nav-toggle');
            const mobileMenu   = document.getElementById('mobile-nav-menu');
            if (mobileToggle && mobileMenu && !mobileToggle.dataset.bound) {
                mobileToggle.dataset.bound = 'true';
                mobileToggle.addEventListener('click', function () {
                    mobileMenu.classList.toggle('hidden');
                });
            }

            // User Profile Dropdown logic
            const profileBtn       = document.getElementById('user-profile-dropdown-btn');
            const profileMenu      = document.getElementById('user-profile-menu');
            const profileContainer = document.getElementById('user-profile-dropdown-container');
            const profileChevron   = document.getElementById('profile-chevron');

            function toggleProfileDropdown() {
                if (!profileMenu) return;
                const isClosed = profileMenu.classList.contains('hidden');
                if (isClosed) {
                    profileMenu.classList.remove('hidden');
                    profileBtn?.setAttribute('aria-expanded', 'true');
                    if (profileChevron) profileChevron.classList.add('rotate-180');
                } else {
                    profileMenu.classList.add('hidden');
                    profileBtn?.setAttribute('aria-expanded', 'false');
                    if (profileChevron) profileChevron.classList.remove('rotate-180');
                }
            }

            function closeProfileDropdown() {
                if (profileMenu && !profileMenu.classList.contains('hidden')) {
                    profileMenu.classList.add('hidden');
                    profileBtn?.setAttribute('aria-expanded', 'false');
                    if (profileChevron) profileChevron.classList.remove('rotate-180');
                }
            }

            if (profileBtn && profileMenu && !profileBtn.dataset.bound) {
                profileBtn.dataset.bound = 'true';
                profileBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    toggleProfileDropdown();
                });

                document.addEventListener('click', function (e) {
                    if (profileContainer && !profileContainer.contains(e.target)) {
                        closeProfileDropdown();
                    }
                });

                document.addEventListener('keydown', function (e) {
                    if (e.key === 'Escape') {
                        closeProfileDropdown();
                    }
                });
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', setupSportPlaceNavbar);
        } else {
            setupSportPlaceNavbar();
        }
    })();
</script>
