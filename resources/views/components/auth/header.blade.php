<header class="w-full max-w-6xl mx-auto flex items-center justify-between py-6 px-4 sm:px-6">
    <!-- Brand Logo -->
    <a href="{{ url('/') }}" class="flex items-center gap-2.5 group">
        <div class="w-9 h-9 rounded-xl bg-[#0A7660] flex items-center justify-center text-white shadow-xs group-hover:scale-105 transition-transform">
            <svg class="w-5 h-5 text-[#DEEF25]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <rect width="18" height="18" x="3" y="3" rx="4"/>
                <line x1="3" x2="21" y1="12" y2="12"/>
                <circle cx="12" cy="12" r="3"/>
            </svg>
        </div>
        <span class="text-xl font-bold tracking-tight text-[#06231D]">
            Sport<span class="text-[#0A7660]">Place</span>
        </span>
    </a>

    <!-- User Profile Avatar Circle (Matches Mockup) -->
    <a href="{{ route('login') }}" class="w-10 h-10 rounded-full bg-[#0A7660] hover:bg-[#0B604F] text-white flex items-center justify-center transition shadow-xs" title="Profil / Masuk">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
        </svg>
    </a>
</header>
