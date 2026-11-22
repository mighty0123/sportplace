{{--
    Komponen Modal Pop-up Bantuan Cepat — SportPlace
    Menampilkan Tutorial Cepat Booking 4 Langkah, FAQ Populer dengan Akordeon, Banner Tautan ke /bantuan, dan Kontak CS.
    Mendukung Simulasi Interaktif Alur Booking (Demo Client-Side).
    Menggunakan Vanilla JS (bebas dependensi eksternal), mendukung tombol keyboard Escape dan klik backdrop.
--}}

{{-- =========================================================================
     MODAL UTAMA: PUSAT BANTUAN & PANDUAN
========================================================================= --}}
<div id="help-modal" class="fixed inset-0 z-[9999] hidden overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="help-modal-title">
    <!-- Backdrop Blur Overlay -->
    <div id="help-modal-backdrop" class="fixed inset-0 bg-[#06231D]/80 backdrop-blur-sm transition-opacity duration-300 opacity-0"></div>

    <!-- Modal Dialog Positioner -->
    <div class="min-h-full flex items-center justify-center p-3 sm:p-5 text-center">
        <!-- Modal Card Container (Lebih Lebar: max-w-4xl) -->
        <div id="help-modal-card" class="relative w-full max-w-4xl bg-white rounded-3xl border border-slate-200 text-left shadow-2xl transform transition-all duration-300 scale-95 opacity-0 my-8 overflow-hidden flex flex-col max-h-[92vh]">
            
            {{-- =========================================================
                 MODAL HEADER
            ========================================================= --}}
            <div class="px-6 sm:px-8 pt-6 pb-4 border-b border-slate-200/90 bg-[#FCFBF5] flex items-start justify-between gap-4">
                <div>
                    <span class="inline-block px-3 py-1 rounded-full bg-[#DEF1E7] border border-[#BBE3D0] text-[#0A7660] text-[11px] font-bold uppercase tracking-wider mb-2">
                        Pusat Bantuan & Panduan
                    </span>
                    <h2 id="help-modal-title" class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                        Ada yang Bisa Kami Bantu?
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-600 mt-1">
                        Pelajari alur sewa lapangan atau temukan jawaban instan untuk pertanyaan umum.
                    </p>
                </div>

                <!-- Close Button [✕] -->
                <button
                    type="button"
                    onclick="closeHelpModal()"
                    class="w-9 h-9 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 hover:text-slate-900 flex items-center justify-center transition shrink-0 cursor-pointer"
                    aria-label="Tutup jendela bantuan"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- =========================================================
                 TAB SWITCHER (SEGMENTED CONTROL)
            ========================================================= --}}
            <div class="px-6 sm:px-8 py-3 bg-[#FCFBF5] border-b border-slate-200">
                <div class="w-full bg-slate-200/70 p-1.5 rounded-2xl grid grid-cols-3 gap-2">
                    <button
                        type="button"
                        id="tab-btn-tutorial"
                        onclick="switchHelpTab('tutorial')"
                        class="tab-help-btn w-full py-2.5 px-3 rounded-xl text-xs sm:text-sm font-bold transition flex items-center justify-center text-center bg-white text-[#0A7660] shadow-xs cursor-pointer whitespace-nowrap"
                    >
                        Cara Booking
                    </button>
                    <button
                        type="button"
                        id="tab-btn-faq"
                        onclick="switchHelpTab('faq')"
                        class="tab-help-btn w-full py-2.5 px-3 rounded-xl text-xs sm:text-sm font-semibold transition flex items-center justify-center text-center text-slate-600 hover:text-slate-900 cursor-pointer whitespace-nowrap"
                    >
                        Tanya Jawab (FAQ)
                    </button>
                    <button
                        type="button"
                        id="tab-btn-contact"
                        onclick="switchHelpTab('contact')"
                        class="tab-help-btn w-full py-2.5 px-3 rounded-xl text-xs sm:text-sm font-semibold transition flex items-center justify-center text-center text-slate-600 hover:text-slate-900 cursor-pointer whitespace-nowrap"
                    >
                        Hubungi CS
                    </button>
                </div>
            </div>

            {{-- =========================================================
                 TAB CONTENT BODY (SCROLLABLE & LEGA)
            ========================================================= --}}
            <div class="p-6 sm:p-8 overflow-y-auto space-y-6 flex-1">

                {{-- TAB 1: PANDUAN CARA BOOKING --}}
                <div id="help-pane-tutorial" class="space-y-6">

                    <!-- Pita Kecil: Info Tanpa Login -->
                    <div class="p-3.5 rounded-xl bg-emerald-50/80 border border-emerald-200/80 flex items-center gap-3 text-xs sm:text-sm text-emerald-950">
                        <svg class="w-5 h-5 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span><strong>Tidak perlu login untuk melihat lapangan:</strong> Anda bebas menjelajah katalog, foto fasilitas, dan jadwal slot jam kosong sebelum memutuskan memesan.</span>
                    </div>

                    <!-- 4 Langkah: Timeline Vertikal Berurutan -->
                    <div class="bg-white rounded-2xl border border-slate-200/90 p-5 sm:p-6 shadow-xs">
                        <h3 class="text-sm sm:text-base font-bold text-slate-900 mb-5">
                            4 Langkah Mudah Sewa Lapangan di SportPlace
                        </h3>

                        <div class="relative pl-8 sm:pl-10 space-y-7 before:absolute before:left-3.5 sm:before:left-4 before:top-2 before:bottom-2 before:w-0.5 before:bg-emerald-200">
                            <!-- Step 1 -->
                            <div class="relative">
                                <span class="absolute -left-8 sm:-left-10 top-0.5 w-7 h-7 rounded-full bg-[#0A7660] text-white text-xs font-bold flex items-center justify-center ring-4 ring-white shadow-xs">
                                    1
                                </span>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-900 leading-snug">Pilih Lapangan</h4>
                                    <p class="text-xs sm:text-sm text-slate-600 mt-1 leading-relaxed">
                                        Lihat foto, jenis lantai, dan fasilitas lengkap arena olahraga pilihan Anda.
                                    </p>
                                </div>
                            </div>

                            <!-- Step 2 -->
                            <div class="relative">
                                <span class="absolute -left-8 sm:-left-10 top-0.5 w-7 h-7 rounded-full bg-[#0A7660] text-white text-xs font-bold flex items-center justify-center ring-4 ring-white shadow-xs">
                                    2
                                </span>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-900 leading-snug">Pilih Jam Main</h4>
                                    <p class="text-xs sm:text-sm text-slate-600 mt-1 leading-relaxed">
                                        Slot hijau menandakan jam yang masih tersedia dan dapat langsung dipesan real-time.
                                    </p>
                                </div>
                            </div>

                            <!-- Step 3 -->
                            <div class="relative">
                                <span class="absolute -left-8 sm:-left-10 top-0.5 w-7 h-7 rounded-full bg-[#0A7660] text-white text-xs font-bold flex items-center justify-center ring-4 ring-white shadow-xs">
                                    3
                                </span>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-900 leading-snug">Bayar Instan</h4>
                                    <p class="text-xs sm:text-sm text-slate-600 mt-1 leading-relaxed">
                                        Selesaikan pembayaran aman via QRIS (semua e-wallet) atau Virtual Account bank resmi.
                                    </p>
                                </div>
                            </div>

                            <!-- Step 4 -->
                            <div class="relative">
                                <span class="absolute -left-8 sm:-left-10 top-0.5 w-7 h-7 rounded-full bg-[#0A7660] text-white text-xs font-bold flex items-center justify-center ring-4 ring-white shadow-xs">
                                    4
                                </span>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-900 leading-snug">Tiket Digital</h4>
                                    <p class="text-xs sm:text-sm text-slate-600 mt-1 leading-relaxed">
                                        Tiket QR langsung terbit di akun Anda. Cukup tunjukkan ke petugas pengelola saat tiba di venue.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Kartu Ringan: Ajakan Simulasi (Pelengkap) -->
                    <div class="p-4 sm:p-5 rounded-2xl bg-[#DEF1E7]/50 border border-[#BBE3D0] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-[#0A7660] text-white flex items-center justify-center shrink-0 shadow-xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900">Ingin mencoba dulu?</h4>
                                <p class="text-xs sm:text-sm text-slate-600 mt-0.5">Simulasi 4 langkah, aman dan tanpa biaya.</p>
                            </div>
                        </div>
                        <button
                            type="button"
                            onclick="startBookingSimulation()"
                            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-[#0A7660] hover:bg-[#0B604F] text-white text-xs sm:text-sm font-bold transition shadow-xs shrink-0 cursor-pointer"
                        >
                            <span>Coba Simulasi</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Kotak Aksi Akun: Disederhanakan -->
                    @auth
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <p class="text-xs font-bold text-slate-900">Halo, {{ auth()->user()->name }}!</p>
                                <p class="text-xs text-slate-500 mt-0.5">Akun Anda siap untuk memesan lapangan favorit.</p>
                            </div>
                            <a
                                href="{{ route('penyewa.lapangan') }}"
                                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-[#0A7660] hover:bg-[#0B604F] text-white text-xs sm:text-sm font-bold transition shadow-xs shrink-0"
                            >
                                <span>Mulai Cari Lapangan</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </a>
                        </div>
                    @else
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <h5 class="text-xs sm:text-sm font-bold text-slate-900">Siap Menjadwalkan Pertandingan?</h5>
                                <p class="text-xs text-slate-500 mt-0.5">Masuk ke akun Anda atau jelajahi ratusan arena olahraga sekarang.</p>
                            </div>
                            <div class="flex items-center gap-2.5 shrink-0">
                                <a
                                    href="{{ route('login') }}"
                                    class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-white border border-slate-300 hover:bg-slate-100 text-slate-800 text-xs font-semibold transition"
                                >
                                    Masuk / Daftar
                                </a>
                                <a
                                    href="{{ route('penyewa.lapangan') }}"
                                    class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl bg-[#0A7660] hover:bg-[#0B604F] text-white text-xs font-bold transition shadow-xs"
                                >
                                    <span>Jelajahi Lapangan</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endauth
                </div>

                {{-- TAB 2: TANYA JAWAB POPULER (FAQ KILAT) --}}
                <div id="help-pane-faq" class="space-y-3.5 hidden">
                    <!-- FAQ Item 1 -->
                    <div class="border border-slate-200 rounded-2xl overflow-hidden transition">
                        <button
                            type="button"
                            onclick="toggleHelpFaq(this)"
                            class="w-full p-4 text-left font-bold text-xs sm:text-sm text-slate-900 bg-[#FCFBF5] hover:bg-slate-50 flex items-center justify-between gap-3 transition cursor-pointer"
                        >
                            <span>Apakah jadwal di SportPlace terjamin akurat?</span>
                            <svg class="faq-chevron w-4 h-4 text-slate-500 transition-transform duration-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div class="faq-body hidden p-4 text-xs sm:text-sm text-slate-600 leading-relaxed border-t border-slate-200 bg-white">
                            Ya, SportPlace terintegrasi langsung dengan jadwal operasional mitra pengelola venue. Slot berwarna hijau menandakan jam kosong yang dapat langsung Anda pesan tanpa risiko jadwal ganda (*double booking*).
                        </div>
                    </div>

                    <!-- FAQ Item 2 -->
                    <div class="border border-slate-200 rounded-2xl overflow-hidden transition">
                        <button
                            type="button"
                            onclick="toggleHelpFaq(this)"
                            class="w-full p-4 text-left font-bold text-xs sm:text-sm text-slate-900 bg-[#FCFBF5] hover:bg-slate-50 flex items-center justify-between gap-3 transition cursor-pointer"
                        >
                            <span>Berapa batas waktu pembayaran setelah memilih slot jam?</span>
                            <svg class="faq-chevron w-4 h-4 text-slate-500 transition-transform duration-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div class="faq-body hidden p-4 text-xs sm:text-sm text-slate-600 leading-relaxed border-t border-slate-200 bg-white">
                            Slot jam yang Anda pilih akan ditahan (*slot lock*) selama 15 menit agar tidak diambil oleh penyewa lain. Jika tidak ada konfirmasi pembayaran dalam rentang waktu tersebut, slot otomatis dilepas kembali ke publik.
                        </div>
                    </div>

                    <!-- FAQ Item 3 -->
                    <div class="border border-slate-200 rounded-2xl overflow-hidden transition">
                        <button
                            type="button"
                            onclick="toggleHelpFaq(this)"
                            class="w-full p-4 text-left font-bold text-xs sm:text-sm text-slate-900 bg-[#FCFBF5] hover:bg-slate-50 flex items-center justify-between gap-3 transition cursor-pointer"
                        >
                            <span>Metode pembayaran apa saja yang didukung?</span>
                            <svg class="faq-chevron w-4 h-4 text-slate-500 transition-transform duration-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div class="faq-body hidden p-4 text-xs sm:text-sm text-slate-600 leading-relaxed border-t border-slate-200 bg-white">
                            Kami mendukung QRIS (bisa di-scan menggunakan GoPay, ShopeePay, OVO, Dana, BCA Mobile, Livin, dll.) serta Virtual Account resmi dari Bank BCA, Mandiri, BNI, dan BRI.
                        </div>
                    </div>

                    <!-- FAQ Item 4 -->
                    <div class="border border-slate-200 rounded-2xl overflow-hidden transition">
                        <button
                            type="button"
                            onclick="toggleHelpFaq(this)"
                            class="w-full p-4 text-left font-bold text-xs sm:text-sm text-slate-900 bg-[#FCFBF5] hover:bg-slate-50 flex items-center justify-between gap-3 transition cursor-pointer"
                        >
                            <span>Apakah bisa membatalkan pesanan atau ganti jadwal?</span>
                            <svg class="faq-chevron w-4 h-4 text-slate-500 transition-transform duration-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div class="faq-body hidden p-4 text-xs sm:text-sm text-slate-600 leading-relaxed border-t border-slate-200 bg-white">
                            Pengajuan *reschedule* atau pembatalan dapat diajukan maksimal H-1 sebelum jadwal bermain dimulai melalui menu Riwayat Booking di akun Anda sesuai dengan ketentuan mitra venue.
                        </div>
                    </div>

                    <!-- Banner Arahan ke Halaman Lengkap /bantuan -->
                    <div class="pt-2">
                        <div class="p-4 rounded-2xl bg-[#EBF5F0] border border-[#BBE3D0] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="min-w-0">
                                <h5 class="text-xs sm:text-sm font-bold text-slate-900">Butuh aturan & kebijakan yang lebih lengkap?</h5>
                                <p class="text-xs text-slate-600 mt-0.5">
                                    Pelajari syarat refund, regulasi cuaca buruk, hingga kemitraan pemilik venue.
                                </p>
                            </div>
                            <a
                                href="{{ route('bantuan') }}"
                                class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl bg-[#0A7660] hover:bg-[#0B604F] text-white text-xs font-bold transition shrink-0 shadow-xs"
                            >
                                <span>Pusat Bantuan Lengkap</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- TAB 3: HUBUNGI CUSTOMER SERVICE (HIERARKI JELAS) --}}
                <div id="help-pane-contact" class="space-y-5 hidden">
                    
                    <!-- WhatsApp Card: OPSI UTAMA (Prominent) -->
                    <div class="p-5 sm:p-6 rounded-2xl bg-white border-2 border-emerald-500/80 shadow-xs space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-[#25D366] text-white flex items-center justify-center shrink-0 shadow-xs">
                                    <!-- Official WhatsApp Icon -->
                                    <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                                        <path d="M12.031 2C6.495 2 2 6.495 2 12.031c0 1.969.569 3.818 1.614 5.385L2 22l4.747-1.55a10.007 10.007 0 005.284 1.581h.005c5.536 0 10.031-4.495 10.031-10.031C22.062 6.495 17.567 2 12.031 2zm0 18.35a8.318 8.318 0 01-4.24-1.152l-.304-.18-3.151 1.028 1.05-3.07-.197-.314A8.32 8.32 0 013.72 12.03c0-4.583 3.729-8.312 8.312-8.312 4.582 0 8.311 3.729 8.311 8.312 0 4.583-3.729 8.32-8.312 8.32zm4.557-6.223c-.25-.125-1.477-.729-1.706-.812-.229-.083-.396-.125-.562.125-.167.25-.646.812-.792.979-.146.167-.292.188-.542.063-.25-.125-1.055-.389-2.01-1.24a7.51 7.51 0 01-1.39-1.729c-.146-.25-.015-.385.11-.51.112-.112.25-.292.375-.438.125-.146.167-.25.25-.417.083-.167.042-.313-.021-.438-.063-.125-.562-1.354-.771-1.854-.204-.488-.411-.422-.562-.43-.146-.007-.313-.009-.479-.009-.167 0-.438.063-.667.313-.229.25-.875.854-.875 2.083 0 1.229.896 2.417 1.021 2.583.125.167 1.762 2.69 4.271 3.771.597.258 1.064.412 1.428.528.6.191 1.146.164 1.578.1.481-.072 1.477-.604 1.685-1.188.209-.583.209-1.083.146-1.188-.062-.104-.229-.167-.479-.292z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-sm sm:text-base font-bold text-slate-900">WhatsApp Customer Care</h4>
                                    <p class="text-xs text-slate-500">Respon cepat setiap hari pukul 08.00 – 22.00 WIB</p>
                                </div>
                            </div>
                        </div>

                        <!-- Topik Cepat (Pre-fill pesan WA) -->
                        <div>
                            <span class="text-xs font-bold text-slate-700 block mb-2">Pilih topik bantuan cepat:</span>
                            <div class="flex flex-wrap gap-2">
                                <button
                                    type="button"
                                    onclick="selectWaTopic('Kendala Pembayaran / Gagal Bayar', this)"
                                    class="wa-topic-chip px-3 py-1.5 rounded-full text-xs font-bold border border-emerald-500 bg-emerald-50 text-[#0A7660] shadow-xs cursor-pointer"
                                >
                                    Pembayaran Gagal
                                </button>
                                <button
                                    type="button"
                                    onclick="selectWaTopic('Permintaan Ubah Jadwal (Reschedule)', this)"
                                    class="wa-topic-chip px-3 py-1.5 rounded-full text-xs font-medium border border-slate-200 bg-white text-slate-700 hover:border-emerald-500 hover:text-[#0A7660] transition cursor-pointer"
                                >
                                    Ubah Jadwal
                                </button>
                                <button
                                    type="button"
                                    onclick="selectWaTopic('Pengajuan Pengembalian Dana (Refund)', this)"
                                    class="wa-topic-chip px-3 py-1.5 rounded-full text-xs font-medium border border-slate-200 bg-white text-slate-700 hover:border-emerald-500 hover:text-[#0A7660] transition cursor-pointer"
                                >
                                    Pengajuan Refund
                                </button>
                                <button
                                    type="button"
                                    onclick="selectWaTopic('Pertanyaan Umum Ketersediaan Lapangan', this)"
                                    class="wa-topic-chip px-3 py-1.5 rounded-full text-xs font-medium border border-slate-200 bg-white text-slate-700 hover:border-emerald-500 hover:text-[#0A7660] transition cursor-pointer"
                                >
                                    Pertanyaan Booking
                                </button>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-2">
                                Siapkan kode booking Anda agar kendala dapat diverifikasi dan ditangani lebih cepat.
                            </p>
                        </div>

                        <!-- Tombol Chat WA -->
                        <a
                            id="wa-contact-link"
                            href="https://wa.me/6282264784108?text=Halo%20SportPlace,%20saya%20butuh%20bantuan%20terkait:%20Kendala%20Pembayaran%20/%20Gagal%20Bayar."
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center justify-center gap-2 w-full py-3 px-4 rounded-xl bg-[#239F50] hover:bg-[#1A7F3F] text-white text-sm font-bold shadow-xs transition cursor-pointer"
                        >
                            <span>Buka Chat WhatsApp Sekarang</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>
                    </div>

                    <!-- Email Card: OPSI SEKUNDER (Outline Style) -->
                    <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-2xs space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <h4 class="text-sm font-bold text-slate-900">Email Pengaduan Resmi</h4>
                                <p class="text-xs text-slate-500 mt-0.5">Untuk korespondensi formal, laporan teknis, dan dokumen pendukung.</p>
                            </div>
                            <a
                                href="mailto:support@sportplace.id?subject=Bantuan%20SportPlace"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl border border-slate-300 hover:border-slate-400 bg-white hover:bg-slate-50 text-slate-800 text-xs sm:text-sm font-bold transition shrink-0"
                            >
                                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <span>Kirim Email</span>
                            </a>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-slate-500 pt-1">
                            <span>Alamat email:</span>
                            <span class="font-mono font-semibold text-slate-800">support@sportplace.id</span>
                            <button
                                type="button"
                                onclick="copySupportEmail()"
                                id="copy-email-tip"
                                class="text-[#0A7660] hover:underline font-semibold ml-1 cursor-pointer"
                            >
                                Salin email
                            </button>
                        </div>
                    </div>

                    <!-- Tautan Pemilik Venue Terpisah -->
                    <div class="pt-2 text-center text-xs text-slate-600">
                        <span>Pemilik venue olahraga? </span>
                        <a href="{{ route('mitra.onboarding.create') }}" class="font-bold text-[#0A7660] hover:underline">
                            Hubungi Tim Kemitraan Venue &rarr;
                        </a>
                    </div>
                </div>

            </div>

            {{-- =========================================================
                 MODAL FOOTER (BERSIH & TUNGGAL)
            ========================================================= --}}
            <div class="px-6 sm:px-8 py-3.5 border-t border-slate-200 bg-[#FCFBF5] flex items-center justify-between text-xs text-slate-500">
                <span>SportPlace Indonesia • Layanan Bantuan Resmi</span>
                <span class="text-[11px] text-slate-400">Tekan Esc atau klik di luar untuk menutup</span>
            </div>

        </div>
    </div>
</div>

{{-- =========================================================================
     MODAL POP-UP SIMULASI BOOKING INTERAKTIF (100% DEMO CLIENT-SIDE)
========================================================================= --}}
<div id="booking-simulation-modal" class="fixed inset-0 z-[10000] hidden overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="sim-modal-title">
    <!-- Backdrop Blur Overlay -->
    <div id="sim-modal-backdrop" class="fixed inset-0 bg-[#06231D]/80 backdrop-blur-sm transition-opacity duration-300 opacity-0"></div>

    <!-- Modal Positioner -->
    <div class="min-h-full flex items-center justify-center p-3 sm:p-5 text-center">
        <!-- Modal Card Container (Lebih Lebar: max-w-4xl) -->
        <div id="sim-modal-card" class="relative w-full max-w-4xl bg-white rounded-3xl border border-slate-200 text-left shadow-2xl transform transition-all duration-300 scale-95 opacity-0 my-8 overflow-hidden flex flex-col max-h-[92vh]">
            
            <!-- Pita Tipis Mode Demo (Kuning Muda Lembut) -->
            <div class="px-6 py-2 bg-amber-50 text-amber-900 border-b border-amber-200/80 text-xs font-medium flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    <span>Mode demo • Tidak ada pemesanan nyata ataupun pemotongan saldo</span>
                </div>
                <button
                    type="button"
                    onclick="closeBookingSimulation()"
                    class="text-amber-800 hover:text-amber-950 font-bold p-1 rounded-md cursor-pointer transition"
                    aria-label="Tutup simulasi"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Stepper Progress Header (Bersatu Tanpa Informasi Ganda) -->
            <div class="px-6 sm:px-8 pt-5 pb-4 bg-white border-b border-slate-200">
                <div class="mb-4">
                    <h2 id="sim-modal-title" class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">
                        Simulasi Alur Reservasi Lapangan
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                        Rasakan alur nyata pemesanan di SportPlace dari awal hingga tiket terbit.
                    </p>
                </div>

                <!-- 4-Steps Indicator with Numbers & Bars -->
                <div class="grid grid-cols-4 gap-2.5 sm:gap-4 pt-1">
                    <!-- Step 1 -->
                    <div id="sim-indicator-1" class="space-y-1.5 text-left">
                        <div class="flex items-center gap-2">
                            <span class="sim-step-num w-6 h-6 rounded-full bg-[#0A7660] text-white text-xs font-bold flex items-center justify-center shrink-0">1</span>
                            <span class="sim-step-label text-xs font-bold text-[#0A7660] truncate">Lapangan</span>
                        </div>
                        <div class="h-1 rounded-full bg-[#0A7660] sim-step-bar transition-colors duration-300"></div>
                    </div>
                    <!-- Step 2 -->
                    <div id="sim-indicator-2" class="space-y-1.5 text-left">
                        <div class="flex items-center gap-2">
                            <span class="sim-step-num w-6 h-6 rounded-full bg-slate-100 text-slate-500 text-xs font-bold flex items-center justify-center shrink-0">2</span>
                            <span class="sim-step-label text-xs font-semibold text-slate-500 truncate">Jam Main</span>
                        </div>
                        <div class="h-1 rounded-full bg-slate-200 sim-step-bar transition-colors duration-300"></div>
                    </div>
                    <!-- Step 3 -->
                    <div id="sim-indicator-3" class="space-y-1.5 text-left">
                        <div class="flex items-center gap-2">
                            <span class="sim-step-num w-6 h-6 rounded-full bg-slate-100 text-slate-500 text-xs font-bold flex items-center justify-center shrink-0">3</span>
                            <span class="sim-step-label text-xs font-semibold text-slate-500 truncate">Bayar</span>
                        </div>
                        <div class="h-1 rounded-full bg-slate-200 sim-step-bar transition-colors duration-300"></div>
                    </div>
                    <!-- Step 4 -->
                    <div id="sim-indicator-4" class="space-y-1.5 text-left">
                        <div class="flex items-center gap-2">
                            <span class="sim-step-num w-6 h-6 rounded-full bg-slate-100 text-slate-500 text-xs font-bold flex items-center justify-center shrink-0">4</span>
                            <span class="sim-step-label text-xs font-semibold text-slate-500 truncate">Tiket</span>
                        </div>
                        <div class="h-1 rounded-full bg-slate-200 sim-step-bar transition-colors duration-300"></div>
                    </div>
                </div>
            </div>

            <!-- Modal Body (Steps Container) -->
            <div class="p-6 sm:p-8 overflow-y-auto space-y-6 flex-1">

                {{-- =========================================================
                     STEP 1: PILIH LAPANGAN (FOTO 16:9, FASILITAS DI DALAM, HARGA MENONJOL)
                ========================================================= --}}
                <div id="sim-step-1" class="space-y-4">
                    <h3 class="text-sm sm:text-base font-bold text-slate-900">
                        Pilih lapangan yang ingin dipesan
                    </h3>

                    <!-- 2 Kartu Lapangan Visual dengan Foto 16:9 -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                        
                        <!-- Court 1: Futsal (Default Terpilih) -->
                        <div
                            id="sim-court-card-1"
                            onclick="selectSimulationCourt(1, 'Arena Futsal Vinyl Super (Indoor)', 150000)"
                            class="sim-court-card rounded-2xl border-2 border-[#0A7660] bg-[#DEF1E7]/25 ring-2 ring-[#0A7660]/20 hover:shadow-md transition cursor-pointer relative overflow-hidden flex flex-col justify-between"
                        >
                            <!-- Foto Lapangan 16:9 -->
                            <div class="relative aspect-video w-full overflow-hidden bg-slate-900">
                                <img
                                    src="{{ asset('images/courts/futsal.jpg') }}"
                                    alt="Arena Futsal Vinyl Super"
                                    class="w-full h-full object-cover"
                                >
                                <span class="absolute top-2.5 left-2.5 px-2.5 py-0.5 rounded-md bg-[#FACC15] text-slate-900 text-[10px] font-extrabold uppercase tracking-wide shadow-xs">
                                    FUTSAL
                                </span>
                                <span class="sim-court-check absolute top-2.5 right-2.5 w-6 h-6 rounded-full bg-[#0A7660] text-white flex items-center justify-center text-xs font-bold shadow-xs">
                                    ✓
                                </span>
                            </div>

                            <!-- Konten Kartu -->
                            <div class="p-4 space-y-2.5 flex-1 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded-md text-[11px] font-medium bg-slate-100 text-slate-600 border border-slate-200/60">
                                            Vinyl Indoor
                                        </span>
                                        <span class="text-xs text-slate-400">• 08:00 - 23:00</span>
                                    </div>
                                    <h4 class="font-bold text-base text-slate-900 mt-1">
                                        Arena Futsal Vinyl Super
                                    </h4>
                                    <p class="text-xs text-slate-500">
                                        Grand Sport Arena • Jakarta Selatan
                                    </p>

                                    <!-- Fasilitas di dalam kartu -->
                                    <div class="flex flex-wrap items-center gap-1.5 pt-2">
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-medium bg-emerald-50 text-[#0A7660] border border-emerald-100">AC Dingin</span>
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-medium bg-emerald-50 text-[#0A7660] border border-emerald-100">Kamar Mandi</span>
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-medium bg-emerald-50 text-[#0A7660] border border-emerald-100">Parkir</span>
                                    </div>
                                </div>

                                <!-- Harga Menonjol -->
                                <div class="pt-3 border-t border-slate-200/80 flex items-baseline justify-between">
                                    <span class="text-[10px] uppercase font-bold text-slate-400">Tarif Sewa</span>
                                    <div>
                                        <span class="text-lg font-black text-[#0A7660]">Rp 150.000</span>
                                        <span class="text-xs text-slate-400 font-normal">/ jam</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Court 2: Badminton (Netral) -->
                        <div
                            id="sim-court-card-2"
                            onclick="selectSimulationCourt(2, 'Badminton Court Karpet BWF (Indoor)', 85000)"
                            class="sim-court-card rounded-2xl border-2 border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50/50 hover:shadow-md transition cursor-pointer relative overflow-hidden flex flex-col justify-between"
                        >
                            <!-- Foto Lapangan 16:9 -->
                            <div class="relative aspect-video w-full overflow-hidden bg-slate-900">
                                <img
                                    src="{{ asset('images/courts/badminton.jpg') }}"
                                    alt="Badminton Court BWF"
                                    class="w-full h-full object-cover"
                                >
                                <span class="absolute top-2.5 left-2.5 px-2.5 py-0.5 rounded-md bg-indigo-600 text-white text-[10px] font-extrabold uppercase tracking-wide shadow-xs">
                                    BADMINTON
                                </span>
                                <span class="sim-court-check hidden absolute top-2.5 right-2.5 w-6 h-6 rounded-full bg-[#0A7660] text-white items-center justify-center text-xs font-bold shadow-xs">
                                    ✓
                                </span>
                            </div>

                            <!-- Konten Kartu -->
                            <div class="p-4 space-y-2.5 flex-1 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded-md text-[11px] font-medium bg-slate-100 text-slate-600 border border-slate-200/60">
                                            Karpet BWF
                                        </span>
                                        <span class="text-xs text-slate-400">• 08:00 - 23:00</span>
                                    </div>
                                    <h4 class="font-bold text-base text-slate-900 mt-1">
                                        Badminton Court BWF
                                    </h4>
                                    <p class="text-xs text-slate-500">
                                        Grand Sport Arena • Jakarta Selatan
                                    </p>

                                    <!-- Fasilitas di dalam kartu -->
                                    <div class="flex flex-wrap items-center gap-1.5 pt-2">
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-medium bg-emerald-50 text-[#0A7660] border border-emerald-100">Karpet BWF</span>
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-medium bg-emerald-50 text-[#0A7660] border border-emerald-100">Loker</span>
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-medium bg-emerald-50 text-[#0A7660] border border-emerald-100">Parkir</span>
                                    </div>
                                </div>

                                <!-- Harga Menonjol -->
                                <div class="pt-3 border-t border-slate-200/80 flex items-baseline justify-between">
                                    <span class="text-[10px] uppercase font-bold text-slate-400">Tarif Sewa</span>
                                    <div>
                                        <span class="text-lg font-black text-[#0A7660]">Rp 85.000</span>
                                        <span class="text-xs text-slate-400 font-normal">/ jam</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- =========================================================
                     STEP 2: PILIH JADWAL & JAM MAIN
                ========================================================= --}}
                <div id="sim-step-2" class="space-y-4 hidden">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm sm:text-base font-bold text-slate-900">
                            Pilih tanggal & slot jam kosong
                        </h3>
                        <span class="text-xs text-emerald-700 font-semibold bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
                            Slot hijau = Kosong
                        </span>
                    </div>

                    <!-- Date Selector Pills -->
                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            onclick="setSimulationDate('Hari Ini, ' + (new Date()).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }), this)"
                            class="sim-date-btn px-4 py-2 rounded-xl text-xs sm:text-sm font-bold bg-[#0A7660] text-white shadow-xs cursor-pointer"
                        >
                            Hari Ini
                        </button>
                        <button
                            type="button"
                            onclick="setSimulationDate('Besok', this)"
                            class="sim-date-btn px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 cursor-pointer"
                        >
                            Besok
                        </button>
                    </div>

                    <!-- Time Slots Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        <!-- Slot 1 (Available) -->
                        <button
                            type="button"
                            onclick="toggleSimulationSlot(this, '16:00 - 17:00')"
                            class="sim-slot-btn p-3.5 rounded-xl border border-emerald-300 bg-emerald-50/60 text-left hover:bg-emerald-100 transition cursor-pointer text-xs sm:text-sm"
                        >
                            <span class="block font-bold text-slate-900">16:00 - 17:00</span>
                            <span class="text-xs text-emerald-700 font-medium">Tersedia</span>
                        </button>

                        <!-- Slot 2 (Booked - Red Disabled) -->
                        <div class="p-3.5 rounded-xl border border-rose-200 bg-rose-50/70 text-left text-xs sm:text-sm opacity-60 cursor-not-allowed">
                            <span class="block font-bold text-slate-500 line-through">17:00 - 18:00</span>
                            <span class="text-xs text-rose-600 font-semibold">Sudah Terisi</span>
                        </div>

                        <!-- Slot 3 (Available) -->
                        <button
                            type="button"
                            onclick="toggleSimulationSlot(this, '18:00 - 19:00')"
                            class="sim-slot-btn p-3.5 rounded-xl border border-emerald-300 bg-emerald-50/60 text-left hover:bg-emerald-100 transition cursor-pointer text-xs sm:text-sm"
                        >
                            <span class="block font-bold text-slate-900">18:00 - 19:00</span>
                            <span class="text-xs text-emerald-700 font-medium">Tersedia</span>
                        </button>

                        <!-- Slot 4 (Selected by default) -->
                        <button
                            type="button"
                            id="sim-default-slot"
                            onclick="toggleSimulationSlot(this, '19:00 - 20:00')"
                            class="sim-slot-btn p-3.5 rounded-xl border-2 border-[#0A7660] bg-[#0A7660] text-white text-left transition cursor-pointer text-xs sm:text-sm shadow-xs"
                        >
                            <div class="flex items-center justify-between">
                                <span class="block font-bold">19:00 - 20:00</span>
                                <span class="text-xs font-bold">✓</span>
                            </div>
                            <span class="text-xs text-emerald-100 font-medium">Terpilih</span>
                        </button>

                        <!-- Slot 5 (Booked - Red Disabled) -->
                        <div class="p-3.5 rounded-xl border border-rose-200 bg-rose-50/70 text-left text-xs sm:text-sm opacity-60 cursor-not-allowed">
                            <span class="block font-bold text-slate-500 line-through">20:00 - 21:00</span>
                            <span class="text-xs text-rose-600 font-semibold">Sudah Terisi</span>
                        </div>

                        <!-- Slot 6 (Available) -->
                        <button
                            type="button"
                            onclick="toggleSimulationSlot(this, '21:00 - 22:00')"
                            class="sim-slot-btn p-3.5 rounded-xl border border-emerald-300 bg-emerald-50/60 text-left hover:bg-emerald-100 transition cursor-pointer text-xs sm:text-sm"
                        >
                            <span class="block font-bold text-slate-900">21:00 - 22:00</span>
                            <span class="text-xs text-emerald-700 font-medium">Tersedia</span>
                        </button>
                    </div>

                    <!-- Live Selection Summary -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                        <div>
                            <span class="text-[11px] text-slate-500 uppercase tracking-wider font-semibold block">Ringkasan Jadwal</span>
                            <span id="sim-selected-slot-text" class="text-sm font-bold text-slate-900">19:00 - 20:00 WIB (1 Jam)</span>
                        </div>
                        <div class="text-right">
                            <span class="text-[11px] text-slate-500 uppercase tracking-wider font-semibold block">Subtotal</span>
                            <span id="sim-subtotal-text" class="text-base font-bold text-[#0A7660]">Rp 150.000</span>
                        </div>
                    </div>
                </div>

                {{-- =========================================================
                     STEP 3: REVIEW & PEMBAYARAN SIMULASI
                ========================================================= --}}
                <div id="sim-step-3" class="space-y-4 hidden">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm sm:text-base font-bold text-slate-900">
                            Review pesanan & metode pembayaran
                        </h3>
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 border border-amber-200 text-amber-800 text-xs font-bold">
                            <span>Slot Terkunci: 14:59</span>
                        </div>
                    </div>

                    <!-- Order Summary Box -->
                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-3 text-xs sm:text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Unit Lapangan:</span>
                            <span id="sim-review-court" class="font-bold text-slate-900">Arena Futsal Vinyl Super</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Jadwal Main:</span>
                            <span id="sim-review-schedule" class="font-bold text-slate-900">Hari Ini • 19:00 - 20:00 WIB</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Nama Pemesan:</span>
                            <span class="font-bold text-slate-900">{{ auth()->user()->name ?? 'Pengunjung Demo (Tamu)' }}</span>
                        </div>
                        <div class="pt-3 border-t border-slate-200 space-y-1.5">
                            <div class="flex items-center justify-between text-slate-600">
                                <span>Tarif Sewa (1 Jam):</span>
                                <span id="sim-cost-court" class="font-medium text-slate-900">Rp 150.000</span>
                            </div>
                            <div class="flex items-center justify-between text-slate-600">
                                <span>Biaya Layanan Platform:</span>
                                <span class="font-medium text-slate-900">Rp 2.500</span>
                            </div>
                            <div class="flex items-center justify-between pt-2 border-t border-dashed border-slate-300 text-base font-bold text-slate-900">
                                <span>Total Tagihan:</span>
                                <span id="sim-cost-total" class="text-[#0A7660]">Rp 152.500</span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method Demo Radio -->
                    <div class="space-y-2">
                        <span class="text-xs font-bold text-slate-700 block">Pilihan Pembayaran Instan:</span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label class="p-3.5 rounded-xl border-2 border-[#0A7660] bg-[#DEF1E7]/30 flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="sim_payment_method" value="qris" checked class="text-[#0A7660] focus:ring-[#0A7660]">
                                <div class="text-xs sm:text-sm">
                                    <span class="font-bold text-slate-900 block">QRIS Instan (Semua E-Wallet)</span>
                                    <span class="text-xs text-slate-500">GoPay, ShopeePay, OVO, BCA Mobile</span>
                                </div>
                            </label>

                            <label class="p-3.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="sim_payment_method" value="va" class="text-[#0A7660] focus:ring-[#0A7660]">
                                <div class="text-xs sm:text-sm">
                                    <span class="font-bold text-slate-900 block">Virtual Account Bank</span>
                                    <span class="text-xs text-slate-500">BCA, Mandiri, BNI, BRI</span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- =========================================================
                     STEP 4: TIKET DIGITAL SIMULASI TERBIT!
                ========================================================= --}}
                <div id="sim-step-4" class="space-y-6 hidden text-center">
                    <div>
                        <span class="inline-block px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-[#0A7660] text-xs font-bold uppercase tracking-wider mb-2">
                            Pembayaran Berhasil
                        </span>
                        <h4 class="text-xl sm:text-2xl font-bold text-slate-900">
                            Tiket Digital Resmi Telah Terbit
                        </h4>
                        <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto mt-1">
                            Inilah bentuk tiket digital yang Anda terima saat melakukan pemesanan nyata di SportPlace.
                        </p>
                    </div>

                    <!-- E-Ticket Card -->
                    <div class="max-w-md mx-auto rounded-3xl border border-slate-200 bg-white shadow-lg overflow-hidden text-left relative">
                        <!-- Ticket Header -->
                        <div class="p-4 bg-[#06231D] text-white flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-[#0B7B69] flex items-center justify-center text-white font-bold text-xs">
                                    SP
                                </div>
                                <span class="font-bold text-sm tracking-tight">SportPlace E-Ticket</span>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full bg-[#239F50] text-white text-[10px] font-bold uppercase tracking-wider">
                                Lunas (Simulasi)
                            </span>
                        </div>

                        <!-- Ticket Body -->
                        <div class="p-5 space-y-4">
                            <div>
                                <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider block">Unit Lapangan</span>
                                <h5 id="sim-ticket-court" class="font-bold text-sm sm:text-base text-slate-900">Arena Futsal Vinyl Super</h5>
                                <p class="text-xs text-slate-500">Grand Sport Arena • Jakarta Selatan</p>
                            </div>

                            <div class="grid grid-cols-2 gap-3 text-xs pt-3 border-t border-dashed border-slate-200">
                                <div>
                                    <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider block">Waktu Main</span>
                                    <span id="sim-ticket-time" class="font-bold text-slate-900">19:00 - 20:00 WIB</span>
                                </div>
                                <div>
                                    <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider block">Kode Booking</span>
                                    <span class="font-bold text-[#0A7660] font-mono">#SP-SIM-2026-X88</span>
                                </div>
                            </div>

                            <!-- QR Code Area -->
                            <div class="pt-3 border-t border-dashed border-slate-200 flex items-center justify-between gap-4">
                                <div>
                                    <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider block">Check-in Venue</span>
                                    <p class="text-xs text-slate-500 mt-0.5 leading-tight">Tunjukkan QR code ini ke petugas venue saat kedatangan.</p>
                                </div>
                                <div class="w-16 h-16 bg-slate-50 border border-slate-200 rounded-xl p-1.5 flex items-center justify-center shrink-0">
                                    <svg class="w-full h-full text-slate-900" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M2 2h8v8H2V2zm2 2v4h4V4H4zm10-2h8v8h-8V2zm2 2v4h4V4h-4zM2 14h8v8H2v-8zm2 2v4h4v-4H4zm10 0h2v2h-2v-2zm4 0h4v2h-4v-2zm0 4h2v2h-2v-2zm-4 2h2v2h-2v-2zm6 0h2v2h-2v-2zM6 6h2v2H6V6zm12 0h2v2h-2V6zM6 18h2v2H6v-2z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Ticket Footer Ribbon -->
                        <div class="px-5 py-2.5 bg-slate-50 border-t border-slate-200 flex items-center justify-between text-xs text-slate-500">
                            <span>Status: Konfirmasi Instan</span>
                            <span class="font-bold text-[#0A7660]">SportPlace Official Demo</span>
                        </div>
                    </div>

                    <!-- What happens next -->
                    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-left space-y-1.5 text-xs text-emerald-900 max-w-md mx-auto">
                        <p class="font-bold text-emerald-800">Informasi Alur Pemesanan Asli:</p>
                        <p class="leading-relaxed">
                            1. Slot jam yang Anda bayar otomatis menjadi merah bagi pengguna lain (anti *double booking*).<br>
                            2. Tiket ini langsung tersimpan otomatis di menu <strong>Riwayat Booking</strong> di profil Anda.<br>
                            3. Anda cukup datang ke venue saat jam main dimulai!
                        </p>
                    </div>
                </div>

            </div>

            {{-- =========================================================
                 MODAL ACTION FOOTER (SATU AKSI UTAMA SOLID, SATU KEMBALI TEKS)
            ========================================================= --}}
            <div class="px-6 sm:px-8 py-4 border-t border-slate-200 bg-[#FCFBF5] flex items-center justify-between gap-3">
                <!-- Back Button (Step 2 & 3) -->
                <button
                    type="button"
                    id="sim-back-btn"
                    onclick="prevSimulationStep()"
                    class="hidden text-xs sm:text-sm font-semibold text-slate-500 hover:text-slate-900 transition cursor-pointer"
                >
                    &larr; Kembali
                </button>

                <!-- Next / Proceed Button (Satu-satunya tombol solid) -->
                <button
                    type="button"
                    id="sim-next-btn"
                    onclick="nextSimulationStep()"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-[#0A7660] hover:bg-[#0B604F] text-white text-xs sm:text-sm font-bold transition shadow-xs cursor-pointer ml-auto"
                >
                    <span>Lanjut ke Jam Main</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </button>

                <!-- Finish / Action Buttons for Step 4 -->
                <div id="sim-finish-actions" class="hidden flex-wrap items-center gap-3 ml-auto">
                    <button
                        type="button"
                        onclick="resetBookingSimulation()"
                        class="px-4 py-2 rounded-xl bg-white border border-slate-300 hover:bg-slate-50 text-slate-800 text-xs sm:text-sm font-semibold transition cursor-pointer"
                    >
                        Coba Lagi
                    </button>
                    <a
                        href="{{ route('penyewa.lapangan') }}"
                        class="inline-flex items-center gap-1.5 px-5 py-2 rounded-xl bg-[#0A7660] hover:bg-[#0B604F] text-white text-xs sm:text-sm font-bold transition shadow-xs"
                    >
                        <span>Cari Lapangan Asli</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- =========================================================================
     JAVASCRIPT LOGIC UNTUK INTERAKSI MODAL & SIMULASI
========================================================================= --}}
<script>
    // State Simulasi Booking
    window.simState = {
        step: 1,
        courtId: 1,
        courtName: 'Arena Futsal Vinyl Super (Indoor)',
        courtPrice: 150000,
        dateText: 'Hari Ini',
        slotTime: '19:00 - 20:00',
    };

    // Buka Simulasi dari Tombol Tutorial
    window.startBookingSimulation = function () {
        if (typeof window.closeHelpModal === 'function') {
            window.closeHelpModal();
        }
        setTimeout(() => {
            window.openBookingSimulation();
        }, 220);
    };

    // Buka Jendela Simulasi
    window.openBookingSimulation = function () {
        const modal = document.getElementById('booking-simulation-modal');
        const backdrop = document.getElementById('sim-modal-backdrop');
        const card = document.getElementById('sim-modal-card');
        if (!modal) return;

        window.resetBookingSimulation();

        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');

        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            backdrop.classList.add('opacity-100');
            card.classList.remove('scale-95', 'opacity-0');
            card.classList.add('scale-100', 'opacity-100');
        }, 10);
    };

    // Tutup Jendela Simulasi
    window.closeBookingSimulation = function () {
        const modal = document.getElementById('booking-simulation-modal');
        const backdrop = document.getElementById('sim-modal-backdrop');
        const card = document.getElementById('sim-modal-card');
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

    // Set Step Simulasi (1..4)
    window.setSimulationStep = function (stepNumber) {
        window.simState.step = stepNumber;

        // Hide all steps & update indicators
        for (let i = 1; i <= 4; i++) {
            const stepEl = document.getElementById('sim-step-' + i);
            const indicatorEl = document.getElementById('sim-indicator-' + i);
            if (stepEl) stepEl.classList.add('hidden');
            if (indicatorEl) {
                const num = indicatorEl.querySelector('.sim-step-num');
                const label = indicatorEl.querySelector('.sim-step-label');
                const bar = indicatorEl.querySelector('.sim-step-bar');

                if (i <= stepNumber) {
                    if (num) {
                        num.className = 'sim-step-num w-6 h-6 rounded-full bg-[#0A7660] text-white text-xs font-bold flex items-center justify-center shrink-0';
                    }
                    if (label) {
                        label.className = 'sim-step-label text-xs font-bold text-[#0A7660] truncate';
                    }
                    if (bar) {
                        bar.className = 'h-1 rounded-full bg-[#0A7660] sim-step-bar transition-colors duration-300';
                    }
                } else {
                    if (num) {
                        num.className = 'sim-step-num w-6 h-6 rounded-full bg-slate-100 text-slate-500 text-xs font-bold flex items-center justify-center shrink-0';
                    }
                    if (label) {
                        label.className = 'sim-step-label text-xs font-semibold text-slate-500 truncate';
                    }
                    if (bar) {
                        bar.className = 'h-1 rounded-full bg-slate-200 sim-step-bar transition-colors duration-300';
                    }
                }
            }
        }

        const activeStepEl = document.getElementById('sim-step-' + stepNumber);
        if (activeStepEl) activeStepEl.classList.remove('hidden');

        // Update buttons state
        const backBtn = document.getElementById('sim-back-btn');
        const nextBtn = document.getElementById('sim-next-btn');
        const finishActions = document.getElementById('sim-finish-actions');

        if (stepNumber === 1) {
            if (backBtn) backBtn.classList.add('hidden');
            if (nextBtn) {
                nextBtn.classList.remove('hidden');
                nextBtn.innerHTML = '<span>Lanjut ke Jam Main</span><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>';
            }
            if (finishActions) finishActions.classList.add('hidden');
        } else if (stepNumber === 2) {
            if (backBtn) backBtn.classList.remove('hidden');
            if (nextBtn) {
                nextBtn.classList.remove('hidden');
                nextBtn.innerHTML = '<span>Lanjut ke Pembayaran</span><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>';
            }
            if (finishActions) finishActions.classList.add('hidden');
        } else if (stepNumber === 3) {
            if (backBtn) backBtn.classList.remove('hidden');
            if (nextBtn) {
                nextBtn.classList.remove('hidden');
                nextBtn.innerHTML = '<span>Bayar Sekarang (Simulasi)</span><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
            }
            if (finishActions) finishActions.classList.add('hidden');

            // Update step 3 texts
            const revCourt = document.getElementById('sim-review-court');
            const revSched = document.getElementById('sim-review-schedule');
            const costCourt = document.getElementById('sim-cost-court');
            const costTotal = document.getElementById('sim-cost-total');
            if (revCourt) revCourt.textContent = window.simState.courtName;
            if (revSched) revSched.textContent = window.simState.dateText + ' • ' + window.simState.slotTime + ' WIB';
            if (costCourt) costCourt.textContent = 'Rp ' + window.simState.courtPrice.toLocaleString('id-ID');
            if (costTotal) costTotal.textContent = 'Rp ' + (window.simState.courtPrice + 2500).toLocaleString('id-ID');
        } else if (stepNumber === 4) {
            if (backBtn) backBtn.classList.add('hidden');
            if (nextBtn) nextBtn.classList.add('hidden');
            if (finishActions) finishActions.classList.remove('hidden');

            // Update step 4 ticket details
            const tickCourt = document.getElementById('sim-ticket-court');
            const tickTime = document.getElementById('sim-ticket-time');
            if (tickCourt) tickCourt.textContent = window.simState.courtName;
            if (tickTime) tickTime.textContent = window.simState.dateText + ', ' + window.simState.slotTime + ' WIB';
        }
    };

    window.nextSimulationStep = function () {
        if (window.simState.step === 3) {
            const nextBtn = document.getElementById('sim-next-btn');
            if (nextBtn) {
                nextBtn.disabled = true;
                nextBtn.innerHTML = '<span class="inline-block animate-spin mr-1">⌛</span> Memproses Tiket...';
            }
            setTimeout(() => {
                if (nextBtn) nextBtn.disabled = false;
                window.setSimulationStep(4);
            }, 500);
        } else if (window.simState.step < 4) {
            window.setSimulationStep(window.simState.step + 1);
        }
    };

    window.prevSimulationStep = function () {
        if (window.simState.step > 1) {
            window.setSimulationStep(window.simState.step - 1);
        }
    };

    // Reset simulasi ke kondisi awal
    window.resetBookingSimulation = function () {
        window.simState = {
            step: 1,
            courtId: 1,
            courtName: 'Arena Futsal Vinyl Super (Indoor)',
            courtPrice: 150000,
            dateText: 'Hari Ini',
            slotTime: '19:00 - 20:00',
        };
        window.selectSimulationCourt(1, 'Arena Futsal Vinyl Super (Indoor)', 150000);
        window.setSimulationStep(1);
    };

    // Pilih Lapangan pada Step 1 (Visual Foto & Checkmark)
    window.selectSimulationCourt = function (id, name, price) {
        window.simState.courtId = id;
        window.simState.courtName = name;
        window.simState.courtPrice = price;

        for (let i = 1; i <= 2; i++) {
            const card = document.getElementById('sim-court-card-' + i);
            if (!card) continue;
            const check = card.querySelector('.sim-court-check');
            if (i === id) {
                card.className = 'sim-court-card rounded-2xl border-2 border-[#0A7660] bg-[#DEF1E7]/25 ring-2 ring-[#0A7660]/20 hover:shadow-md transition cursor-pointer relative overflow-hidden flex flex-col justify-between';
                if (check) check.classList.remove('hidden');
            } else {
                card.className = 'sim-court-card rounded-2xl border-2 border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50/50 hover:shadow-md transition cursor-pointer relative overflow-hidden flex flex-col justify-between';
                if (check) check.classList.add('hidden');
            }
        }

        // Update subtotal
        const subtotal = document.getElementById('sim-subtotal-text');
        if (subtotal) subtotal.textContent = 'Rp ' + price.toLocaleString('id-ID');
    };

    // Ganti Tanggal pada Step 2
    window.setSimulationDate = function (dateLabel, btn) {
        window.simState.dateText = dateLabel;
        document.querySelectorAll('.sim-date-btn').forEach(b => {
            b.className = 'sim-date-btn px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 cursor-pointer';
        });
        if (btn) {
            btn.className = 'sim-date-btn px-4 py-2 rounded-xl text-xs sm:text-sm font-bold bg-[#0A7660] text-white shadow-xs cursor-pointer';
        }
    };

    // Toggle Slot Jam pada Step 2
    window.toggleSimulationSlot = function (btn, timeRange) {
        window.simState.slotTime = timeRange;
        document.querySelectorAll('.sim-slot-btn').forEach(b => {
            b.className = 'sim-slot-btn p-3.5 rounded-xl border border-emerald-300 bg-emerald-50/60 text-left hover:bg-emerald-100 transition cursor-pointer text-xs sm:text-sm';
            const check = b.querySelector('.sim-slot-check');
            if (check) check.remove();
        });

        btn.className = 'sim-slot-btn p-3.5 rounded-xl border-2 border-[#0A7660] bg-[#0A7660] text-white text-left transition cursor-pointer text-xs sm:text-sm shadow-xs';
        const spanText = document.getElementById('sim-selected-slot-text');
        if (spanText) spanText.textContent = timeRange + ' WIB (1 Jam)';
    };

    // Setup Event Listeners Simulasi
    function setupSimulationListeners() {
        const backdrop = document.getElementById('sim-modal-backdrop');
        if (backdrop && !backdrop.dataset.listenerBound) {
            backdrop.dataset.listenerBound = 'true';
            backdrop.addEventListener('click', closeBookingSimulation);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupSimulationListeners);
    } else {
        setupSimulationListeners();
    }

    // Escape listener untuk kedua modal
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            const simModal = document.getElementById('booking-simulation-modal');
            if (simModal && !simModal.classList.contains('hidden')) {
                closeBookingSimulation();
            }
        }
    });

    // =========================================================
    // MODAL BANTUAN DASAR
    // =========================================================

    // Buka Modal Bantuan
    window.openHelpModal = function (defaultTab = 'tutorial') {
        const modal = document.getElementById('help-modal');
        const backdrop = document.getElementById('help-modal-backdrop');
        const card = document.getElementById('help-modal-card');
        if (!modal) return;

        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');

        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            backdrop.classList.add('opacity-100');
            card.classList.remove('scale-95', 'opacity-0');
            card.classList.add('scale-100', 'opacity-100');
        }, 10);

        if (defaultTab) {
            switchHelpTab(defaultTab);
        }
    };

    // Tutup Modal Bantuan
    window.closeHelpModal = function () {
        const modal = document.getElementById('help-modal');
        const backdrop = document.getElementById('help-modal-backdrop');
        const card = document.getElementById('help-modal-card');
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

    // Pindah Tab di dalam Modal (Segmented Control)
    window.switchHelpTab = function (tabName) {
        const tabs = ['tutorial', 'faq', 'contact'];
        tabs.forEach(t => {
            const pane = document.getElementById('help-pane-' + t);
            const btn = document.getElementById('tab-btn-' + t);
            if (pane) pane.classList.add('hidden');
            if (btn) {
                btn.className = 'tab-help-btn w-full py-2.5 px-3 rounded-xl text-xs sm:text-sm font-semibold transition flex items-center justify-center text-center text-slate-600 hover:text-slate-900 cursor-pointer whitespace-nowrap';
            }
        });

        const activePane = document.getElementById('help-pane-' + tabName);
        const activeBtn = document.getElementById('tab-btn-' + tabName);
        if (activePane) activePane.classList.remove('hidden');
        if (activeBtn) {
            activeBtn.className = 'tab-help-btn w-full py-2.5 px-3 rounded-xl text-xs sm:text-sm font-bold transition flex items-center justify-center text-center bg-white text-[#0A7660] shadow-xs cursor-pointer whitespace-nowrap';
        }
    };

    // Akordeon FAQ Toggle
    window.toggleHelpFaq = function (btn) {
        const body = btn.nextElementSibling;
        const chevron = btn.querySelector('.faq-chevron');
        if (!body) return;

        const isHidden = body.classList.contains('hidden');
        document.querySelectorAll('.faq-body').forEach(b => b.classList.add('hidden'));
        document.querySelectorAll('.faq-chevron').forEach(c => c.classList.remove('rotate-180'));

        if (isHidden) {
            body.classList.remove('hidden');
            if (chevron) chevron.classList.add('rotate-180');
        }
    };

    // Pilih Topik Cepat WhatsApp
    window.selectWaTopic = function (topic, el) {
        document.querySelectorAll('.wa-topic-chip').forEach(c => {
            c.className = 'wa-topic-chip px-3 py-1.5 rounded-full text-xs font-medium border border-slate-200 bg-white text-slate-700 hover:border-emerald-500 hover:text-[#0A7660] transition cursor-pointer';
        });
        el.className = 'wa-topic-chip px-3 py-1.5 rounded-full text-xs font-bold border border-emerald-500 bg-emerald-50 text-[#0A7660] shadow-xs cursor-pointer';
        
        const waLink = document.getElementById('wa-contact-link');
        if (waLink) {
            const msg = encodeURIComponent('Halo SportPlace, saya butuh bantuan terkait: ' + topic + '.');
            waLink.href = 'https://wa.me/6282264784108?text=' + msg;
        }
    };

    // Salin Email CS
    window.copySupportEmail = function () {
        navigator.clipboard.writeText('support@sportplace.id').then(() => {
            const tip = document.getElementById('copy-email-tip');
            if (tip) {
                tip.textContent = 'Email tersalin!';
                setTimeout(() => { tip.textContent = 'Salin email'; }, 2000);
            }
        });
    };

    // Setup Backdrop Listener
    function setupHelpModal() {
        const backdrop = document.getElementById('help-modal-backdrop');
        if (backdrop && !backdrop.dataset.listenerBound) {
            backdrop.dataset.listenerBound = 'true';
            backdrop.addEventListener('click', closeHelpModal);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupHelpModal);
    } else {
        setupHelpModal();
    }

    // Keyboard listener Escape
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('help-modal');
            if (modal && !modal.classList.contains('hidden')) {
                closeHelpModal();
            }
        }
    });

    // Event listener delegasi untuk data-open-help-modal
    document.addEventListener('click', function (e) {
        const trigger = e.target.closest('[data-open-help-modal]');
        if (trigger) {
            e.preventDefault();
            const tab = trigger.getAttribute('data-open-help-modal') || 'tutorial';
            if (typeof window.openHelpModal === 'function') {
                window.openHelpModal(tab);
            }
        }
    });
</script>
