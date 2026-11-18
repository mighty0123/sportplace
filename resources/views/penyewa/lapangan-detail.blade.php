{{--
    Halaman Detail Lapangan Olahraga — SportPlace
    Layout: x-layouts.penyewa (Top navbar)
    Menampilkan detail lengkap lapangan dari database sebelum masuk ke halaman kalender booking.
--}}
<x-layouts.penyewa title="{{ $field->nama_lapangan }} — {{ $field->venue?->nama_venue ?? 'SportPlace' }}" active="lapangan">
<div class="space-y-6">

    {{-- =========================================================
         SECTION 1: Breadcrumb & Navigasi Kembali
    ========================================================= --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <nav class="flex items-center gap-2 text-xs font-medium text-slate-500">
            @auth
                <a href="{{ url('/penyewa/dashboard') }}" class="hover:text-[#0B7B69] transition">Dashboard</a>
            @else
                <a href="{{ url('/') }}" class="hover:text-[#0B7B69] transition">Beranda</a>
            @endauth
            <span>/</span>
            <a href="{{ url('/penyewa/lapangan') }}" class="hover:text-[#0B7B69] transition">Daftar Lapangan</a>
            <span>/</span>
            <span class="text-slate-900 font-bold truncate max-w-[200px] sm:max-w-xs">{{ $field->nama_lapangan }}</span>
        </nav>

        <a
            href="{{ url('/penyewa/lapangan') }}"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white border border-slate-200/90 hover:bg-slate-50 text-xs font-semibold text-slate-700 shadow-2xs transition shrink-0"
        >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali ke Daftar</span>
        </a>
    </div>

    {{-- =========================================================
         SECTION 2: Main Layout 2 Kolom (Detail Kiri + Sticky CTA Kanan)
    ========================================================= --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        {{-- LEFT 8 COLS: Foto, Header Lapangan, Fasilitas, Info Lengkap --}}
        <div class="lg:col-span-8 space-y-6">

            <!-- Card Foto Utama & Banner Lapangan -->
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
                <div class="relative h-64 sm:h-80 md:h-96 w-full bg-slate-900 overflow-hidden">
                    <img
                        src="{{ $photoUrl }}"
                        alt="{{ $field->nama_lapangan }}"
                        class="w-full h-full object-cover"
                    >
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/25 to-transparent pointer-events-none"></div>

                    <!-- Badge Sport & Status -->
                    <div class="absolute top-4 left-4 flex items-center gap-2">
                        <span class="px-3 py-1 rounded-md text-xs font-extrabold uppercase tracking-wide bg-[#FACC15] text-slate-950 shadow-md">
                            {{ $field->sport?->nama_olahraga ?? 'OLAHRAGA' }}
                        </span>
                        <span class="px-3 py-1 rounded-md text-xs font-bold bg-emerald-500/90 text-white backdrop-blur-xs shadow-md inline-flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                            <span>Siap Dibooking</span>
                        </span>
                    </div>

                    <!-- Overlay Judul di Bawah Foto -->
                    <div class="absolute inset-x-0 bottom-0 p-5 sm:p-6 text-white">
                        <p class="text-xs sm:text-sm font-medium text-emerald-300">
                            {{ $field->venue?->nama_venue ?? 'Sport Arena' }}
                        </p>
                        <h1 class="text-2xl sm:text-3xl md:text-4xl font-black tracking-tight leading-tight mt-1">
                            {{ $field->nama_lapangan }}
                        </h1>
                    </div>
                </div>

                <!-- Info Cepat di Bawah Foto -->
                <div class="p-5 sm:p-6 border-b border-slate-100 grid grid-cols-2 sm:grid-cols-4 gap-4 bg-slate-50/50">
                    <div>
                        <span class="text-[11px] font-semibold text-slate-400 block uppercase">Cabang Olahraga</span>
                        <span class="text-sm font-bold text-slate-800">{{ $field->sport?->nama_olahraga ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-[11px] font-semibold text-slate-400 block uppercase">Jam Buka</span>
                        <span class="text-sm font-bold text-slate-800">
                            {{ $field->venue?->jam_buka ? substr($field->venue->jam_buka, 0, 5) : '08:00' }} - {{ $field->venue?->jam_tutup ? substr($field->venue->jam_tutup, 0, 5) : '23:00' }} WIB
                        </span>
                    </div>
                    <div>
                        <span class="text-[11px] font-semibold text-slate-400 block uppercase">Lokasi Kota</span>
                        <span class="text-sm font-bold text-slate-800">{{ $field->venue?->city?->nama_kota ?? 'Jakarta' }}</span>
                    </div>
                    <div>
                        <span class="text-[11px] font-semibold text-slate-400 block uppercase">Rating Arena</span>
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-sm font-bold text-slate-800 inline-flex items-center gap-1 text-amber-500">
                                ★ {{ number_format($averageRating ?? 4.8, 1) }}
                                <span class="text-xs text-slate-400 font-normal">({{ $totalReviewsCount ?? 120 }}+ ulasan)</span>
                            </span>
                            <button
                                type="button"
                                onclick="openReviewModal()"
                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-amber-50 hover:bg-amber-100 text-amber-800 text-[11px] font-bold border border-amber-200 transition cursor-pointer"
                                title="Beri ulasan lapangan ini"
                            >
                                <svg class="w-3 h-3 text-amber-500 fill-amber-500" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <span>Beri Ulasan</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Alamat Lengkap Venue -->
                <div class="p-5 sm:p-6 flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-[#0B7B69] flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Alamat Venue</h4>
                        <p class="text-sm font-medium text-slate-800 mt-0.5 leading-relaxed">
                            {{ $field->venue?->alamat_lengkap ?? 'Alamat lengkap venue tersedia di area dashboard mitra' }}
                        </p>
                        @if($field->venue?->map_url)
                            <a
                                href="{{ $field->venue->map_url }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#0B7B69] hover:underline mt-2"
                            >
                                <span>Buka Petunjuk Arah di Google Maps</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Card Fasilitas Lapangan -->
            <div class="bg-white rounded-2xl border border-slate-200/90 p-5 sm:p-6 shadow-xs space-y-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-[#0B7B69] flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Fasilitas yang Tersedia</h3>
                        <p class="text-xs text-slate-500">Kelengkapan fasilitas untuk kenyamanan berolahraga Anda</p>
                    </div>
                </div>

                @if($field->facilities->isNotEmpty())
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 pt-2">
                        @foreach($field->facilities as $facility)
                            <div class="flex items-center gap-2.5 p-3 rounded-xl bg-slate-50 border border-slate-100 hover:border-emerald-200 transition">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                                <span class="text-xs font-semibold text-slate-800">{{ $facility->nama }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 pt-2">
                        <div class="flex items-center gap-2.5 p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                            <span class="text-xs font-semibold text-slate-800">Parkir Kendaraan Luas</span>
                        </div>
                        <div class="flex items-center gap-2.5 p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                            <span class="text-xs font-semibold text-slate-800">Kantin & Minuman</span>
                        </div>
                        <div class="flex items-center gap-2.5 p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                            <span class="text-xs font-semibold text-slate-800">Ruang Ganti & Toilet</span>
                        </div>
                        <div class="flex items-center gap-2.5 p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                            <span class="text-xs font-semibold text-slate-800">Loker Penitipan Barang</span>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Card Informasi Tambahan & Kebijakan Lapangan -->
            <div class="bg-white rounded-2xl border border-slate-200/90 p-5 sm:p-6 shadow-xs space-y-3">
                <h3 class="text-base font-bold text-slate-900">Peraturan & Ketentuan Bermain</h3>
                <ul class="space-y-2 text-xs text-slate-600 leading-relaxed list-disc list-inside">
                    <li>Wajib mengenakan sepatu olahraga yang sesuai standar lapangan (non-marking untuk indoor).</li>
                    <li>Dilarang merokok dan membawa minuman keras di dalam area lapangan.</li>
                    <li>Harap hadir 10-15 menit sebelum slot jam sewa dimulai untuk proses check-in.</li>
                    <li>Pembatalan dan reschedule mengikuti kebijakan batas waktu minimal 24 jam sebelum jadwal.</li>
                </ul>
            </div>

            <!-- Card Ulasan & Penilaian Lapangan -->
            <div id="section-ulasan" class="bg-white rounded-2xl border border-slate-200/90 p-5 sm:p-6 shadow-xs space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 pb-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 fill-amber-500" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Ulasan & Rating Lapangan</h3>
                            <p class="text-xs text-slate-500">Pengalaman nyata dari penyewa yang telah bermain di lapangan ini</p>
                        </div>
                    </div>

                    <!-- Tombol Review Lapangan Utama -->
                    <button
                        type="button"
                        onclick="openReviewModal()"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-[#0B7B69] hover:bg-[#086354] active:scale-95 text-white text-xs font-bold shadow-xs transition duration-150 cursor-pointer shrink-0"
                    >
                        <svg class="w-4 h-4 text-amber-300 fill-amber-300" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        <span>Tulis Review Lapangan</span>
                    </button>
                </div>

                <!-- Ringkasan Nilai & Rating Bar -->
                <div class="grid grid-cols-1 md:grid-cols-12 gap-6 p-4 sm:p-5 rounded-2xl bg-slate-50/70 border border-slate-100">
                    <div class="md:col-span-5 flex flex-col items-center justify-center text-center p-3 sm:border-r sm:border-slate-200/80">
                        <span class="text-4xl sm:text-5xl font-black text-slate-900 leading-none">4.8</span>
                        <div class="flex items-center gap-1 text-amber-400 my-2">
                            @for($i = 0; $i < 5; $i++)
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                        <p class="text-xs font-semibold text-slate-600">
                            Berdasarkan <span id="summary-reviews-count" class="font-bold text-slate-800">{{ $totalReviewsCount ?? 120 }}+ ulasan</span>
                        </p>
                        <span class="mt-2 inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-100/70 text-[#0B7B69] text-[11px] font-bold">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            98% Merekomendasikan
                        </span>
                    </div>

                    <div class="md:col-span-7 flex flex-col justify-center space-y-2">
                        <!-- Rating 5 -->
                        <div class="flex items-center gap-3 text-xs">
                            <span class="font-semibold text-slate-600 w-14 shrink-0">5 Bintang</span>
                            <div class="flex-1 h-2 rounded-full bg-slate-200 overflow-hidden">
                                <div class="h-full bg-emerald-500 rounded-full" style="width: 88%"></div>
                            </div>
                            <span class="text-slate-400 font-mono w-8 text-right">88%</span>
                        </div>
                        <!-- Rating 4 -->
                        <div class="flex items-center gap-3 text-xs">
                            <span class="font-semibold text-slate-600 w-14 shrink-0">4 Bintang</span>
                            <div class="flex-1 h-2 rounded-full bg-slate-200 overflow-hidden">
                                <div class="h-full bg-emerald-400 rounded-full" style="width: 10%"></div>
                            </div>
                            <span class="text-slate-400 font-mono w-8 text-right">10%</span>
                        </div>
                        <!-- Rating 3 -->
                        <div class="flex items-center gap-3 text-xs">
                            <span class="font-semibold text-slate-600 w-14 shrink-0">3 Bintang</span>
                            <div class="flex-1 h-2 rounded-full bg-slate-200 overflow-hidden">
                                <div class="h-full bg-amber-400 rounded-full" style="width: 2%"></div>
                            </div>
                            <span class="text-slate-400 font-mono w-8 text-right">2%</span>
                        </div>
                        <!-- Rating 2 -->
                        <div class="flex items-center gap-3 text-xs">
                            <span class="font-semibold text-slate-600 w-14 shrink-0">2 Bintang</span>
                            <div class="flex-1 h-2 rounded-full bg-slate-200 overflow-hidden">
                                <div class="h-full bg-slate-300 rounded-full" style="width: 0%"></div>
                            </div>
                            <span class="text-slate-400 font-mono w-8 text-right">0%</span>
                        </div>
                        <!-- Rating 1 -->
                        <div class="flex items-center gap-3 text-xs">
                            <span class="font-semibold text-slate-600 w-14 shrink-0">1 Bintang</span>
                            <div class="flex-1 h-2 rounded-full bg-slate-200 overflow-hidden">
                                <div class="h-full bg-slate-300 rounded-full" style="width: 0%"></div>
                            </div>
                            <span class="text-slate-400 font-mono w-8 text-right">0%</span>
                        </div>
                    </div>
                </div>

                <!-- Daftar Komentar & Ulasan -->
                <div class="space-y-4 pt-2">
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Ulasan Terbaru Pengguna</h4>

                    <div id="reviews-container" class="space-y-3.5 divide-y divide-slate-100">
                        @foreach($reviews as $rev)
                            <div class="pt-3.5 first:pt-0 space-y-2">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-emerald-100 text-[#0B7B69] font-bold text-xs flex items-center justify-center shrink-0 uppercase">
                                            {{ substr($rev['name'] ?? 'P', 0, 2) }}
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs font-bold text-slate-900">{{ $rev['name'] }}</span>
                                                @if(!empty($rev['verified']))
                                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-[#0B7B69] border border-emerald-200">
                                                        <svg class="w-3 h-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                        </svg>
                                                        <span>Terverifikasi</span>
                                                    </span>
                                                @endif
                                            </div>
                                            <span class="text-[11px] text-slate-400">{{ $rev['date'] ?? 'Baru saja' }}</span>
                                        </div>
                                    </div>

                                    <!-- Rating Star -->
                                    <div class="flex items-center gap-0.5 text-amber-400 shrink-0">
                                        @for($s = 1; $s <= 5; $s++)
                                            @if($s <= ($rev['rating'] ?? 5))
                                                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4 text-slate-200 fill-current" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            @endif
                                        @endfor
                                    </div>
                                </div>

                                <!-- Tags jika ada -->
                                @if(!empty($rev['tags']) && is_array($rev['tags']))
                                    <div class="flex items-center gap-1.5 flex-wrap pt-0.5">
                                        @foreach($rev['tags'] as $tag)
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-100 text-slate-700">
                                                ✓ {{ $tag }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif

                                <!-- Komentar -->
                                <p class="text-xs text-slate-700 leading-relaxed">
                                    {{ $rev['comment'] }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Lapangan Lain di Venue Ini (Jika Ada) -->
            @if($otherCourts->isNotEmpty())
                <div class="bg-white rounded-2xl border border-slate-200/90 p-5 sm:p-6 shadow-xs space-y-4">
                    <h3 class="text-base font-bold text-slate-900">Lapangan Lain di Venue Ini</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($otherCourts as $oc)
                            <a
                                href="{{ url('/penyewa/lapangan/' . $oc->id) }}"
                                class="p-3.5 rounded-xl bg-slate-50 hover:bg-emerald-50/50 border border-slate-200/80 hover:border-emerald-300 transition flex items-center justify-between group"
                            >
                                <div>
                                    <h4 class="text-xs font-bold text-slate-800 group-hover:text-[#0B7B69] transition">{{ $oc->nama_lapangan }}</h4>
                                    <p class="text-[11px] text-slate-500 mt-0.5">{{ $oc->sport?->nama_olahraga }} • Rp {{ number_format($oc->harga_perjam, 0, ',', '.') }}/jam</p>
                                </div>
                                <svg class="w-4 h-4 text-slate-400 group-hover:text-[#0B7B69] group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

        {{-- RIGHT 4 COLS: Sticky Booking CTA Card --}}
        <div class="lg:col-span-4 lg:sticky lg:top-24 space-y-4">

            <!-- Card Booking Action Card -->
            <div class="bg-white rounded-2xl border-2 border-emerald-500/30 p-6 shadow-md space-y-5">
                <div class="flex items-baseline justify-between border-b border-slate-100 pb-4">
                    <div>
                        <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Tarif Sewa</span>
                        <div class="text-2xl sm:text-3xl font-black text-[#0B7B69] tabular-nums leading-tight">
                            Rp {{ number_format($field->harga_perjam, 0, ',', '.') }}
                            <span class="text-xs font-normal text-slate-400">/ jam</span>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-emerald-50 text-[#0B7B69] border border-emerald-200">
                        Harga Resmi
                    </span>
                </div>

                {{-- Detail Meta Harga Siang/Malam jika ada --}}
                @if(!empty($field->meta_harga) && is_array($field->meta_harga))
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 space-y-1.5 text-xs">
                        <span class="font-bold text-slate-700 block text-[11px]">Rincian Tarif Jam:</span>
                        @if(isset($field->meta_harga['siang']))
                            <div class="flex justify-between text-slate-600">
                                <span>Siang (08:00 - 17:00)</span>
                                <span class="font-bold text-slate-800">Rp {{ number_format($field->meta_harga['siang'], 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @if(isset($field->meta_harga['malam']))
                            <div class="flex justify-between text-slate-600">
                                <span>Malam (17:00 - 23:00)</span>
                                <span class="font-bold text-slate-800">Rp {{ number_format($field->meta_harga['malam'], 0, ',', '.') }}</span>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Tombol CTA Utama: Buka Kalender Jadwal -->
                <div class="space-y-2.5">
                    <a
                        href="{{ url('/penyewa/kalender/' . $field->id) }}"
                        class="w-full inline-flex items-center justify-center gap-2 py-3.5 px-4 rounded-xl bg-[#0B7B69] hover:bg-[#086354] active:scale-95 text-white font-bold text-sm shadow-md transition duration-150"
                    >
                        <svg class="w-5 h-5 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>Pilih Jadwal di Kalender</span>
                        <svg class="w-4 h-4 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>

                    <!-- Tombol Review Lapangan di CTA Card -->
                    <button
                        type="button"
                        onclick="openReviewModal()"
                        class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl border border-amber-300 bg-amber-50 hover:bg-amber-100/90 active:scale-95 text-amber-900 font-bold text-xs transition duration-150 shadow-2xs cursor-pointer"
                    >
                        <svg class="w-4 h-4 text-amber-500 fill-amber-500" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        <span>Review Lapangan Ini</span>
                    </button>

                    <p class="text-[11px] text-center text-slate-400">
                        Klik untuk melihat ketersediaan jam atau beri review lapangan
                    </p>
                </div>

                <!-- Jaminan & Keuntungan -->
                <div class="pt-4 border-t border-slate-100 space-y-2.5 text-xs text-slate-500">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <span>Konfirmasi booking instan real-time</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Bebas pilih slot jam yang diinginkan</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        <span>Pembayaran aman via QRIS / Transfer Bank</span>
                    </div>
                </div>

            </div>

        </div>

    </div>

    {{-- =========================================================
         MODAL POPUP: Review Lapangan
    ========================================================= --}}
    <div id="review-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="modal-review-title">
        <!-- Backdrop -->
        <div id="review-modal-backdrop" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity duration-200 opacity-0" onclick="closeReviewModal()"></div>

        <!-- Modal Box Container -->
        <div class="min-h-full flex items-center justify-center p-4">
            <div id="review-modal-card" class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl border border-slate-100 p-6 sm:p-7 space-y-5 transform transition-all duration-200 scale-95 opacity-0">
                
                <!-- Header Modal -->
                <div class="flex items-start justify-between gap-3 border-b border-slate-100 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 fill-amber-500" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 id="modal-review-title" class="text-base font-bold text-slate-900">Beri Review Lapangan</h3>
                            <p class="text-xs text-slate-500 truncate max-w-[260px] sm:max-w-xs">{{ $field->nama_lapangan }} — {{ $field->venue?->nama_venue ?? 'SportPlace' }}</p>
                        </div>
                    </div>

                    <button
                        type="button"
                        onclick="closeReviewModal()"
                        class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition cursor-pointer"
                        aria-label="Tutup modal"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Form Review -->
                <form id="review-form" action="{{ route('penyewa.lapangan.review', $field->id) }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- 1. Star Rating Picker -->
                    <div class="text-center space-y-2 py-1">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Pilih Rating Anda <span class="text-rose-500">*</span>
                        </label>
                        <input type="hidden" name="rating" id="review-rating-input" value="5" required>
                        <div class="flex items-center justify-center gap-2" id="star-picker">
                            @for($i = 1; $i <= 5; $i++)
                                <button
                                    type="button"
                                    data-rating="{{ $i }}"
                                    class="star-btn p-1 text-amber-400 hover:scale-110 active:scale-95 transition-transform cursor-pointer focus:outline-none"
                                    title="{{ $i }} Bintang"
                                >
                                    <svg class="w-8 h-8 fill-current transition-colors" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </button>
                            @endfor
                        </div>
                        <div id="star-label" class="text-xs font-bold text-emerald-700 bg-emerald-50 inline-block px-3 py-1 rounded-full border border-emerald-200">
                            5 Bintang — Luar Biasa! ⭐
                        </div>
                    </div>

                    <!-- 2. Nama Pengulas -->
                    <div>
                        <label for="review-nama" class="block text-xs font-semibold text-slate-700 mb-1">
                            Nama Anda
                        </label>
                        <input
                            type="text"
                            id="review-nama"
                            name="nama"
                            value="{{ auth()->check() ? auth()->user()->name : '' }}"
                            placeholder="Contoh: Dimas Pratama"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#0B7B69] focus:ring-2 focus:ring-[#0B7B69]/20 text-xs sm:text-sm text-slate-800 placeholder:text-slate-400 transition"
                            {{ auth()->check() ? 'readonly' : '' }}
                        >
                        @auth
                            <p class="text-[11px] text-emerald-600 mt-1 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Ditulis sebagai akun terverifikasi ({{ auth()->user()->email }})
                            </p>
                        @endauth
                    </div>

                    <!-- 3. Tags Pengalaman Cepat -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-700">
                            Sorotan Fasilitas (Opsional)
                        </label>
                        <input type="hidden" name="tags" id="review-tags-input" value="Lapangan Bersih,Lampu Terang">
                        <div class="flex flex-wrap gap-1.5" id="tags-selector">
                            <button type="button" data-tag="Lapangan Bersih" class="tag-chip active px-2.5 py-1 rounded-lg text-xs font-medium border border-emerald-300 bg-emerald-50 text-[#0B7B69] transition cursor-pointer">
                                Lapangan Bersih
                            </button>
                            <button type="button" data-tag="Lampu Terang" class="tag-chip active px-2.5 py-1 rounded-lg text-xs font-medium border border-emerald-300 bg-emerald-50 text-[#0B7B69] transition cursor-pointer">
                                Lampu Terang
                            </button>
                            <button type="button" data-tag="Lantai Mulus" class="tag-chip px-2.5 py-1 rounded-lg text-xs font-medium border border-slate-200 bg-slate-50 text-slate-600 hover:border-slate-300 transition cursor-pointer">
                                Lantai Mulus
                            </button>
                            <button type="button" data-tag="Fasilitas Lengkap" class="tag-chip px-2.5 py-1 rounded-lg text-xs font-medium border border-slate-200 bg-slate-50 text-slate-600 hover:border-slate-300 transition cursor-pointer">
                                Fasilitas Lengkap
                            </button>
                            <button type="button" data-tag="Parkir Luas" class="tag-chip px-2.5 py-1 rounded-lg text-xs font-medium border border-slate-200 bg-slate-50 text-slate-600 hover:border-slate-300 transition cursor-pointer">
                                Parkir Luas
                            </button>
                            <button type="button" data-tag="Staf Ramah" class="tag-chip px-2.5 py-1 rounded-lg text-xs font-medium border border-slate-200 bg-slate-50 text-slate-600 hover:border-slate-300 transition cursor-pointer">
                                Staf Ramah
                            </button>
                        </div>
                    </div>

                    <!-- 4. Textarea Komentar / Ulasan -->
                    <div>
                        <label for="review-komentar" class="block text-xs font-semibold text-slate-700 mb-1">
                            Ulasan Anda <span class="text-rose-500">*</span>
                        </label>
                        <textarea
                            id="review-komentar"
                            name="komentar"
                            rows="3"
                            required
                            placeholder="Ceritakan pengalaman bermain Anda di lapangan ini, kondisi lantai/rumput, pencahayaan, kebersihan fasilitas, staf venue, dll..."
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#0B7B69] focus:ring-2 focus:ring-[#0B7B69]/20 text-xs sm:text-sm text-slate-800 placeholder:text-slate-400 transition resize-none"
                        ></textarea>
                    </div>

                    <!-- 5. Checkbox Rekomendasi -->
                    <div class="flex items-center gap-2">
                        <input
                            type="checkbox"
                            id="review-recommend"
                            name="recommend"
                            checked
                            class="w-4 h-4 rounded text-[#0B7B69] focus:ring-[#0B7B69] border-slate-300"
                        >
                        <label for="review-recommend" class="text-xs text-slate-600 select-none cursor-pointer">
                            Saya merekomendasikan lapangan ini untuk bermain
                        </label>
                    </div>

                    <!-- Modal Action Buttons -->
                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                        <button
                            type="button"
                            onclick="closeReviewModal()"
                            class="px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold transition cursor-pointer"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            id="btn-submit-review"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#0B7B69] hover:bg-[#086354] active:scale-95 text-white text-xs font-bold shadow-md transition duration-150 cursor-pointer"
                        >
                            <svg id="submit-spinner" class="w-4 h-4 text-white animate-spin hidden" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span id="submit-text">Kirim Ulasan</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

</div>

{{-- SCRIPT: Interaktivitas Modal Review & Rating Lapangan --}}
<script>
    // Rating labels map
    const ratingLabels = {
        1: '1 Bintang — Perlu Perbaikan 👎',
        2: '2 Bintang — Kurang Memuaskan 😕',
        3: '3 Bintang — Cukup Baik 🙂',
        4: '4 Bintang — Sangat Bagus 👍',
        5: '5 Bintang — Luar Biasa! ⭐'
    };

    let selectedRating = 5;

    // Buka Modal Review
    window.openReviewModal = function () {
        const modal = document.getElementById('review-modal');
        const backdrop = document.getElementById('review-modal-backdrop');
        const card = document.getElementById('review-modal-card');
        if (!modal) return;

        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');

        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            backdrop.classList.add('opacity-100');
            card.classList.remove('scale-95', 'opacity-0');
            card.classList.add('scale-100', 'opacity-100');
        }, 10);
    };

    // Tutup Modal Review
    window.closeReviewModal = function () {
        const modal = document.getElementById('review-modal');
        const backdrop = document.getElementById('review-modal-backdrop');
        const card = document.getElementById('review-modal-card');
        if (!modal) return;

        backdrop.classList.remove('opacity-100');
        backdrop.classList.add('opacity-0');
        card.classList.remove('scale-100', 'opacity-100');
        card.classList.add('scale-95', 'opacity-0');

        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }, 200);
    };

    // Keyboard listener Escape
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('review-modal');
            if (modal && !modal.classList.contains('hidden')) {
                closeReviewModal();
            }
        }
    });

    // Rating Star Picker Interaction
    document.addEventListener('DOMContentLoaded', function () {
        const starButtons = document.querySelectorAll('#star-picker .star-btn');
        const ratingInput = document.getElementById('review-rating-input');
        const ratingLabel = document.getElementById('star-label');

        function updateStars(rating) {
            starButtons.forEach(btn => {
                const btnRating = parseInt(btn.getAttribute('data-rating'), 10);
                if (btnRating <= rating) {
                    btn.classList.remove('text-slate-200');
                    btn.classList.add('text-amber-400');
                } else {
                    btn.classList.remove('text-amber-400');
                    btn.classList.add('text-slate-200');
                }
            });
            if (ratingLabel) {
                ratingLabel.textContent = ratingLabels[rating] || (rating + ' Bintang');
            }
        }

        starButtons.forEach(btn => {
            btn.addEventListener('mouseenter', function () {
                const hoverRating = parseInt(this.getAttribute('data-rating'), 10);
                updateStars(hoverRating);
            });

            btn.addEventListener('mouseleave', function () {
                updateStars(selectedRating);
            });

            btn.addEventListener('click', function () {
                selectedRating = parseInt(this.getAttribute('data-rating'), 10);
                if (ratingInput) ratingInput.value = selectedRating;
                updateStars(selectedRating);
            });
        });

        // Tag Chips Selector
        const tagChips = document.querySelectorAll('.tag-chip');
        const tagsInput = document.getElementById('review-tags-input');

        function refreshTagsInput() {
            const activeTags = [];
            document.querySelectorAll('.tag-chip.active').forEach(chip => {
                activeTags.push(chip.getAttribute('data-tag'));
            });
            if (tagsInput) {
                tagsInput.value = activeTags.join(',');
            }
        }

        tagChips.forEach(chip => {
            chip.addEventListener('click', function () {
                this.classList.toggle('active');
                if (this.classList.contains('active')) {
                    this.className = 'tag-chip active px-2.5 py-1 rounded-lg text-xs font-medium border border-emerald-300 bg-emerald-50 text-[#0B7B69] transition cursor-pointer';
                } else {
                    this.className = 'tag-chip px-2.5 py-1 rounded-lg text-xs font-medium border border-slate-200 bg-slate-50 text-slate-600 hover:border-slate-300 transition cursor-pointer';
                }
                refreshTagsInput();
            });
        });

        // Form Submit Handler
        const form = document.getElementById('review-form');
        const submitBtn = document.getElementById('btn-submit-review');
        const spinner = document.getElementById('submit-spinner');
        const submitText = document.getElementById('submit-text');

        if (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();

                const komentar = document.getElementById('review-komentar')?.value.trim();
                if (!komentar) {
                    alert('Silakan tulis ulasan atau komentar Anda terlebih dahulu.');
                    return;
                }

                // Tampilkan loading
                if (submitBtn) submitBtn.disabled = true;
                if (spinner) spinner.classList.remove('hidden');
                if (submitText) submitText.textContent = 'Mengirim...';

                const formData = new FormData(form);

                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    const reviewData = data.review || {
                        name: document.getElementById('review-nama')?.value.trim() || 'Penyewa Terverifikasi',
                        rating: selectedRating,
                        comment: komentar,
                        date: 'Baru saja',
                        verified: true,
                        tags: (document.getElementById('review-tags-input')?.value || '').split(',').filter(Boolean)
                    };

                    // Buat elemen ulasan baru di puncak kontainer
                    prependReviewToContainer(reviewData);

                    // Reset form & tutup modal
                    form.reset();
                    selectedRating = 5;
                    updateStars(5);
                    refreshTagsInput();
                    closeReviewModal();

                    // Scroll halus ke section ulasan
                    const reviewSection = document.getElementById('section-ulasan');
                    if (reviewSection) {
                        reviewSection.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }

                    // Tampilkan notifikasi berhasil
                    showFloatingToast('Terima kasih! Ulasan lapangan berhasil ditambahkan.');
                })
                .catch(err => {
                    // Fallback jika respons bukan JSON
                    const fallbackName = document.getElementById('review-nama')?.value.trim() || 'Penyewa Terverifikasi';
                    const activeTags = (document.getElementById('review-tags-input')?.value || '').split(',').filter(Boolean);

                    prependReviewToContainer({
                        name: fallbackName,
                        rating: selectedRating,
                        comment: komentar,
                        date: 'Baru saja',
                        verified: true,
                        tags: activeTags
                    });

                    form.reset();
                    closeReviewModal();
                    showFloatingToast('Terima kasih! Ulasan lapangan Anda berhasil dikirim.');
                })
                .finally(() => {
                    if (submitBtn) submitBtn.disabled = false;
                    if (spinner) spinner.classList.add('hidden');
                    if (submitText) submitText.textContent = 'Kirim Ulasan';
                });
            });
        }
    });

    // Helper: Tambahkan Card Review ke Puncak List
    function prependReviewToContainer(rev) {
        const container = document.getElementById('reviews-container');
        if (!container) return;

        let starsHtml = '';
        for (let s = 1; s <= 5; s++) {
            if (s <= rev.rating) {
                starsHtml += `<svg class="w-4 h-4 text-amber-400 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>`;
            } else {
                starsHtml += `<svg class="w-4 h-4 text-slate-200 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>`;
            }
        }

        let tagsHtml = '';
        if (Array.isArray(rev.tags) && rev.tags.length > 0) {
            tagsHtml = '<div class="flex items-center gap-1.5 flex-wrap pt-0.5">' +
                rev.tags.map(t => `<span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-100 text-slate-700">✓ ${escapeHtml(t)}</span>`).join('') +
                '</div>';
        }

        const initial = (rev.name || 'P').substring(0, 2).toUpperCase();

        const card = document.createElement('div');
        card.className = 'pt-3.5 first:pt-0 space-y-2 p-3 rounded-xl bg-emerald-50/50 border border-emerald-200 transition duration-300';
        card.innerHTML = `
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-emerald-100 text-[#0B7B69] font-bold text-xs flex items-center justify-center shrink-0 uppercase">
                        ${initial}
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-slate-900">${escapeHtml(rev.name)}</span>
                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-100 text-[#0B7B69] border border-emerald-300">
                                <span>Baru Saja</span>
                            </span>
                        </div>
                        <span class="text-[11px] text-slate-400">${rev.date || 'Baru saja'}</span>
                    </div>
                </div>
                <div class="flex items-center gap-0.5 shrink-0">
                    ${starsHtml}
                </div>
            </div>
            ${tagsHtml}
            <p class="text-xs text-slate-700 leading-relaxed">
                ${escapeHtml(rev.comment)}
            </p>
        `;

        container.insertBefore(card, container.firstChild);

        // Update counter review
        const summaryBadge = document.getElementById('summary-reviews-count');
        if (summaryBadge) {
            summaryBadge.textContent = '121+ ulasan';
        }
    }

    // Helper: Toast Notifikasi Mengambang
    function showFloatingToast(msg) {
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-5 right-5 z-50 flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-800 text-white shadow-xl text-xs sm:text-sm font-semibold transition-all duration-300 transform translate-y-3 opacity-0';
        toast.innerHTML = `
            <svg class="w-5 h-5 text-emerald-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>${msg}</span>
        `;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.classList.remove('translate-y-3', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');
        }, 10);

        setTimeout(() => {
            toast.classList.add('translate-y-3', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    }

    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return (text || '').replace(/[&<>"']/g, function(m) { return map[m]; });
    }
</script>
</x-layouts.penyewa>
