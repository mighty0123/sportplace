{{--
    Halaman Detail Booking & Pembayaran (Invoice & Tiket Check-in Arena) — SportPlace
    Layout : x-layouts.penyewa
    Desain : Sesuai referensi Figma (Detail Lapangan, Rincian Biaya/Invoice, Tiket Check-in Arena dengan QR Code)
--}}
<x-layouts.penyewa title="Detail Booking & Pembayaran — SportPlace" active="riwayat">
<div class="max-w-6xl mx-auto space-y-6 pb-12">

    {{-- =========================================================
         BREADCRUMB / BACK LINK
    ========================================================= --}}
    <div>
        <a href="{{ route('penyewa.riwayat') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 transition">
            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali ke Riwayat Booking</span>
        </a>
    </div>

    {{-- =========================================================
         PAGE HEADER: TITLE, BADGE & STATUS
    ========================================================= --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-200/80 pb-4">
        <div>
            <div class="flex items-center gap-2.5 flex-wrap">
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                    Detail Booking #{{ $bookingCode }}
                </h1>
                <span class="px-2.5 py-0.5 rounded-md text-[11px] font-bold uppercase tracking-wider bg-slate-100 text-slate-700 border border-slate-200">
                    {{ $sport->nama_olahraga ?? 'Futsal' }}
                </span>
            </div>
            <p class="text-xs text-slate-400 mt-1 flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span>Dipesan pada {{ $dipesanPada }}</span>
            </p>
        </div>

        <div>
            @if ($isPaid)
                <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 shadow-2xs">
                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Terkonfirmasi</span>
                </span>
            @elseif ($isCancelled)
                <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-rose-50 text-rose-800 border border-rose-200 shadow-2xs">
                    <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    <span>Dibatalkan</span>
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    <span>Menunggu Pembayaran</span>
                </span>
            @endif
        </div>
    </div>

    {{-- =========================================================
         MAIN 2-COLUMNS GRID
    ========================================================= --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        {{-- =========================================================
             LEFT COLUMN (7-8 Cols): Detail Lapangan + Rincian Biaya
        ========================================================= --}}
        <div class="lg:col-span-7 xl:col-span-8 space-y-6">

            {{-- 1. CARD DETAIL LAPANGAN & JADWAL --}}
            <div class="bg-white rounded-2xl border border-slate-200/90 overflow-hidden shadow-xs">
                <!-- Hero Image -->
                <div class="relative h-56 sm:h-64 w-full bg-slate-900 overflow-hidden">
                    <img
                        src="{{ $courtPhoto }}"
                        alt="{{ $venue->nama_venue ?? 'Lapangan' }}"
                        class="w-full h-full object-cover"
                        loading="lazy"
                        onerror="this.onerror=null; this.src='{{ asset('images/courts/futsal.jpg') }}';"
                    >
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-black/20"></div>

                    <!-- Floating Badges Top Left -->
                    <div class="absolute top-4 left-4 flex items-center gap-2">
                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-[#07362B] text-white shadow-xs">
                            {{ $sport->nama_olahraga ?? 'Futsal' }}
                        </span>
                        <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-white/90 text-slate-800 shadow-xs backdrop-blur-xs">
                            Indoor Hall
                        </span>
                    </div>

                    <!-- Floating Status Badge Bottom Right -->
                    <div class="absolute bottom-4 right-4">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-white text-slate-900 shadow-sm">
                            <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                            <span>Jadwal Terjadwal</span>
                        </span>
                    </div>
                </div>

                <!-- Content Body -->
                <div class="p-5 sm:p-6 space-y-5">
                    <!-- Title & Rating -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-slate-100 pb-4">
                        <div>
                            <h2 class="text-base sm:text-lg font-bold text-slate-900">
                                {{ $venue->nama_venue ?? 'Sport Arena' }} — {{ $field->nama_lapangan ?? 'Lapangan' }}
                            </h2>
                            <p class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span>{{ $venue->alamat_lengkap ?? 'Lokasi Venue' }}</span>
                                @if (!empty($venue->city?->nama_kota))
                                    <span>, {{ $venue->city->nama_kota }}</span>
                                @endif
                            </p>
                        </div>

                        <div class="flex items-center gap-3 shrink-0 self-start sm:self-center">
                            <span class="inline-flex items-center gap-1 text-xs font-bold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200/80">
                                <svg class="w-3.5 h-3.5 fill-current text-amber-500" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <span>4.8</span>
                            </span>

                            <a
                                href="{{ $venue->map_url ?? 'https://maps.google.com' }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center gap-1 text-xs font-bold text-[#0B7B69] hover:underline"
                            >
                                <span>Buka di Maps</span>
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- 4-Box Grid (2x2) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <!-- Tanggal Main -->
                        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-[#0B7B69] flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tanggal Main</span>
                                <span class="block text-xs font-bold text-slate-800 mt-0.5 truncate">{{ $tanggalMain }}</span>
                            </div>
                        </div>

                        <!-- Sesi & Durasi -->
                        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-[#0B7B69] flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Sesi & Durasi</span>
                                <span class="block text-xs font-bold text-slate-800 mt-0.5 truncate">{{ $sesiDurasi }}</span>
                            </div>
                        </div>

                        <!-- Tipe Lapangan -->
                        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-[#0B7B69] flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect width="18" height="18" x="3" y="3" rx="3"/>
                                    <line x1="3" x2="21" y1="12" y2="12"/>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tipe Lapangan</span>
                                <span class="block text-xs font-bold text-slate-800 mt-0.5 truncate">Court {{ $field->id }} — {{ $field->nama_lapangan }}</span>
                            </div>
                        </div>

                        <!-- Nama Pemesan -->
                        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-[#0B7B69] flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Nama Pemesan</span>
                                <span class="block text-xs font-bold text-slate-800 mt-0.5 truncate">{{ $customer->name ?? 'Penyewa' }}</span>
                                <span class="block text-[11px] text-slate-500">{{ $customer->no_hp ?? '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Fasilitas Termasuk -->
                    <div class="pt-2">
                        <span class="block text-xs font-semibold text-slate-700 mb-2">Fasilitas Termasuk:</span>
                        <div class="flex flex-wrap items-center gap-2">
                            @foreach ($facilities as $facility)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/80 shadow-2xs">
                                    <svg class="w-3 h-3 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span>{{ $facility }}</span>
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. CARD RINCIAN BIAYA & INVOICE --}}
            <div class="bg-white rounded-2xl border border-slate-200/90 p-5 sm:p-6 shadow-xs space-y-5">
                <!-- Header Card -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">
                            Rincian Biaya & Invoice
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Metode: <strong class="text-slate-700">{{ $paymentMethod }}</strong> • Lunas Otomatis
                        </p>
                    </div>

                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-mono font-bold bg-slate-100 text-slate-700 border border-slate-200 self-start sm:self-auto">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Trx ID: {{ $trxId }}</span>
                    </span>
                </div>

                <!-- Line Items Breakdown -->
                <div class="space-y-3 text-xs sm:text-sm">
                    @foreach ($lineItems as $item)
                        <div class="flex items-center justify-between text-slate-600">
                            <span class="truncate pr-4">• {{ $item['label'] }}</span>
                            <span class="font-bold text-slate-800 tabular-nums shrink-0">Rp {{ number_format($item['price'], 0, ',', '.') }}</span>
                        </div>
                    @endforeach

                    <div class="flex items-center justify-between text-slate-600">
                        <span>• Biaya Layanan Platform</span>
                        <span class="font-bold text-slate-800 tabular-nums shrink-0">Rp {{ number_format($serviceFee, 0, ',', '.') }}</span>
                    </div>

                    <div class="flex items-center justify-between text-emerald-700 font-semibold">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            <span>Promo Diskon (SPORTSPACEBARU)</span>
                        </span>
                        <span class="tabular-nums shrink-0">-Rp {{ number_format($promoDiscount, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Divider & Total -->
                <div class="border-t border-slate-100 pt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">TOTAL BAYAR</span>
                        <div class="flex items-center gap-2.5 mt-0.5">
                            <span class="text-xl sm:text-2xl font-extrabold text-slate-900 tabular-nums">
                                Rp {{ number_format($totalBayar, 0, ',', '.') }}
                            </span>
                            @if ($isPaid)
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase tracking-wide bg-emerald-100 text-emerald-800">
                                    LUNAS
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="sm:text-right">
                        <span class="block text-[10px] text-slate-400">Waktu Transaksi</span>
                        <span class="block text-xs font-semibold text-slate-700 mt-0.5">{{ $dipesanPada }}</span>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="border-t border-slate-100 pt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs text-slate-500">
                    <button
                        type="button"
                        onclick="window.print()"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold transition cursor-pointer shadow-2xs self-start"
                    >
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        <span>Unduh Invoice (PDF)</span>
                    </button>

                    <p class="flex items-center gap-1.5 text-slate-500 text-[11px]">
                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span>Faktur resmi dikirim ke <strong class="text-slate-700">{{ $customer->email }}</strong></span>
                    </p>
                </div>
            </div>

        </div>

        {{-- =========================================================
             RIGHT COLUMN (5-4 Cols): Tiket Check-in Arena + Actions
        ========================================================= --}}
        <div class="lg:col-span-5 xl:col-span-4 space-y-5 sticky top-24">

            {{-- 1. KARTU TIKET CHECK-IN ARENA --}}
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-md overflow-hidden relative">
                <!-- Ticket Header -->
                <div class="bg-[#06231D] text-white p-4 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-md bg-[#0B7B69] flex items-center justify-center text-white text-xs font-extrabold">
                            SP
                        </div>
                        <span class="text-xs font-bold tracking-wider uppercase text-emerald-100">
                            TIKET CHECK-IN ARENA
                        </span>
                    </div>

                    <svg class="w-4 h-4 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                </div>

                <!-- Ticket Body -->
                <div class="p-6 text-center space-y-4">
                    <p class="text-xs text-slate-500 leading-relaxed px-2">
                        Tunjukkan QR code ini kepada petugas resepsionis arena saat tiba di lokasi
                    </p>

                    <!-- QR Code Canvas / Vector -->
                    <div class="relative w-48 h-48 mx-auto bg-white p-3 rounded-2xl border border-slate-200/90 shadow-xs flex items-center justify-center">
                        {{-- SVG QR Code Representation with SportPlace emblem center --}}
                        <svg class="w-full h-full text-slate-900" viewBox="0 0 100 100" fill="currentColor">
                            <!-- Top Left Finder Pattern -->
                            <rect x="5" y="5" width="26" height="26" rx="4" fill="none" stroke="currentColor" stroke-width="5"/>
                            <rect x="13" y="13" width="10" height="10" rx="2" fill="currentColor"/>

                            <!-- Top Right Finder Pattern -->
                            <rect x="69" y="5" width="26" height="26" rx="4" fill="none" stroke="currentColor" stroke-width="5"/>
                            <rect x="77" y="13" width="10" height="10" rx="2" fill="currentColor"/>

                            <!-- Bottom Left Finder Pattern -->
                            <rect x="5" y="69" width="26" height="26" rx="4" fill="none" stroke="currentColor" stroke-width="5"/>
                            <rect x="13" y="77" width="10" height="10" rx="2" fill="currentColor"/>

                            <!-- QR Data Grid Blocks -->
                            <rect x="36" y="8" width="5" height="5"/>
                            <rect x="46" y="8" width="5" height="5"/>
                            <rect x="56" y="8" width="5" height="5"/>
                            <rect x="36" y="18" width="5" height="5"/>
                            <rect x="46" y="24" width="5" height="5"/>
                            <rect x="56" y="18" width="5" height="5"/>

                            <rect x="8" y="36" width="5" height="5"/>
                            <rect x="18" y="42" width="5" height="5"/>
                            <rect x="24" y="36" width="5" height="5"/>
                            <rect x="8" y="52" width="5" height="5"/>
                            <rect x="24" y="52" width="5" height="5"/>

                            <rect x="69" y="36" width="5" height="5"/>
                            <rect x="79" y="42" width="5" height="5"/>
                            <rect x="89" y="36" width="5" height="5"/>
                            <rect x="74" y="52" width="5" height="5"/>
                            <rect x="84" y="58" width="5" height="5"/>

                            <rect x="36" y="69" width="5" height="5"/>
                            <rect x="46" y="76" width="5" height="5"/>
                            <rect x="56" y="69" width="5" height="5"/>
                            <rect x="36" y="86" width="5" height="5"/>
                            <rect x="46" y="86" width="5" height="5"/>
                            <rect x="56" y="86" width="5" height="5"/>

                            <!-- Center SportPlace Lightning Emblem -->
                            <circle cx="50" cy="50" r="14" fill="#06231D"/>
                            <path d="M51 41l-6 10h5l-2 8 8-11h-5l2-7z" fill="#10B981"/>
                        </svg>
                    </div>

                    <!-- Kode Booking Box -->
                    <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-3.5 flex items-center justify-between">
                        <div class="text-left">
                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">KODE BOOKING</span>
                            <span id="ticket-code-display" class="block text-sm sm:text-base font-extrabold text-slate-900 tracking-wider font-mono">
                                {{ $ticketCode }}
                            </span>
                        </div>

                        <button
                            type="button"
                            id="btn-copy-code"
                            onclick="copyTicketCode('{{ $ticketCode }}')"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-200/70 transition cursor-pointer"
                            title="Salin Kode Booking"
                        >
                            <svg id="copy-icon-default" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            <svg id="copy-icon-success" class="w-4 h-4 text-emerald-600 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span id="copy-text-feedback" class="text-[11px] font-semibold hidden text-emerald-700">Tersalin!</span>
                        </button>
                    </div>
                </div>

                <!-- Ticket Tear Line with Semicircle Notch Cutouts -->
                <div class="relative flex items-center justify-between my-1 px-3">
                    <div class="w-5 h-5 -ml-5.5 rounded-full bg-[#F4F7F5] border-r border-slate-200"></div>
                    <div class="flex-1 border-b-2 border-dashed border-slate-200"></div>
                    <div class="w-5 h-5 -mr-5.5 rounded-full bg-[#F4F7F5] border-l border-slate-200"></div>
                </div>

                <!-- Ticket Footer Validity -->
                <div class="p-4 bg-slate-50 text-center">
                    <p class="text-xs font-semibold text-slate-500 flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Berlaku hingga {{ $berlakuHingga }}</span>
                    </p>
                </div>
            </div>

            {{-- 2. ACTION BUTTONS --}}
            <div class="space-y-2.5">
                <!-- Beri Ulasan Button -->
                <button
                    type="button"
                    onclick="alert('Terima kasih! Fitur ulasan lapangan akan segera hadir.')"
                    class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl bg-[#0B7B69] hover:bg-[#086354] active:scale-95 text-white font-bold text-xs sm:text-sm shadow-xs transition duration-150 cursor-pointer"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <span>Beri Ulasan Lapangan</span>
                </button>

                <!-- Batalkan Pemesanan Button -->
                @if (!$isCancelled)
                    <button
                        type="button"
                        onclick="document.getElementById('cancel-modal').classList.remove('hidden')"
                        class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl bg-rose-50 hover:bg-rose-100/90 text-rose-600 border border-rose-200/90 font-bold text-xs sm:text-sm transition duration-150 cursor-pointer"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Batalkan Pemesanan</span>
                    </button>
                @endif
            </div>

            {{-- 3. SUPPORT HELP BOX --}}
            <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-xl bg-slate-50 border border-slate-200 text-[#0B7B69] flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h4 class="text-xs font-bold text-slate-800">Perlu Bantuan?</h4>
                        <p class="text-[11px] text-slate-400 truncate">Hubungi tim customer support</p>
                    </div>
                </div>

                <a
                    href="https://wa.me/6281234567890?text=Halo%20SportPlace%2C%20saya%20butuh%20bantuan%20mengenai%20booking%20{{ $bookingCode }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition shrink-0 cursor-pointer"
                >
                    Chat CS
                </a>
            </div>

        </div>

    </div>

</div>

{{-- =========================================================
     MODAL KONFIRMASI PEMBATALAN
========================================================= --}}
<div
    id="cancel-modal"
    class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-xs flex items-center justify-center p-4 transition-opacity"
    role="dialog"
    aria-modal="true"
>
    <div class="bg-white rounded-2xl border border-slate-200 max-w-sm w-full p-6 shadow-2xl relative">
        <h3 class="text-base font-bold text-slate-900 mb-2">
            Batalkan Pemesanan Ini?
        </h3>
        <p class="text-xs text-slate-500 leading-relaxed mb-6">
            Apakah Anda yakin ingin membatalkan booking lapangan <strong>{{ $venue->nama_venue }}</strong>? Tindakan ini tidak dapat diurungkan.
        </p>

        <form method="POST" action="{{ route('penyewa.booking.cancel', $booking->id) }}" class="flex items-center justify-end gap-2.5">
            @csrf
            <button
                type="button"
                onclick="document.getElementById('cancel-modal').classList.add('hidden')"
                class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition"
            >
                Batal
            </button>
            <button
                type="submit"
                class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-xs transition"
            >
                Ya, Batalkan
            </button>
        </form>
    </div>
</div>

{{-- Copy to Clipboard JavaScript --}}
<script>
function copyTicketCode(code) {
    const defaultIcon = document.getElementById('copy-icon-default');
    const successIcon = document.getElementById('copy-icon-success');
    const feedback = document.getElementById('copy-text-feedback');

    function showFeedback() {
        if (defaultIcon) defaultIcon.classList.add('hidden');
        if (successIcon) successIcon.classList.remove('hidden');
        if (feedback) feedback.classList.remove('hidden');

        setTimeout(() => {
            if (defaultIcon) defaultIcon.classList.remove('hidden');
            if (successIcon) successIcon.classList.add('hidden');
            if (feedback) feedback.classList.add('hidden');
        }, 2000);
    }

    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(code).then(() => {
            showFeedback();
        }).catch(() => {
            showFallbackPrompt(code);
        });
    } else {
        showFallbackPrompt(code);
    }

    function showFallbackPrompt(val) {
        try {
            const ta = document.createElement('textarea');
            ta.value = val;
            ta.style.position = 'fixed';
            ta.style.left = '-9999px';
            document.body.appendChild(ta);
            ta.focus();
            ta.select();
            document.execCommand('copy');
            document.body.removeChild(ta);
            showFeedback();
        } catch (e) {
            prompt('Salin kode booking:', val);
        }
    }
}
</script>
</x-layouts.penyewa>
