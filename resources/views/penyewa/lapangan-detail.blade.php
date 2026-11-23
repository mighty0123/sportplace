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
                <div class="relative h-64 sm:h-80 md:h-96 w-full bg-slate-900 overflow-hidden group select-none">
                    <img
                        id="main-field-image"
                        src="{{ $photoUrl }}"
                        alt="{{ $field->nama_lapangan }}"
                        class="w-full h-full object-cover transition-opacity duration-200"
                    >
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/25 to-transparent pointer-events-none"></div>

                    <!-- Badge Sport & Status (Kiri Atas) -->
                    <div class="absolute top-4 left-4 flex items-center gap-2 z-10">
                        <span class="px-3 py-1 rounded-md text-xs font-extrabold uppercase tracking-wide bg-[#FACC15] text-slate-950 shadow-md">
                            {{ $field->sport?->nama_olahraga ?? 'OLAHRAGA' }}
                        </span>
                        <span class="px-3 py-1 rounded-md text-xs font-bold bg-emerald-500/90 text-white backdrop-blur-xs shadow-md inline-flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                            <span>Siap Dibooking</span>
                        </span>
                    </div>

                    <!-- Counter Foto (Kanan Atas) -->
                    @if(!empty($galleryPhotos) && count($galleryPhotos) > 1)
                        <div class="absolute top-4 right-4 z-10">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-900/75 backdrop-blur-md text-white border border-white/20 shadow-md">
                                <span id="slider-current-num">1</span> / {{ count($galleryPhotos) }}
                            </span>
                        </div>
                    @endif

                    <!-- Tombol Navigasi Geser Kiri / Kanan pada Gambar Besar -->
                    @if(!empty($galleryPhotos) && count($galleryPhotos) > 1)
                        <button
                            type="button"
                            onclick="prevPhoto()"
                            class="absolute left-3 top-1/2 -translate-y-1/2 w-9 sm:w-10 h-9 sm:h-10 rounded-full bg-black/40 hover:bg-black/80 text-white flex items-center justify-center backdrop-blur-xs transition cursor-pointer border border-white/20 shadow-lg active:scale-95 focus:outline-none z-10"
                            aria-label="Foto sebelumnya"
                            title="Foto sebelumnya"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        <button
                            type="button"
                            onclick="nextPhoto()"
                            class="absolute right-3 top-1/2 -translate-y-1/2 w-9 sm:w-10 h-9 sm:h-10 rounded-full bg-black/40 hover:bg-black/80 text-white flex items-center justify-center backdrop-blur-xs transition cursor-pointer border border-white/20 shadow-lg active:scale-95 focus:outline-none z-10"
                            aria-label="Foto berikutnya"
                            title="Foto berikutnya"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    @endif

                    <!-- Overlay Judul di Bawah Foto -->
                    <div class="absolute inset-x-0 bottom-0 p-5 sm:p-6 text-white pointer-events-none z-10">
                        <p class="text-xs sm:text-sm font-medium text-emerald-300">
                            {{ $field->venue?->nama_venue ?? 'Sport Arena' }}
                        </p>
                        <h1 class="text-2xl sm:text-3xl md:text-4xl font-black tracking-tight leading-tight mt-1">
                            {{ $field->nama_lapangan }}
                        </h1>
                    </div>
                </div>

                {{-- LIST GAMBAR DI BAWAHNYA (LANGSUNG TAMPIL TANPA TEKS HEADER) --}}
                @if(!empty($galleryPhotos) && count($galleryPhotos) > 0)
                    <div class="px-4 sm:px-6 py-3 border-b border-slate-100 bg-slate-50/60 relative group/slider">
                        <!-- Slider Container yang Bisa Digeser / Scroll Horizontal -->
                        <div
                            id="court-gallery-slider"
                            class="flex items-center gap-2.5 sm:gap-3 overflow-x-auto scroll-smooth py-1 no-scrollbar cursor-grab active:cursor-grabbing select-none"
                            style="scrollbar-width: none; -ms-overflow-style: none;"
                        >
                            @foreach($galleryPhotos as $index => $item)
                                <button
                                    type="button"
                                    data-index="{{ $index }}"
                                    data-url="{{ $item['url'] }}"
                                    data-primary="{{ $item['is_primary'] ? '1' : '0' }}"
                                    onclick="selectGalleryPhoto({{ $index }})"
                                    class="gallery-thumb-btn group relative shrink-0 w-24 sm:w-32 h-16 sm:h-20 rounded-xl overflow-hidden border-2 transition-all duration-200 cursor-pointer focus:outline-none {{ $index === 0 ? 'border-[#0B7B69] ring-2 ring-[#0B7B69]/30 shadow-md scale-[1.02]' : 'border-slate-200 hover:border-slate-300 opacity-75 hover:opacity-100' }}"
                                >
                                    <img
                                        src="{{ $item['url'] }}"
                                        alt="Foto Lapangan {{ $index + 1 }}"
                                        class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105 pointer-events-none"
                                        loading="lazy"
                                    >

                                    <!-- Badge is_primary jika foto utama di database -->
                                    @if($item['is_primary'])
                                        <span class="absolute top-1 left-1 px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-[#0B7B69] text-white shadow-xs">
                                            Utama
                                        </span>
                                    @endif

                                    <!-- Indikator Aktif -->
                                    <div class="thumb-active-check absolute top-1 right-1 w-4 h-4 rounded-full bg-[#0B7B69] text-white flex items-center justify-center text-[10px] font-bold shadow-xs {{ $index === 0 ? '' : 'hidden' }}">
                                        ✓
                                    </div>
                                </button>
                            @endforeach
                        </div>

                        <!-- Tombol Geser Kiri / Kanan Melayang di Sisi Slider (jika foto lebih dari 3) -->
                        @if(count($galleryPhotos) > 3)
                            <button
                                type="button"
                                onclick="scrollGallerySlider('left')"
                                class="absolute left-1.5 top-1/2 -translate-y-1/2 w-7 h-7 rounded-full bg-white/95 hover:bg-white text-slate-700 shadow-md border border-slate-200 flex items-center justify-center transition active:scale-95 cursor-pointer z-10 opacity-0 group-hover/slider:opacity-100 sm:opacity-90"
                                title="Geser ke kiri"
                                aria-label="Geser ke kiri"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </button>
                            <button
                                type="button"
                                onclick="scrollGallerySlider('right')"
                                class="absolute right-1.5 top-1/2 -translate-y-1/2 w-7 h-7 rounded-full bg-white/95 hover:bg-white text-slate-700 shadow-md border border-slate-200 flex items-center justify-center transition active:scale-95 cursor-pointer z-10 opacity-0 group-hover/slider:opacity-100 sm:opacity-90"
                                title="Geser ke kanan"
                                aria-label="Geser ke kanan"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7m0 0l-7 7"/>
                                </svg>
                            </button>
                        @endif
                    </div>
                @endif

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
         COMPONENT: Modal Popup Review Lapangan
    ========================================================= --}}
    <x-penyewa.review-modal :field="$field" />

