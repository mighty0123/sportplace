{{--
    Halaman Review & Konfirmasi Pembayaran (Checkout) — SportPlace
    Layout  : x-layouts.penyewa
    Features: Review jadwal sewa, data pemesan, opsi metode pembayaran (VA/QRIS), promo voucher, dan tombol "Bayar Sekarang"
--}}
<x-layouts.penyewa title="Review & Konfirmasi Pembayaran — SportPlace" active="riwayat">
<div class="max-w-6xl mx-auto space-y-6 pb-16">

    {{-- =========================================================
         BREADCRUMB / BACK LINK
    ========================================================= --}}
    <div>
        <a href="{{ route('penyewa.kalender', ['id' => $field->id, 'date' => $tanggalSewaRaw]) }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 transition">
            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali ke Jadwal Lapangan</span>
        </a>
    </div>

    {{-- =========================================================
         PAGE HEADER & STEPPER PROGRESS INDICATOR
    ========================================================= --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200/80 pb-5">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                Review & Konfirmasi Pembayaran
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Periksa kembali detail pesanan Anda dan pilih metode pembayaran sebelum menyelesaikan transaksi.
            </p>
        </div>

        {{-- Stepper Progress --}}
        <div class="flex items-center gap-2 sm:gap-3 text-xs shrink-0 self-start md:self-auto">
            <!-- Step 1 (Done) -->
            <div class="flex items-center gap-1.5 text-emerald-700 font-bold">
                <div class="w-6 h-6 rounded-full bg-emerald-100 text-[#0B7B69] flex items-center justify-center text-xs">
                    <svg class="w-3.5 h-3.5 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <span class="hidden sm:inline">Pilih Jadwal</span>
            </div>

            <div class="w-6 sm:w-8 h-0.5 bg-emerald-300"></div>

            <!-- Step 2 (Active) -->
            <div class="flex items-center gap-1.5 text-[#0B7B69] font-extrabold">
                <div class="w-6 h-6 rounded-full bg-[#0B7B69] text-white flex items-center justify-center text-xs shadow-xs ring-4 ring-emerald-500/20">
                    2
                </div>
                <span>Review & Bayar</span>
            </div>

            <div class="w-6 sm:w-8 h-0.5 bg-slate-200"></div>

            <!-- Step 3 (Upcoming) -->
            <div class="flex items-center gap-1.5 text-slate-400 font-medium">
                <div class="w-6 h-6 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center text-xs">
                    3
                </div>
                <span class="hidden sm:inline">Tiket Arena</span>
            </div>
        </div>
    </div>

    {{-- =========================================================
         MAIN FORM & 2-COLUMNS GRID
    ========================================================= --}}
    <form id="checkout-form" method="POST" action="{{ route('penyewa.pembayaran.process', $booking->id ?? null) }}">
        @csrf
        <input type="hidden" name="booking_id" value="{{ $booking->id ?? '' }}">
        <input type="hidden" name="field_id" value="{{ $field->id }}">
        <input type="hidden" name="tanggal_sewa" value="{{ $tanggalSewaRaw }}">
        <input type="hidden" name="total_bayar" id="input-total-bayar" value="{{ $totalBayar }}">
        <input type="hidden" name="slots" id="input-slots" value="{{ is_array($slotsPayload) ? json_encode($slotsPayload) : $slotsPayload }}">

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            {{-- =========================================================
                 LEFT COLUMN (7-8 Cols): Order Details, Contact, Payment Methods
            ========================================================= --}}
            <div class="lg:col-span-7 xl:col-span-8 space-y-6">

                {{-- 1. CARD DETAIL LAPANGAN & JADWAL --}}
                <div class="bg-white rounded-2xl border border-slate-200/90 overflow-hidden shadow-xs">
                    <div class="relative h-44 sm:h-52 w-full bg-slate-900 overflow-hidden">
                        <img
                            src="{{ $courtPhoto }}"
                            alt="{{ $venue->nama_venue ?? 'Lapangan' }}"
                            class="w-full h-full object-cover"
                            onerror="this.onerror=null; this.src='{{ asset('images/courts/futsal.jpg') }}';"
                        >
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>

                        <!-- Badges Top Left -->
                        <div class="absolute top-3.5 left-3.5 flex items-center gap-2">
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-[#07362B] text-white shadow-xs">
                                {{ $sport->nama_olahraga ?? 'Futsal' }}
                            </span>
                            <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-white/90 text-slate-800 shadow-xs backdrop-blur-xs">
                                Indoor Hall
                            </span>
                        </div>
                    </div>

                    <div class="p-5 sm:p-6 space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-slate-100 pb-3.5">
                            <div>
                                <h2 class="text-base sm:text-lg font-bold text-slate-900">
                                    {{ $venue->nama_venue ?? 'Arena' }} — {{ $field->nama_lapangan }}
                                </h2>
                                <p class="text-xs text-slate-500 mt-0.5 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span>{{ $venue->alamat_lengkap ?? 'Indonesia' }}</span>
                                </p>
                            </div>

                            <a
                                href="{{ $venue->map_url ?? 'https://maps.google.com' }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center gap-1 text-xs font-bold text-[#0B7B69] hover:underline shrink-0"
                            >
                                <span>Buka di Maps</span>
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                            </a>
                        </div>

                        <!-- 4-Box Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-[#0B7B69] flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tanggal Main</span>
                                    <span class="block text-xs font-bold text-slate-800 mt-0.5">{{ $tanggalMain }}</span>
                                </div>
                            </div>

                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-[#0B7B69] flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Sesi & Durasi</span>
                                    <span class="block text-xs font-bold text-slate-800 mt-0.5">{{ $sesiDurasi }}</span>
                                </div>
                            </div>

                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-[#0B7B69] flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect width="18" height="18" x="3" y="3" rx="3"/>
                                        <line x1="3" x2="21" y1="12" y2="12"/>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tipe Lapangan</span>
                                    <span class="block text-xs font-bold text-slate-800 mt-0.5">Court {{ $field->id }} — {{ $field->nama_lapangan }}</span>
                                </div>
                            </div>

                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-[#0B7B69] flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="10" stroke-width="2"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2"/>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Durasi</span>
                                    <span class="block text-xs font-bold text-slate-800 mt-0.5">{{ $totalJam }} Jam Permainan</span>
                                </div>
                            </div>
                        </div>

                        <!-- Fasilitas Lapangan -->
                        <div class="pt-1">
                            <span class="block text-[11px] font-semibold text-slate-600 mb-2">Fasilitas Arena Termasuk:</span>
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

                {{-- 2. CARD INFORMASI PEMESAN --}}
                <div class="bg-white rounded-2xl border border-slate-200/90 p-5 sm:p-6 shadow-xs space-y-4">
                    <div class="border-b border-slate-100 pb-3">
                        <h3 class="text-base font-bold text-slate-900">
                            Informasi Kontak Pemesan
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            E-tiket QR Code dan faktur resmi akan dikirimkan ke kontak di bawah ini.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">Nama Lengkap Pemesan</label>
                            <input
                                type="text"
                                name="nama_pemesan"
                                value="{{ $customer->name ?? 'Dimas Pratama' }}"
                                required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/90 focus:border-[#0B7B69] focus:ring-2 focus:ring-emerald-500/20 text-xs font-medium text-slate-800 bg-slate-50/50 outline-none transition"
                            >
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">Nomor WhatsApp / HP</label>
                            <input
                                type="text"
                                name="no_hp"
                                value="{{ $customer->no_hp ?? '+62 812-3456-7890' }}"
                                required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/90 focus:border-[#0B7B69] focus:ring-2 focus:ring-emerald-500/20 text-xs font-medium text-slate-800 bg-slate-50/50 outline-none transition"
                            >
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block font-bold text-slate-700 mb-1.5">Alamat Email (Faktur & Tiket)</label>
                            <input
                                type="email"
                                name="email"
                                value="{{ $customer->email ?? 'dimas.pratama@email.com' }}"
                                required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/90 focus:border-[#0B7B69] focus:ring-2 focus:ring-emerald-500/20 text-xs font-medium text-slate-800 bg-slate-50/50 outline-none transition"
                            >
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block font-semibold text-slate-600 mb-1.5">Catatan Khusus untuk Arena (Opsional)</label>
                            <textarea
                                name="catatan"
                                rows="2"
                                placeholder="Contoh: Mohon sediakan bola futsal atau rompi latihan."
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/90 focus:border-[#0B7B69] focus:ring-2 focus:ring-emerald-500/20 text-xs text-slate-800 placeholder-slate-400 bg-slate-50/50 outline-none transition"
                            ></textarea>
                        </div>
                    </div>
                </div>

                {{-- 3. CARD PILIHAN METODE PEMBAYARAN --}}
                <div class="bg-white rounded-2xl border border-slate-200/90 p-5 sm:p-6 shadow-xs space-y-4">
                    <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-bold text-slate-900">
                                Pilih Metode Pembayaran
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Verifikasi otomatis 24 jam dengan konfirmasi instan.
                            </p>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-800 border border-emerald-200/80">
                            Lunas Otomatis
                        </span>
                    </div>

                    <!-- Kategori 1: Virtual Account -->
                    <div class="space-y-2.5">
                        <span class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Virtual Account (Bank Transfer)</span>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <!-- BCA Virtual Account (Default) -->
                            <label class="payment-option relative flex items-center gap-3 p-3.5 rounded-xl border-2 border-[#0B7B69] bg-emerald-50/40 cursor-pointer transition">
                                <input type="radio" name="metode_pembayaran" value="BCA Virtual Account" checked class="text-[#0B7B69] focus:ring-[#0B7B69] w-4 h-4">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-bold text-slate-900">BCA Virtual Account</span>
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-extrabold bg-blue-100 text-blue-800">BCA</span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Verifikasi otomatis tanpa bukti transfer</p>
                                </div>
                            </label>

                            <!-- Mandiri Virtual Account -->
                            <label class="payment-option relative flex items-center gap-3 p-3.5 rounded-xl border border-slate-200 hover:border-slate-300 bg-white cursor-pointer transition">
                                <input type="radio" name="metode_pembayaran" value="Mandiri Virtual Account" class="text-[#0B7B69] focus:ring-[#0B7B69] w-4 h-4">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-bold text-slate-800">Mandiri Virtual Account</span>
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-extrabold bg-amber-100 text-amber-800">MANDIRI</span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Livin' by Mandiri & ATM</p>
                                </div>
                            </label>

                            <!-- BNI Virtual Account -->
                            <label class="payment-option relative flex items-center gap-3 p-3.5 rounded-xl border border-slate-200 hover:border-slate-300 bg-white cursor-pointer transition">
                                <input type="radio" name="metode_pembayaran" value="BNI Virtual Account" class="text-[#0B7B69] focus:ring-[#0B7B69] w-4 h-4">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-bold text-slate-800">BNI Virtual Account</span>
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-extrabold bg-teal-100 text-teal-800">BNI</span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 mt-0.5">BNI Mobile Banking & ATM</p>
                                </div>
                            </label>

                            <!-- BRI Virtual Account -->
                            <label class="payment-option relative flex items-center gap-3 p-3.5 rounded-xl border border-slate-200 hover:border-slate-300 bg-white cursor-pointer transition">
                                <input type="radio" name="metode_pembayaran" value="BRI Virtual Account" class="text-[#0B7B69] focus:ring-[#0B7B69] w-4 h-4">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-bold text-slate-800">BRI Virtual Account (BRIVA)</span>
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-extrabold bg-blue-100 text-blue-900">BRI</span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 mt-0.5">BRImo & ATM BRI</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Kategori 2: QRIS & E-Wallet -->
                    <div class="space-y-2.5 pt-2 border-t border-slate-100">
                        <span class="block text-xs font-bold text-slate-700 uppercase tracking-wider">QRIS & Instant E-Wallet</span>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <!-- QRIS -->
                            <label class="payment-option relative flex items-center gap-3 p-3.5 rounded-xl border border-slate-200 hover:border-slate-300 bg-white cursor-pointer transition">
                                <input type="radio" name="metode_pembayaran" value="QRIS" class="text-[#0B7B69] focus:ring-[#0B7B69] w-4 h-4">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-bold text-slate-800">QRIS (Semua E-Wallet & Bank)</span>
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-extrabold bg-rose-100 text-rose-800">QRIS</span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 mt-0.5">GoPay, OVO, Dana, ShopeePay</p>
                                </div>
                            </label>

                            <!-- GoPay Direct -->
                            <label class="payment-option relative flex items-center gap-3 p-3.5 rounded-xl border border-slate-200 hover:border-slate-300 bg-white cursor-pointer transition">
                                <input type="radio" name="metode_pembayaran" value="GoPay" class="text-[#0B7B69] focus:ring-[#0B7B69] w-4 h-4">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-bold text-slate-800">GoPay</span>
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-extrabold bg-emerald-100 text-emerald-800">GOPAY</span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Aplikasi Gojek terhubung</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- 4. CARD PROMO VOUCHER --}}
                <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-xs space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            <span>Voucher Promo Diskon</span>
                        </span>
                        <span class="text-[11px] text-emerald-700 font-bold bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200/80">
                            Diskon Rp 20.000 Aktif
                        </span>
                    </div>

                    <div class="flex items-center gap-2">
                        <input
                            type="text"
                            value="SPORTSPACEBARU"
                            readonly
                            class="flex-1 bg-slate-50 border border-emerald-300 text-emerald-800 font-mono font-bold text-xs rounded-xl px-3.5 py-2.5 outline-none"
                        >
                        <button
                            type="button"
                            class="px-4 py-2.5 rounded-xl bg-emerald-50 border border-emerald-300 text-emerald-800 font-bold text-xs hover:bg-emerald-100 transition shrink-0 cursor-default"
                        >
                            Tersedia
                        </button>
                    </div>
                </div>

            </div>

            {{-- =========================================================
                 RIGHT COLUMN (5-4 Cols): Sticky Ringkasan & Tombol Bayar
            ========================================================= --}}
            <div class="lg:col-span-5 xl:col-span-4 space-y-4 sticky top-24">

                <!-- Countdown Timer Card -->
                <div class="bg-amber-50/90 border border-amber-200/90 rounded-2xl p-4 flex items-center justify-between gap-3 text-amber-900 shadow-2xs">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" stroke-width="2" stroke-dasharray="32" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <div>
                            <span class="block text-[11px] font-bold uppercase tracking-wider text-amber-800">Selesaikan Pembayaran</span>
                            <span class="block text-xs font-semibold text-amber-950 mt-0.5">Slot ditahan sementara</span>
                        </div>
                    </div>
                    <span id="countdown-timer" class="text-sm font-mono font-black text-amber-900 bg-white/80 px-2.5 py-1 rounded-lg border border-amber-200 shadow-2xs tabular-nums">
                        14:59
                    </span>
                </div>

                <!-- Sticky Summary Card -->
                <div class="bg-white rounded-2xl border border-slate-200/90 p-5 sm:p-6 shadow-md space-y-5">
                    <div class="border-b border-slate-100 pb-3">
                        <h3 class="text-base font-bold text-slate-900">
                            Ringkasan Pembayaran
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Rincian tagihan sebelum pembayaran diproses
                        </p>
                    </div>

                    <!-- Breakdown items -->
                    <div class="space-y-3 text-xs sm:text-sm">
                        @foreach ($lineItems as $item)
                            <div class="flex items-center justify-between text-slate-600">
                                <span class="truncate pr-3">• {{ $item['label'] }}</span>
                                <span class="font-bold text-slate-800 tabular-nums shrink-0">Rp {{ number_format($item['price'], 0, ',', '.') }}</span>
                            </div>
                        @endforeach

                        <div class="flex items-center justify-between text-slate-600">
                            <span>• Biaya Layanan Platform</span>
                            <span class="font-bold text-slate-800 tabular-nums shrink-0">Rp {{ number_format($serviceFee, 0, ',', '.') }}</span>
                        </div>

                        <div class="flex items-center justify-between text-emerald-700 font-semibold">
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                <span>Promo (SPORTSPACEBARU)</span>
                            </span>
                            <span class="tabular-nums shrink-0">-Rp {{ number_format($promoDiscount, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Total divider -->
                    <div class="border-t border-slate-100 pt-4 flex items-center justify-between">
                        <div>
                            <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">TOTAL TAGIHAN</span>
                            <span class="text-xl sm:text-2xl font-extrabold text-[#0B7B69] tabular-nums mt-0.5 block">
                                Rp {{ number_format($totalBayar, 0, ',', '.') }}
                            </span>
                        </div>
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase tracking-wide bg-emerald-100 text-emerald-800">
                            HEMAT Rp 20rb
                        </span>
                    </div>

                    <!-- Terms agreement checkbox -->
                    <div class="pt-2">
                        <label class="flex items-start gap-2.5 text-xs text-slate-500 cursor-pointer">
                            <input type="checkbox" id="terms-agree" checked required class="mt-0.5 rounded text-[#0B7B69] focus:ring-[#0B7B69]">
                            <span class="leading-relaxed">
                                Saya menyetujui <a href="#" class="text-[#0B7B69] font-bold hover:underline">Syarat & Ketentuan Sewa</a> serta Kebijakan Pembatalan SportPlace.
                            </span>
                        </label>
                    </div>

                    <!-- Primary Pay Button -->
                    <div class="pt-1">
                        <button
                            type="submit"
                            id="btn-pay-now"
                            class="w-full py-3.5 px-4 rounded-xl bg-[#0B7B69] hover:bg-[#086354] active:scale-[0.98] text-white font-extrabold text-sm shadow-md hover:shadow-lg flex items-center justify-center gap-2 transition duration-150 cursor-pointer"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <span>Bayar Sekarang • Rp {{ number_format($totalBayar, 0, ',', '.') }}</span>
                            <svg class="w-4 h-4 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Security trust footer -->
                    <div class="pt-3 border-t border-slate-100 space-y-2 text-[11px] text-slate-400">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <span>Transaksi Terenkripsi SSL 256-Bit</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                            </svg>
                            <span>Tiket check-in QR Code terbit instan setelah bayar</span>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </form>

</div>

{{-- Interactive Radio Selection & Countdown JS --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Payment Method Radio Styling Highlight
    const paymentOptions = document.querySelectorAll('.payment-option');
    paymentOptions.forEach(option => {
        const radio = option.querySelector('input[type="radio"]');
        if (!radio) return;

        radio.addEventListener('change', function () {
            paymentOptions.forEach(opt => {
                opt.classList.remove('border-2', 'border-[#0B7B69]', 'bg-emerald-50/40');
                opt.classList.add('border-slate-200', 'bg-white');
            });

            if (radio.checked) {
                option.classList.remove('border-slate-200', 'bg-white');
                option.classList.add('border-2', 'border-[#0B7B69]', 'bg-emerald-50/40');
            }
        });
    });

    // 2. Countdown Timer (14:59 down to 00:00)
    let totalSeconds = 14 * 60 + 59;
    const timerElem = document.getElementById('countdown-timer');

    if (timerElem) {
        const interval = setInterval(function () {
            if (totalSeconds <= 0) {
                clearInterval(interval);
                timerElem.textContent = '00:00';
                return;
            }
            totalSeconds--;
            const mins = Math.floor(totalSeconds / 60);
            const secs = totalSeconds % 60;
            timerElem.textContent = (mins < 10 ? '0' : '') + mins + ':' + (secs < 10 ? '0' : '') + secs;
        }, 1000);
    }
});
</script>
</x-layouts.penyewa>
