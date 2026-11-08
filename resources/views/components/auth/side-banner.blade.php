@props([
    'variant' => 'register'
])

@if($variant === 'login')
    <!-- Login Side Banner with Indoor Arena Background Image -->
    <div class="relative w-full lg:w-5/12 rounded-3xl overflow-hidden min-h-[460px] lg:min-h-full flex flex-col justify-between p-8 sm:p-10 text-white bg-slate-900 shadow-sm">
        <!-- Background Image with Dark Emerald Overlay -->
        <img 
            src="{{ asset('images/fields/arena-sport-center.jpg') }}" 
            alt="SportPlace Arena" 
            class="absolute inset-0 w-full h-full object-cover object-center"
        />
        <div class="absolute inset-0 bg-gradient-to-b from-[#06231D]/90 via-[#0B322A]/85 to-[#06231D]/95"></div>

        <!-- Ambient Glow -->
        <div class="absolute -top-12 -left-12 w-64 h-64 bg-[#DEEF25]/10 blur-3xl pointer-events-none rounded-full"></div>

        <!-- Top Content -->
        <div class="relative z-10 space-y-6">
            <!-- Badges -->
            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-[#DEEF25] text-[#06231D] text-[11px] font-bold tracking-wide uppercase">
                    Resmi & Terverifikasi
                </span>
                <span class="text-xs text-emerald-100/90 font-medium">
                    Solusi Lapangan Indonesia
                </span>
            </div>

            <!-- Brand Identity Box -->
            <div class="flex items-center gap-3 pt-2">
                <div class="w-11 h-11 rounded-xl bg-[#0A7660] border border-emerald-400/30 flex items-center justify-center text-white shadow-xs">
                    <svg class="w-6 h-6 text-[#DEEF25]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="18" height="18" x="3" y="3" rx="4"/>
                        <line x1="3" x2="21" y1="12" y2="12"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-white tracking-tight leading-none">SportPlace</h2>
                    <p class="text-[10px] font-semibold text-emerald-200/80 tracking-widest uppercase mt-0.5">Your Game. Your Place.</p>
                </div>
            </div>
        </div>

        <!-- Bottom Content -->
        <div class="relative z-10 pt-16 lg:pt-24 space-y-3">
            <span class="text-xs font-bold text-[#DEEF25] uppercase tracking-wider block">
                Platform Olahraga #1
            </span>
            <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-white leading-tight">
                Temukan lapangan.<br>
                <span class="text-[#DEEF25]">Mulai permainanmu.</span>
            </h1>
            <p class="text-xs sm:text-sm text-emerald-100/80 leading-relaxed max-w-sm">
                Booking lapangan olahraga jadi lebih mudah, cepat, dan terpercaya di lebih dari 450+ venue pilihan se-Jabodetabek, Surabaya, dan Bali.
            </p>
        </div>
    </div>
@else
    <!-- Register Side Banner with Rich Dark Green Gradient -->
    <div class="relative w-full lg:w-5/12 rounded-3xl overflow-hidden min-h-[500px] lg:min-h-full flex flex-col justify-between p-8 sm:p-10 text-white bg-gradient-to-b from-[#06231D] via-[#0B322A] to-[#06231D] shadow-sm">
        <!-- Ambient Decorative Glow -->
        <div class="absolute -top-16 -right-16 w-72 h-72 bg-[#0A7660]/30 blur-3xl pointer-events-none rounded-full"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-[#DEEF25]/10 blur-3xl pointer-events-none rounded-full"></div>

        <!-- Top Content -->
        <div class="relative z-10 space-y-6">
            <!-- Pill Badge -->
            <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-[#0C4E40]/80 border border-[#43E5C5]/20 text-[#DEEF25] text-xs font-bold tracking-wider uppercase">
                <span>• RESMI & TERVERIFIKASI • INDONESIA</span>
            </div>

            <!-- Main Headline -->
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight text-white leading-[1.15]">
                Temukan<br>lapangan.<br>
                <span class="text-[#DEEF25]">Mulai<br>permainanmu.</span>
            </h1>

            <p class="text-xs sm:text-sm text-emerald-100/80 leading-relaxed max-w-sm">
                Buat akun SportPlace dan nikmati kemudahan booking 450+ venue olahraga terverifikasi di Jabodetabek, Bandung, Surabaya, dan kota lainnya.
            </p>
        </div>

        <!-- Value Proposition Bullet Points -->
        <div class="relative z-10 pt-10 space-y-5">
            <!-- Bullet 1 -->
            <div class="flex items-start gap-3">
                <div class="w-6 h-6 rounded-full bg-[#DEEF25] text-[#06231D] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">Booking instan real-time</h3>
                    <p class="text-xs text-emerald-100/70 leading-snug mt-0.5">Pesan jadwal dalam 30 detik tanpa konfirmasi chat admin manual.</p>
                </div>
            </div>

            <!-- Bullet 2 -->
            <div class="flex items-start gap-3">
                <div class="w-6 h-6 rounded-full bg-[#DEEF25] text-[#06231D] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">Jaminan jadwal transparan</h3>
                    <p class="text-xs text-emerald-100/70 leading-snug mt-0.5">Slot slot akurat 99.8% dengan proteksi garansi refund & reschedule.</p>
                </div>
            </div>

            <!-- Bullet 3 -->
            <div class="flex items-start gap-3">
                <div class="w-6 h-6 rounded-full bg-[#DEEF25] text-[#06231D] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">Solusi lengkap Mitra Venue</h3>
                    <p class="text-xs text-emerald-100/70 leading-snug mt-0.5">Atur tarif jam sibuk, sinkronisasi kasir, dan pencairan instan H+0.</p>
                </div>
            </div>
        </div>
    </div>
@endif
