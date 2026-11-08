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
            <a href="{{ url('/penyewa/dashboard') }}" class="hover:text-[#0B7B69] transition">Dashboard</a>
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
                        <span class="text-sm font-bold text-slate-800 inline-flex items-center gap-1 text-amber-500">
                            ★ 4.8 <span class="text-xs text-slate-400 font-normal">(120+ ulasan)</span>
                        </span>
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

                    <p class="text-[11px] text-center text-slate-400">
                        Klik untuk melihat ketersediaan jam dan booking langsung
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

</div>
</x-layouts.penyewa>
