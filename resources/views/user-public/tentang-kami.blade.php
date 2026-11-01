<x-layouts.public title="Tentang Kami — SportPlace Indonesia">

    {{-- =========================================================
         1. HERO SECTION: Temukan Lapangan. Mulai Permainanmu.
    ========================================================= --}}
    <section class="bg-[#EBF5F0] py-14 sm:py-20 lg:py-24 border-b border-[#D8ECE0]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
                
                <!-- Left Column -->
                <div class="lg:col-span-6 space-y-6">
                    <!-- Pill Tag -->
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#DEF1E7] border border-[#BBE3D0] text-[#0A7660] text-xs font-semibold uppercase tracking-wider">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#0A7660]"></span>
                        <span>Tentang SportPlace Indonesia</span>
                    </div>

                    <!-- Heading -->
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-[#1B2724] leading-[1.15]">
                        Temukan Lapangan.<br>
                        Mulai Permainanmu.
                    </h1>

                    <!-- Description -->
                    <p class="text-base sm:text-lg text-[#5A6C5B] leading-relaxed max-w-xl">
                        SportPlace membantu pegiat olahraga menemukan dan menyewa lapangan dengan lebih mudah, transparan, dan terorganisir di seluruh Indonesia tanpa drama konfirmasi berulang.
                    </p>

                    <!-- CTAs -->
                    <div class="flex flex-wrap items-center gap-4 pt-2">
                        <a 
                            href="{{ route('penyewa.lapangan') }}" 
                            class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl bg-[#0A7660] hover:bg-[#0B604F] text-white font-semibold text-sm shadow-sm transition group"
                        >
                            <span>Jelajahi Lapangan</span>
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>

                        <a 
                            href="#cara-kerja" 
                            class="inline-flex items-center gap-1.5 px-5 py-3.5 rounded-xl text-[#0A7660] hover:text-[#0B604F] hover:bg-[#DEF1E7]/60 font-semibold text-sm transition"
                        >
                            <span>Pelajari Cara Kerja</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Right Column: Card with Sports Arena Photo & Floating Badges -->
                <div class="lg:col-span-6">
                    <div class="relative rounded-3xl overflow-hidden shadow-xl aspect-[16/11] bg-slate-900 border border-emerald-950/20 group">
                        <img 
                            src="{{ asset('images/courts/badminton.jpg') }}" 
                            alt="Arena Lapangan SportPlace" 
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>

                        <!-- Stat Badge 1 (Top Left) -->
                        <div class="absolute top-4 left-4 sm:top-6 sm:left-6 bg-white/95 backdrop-blur-md p-3 sm:p-4 rounded-2xl shadow-lg border border-white/60 flex items-center gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-[#DDF4EF] text-[#0A7660] flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <div>
                                <div class="text-lg sm:text-xl font-extrabold text-[#1B2724] leading-tight">450+</div>
                                <div class="text-xs text-[#5A6C5B] font-medium">Arena Terdaftar</div>
                            </div>
                        </div>

                        <!-- Stat Badge 2 (Below Stat 1) -->
                        <div class="absolute top-20 left-4 sm:top-24 sm:left-6 bg-white/95 backdrop-blur-md p-3 sm:p-4 rounded-2xl shadow-lg border border-white/60 flex items-center gap-3.5 mt-2">
                            <div class="w-10 h-10 rounded-xl bg-[#F6F9D8] text-[#939E1A] flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="text-lg sm:text-xl font-extrabold text-[#1B2724] leading-tight">99.8%</div>
                                <div class="text-xs text-[#5A6C5B] font-medium">Akurasi Jadwal Real-Time</div>
                            </div>
                        </div>

                        <!-- Stat Badge 3 (Bottom Full Width Bar) -->
                        <div class="absolute bottom-4 inset-x-4 sm:bottom-6 sm:inset-x-6 bg-white/95 backdrop-blur-md px-4 py-3 sm:px-5 sm:py-3.5 rounded-2xl shadow-lg border border-white/60 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-[#DEEF25] text-[#06231D] flex items-center justify-center shrink-0 shadow-xs">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs sm:text-sm font-bold text-[#1B2724] truncate">Standard BWF Certified</div>
                                    <div class="text-[11px] sm:text-xs text-[#7D8C78]">Lantai karpet vinyl 4.5 mm</div>
                                </div>
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#E8F7EE] text-[#0A7660] text-xs font-semibold shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Terverifikasi</span>
                            </span>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- =========================================================
         2. SECTION: SOLUSI OLAHRAGA MODERN (Menghubungkan Pemain & Arena)
    ========================================================= --}}
    <section id="cara-kerja" class="py-16 sm:py-24 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-start">
            
            <!-- Left Column: Story -->
            <div class="lg:col-span-5 space-y-6">
                <!-- Tag -->
                <div class="inline-block text-xs font-bold uppercase tracking-wider text-[#0A7660] bg-[#DEF1E7] border border-[#BBE3D0] px-3.5 py-1.5 rounded-full">
                    Solusi Olahraga Modern
                </div>

                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-[#1B2724] leading-tight">
                    Menghubungkan Pemain &<br class="hidden sm:inline"> Arena Tanpa Friksi
                </h2>

                <div class="space-y-4 text-sm sm:text-base text-[#5A6C5B] leading-relaxed">
                    <p>
                        Selama bertahun-tahun, menyewa lapangan olahraga identik dengan chat WhatsApp yang lama dibalas, jadwal ganda (double-booking), dan ketidakpastian fasilitas di lokasi.
                    </p>
                    <p>
                        SportPlace hadir memodernisasi ekosistem olahraga amatir di Indonesia. Kami mengintegrasikan sistem kasir pengelola venue secara langsung ke dalam platform pencarian berbasis jadwal real-time, memastikan kepastian slot 100% transparan sebelum Anda melangkah keluar rumah.
                    </p>
                </div>
            </div>

            <!-- Right Column: 4 Steps (2x2 Grid) -->
            <div class="lg:col-span-7">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    
                    <!-- Step 1: Cari -->
                    <div class="bg-white rounded-2xl p-6 border border-[#E1E3D9] hover:border-[#88E7D4] transition shadow-xs flex flex-col justify-between group">
                        <div>
                            <div class="w-11 h-11 rounded-xl bg-[#DDF4EF] text-[#0A7660] flex items-center justify-center mb-4 group-hover:scale-105 transition-transform">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <span class="block text-2xs font-bold uppercase tracking-wider text-[#7D8C78] mb-1">
                                Langkah 01
                            </span>
                            <h3 class="text-lg font-bold text-[#1B2724] mb-2">
                                Cari
                            </h3>
                            <p class="text-xs sm:text-sm text-[#5A6C5B] leading-relaxed">
                                Filter cabang olahraga (bulutangkis, futsal, basket, tenis, padel), radius jarak, dan jam operasional yang pas.
                            </p>
                        </div>
                    </div>

                    <!-- Step 2: Pilih -->
                    <div class="bg-white rounded-2xl p-6 border border-[#E1E3D9] hover:border-[#88E7D4] transition shadow-xs flex flex-col justify-between group">
                        <div>
                            <div class="w-11 h-11 rounded-xl bg-[#DDF4EF] text-[#0A7660] flex items-center justify-center mb-4 group-hover:scale-105 transition-transform">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                </svg>
                            </div>
                            <span class="block text-2xs font-bold uppercase tracking-wider text-[#7D8C78] mb-1">
                                Langkah 02
                            </span>
                            <h3 class="text-lg font-bold text-[#1B2724] mb-2">
                                Pilih
                            </h3>
                            <p class="text-xs sm:text-sm text-[#5A6C5B] leading-relaxed">
                                Cek spesifikasi material lapangan, ketersediaan shower, parkir mobil, hingga slot jadwal real-time per jam.
                            </p>
                        </div>
                    </div>

                    <!-- Step 3: Booking -->
                    <div class="bg-white rounded-2xl p-6 border border-[#E1E3D9] hover:border-[#88E7D4] transition shadow-xs flex flex-col justify-between group">
                        <div>
                            <div class="w-11 h-11 rounded-xl bg-[#DDF4EF] text-[#0A7660] flex items-center justify-center mb-4 group-hover:scale-105 transition-transform">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                            </div>
                            <span class="block text-2xs font-bold uppercase tracking-wider text-[#7D8C78] mb-1">
                                Langkah 03
                            </span>
                            <h3 class="text-lg font-bold text-[#1B2724] mb-2">
                                Booking
                            </h3>
                            <p class="text-xs sm:text-sm text-[#5A6C5B] leading-relaxed">
                                Kunci slot kamu dengan pembayaran QRIS / Virtual Account aman tanpa biaya admin tersembunyi.
                            </p>
                        </div>
                    </div>

                    <!-- Step 4: Main -->
                    <div class="bg-white rounded-2xl p-6 border border-[#E1E3D9] hover:border-[#88E7D4] transition shadow-xs flex flex-col justify-between group">
                        <div>
                            <div class="w-11 h-11 rounded-xl bg-[#DEEF25] text-[#06231D] flex items-center justify-center mb-4 group-hover:scale-105 transition-transform shadow-xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <span class="block text-2xs font-bold uppercase tracking-wider text-[#7D8C78] mb-1">
                                Langkah 04
                            </span>
                            <h3 class="text-lg font-bold text-[#1B2724] mb-2">
                                Main
                            </h3>
                            <p class="text-xs sm:text-sm text-[#5A6C5B] leading-relaxed">
                                Cukup tunjukkan kode QR e-tiket ke petugas lapangan saat tiba. Tanpa antri, langsung bertanding!
                            </p>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section>

    {{-- =========================================================
         3. SECTION: KEUNGGULAN LAYANAN (Kenapa Memilih SportPlace?)
    ========================================================= --}}
    <section class="py-16 sm:py-20 bg-[#F8F7F0] border-y border-[#E1E3D9]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Section Header -->
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
                <div>
                    <div class="inline-block text-xs font-bold uppercase tracking-wider text-[#0A7660] bg-[#DEF1E7] border border-[#BBE3D0] px-3.5 py-1.5 rounded-full mb-3">
                        Keunggulan Layanan
                    </div>
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-[#1B2724]">
                        Kenapa Memilih SportPlace?
                    </h2>
                </div>
                <p class="text-sm text-[#5A6C5B] max-w-md leading-relaxed md:text-right">
                    Standar operasional ketat untuk memberikan rasa aman, akurat, dan kepastian berolahraga bagi komunitas Anda.
                </p>
            </div>

            <!-- Cards Layout -->
            <div class="space-y-6">
                <!-- Row 1: 3 Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    
                    <!-- Card 1 -->
                    <div class="bg-white rounded-2xl p-7 border border-[#E1E3D9] hover:border-[#88E7D4] transition shadow-xs">
                        <div class="w-11 h-11 rounded-xl bg-[#DDF4EF] text-[#0A7660] flex items-center justify-center mb-5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-[#1B2724] mb-2.5">
                            Cari Lebih Mudah
                        </h3>
                        <p class="text-xs sm:text-sm text-[#5A6C5B] leading-relaxed">
                            Temukan lapangan berdasarkan cabang olahraga, kelurahan terdekat, tarif per jam, hingga ulasan asli pemain yang telah berkunjung.
                        </p>
                    </div>

                    <!-- Card 2 -->
                    <div class="bg-white rounded-2xl p-7 border border-[#E1E3D9] hover:border-[#88E7D4] transition shadow-xs">
                        <div class="w-11 h-11 rounded-xl bg-[#DDF4EF] text-[#0A7660] flex items-center justify-center mb-5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-[#1B2724] mb-2.5">
                            Ketersediaan Lebih Jelas
                        </h3>
                        <p class="text-xs sm:text-sm text-[#5A6C5B] leading-relaxed">
                            Pantau slot jam kosong secara live yang tersinkronisasi langsung dengan software kasir venue. Menghindari konflik jadwal secara permanen.
                        </p>
                    </div>

                    <!-- Card 3 -->
                    <div class="bg-white rounded-2xl p-7 border border-[#E1E3D9] hover:border-[#88E7D4] transition shadow-xs">
                        <div class="w-11 h-11 rounded-xl bg-[#DDF4EF] text-[#0A7660] flex items-center justify-center mb-5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-[#1B2724] mb-2.5">
                            Harga Lebih Transparan
                        </h3>
                        <p class="text-xs sm:text-sm text-[#5A6C5B] leading-relaxed">
                            Tarif sewa sesuai patokan resmi pemilik arena. Tanpa mark-up tersembunyi, rincian biaya layanan selalu terpampang di layar pembayaran.
                        </p>
                    </div>

                </div>

                <!-- Row 2: 2 Cards (Normal & Highlighted) -->
                <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                    
                    <!-- Card 4 (5 cols) -->
                    <div class="md:col-span-5 bg-white rounded-2xl p-7 border border-[#E1E3D9] hover:border-[#88E7D4] transition shadow-xs flex flex-col justify-between">
                        <div>
                            <div class="w-11 h-11 rounded-xl bg-[#DDF4EF] text-[#0A7660] flex items-center justify-center mb-5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-[#1B2724] mb-2.5">
                                Booking Lebih Terorganisir
                            </h3>
                            <p class="text-xs sm:text-sm text-[#5A6C5B] leading-relaxed">
                                Kelola jadwal mingguan tim Anda dalam satu kalender. Dapatkan invoice digital, bukti tagihan, dan catatan perpanjangan rutin dengan satu sentuhan.
                            </p>
                        </div>
                    </div>

                    <!-- Card 5 (7 cols - Highlighted with Lime Badge) -->
                    <div class="md:col-span-7 bg-white rounded-2xl p-7 border-2 border-[#BBEDE3] hover:border-[#0A7660] transition shadow-sm flex flex-col justify-between relative overflow-hidden">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <span class="inline-block text-2xs font-bold uppercase tracking-wider text-[#0A7660] bg-[#DDF4EF] px-2.5 py-1 rounded-md">
                                    Quality Assured
                                </span>
                            </div>
                            
                            <div class="flex flex-col sm:flex-row items-start gap-4">
                                <div class="w-12 h-12 rounded-xl bg-[#DEEF25] text-[#06231D] flex items-center justify-center shrink-0 shadow-xs">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="space-y-2">
                                    <h3 class="text-lg font-bold text-[#1B2724]">
                                        Venue Terverifikasi & Terkurasi
                                    </h3>
                                    <p class="text-xs sm:text-sm text-[#5A6C5B] leading-relaxed">
                                        Tim SportPlace melakukan inspeksi fisik terhadap standar pencahayaan (Lux), sirkulasi udara indoor, kebersihan loker dan kamar mandi, serta kelayakan karpet lantai sebelum sebuah arena tayang di aplikasi kami.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section>

    {{-- =========================================================
         4. SECTION: FASILITAS LENGKAP UNTUK SETIAP LAGA (WIDE BANNER)
    ========================================================= --}}
    <section class="py-12 sm:py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative rounded-3xl overflow-hidden min-h-[360px] sm:min-h-[420px] flex items-center p-8 sm:p-14 lg:p-16 shadow-2xl">
            <!-- Background Image -->
            <img 
                src="{{ asset('images/courts/futsal.jpg') }}" 
                alt="Fasilitas Lengkap SportPlace" 
                class="absolute inset-0 w-full h-full object-cover object-center"
            />
            <!-- Dark Gradient Vignette for Readability -->
            <div class="absolute inset-0 bg-gradient-to-r from-black/85 via-black/65 to-black/30"></div>

            <!-- Content -->
            <div class="relative max-w-xl space-y-4">
                <span class="inline-block text-2xs sm:text-xs font-bold uppercase tracking-wider text-[#DEEF25] bg-[#DEEF25]/20 border border-[#DEEF25]/40 px-3.5 py-1 rounded-full backdrop-blur-xs">
                    Kenyamanan Maksimal
                </span>
                
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white tracking-tight leading-tight">
                    Fasilitas Lengkap untuk Setiap Laga
                </h2>

                <p class="text-sm sm:text-base text-white/85 leading-relaxed">
                    Loker berpendingin, shower air panas, hingga tribun penonton yang bersih. Kami menjamin waktu luang berolahraga Anda menjadi momen paling menyegarkan dalam sepekan.
                </p>
            </div>
        </div>
    </section>

    {{-- =========================================================
         5. SECTION: DUA SISI PLATFORM (Penyewa & Mitra Venue)
    ========================================================= --}}
    <section class="py-14 sm:py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <!-- Card 1: Untuk Penyewa Lapangan (Light Theme) -->
            <div class="bg-white rounded-3xl p-8 sm:p-10 border border-[#E1E3D9] shadow-sm flex flex-col justify-between hover:shadow-md transition">
                <div>
                    <!-- Badge Icon -->
                    <div class="w-12 h-12 rounded-2xl bg-[#DDF4EF] text-[#0A7660] flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>

                    <span class="block text-2xs font-bold uppercase tracking-wider text-[#0A7660] mb-1.5">
                        Komunitas & Pemain
                    </span>

                    <h3 class="text-2xl sm:text-3xl font-extrabold text-[#1B2724] mb-3">
                        Untuk Penyewa Lapangan
                    </h3>

                    <p class="text-sm text-[#5A6C5B] leading-relaxed mb-6">
                        Nikmati keleluasaan merencanakan sparring rutin atau pertandingan santai bersama rekan tanpa repot menelepon satu-satu.
                    </p>

                    <!-- Feature Checklist -->
                    <ul class="space-y-3.5 mb-8 text-xs sm:text-sm text-[#1B2724] font-medium">
                        <li class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full bg-[#E8F7EE] text-[#0A7660] flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <span>Akses ke ratusan venue multi-cabang terverifikasi</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full bg-[#E8F7EE] text-[#0A7660] flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <span>Booking fleksibel hingga 1-30 hari ke depan secara online</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full bg-[#E8F7EE] text-[#0A7660] flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <span>Fitur bagi tagihan (Split Bill) instan via tautan WhatsApp</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full bg-[#E8F7EE] text-[#0A7660] flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <span>Garansi refund & reschedule mudah bila terjadi hal tak terduga</span>
                        </li>
                    </ul>
                </div>

                <a 
                    href="{{ route('penyewa.lapangan') }}" 
                    class="inline-flex items-center justify-center gap-2 w-full sm:w-auto self-start px-6 py-3.5 rounded-xl bg-[#0A7660] hover:bg-[#0B604F] text-white font-semibold text-sm transition group"
                >
                    <span>Jelajahi Lapangan Sekarang</span>
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </a>
            </div>

            <!-- Card 2: Untuk Mitra Pemilik Venue (Dark Theme) -->
            <div class="bg-[#06231D] text-white rounded-3xl p-8 sm:p-10 border border-[#0C342C] shadow-xl flex flex-col justify-between hover:shadow-2xl transition">
                <div>
                    <!-- Badge Icon -->
                    <div class="w-12 h-12 rounded-2xl bg-[#DEEF25]/20 text-[#DEEF25] border border-[#DEEF25]/30 flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>

                    <span class="block text-2xs font-bold uppercase tracking-wider text-[#DEEF25] mb-1.5">
                        Pengelola Arena
                    </span>

                    <h3 class="text-2xl sm:text-3xl font-extrabold text-white mb-3">
                        Untuk Mitra Pemilik Venue
                    </h3>

                    <p class="text-sm text-emerald-100/80 leading-relaxed mb-6">
                        Tingkatkan okupansi jam sepi (off-peak) dan otomatisasi seluruh operasional arena Anda dengan software manajemen modern.
                    </p>

                    <!-- Feature Checklist -->
                    <ul class="space-y-3.5 mb-8 text-xs sm:text-sm text-emerald-100/90 font-medium">
                        <li class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full bg-[#DEEF25]/20 text-[#DEEF25] flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <span>Dashboard pintar POS kasir & kalender slot real-time terpadu</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full bg-[#DEEF25]/20 text-[#DEEF25] flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <span>Pencegahan double-booking lintas reservasi offline dan online</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full bg-[#DEEF25]/20 text-[#DEEF25] flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <span>Penyaluran dana otomatis (Settlement) H+0 langsung ke rekening</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full bg-[#DEEF25]/20 text-[#DEEF25] flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <span>Analisis performa bisnis & laporan performa pendapatan bulanan</span>
                        </li>
                    </ul>
                </div>

                <a 
                    href="{{ route('register', ['role' => 'mitra']) }}" 
                    class="inline-flex items-center justify-center gap-2 w-full sm:w-auto self-start px-6 py-3.5 rounded-xl bg-[#DEEF25] hover:bg-[#CAD922] text-[#06231D] font-bold text-sm transition shadow-xs group"
                >
                    <span>Daftar sebagai Mitra Venue</span>
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </a>
            </div>

        </div>
    </section>

    {{-- =========================================================
         6. SECTION: KOMITMEN STANDAR PELAYANAN (5 Compact Feature Cards)
    ========================================================= --}}
    <section class="py-14 sm:py-20 bg-[#F4F9F6] border-t border-[#E1E3D9]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="inline-block text-2xs font-bold uppercase tracking-wider text-[#0A7660] bg-[#DEF1E7] border border-[#BBE3D0] px-3.5 py-1.5 rounded-full mb-3">
                    Komitmen Standar Pelayanan
                </span>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-[#1B2724] mb-3">
                    Dibangun untuk Pengalaman Booking yang Jelas
                </h2>
                <p class="text-sm text-[#5A6C5B]">
                    Fondasi teknologi kami bertumpu pada transparansi tanpa kompromi.
                </p>
            </div>

            <!-- 5 Cards in a Row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                
                <!-- Card 1 -->
                <div class="bg-white rounded-2xl p-5 border border-[#E1E3D9] text-center flex flex-col items-center hover:border-[#88E7D4] transition shadow-2xs">
                    <div class="w-10 h-10 rounded-full bg-[#DDF4EF] text-[#0A7660] flex items-center justify-center mb-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <h3 class="text-xs font-bold text-[#1B2724] mb-1.5">
                        Informasi Lapangan Terstruktur
                    </h3>
                    <p class="text-2xs text-[#5A6C5B] leading-relaxed">
                        Foto asli, ukuran lapangan presisi, dan rincian fasilitas lengkap.
                    </p>
                </div>

                <!-- Card 2 -->
                <div class="bg-white rounded-2xl p-5 border border-[#E1E3D9] text-center flex flex-col items-center hover:border-[#88E7D4] transition shadow-2xs">
                    <div class="w-10 h-10 rounded-full bg-[#DDF4EF] text-[#0A7660] flex items-center justify-center mb-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <h3 class="text-xs font-bold text-[#1B2724] mb-1.5">
                        Slot Real-Time 99.8%
                    </h3>
                    <p class="text-2xs text-[#5A6C5B] leading-relaxed">
                        Sinkronisasi instan dalam 300 milidetik ke basis kasir venue.
                    </p>
                </div>

                <!-- Card 3 -->
                <div class="bg-white rounded-2xl p-5 border border-[#E1E3D9] text-center flex flex-col items-center hover:border-[#88E7D4] transition shadow-2xs">
                    <div class="w-10 h-10 rounded-full bg-[#DDF4EF] text-[#0A7660] flex items-center justify-center mb-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xs font-bold text-[#1B2724] mb-1.5">
                        Status Transparan
                    </h3>
                    <p class="text-2xs text-[#5A6C5B] leading-relaxed">
                        Konfirmasi bukti sewa resmi yang mengikat secara hukum legal.
                    </p>
                </div>

                <!-- Card 4 -->
                <div class="bg-white rounded-2xl p-5 border border-[#E1E3D9] text-center flex flex-col items-center hover:border-[#88E7D4] transition shadow-2xs">
                    <div class="w-10 h-10 rounded-full bg-[#DDF4EF] text-[#0A7660] flex items-center justify-center mb-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h3 class="text-xs font-bold text-[#1B2724] mb-1.5">
                        Pembayaran Aman
                    </h3>
                    <p class="text-2xs text-[#5A6C5B] leading-relaxed">
                        Enkripsi 256-bit standar PCI-DSS dengan gateway berlisensi BI.
                    </p>
                </div>

                <!-- Card 5 -->
                <div class="bg-white rounded-2xl p-5 border border-[#E1E3D9] text-center flex flex-col items-center hover:border-[#88E7D4] transition shadow-2xs">
                    <div class="w-10 h-10 rounded-full bg-[#DDF4EF] text-[#0A7660] flex items-center justify-center mb-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xs font-bold text-[#1B2724] mb-1.5">
                        Dukungan Siaga
                    </h3>
                    <p class="text-2xs text-[#5A6C5B] leading-relaxed">
                        Tim Customer Care siap membantu 7 hari seminggu (06:00 hingga 24:00 WIB).
                    </p>
                </div>

            </div>

        </div>
    </section>

    {{-- =========================================================
         7. SECTION: CTA BANNER (Siap Mengatur Jadwal Main Berikutnya?)
    ========================================================= --}}
    <section class="py-12 sm:py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-10">
        <div class="bg-[#0A7660] text-white rounded-3xl p-8 sm:p-12 lg:p-14 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-8 relative overflow-hidden">
            <!-- Decorative Subtle Ambient Glow -->
            <div class="absolute -right-12 -top-12 w-64 h-64 bg-[#DEEF25]/15 blur-3xl rounded-full pointer-events-none"></div>

            <div class="relative space-y-2.5 max-w-xl">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                    Siap Mengatur Jadwal Main Berikutnya?
                </h2>
                <p class="text-sm sm:text-base text-emerald-100/90 leading-relaxed">
                    Gabunglah lebih dari puluhan ribu komunitas olahraga kami dan rasakan kemudahan rencana booking modern.
                </p>
            </div>

            <div class="relative flex flex-wrap items-center gap-3 shrink-0">
                <a 
                    href="{{ route('penyewa.lapangan') }}" 
                    class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl bg-[#DEEF25] hover:bg-[#CAD922] text-[#06231D] font-bold text-sm transition shadow-sm group"
                >
                    <span>Cari Lapangan Terdekat</span>
                    <svg class="w-4 h-4 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                </a>

                <a 
                    href="{{ route('login') }}" 
                    class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl border border-white/40 hover:bg-white/10 text-white font-semibold text-sm transition"
                >
                    <span>Hubungi Kami</span>
                </a>
            </div>
        </div>
    </section>

</x-layouts.public>

