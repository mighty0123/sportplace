<header class="sticky top-0 z-50 bg-[#06231D] border-b border-[#0C342C]/80 backdrop-blur-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            
            <!-- Left: Logo & Navigation Links -->
            <div class="flex items-center gap-8 lg:gap-10">
                <a href="{{ url('/') }}" class="flex items-center gap-2.5 group">
                    <!-- Brand Icon Emblem -->
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-[#0A7660] to-[#0B604F] p-0.5 shadow-sm group-hover:scale-105 transition-transform flex items-center justify-center">
                        <svg class="w-5 h-5 text-[#DEEF25]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="18" height="18" x="3" y="3" rx="4"/>
                            <line x1="3" x2="21" y1="12" y2="12"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </div>
                    <span class="text-xl font-bold tracking-tight text-white">
                        Sport<span class="text-[#DEEF25]">Place</span>
                    </span>
                </a>

                <!-- Desktop Nav Links -->
                <nav class="hidden md:flex items-center gap-7">
                    <a href="#keunggulan" class="text-sm font-medium text-emerald-100/80 hover:text-white transition">
                        Tentang Kami
                    </a>
                </nav>
            </div>

            <!-- Right: Action Buttons & Auth -->
            <div class="hidden md:flex items-center gap-6">
                <a href="#bantuan" class="text-sm font-medium text-emerald-100/80 hover:text-white transition">
                    Bantuan
                </a>

                @auth
                    @if(auth()->user()->role === 'mitra')
                        <a href="{{ route('mitra.dashboard') }}" class="text-sm font-medium text-white hover:text-[#DEEF25] transition">
                            Dashboard Mitra
                        </a>
                    @else
                        <a href="{{ url('/dashboard') }}" class="text-sm font-medium text-white hover:text-[#DEEF25] transition">
                            Dashboard
                        </a>
                    @endif

                    <!-- User Profile Avatar Pill -->
                    <div class="flex items-center gap-3 pl-2 border-l border-emerald-800/60">
                        <span class="text-xs font-medium text-emerald-200 hidden lg:inline-block">
                            {{ auth()->user()->name }}
                        </span>
                        <a href="{{ route('logout') }}" title="Keluar" class="w-9 h-9 rounded-full bg-emerald-900/60 border border-emerald-700/60 flex items-center justify-center text-emerald-200 hover:text-white hover:bg-emerald-800 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </a>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-emerald-100/90 hover:text-white transition px-2">
                        Login
                    </a>

                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-5 py-2 text-sm font-semibold text-white bg-[#0A7660] hover:bg-[#0B604F] active:bg-[#0C4E40] rounded-full transition shadow-xs">
                        Daftar
                    </a>

                    <!-- Guest User Icon -->
                    <div class="w-9 h-9 rounded-full bg-emerald-900/40 border border-emerald-700/50 flex items-center justify-center text-emerald-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                @endauth
            </div>

            <!-- Mobile Menu Toggle Button -->
            <div class="flex md:hidden items-center gap-3">
                @auth
                    <a href="{{ auth()->user()->role === 'mitra' ? route('mitra.dashboard') : url('/dashboard') }}" class="text-xs px-3 py-1.5 rounded-full bg-emerald-900/80 text-emerald-200 border border-emerald-700/60 font-medium">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-xs px-3 py-1.5 rounded-full text-white bg-[#0A7660] font-medium">
                        Login
                    </a>
                @endauth

                <button type="button" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')" class="p-2 rounded-lg text-emerald-200 hover:text-white hover:bg-emerald-900/50 focus:outline-none" aria-label="Toggle Navigation">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
                    </svg>
                </button>
            </div>

        </div>
    </div>

    <!-- Mobile Drawer Menu -->
    <div id="mobile-menu" class="hidden md:hidden bg-[#06231D] border-t border-emerald-900/60 px-4 pt-3 pb-6 space-y-3">
        <a href="#lapangan-populer" onclick="document.getElementById('mobile-menu').classList.add('hidden')" class="block py-2 text-base font-medium text-emerald-100 hover:text-white">
            Explore Lapangan
        </a>
        <a href="#keunggulan" onclick="document.getElementById('mobile-menu').classList.add('hidden')" class="block py-2 text-base font-medium text-emerald-100 hover:text-white">
            Tentang Kami
        </a>
        <a href="#bantuan" onclick="document.getElementById('mobile-menu').classList.add('hidden')" class="block py-2 text-base font-medium text-emerald-100 hover:text-white">
            Bantuan & FAQ
        </a>
        <div class="pt-3 border-t border-emerald-900/60 flex items-center gap-3">
            @auth
                <a href="{{ route('logout') }}" class="w-full text-center py-2 text-sm font-semibold rounded-lg bg-emerald-900/80 text-white">
                    Keluar ({{ auth()->user()->name }})
                </a>
            @else
                <a href="{{ route('login') }}" class="w-1/2 text-center py-2.5 text-sm font-medium text-white border border-emerald-700/60 rounded-xl">
                    Login
                </a>
                <a href="{{ route('register') }}" class="w-1/2 text-center py-2.5 text-sm font-semibold text-white bg-[#0A7660] rounded-xl">
                    Daftar
                </a>
            @endauth
        </div>
    </div>
</header>
