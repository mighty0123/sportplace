<section class="relative bg-[#06231D] text-white py-20 lg:py-24 overflow-hidden">
    <!-- Ambient Glow & Subtle Texture -->
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[350px] bg-gradient-to-r from-[#0A7660]/20 via-[#DEEF25]/10 to-[#0A7660]/20 blur-3xl rounded-full"></div>
    </div>

    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <!-- Title -->
        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight text-white mb-4">
            Siap Mulai Main?
        </h2>

        <!-- Subtitle -->
        <p class="text-sm sm:text-base text-emerald-100/90 max-w-xl mx-auto mb-9 leading-relaxed">
            Temukan lapangan yang sesuai dengan kebutuhanmu sekarang dan ajak teman-temanmu bertanding.
        </p>

        <!-- Buttons -->
        <div class="flex flex-wrap items-center justify-center gap-4">
            <a 
                href="#lapangan-populer" 
                class="inline-flex items-center justify-center px-7 py-3 rounded-full bg-[#0A7660] hover:bg-[#0B604F] active:bg-[#0C4E40] text-white text-sm font-semibold transition shadow-sm"
            >
                Cari Lapangan
            </a>

            @guest
                <a 
                    href="{{ route('register') }}" 
                    class="inline-flex items-center justify-center px-7 py-3 rounded-full bg-[#DEEF25] hover:bg-[#BFCF17] active:bg-[#939E1A] text-[#0B322A] text-sm font-semibold transition shadow-sm hover:shadow"
                >
                    Daftar Sekarang
                </a>
            @else
                <a 
                    href="{{ auth()->user()->role === 'mitra' ? route('mitra.dashboard') : url('/dashboard') }}" 
                    class="inline-flex items-center justify-center px-7 py-3 rounded-full bg-[#DEEF25] hover:bg-[#BFCF17] text-[#0B322A] text-sm font-semibold transition shadow-sm"
                >
                    Masuk ke Dashboard
                </a>
            @endguest
        </div>
    </div>
</section>