</div>

{{-- SCRIPT: Interaktivitas Modal Review & Rating Lapangan --}}
<script>
    // =========================================================
    // GALERI FOTO & SUDUT PANDANG LAPANGAN
    // =========================================================
    const galleryPhotos = @json($galleryPhotos ?? []);
    let currentPhotoIndex = 0;

    // Fungsi Pilih Foto dari Galeri (Thumbnail / Slider)
    window.selectGalleryPhoto = function(index) {
        if (!galleryPhotos || !galleryPhotos[index]) return;

        currentPhotoIndex = index;
        const photo = galleryPhotos[index];

        const mainImg = document.getElementById('main-field-image');
        const captionEl = document.getElementById('main-photo-caption');
        const sliderNumEl = document.getElementById('slider-current-num');

        if (mainImg) {
            mainImg.classList.add('opacity-40');
            setTimeout(() => {
                mainImg.src = photo.url;
                mainImg.classList.remove('opacity-40');
            }, 120);
        }

        if (captionEl) {
            captionEl.textContent = photo.caption;
        }

        if (sliderNumEl) {
            sliderNumEl.textContent = index + 1;
        }

        // Update styling tombol thumbnail aktif
        const thumbBtns = document.querySelectorAll('.gallery-thumb-btn');
        thumbBtns.forEach((btn, idx) => {
            const checkIcon = btn.querySelector('.thumb-active-check');
            if (idx === index) {
                btn.className = 'gallery-thumb-btn group relative shrink-0 w-28 sm:w-36 h-20 sm:h-24 rounded-xl overflow-hidden border-2 border-[#0B7B69] ring-2 ring-[#0B7B69]/30 shadow-md scale-[1.02] transition-all duration-200 cursor-pointer focus:outline-none';
                if (checkIcon) checkIcon.classList.remove('hidden');

                // Pastikan thumbnail yang terpilih terlihat di dalam scroll viewport
                btn.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
            } else {
                btn.className = 'gallery-thumb-btn group relative shrink-0 w-28 sm:w-36 h-20 sm:h-24 rounded-xl overflow-hidden border-2 border-slate-200 hover:border-slate-300 opacity-75 hover:opacity-100 transition-all duration-200 cursor-pointer focus:outline-none';
                if (checkIcon) checkIcon.classList.add('hidden');
            }
        });
    };

    // Navigasi Foto Berikutnya
    window.nextPhoto = function() {
        if (!galleryPhotos || galleryPhotos.length <= 1) return;
        const nextIdx = (currentPhotoIndex + 1) % galleryPhotos.length;
        selectGalleryPhoto(nextIdx);
    };

    // Navigasi Foto Sebelumnya
    window.prevPhoto = function() {
        if (!galleryPhotos || galleryPhotos.length <= 1) return;
        const prevIdx = (currentPhotoIndex - 1 + galleryPhotos.length) % galleryPhotos.length;
        selectGalleryPhoto(prevIdx);
    };

    // Geser Slider Thumbnail secara horizontal
    window.scrollGallerySlider = function(direction) {
        const slider = document.getElementById('court-gallery-slider');
        if (!slider) return;
        const scrollDistance = 220;
        slider.scrollBy({
            left: direction === 'left' ? -scrollDistance : scrollDistance,
            behavior: 'smooth'
        });
    };

    // Inisialisasi Drag-to-Scroll & Touch Geser untuk Slider
    document.addEventListener('DOMContentLoaded', function() {
        const slider = document.getElementById('court-gallery-slider');
        if (slider) {
            let isDown = false;
            let startX;
            let scrollLeft;

            slider.addEventListener('mousedown', (e) => {
                isDown = true;
                startX = e.pageX - slider.offsetLeft;
                scrollLeft = slider.scrollLeft;
            });

            slider.addEventListener('mouseleave', () => {
                isDown = false;
            });

            slider.addEventListener('mouseup', () => {
                isDown = false;
            });

            slider.addEventListener('mousemove', (e) => {
                if (!isDown) return;
                e.preventDefault();
                const x = e.pageX - slider.offsetLeft;
                const walk = (x - startX) * 1.5; // multiplier geser
                slider.scrollLeft = scrollLeft - walk;
            });
        }

        // Touch Swipe Gesture pada Foto Utama
        const mainImage = document.getElementById('main-field-image');
        if (mainImage && mainImage.parentElement) {
            let touchStartX = 0;
            let touchEndX = 0;

            mainImage.parentElement.addEventListener('touchstart', (e) => {
                touchStartX = e.changedTouches[0].screenX;
            }, { passive: true });

            mainImage.parentElement.addEventListener('touchend', (e) => {
                touchEndX = e.changedTouches[0].screenX;
                const diff = touchStartX - touchEndX;
                if (Math.abs(diff) > 45) {
                    if (diff > 0) {
                        nextPhoto(); // Swipe kiri -> foto berikutnya
                    } else {
                        prevPhoto(); // Swipe kanan -> foto sebelumnya
                    }
                }
            }, { passive: true });
        }

        // Keyboard Shortcut Panah Kiri / Kanan
        document.addEventListener('keydown', function(e) {
            const reviewModal = document.getElementById('review-modal');
            if (reviewModal && !reviewModal.classList.contains('hidden')) {
                return; // Jangan geser foto jika modal review sedang terbuka
            }
            if (e.key === 'ArrowRight') {
                nextPhoto();
            } else if (e.key === 'ArrowLeft') {
                prevPhoto();
            }
        });
    });

    // Helper: Tambahkan Card Review ke Puncak List (Dipanggil oleh Review Modal Component)
    window.prependReviewToContainer = function (rev) {
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
    };

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
