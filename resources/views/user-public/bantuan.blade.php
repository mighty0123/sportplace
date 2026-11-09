<x-layouts.public title="Pusat Bantuan & FAQ — SportPlace Indonesia">

    {{-- =========================================================
         1. HERO SECTION: PENCARIAN PUSAT BANTUAN
    ========================================================= --}}
    <section class="relative bg-[#06231D] text-white pt-14 pb-18 lg:pt-20 lg:pb-24 overflow-hidden border-b border-[#0C342C]">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -top-24 left-1/2 -translate-x-1/2 w-[700px] h-[400px] bg-gradient-to-b from-[#0A7660]/30 via-[#0C4E40]/20 to-transparent blur-3xl rounded-full"></div>
            <div class="absolute bottom-0 right-10 w-80 h-80 bg-[#DEEF25]/5 blur-3xl rounded-full"></div>
        </div>

        <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-5">
            <!-- Badge -->
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#0C4E40]/80 border border-[#43E5C5]/30 text-emerald-200 text-xs font-semibold backdrop-blur-sm">
                <span class="w-2 h-2 rounded-full bg-[#DEEF25] animate-pulse"></span>
                <span>Pusat Bantuan & Tanya Jawab Resmi</span>
            </div>

            <!-- Title -->
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight text-white leading-tight">
                Bagaimana Kami Bisa Membantu Anda?
            </h1>
            <p class="text-sm sm:text-base text-emerald-100/80 max-w-xl mx-auto leading-relaxed">
                Temukan panduan pemesanan lapangan, ketentuan pembayaran, regulasi refund, dan informasi kemitraan.
            </p>

            <!-- Search Bar FAQ -->
            <div class="pt-2 max-w-2xl mx-auto">
                <div class="relative flex items-center bg-[#082B24] border-2 border-emerald-600/40 focus-within:border-[#DEEF25] rounded-2xl shadow-lg transition-all duration-200 overflow-hidden">
                    <div class="pl-4 text-emerald-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input
                        id="faq-search-input"
                        type="text"
                        oninput="filterFaq()"
                        placeholder="Ketik kata kunci pertanyaan (misal: refund, QRIS, jadwal, mitra)..."
                        class="w-full py-4 px-3 bg-transparent text-sm sm:text-base text-white placeholder-emerald-400/60 outline-none"
                    >
                    <button
                        type="button"
                        onclick="clearFaqSearch()"
                        id="faq-clear-btn"
                        class="hidden pr-4 text-emerald-400 hover:text-white transition"
                        title="Hapus pencarian"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Quick Interactive Demo Simulation Trigger Banner -->
                <div class="mt-4 flex flex-col sm:flex-row items-center justify-between gap-3 p-3.5 rounded-2xl bg-[#082B24]/90 border border-emerald-600/30 text-left">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-[#DEEF25]/20 text-[#DEEF25] flex items-center justify-center shrink-0 text-sm">🎮</span>
                        <div>
                            <p class="text-xs font-bold text-white">Baru pertama kali menggunakan SportPlace?</p>
                            <p class="text-[11px] text-emerald-200/80">Coba simulasi interaktif alur sewa lapangan tanpa mengubah database.</p>
                        </div>
                    </div>
                    <button
                        type="button"
                        onclick="openBookingSimulation()"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl bg-[#DEEF25] hover:bg-[#cde01e] text-[#06231D] text-xs font-bold transition shadow-xs shrink-0 cursor-pointer"
                    >
                        <span>Mulai Simulasi</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </section>

    {{-- =========================================================
         2. FAQ CONTENT SECTION WITH CATEGORY FILTER
    ========================================================= --}}
    <section class="py-12 sm:py-16 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Category Filter Pills -->
        <div class="flex items-center gap-2 overflow-x-auto pb-4 mb-8 no-scrollbar">
            <button type="button" onclick="selectFaqCategory('all', this)" class="faq-filter-btn px-4 py-2 rounded-full text-xs sm:text-sm font-bold bg-[#0A7660] text-white shadow-xs transition shrink-0 cursor-pointer">
                Semua Pertanyaan
            </button>
            <button type="button" onclick="selectFaqCategory('booking', this)" class="faq-filter-btn px-4 py-2 rounded-full text-xs sm:text-sm font-semibold bg-white border border-[#E1E3D9] text-[#5A6C5B] hover:text-[#1B2724] hover:bg-[#F4F7F5] transition shrink-0 cursor-pointer">
                Pemesanan & Jadwal
            </button>
            <button type="button" onclick="selectFaqCategory('payment', this)" class="faq-filter-btn px-4 py-2 rounded-full text-xs sm:text-sm font-semibold bg-white border border-[#E1E3D9] text-[#5A6C5B] hover:text-[#1B2724] hover:bg-[#F4F7F5] transition shrink-0 cursor-pointer">
                Pembayaran & Tiket
            </button>
            <button type="button" onclick="selectFaqCategory('refund', this)" class="faq-filter-btn px-4 py-2 rounded-full text-xs sm:text-sm font-semibold bg-white border border-[#E1E3D9] text-[#5A6C5B] hover:text-[#1B2724] hover:bg-[#F4F7F5] transition shrink-0 cursor-pointer">
                Pembatalan & Refund
            </button>
            <button type="button" onclick="selectFaqCategory('mitra', this)" class="faq-filter-btn px-4 py-2 rounded-full text-xs sm:text-sm font-semibold bg-white border border-[#E1E3D9] text-[#5A6C5B] hover:text-[#1B2724] hover:bg-[#F4F7F5] transition shrink-0 cursor-pointer">
                Kemitraan Venue
            </button>
            <button type="button" onclick="selectFaqCategory('account', this)" class="faq-filter-btn px-4 py-2 rounded-full text-xs sm:text-sm font-semibold bg-white border border-[#E1E3D9] text-[#5A6C5B] hover:text-[#1B2724] hover:bg-[#F4F7F5] transition shrink-0 cursor-pointer">
                Akun & Keamanan
            </button>
        </div>

        <!-- FAQ Items Accordion Container -->
        <div id="faq-list" class="space-y-4">

            {{-- CATEGORY: BOOKING --}}
            <div class="faq-item border border-[#E1E3D9] rounded-2xl overflow-hidden bg-white shadow-2xs transition" data-category="booking">
                <button type="button" onclick="toggleFaqAccordion(this)" class="w-full p-5 sm:p-6 text-left font-bold text-sm sm:text-base text-[#1B2724] bg-white hover:bg-[#FCFBF5] flex items-center justify-between gap-4 transition cursor-pointer">
                    <span>Bagaimana cara memesan jadwal lapangan di SportPlace?</span>
                    <svg class="faq-chevron w-5 h-5 text-[#5A6C5B] transition-transform duration-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="faq-content hidden px-5 sm:px-6 pb-6 pt-1 text-xs sm:text-sm text-[#5A6C5B] leading-relaxed border-t border-slate-100 bg-[#FCFBF5]/60">
                    Pilih cabang olahraga dan kota di halaman pencarian. Buka detail lapangan untuk melihat fasilitas dan jam operasional, lalu klik <strong>"Pilih Jadwal di Kalender"</strong>. Pilih slot jam hijau yang tersedia, login/daftar akun, dan selesaikan pembayaran. Tiket digital QR otomatis terbit di akun Anda.
                </div>
            </div>

            <div class="faq-item border border-[#E1E3D9] rounded-2xl overflow-hidden bg-white shadow-2xs transition" data-category="booking">
                <button type="button" onclick="toggleFaqAccordion(this)" class="w-full p-5 sm:p-6 text-left font-bold text-sm sm:text-base text-[#1B2724] bg-white hover:bg-[#FCFBF5] flex items-center justify-between gap-4 transition cursor-pointer">
                    <span>Apakah saya bisa memesan lapangan untuk beberapa hari ke depan?</span>
                    <svg class="faq-chevron w-5 h-5 text-[#5A6C5B] transition-transform duration-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="faq-content hidden px-5 sm:px-6 pb-6 pt-1 text-xs sm:text-sm text-[#5A6C5B] leading-relaxed border-t border-slate-100 bg-[#FCFBF5]/60">
                    Ya, sistem kalender SportPlace memungkinkan Anda memesan jadwal bermain mulai dari hari yang sama hingga 10–30 hari ke depan, tergantung kebijakan slot yang dibuka oleh masing-masing mitra arena.
                </div>
            </div>

            <div class="faq-item border border-[#E1E3D9] rounded-2xl overflow-hidden bg-white shadow-2xs transition" data-category="booking">
                <button type="button" onclick="toggleFaqAccordion(this)" class="w-full p-5 sm:p-6 text-left font-bold text-sm sm:text-base text-[#1B2724] bg-white hover:bg-[#FCFBF5] flex items-center justify-between gap-4 transition cursor-pointer">
                    <span>Apakah jadwal yang tertera sudah pasti kosong (real-time)?</span>
                    <svg class="faq-chevron w-5 h-5 text-[#5A6C5B] transition-transform duration-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="faq-content hidden px-5 sm:px-6 pb-6 pt-1 text-xs sm:text-sm text-[#5A6C5B] leading-relaxed border-t border-slate-100 bg-[#FCFBF5]/60">
                    Benar. Jadwal di SportPlace tersinkronisasi langsung dengan sistem reservasi kasir pengelola arena. Slot jam hijau dijamin kosong dan tidak ada risiko bentrok jadwal (*double-booking*).
                </div>
            </div>

            {{-- CATEGORY: PAYMENT --}}
            <div class="faq-item border border-[#E1E3D9] rounded-2xl overflow-hidden bg-white shadow-2xs transition" data-category="payment">
                <button type="button" onclick="toggleFaqAccordion(this)" class="w-full p-5 sm:p-6 text-left font-bold text-sm sm:text-base text-[#1B2724] bg-white hover:bg-[#FCFBF5] flex items-center justify-between gap-4 transition cursor-pointer">
                    <span>Metode pembayaran apa saja yang tersedia di SportPlace?</span>
                    <svg class="faq-chevron w-5 h-5 text-[#5A6C5B] transition-transform duration-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="faq-content hidden px-5 sm:px-6 pb-6 pt-1 text-xs sm:text-sm text-[#5A6C5B] leading-relaxed border-t border-slate-100 bg-[#FCFBF5]/60">
                    Kami mendukung:
                    <ul class="list-disc pl-5 mt-2 space-y-1">
                        <li><strong>QRIS Nasional:</strong> GoPay, OVO, Dana, LinkAja, ShopeePay, serta seluruh aplikasi m-Banking.</li>
                        <li><strong>Virtual Account Otomatis:</strong> Bank BCA, Mandiri, BNI, dan BRI dengan verifikasi otomatis 24 jam.</li>
                    </ul>
                </div>
            </div>

            <div class="faq-item border border-[#E1E3D9] rounded-2xl overflow-hidden bg-white shadow-2xs transition" data-category="payment">
                <button type="button" onclick="toggleFaqAccordion(this)" class="w-full p-5 sm:p-6 text-left font-bold text-sm sm:text-base text-[#1B2724] bg-white hover:bg-[#FCFBF5] flex items-center justify-between gap-4 transition cursor-pointer">
                    <span>Berapa batas waktu pembayaran setelah memilih jam main?</span>
                    <svg class="faq-chevron w-5 h-5 text-[#5A6C5B] transition-transform duration-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="faq-content hidden px-5 sm:px-6 pb-6 pt-1 text-xs sm:text-sm text-[#5A6C5B] leading-relaxed border-t border-slate-100 bg-[#FCFBF5]/60">
                    Slot jam yang Anda pilih akan ditahan (*hold slot*) selama <strong>15 menit</strong>. Anda harus menyelesaikan transfer sebelum hitung mundur selesai. Jika kedaluwarsa, slot tersebut otomatis terbuka kembali untuk penyewa lain.
                </div>
            </div>

            <div class="faq-item border border-[#E1E3D9] rounded-2xl overflow-hidden bg-white shadow-2xs transition" data-category="payment">
                <button type="button" onclick="toggleFaqAccordion(this)" class="w-full p-5 sm:p-6 text-left font-bold text-sm sm:text-base text-[#1B2724] bg-white hover:bg-[#FCFBF5] flex items-center justify-between gap-4 transition cursor-pointer">
                    <span>Saldo sudah terpotong namun tiket belum terbit, apa yang harus dilakukan?</span>
                    <svg class="faq-chevron w-5 h-5 text-[#5A6C5B] transition-transform duration-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="faq-content hidden px-5 sm:px-6 pb-6 pt-1 text-xs sm:text-sm text-[#5A6C5B] leading-relaxed border-t border-slate-100 bg-[#FCFBF5]/60">
                    Tunggu 1–2 menit lalu segarkan (*refresh*) halaman Riwayat Booking. Jika tiket belum muncul, segera kirim bukti transfer Anda ke WhatsApp Customer Support kami. Tim operasional kami akan memverifikasi transaksi Anda dalam waktu 5–15 menit.
                </div>
            </div>

            {{-- CATEGORY: REFUND --}}
            <div class="faq-item border border-[#E1E3D9] rounded-2xl overflow-hidden bg-white shadow-2xs transition" data-category="refund">
                <button type="button" onclick="toggleFaqAccordion(this)" class="w-full p-5 sm:p-6 text-left font-bold text-sm sm:text-base text-[#1B2724] bg-white hover:bg-[#FCFBF5] flex items-center justify-between gap-4 transition cursor-pointer">
                    <span>Bagaimana kebijakan pembatalan (cancel) dan pengembalian dana (refund)?</span>
                    <svg class="faq-chevron w-5 h-5 text-[#5A6C5B] transition-transform duration-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="faq-content hidden px-5 sm:px-6 pb-6 pt-1 text-xs sm:text-sm text-[#5A6C5B] leading-relaxed border-t border-slate-100 bg-[#FCFBF5]/60">
                    Pembatalan dapat diajukan selambat-lambatnya <strong>H-1 (24 jam sebelum jam mulai)</strong> melalui menu Riwayat Booking. Dana akan dikembalikan sesuai persentase kebijakan mitra arena yang tertera pada lembar rincian sewa.
                </div>
            </div>

            <div class="faq-item border border-[#E1E3D9] rounded-2xl overflow-hidden bg-white shadow-2xs transition" data-category="refund">
                <button type="button" onclick="toggleFaqAccordion(this)" class="w-full p-5 sm:p-6 text-left font-bold text-sm sm:text-base text-[#1B2724] bg-white hover:bg-[#FCFBF5] flex items-center justify-between gap-4 transition cursor-pointer">
                    <span>Bagaimana jika terjadi cuaca buruk (hujan deras) pada lapangan outdoor?</span>
                    <svg class="faq-chevron w-5 h-5 text-[#5A6C5B] transition-transform duration-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="faq-content hidden px-5 sm:px-6 pb-6 pt-1 text-xs sm:text-sm text-[#5A6C5B] leading-relaxed border-t border-slate-100 bg-[#FCFBF5]/60">
                    Untuk arena terbuka (*outdoor*), kondisi *force majeure* cuaca buruk mendapatkan jaminan *reschedule* gratis atau *voucher refund* penuh setelah adanya konfirmasi resmi dari petugas pengelola venue di lokasi.
                </div>
            </div>

            {{-- CATEGORY: MITRA --}}
            <div class="faq-item border border-[#E1E3D9] rounded-2xl overflow-hidden bg-white shadow-2xs transition" data-category="mitra">
                <button type="button" onclick="toggleFaqAccordion(this)" class="w-full p-5 sm:p-6 text-left font-bold text-sm sm:text-base text-[#1B2724] bg-white hover:bg-[#FCFBF5] flex items-center justify-between gap-4 transition cursor-pointer">
                    <span>Bagaimana cara mendaftarkan arena lapangan saya menjadi Mitra SportPlace?</span>
                    <svg class="faq-chevron w-5 h-5 text-[#5A6C5B] transition-transform duration-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="faq-content hidden px-5 sm:px-6 pb-6 pt-1 text-xs sm:text-sm text-[#5A6C5B] leading-relaxed border-t border-slate-100 bg-[#FCFBF5]/60">
                    Klik tombol <strong>"Daftar sebagai Mitra"</strong> pada menu navigasi atau halaman Tentang Kami. Isi formulir profil usaha (KTP, NPWP, foto arena, dan detail lapangan). Tim kurasi SportPlace akan meninjau dan memverifikasi berkas Anda dalam waktu 1x24 jam kerja.
                </div>
            </div>

            <div class="faq-item border border-[#E1E3D9] rounded-2xl overflow-hidden bg-white shadow-2xs transition" data-category="mitra">
                <button type="button" onclick="toggleFaqAccordion(this)" class="w-full p-5 sm:p-6 text-left font-bold text-sm sm:text-base text-[#1B2724] bg-white hover:bg-[#FCFBF5] flex items-center justify-between gap-4 transition cursor-pointer">
                    <span>Kapan dan bagaimana pencairan saldo pendapatan sewa mitra?</span>
                    <svg class="faq-chevron w-5 h-5 text-[#5A6C5B] transition-transform duration-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="faq-content hidden px-5 sm:px-6 pb-6 pt-1 text-xs sm:text-sm text-[#5A6C5B] leading-relaxed border-t border-slate-100 bg-[#FCFBF5]/60">
                    Dana booking otomatis masuk ke saldo dompet mitra setelah slot sewa selesai dilaksanakan. Mitra dapat melakukan penarikan (*withdrawal*) ke rekening bank terdaftar kapan saja dengan proses transfer instan.
                </div>
            </div>

            {{-- CATEGORY: ACCOUNT --}}
            <div class="faq-item border border-[#E1E3D9] rounded-2xl overflow-hidden bg-white shadow-2xs transition" data-category="account">
                <button type="button" onclick="toggleFaqAccordion(this)" class="w-full p-5 sm:p-6 text-left font-bold text-sm sm:text-base text-[#1B2724] bg-white hover:bg-[#FCFBF5] flex items-center justify-between gap-4 transition cursor-pointer">
                    <span>Bagaimana jika saya lupa kata sandi akun?</span>
                    <svg class="faq-chevron w-5 h-5 text-[#5A6C5B] transition-transform duration-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="faq-content hidden px-5 sm:px-6 pb-6 pt-1 text-xs sm:text-sm text-[#5A6C5B] leading-relaxed border-t border-slate-100 bg-[#FCFBF5]/60">
                    Buka halaman Login, lalu klik tautan <strong>"Lupa password?"</strong>. Masukkan alamat email terdaftar Anda untuk menerima tautan pemulihan kata sandi baru.
                </div>
            </div>

        </div>

        <!-- Empty State jika pencarian tidak ditemukan -->
        <div id="faq-empty-state" class="hidden text-center py-12 p-8 bg-white rounded-3xl border border-[#E1E3D9]">
            <div class="w-12 h-12 rounded-full bg-[#F4F7F5] text-[#5A6C5B] flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h4 class="text-base font-bold text-[#1B2724]">Pertanyaan Tidak Ditemukan</h4>
            <p class="text-xs sm:text-sm text-[#5A6C5B] mt-1 max-w-sm mx-auto">
                Coba gunakan kata kunci lain atau langsung hubungi tim Customer Support kami di bawah.
            </p>
        </div>

    </section>

    {{-- =========================================================
         3. BOTTOM SUPPORT CONTACT CARDS
    ========================================================= --}}
    <section class="py-12 sm:py-16 bg-[#F4F9F6] border-t border-[#E1E3D9]">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-xl mx-auto mb-10">
                <span class="inline-block text-2xs font-bold uppercase tracking-wider text-[#0A7660] bg-[#DEF1E7] border border-[#BBE3D0] px-3.5 py-1 rounded-full mb-2">
                    Bantuan Langsung
                </span>
                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-[#1B2724]">
                    Masih Membutuhkan Bantuan?
                </h2>
                <p class="text-xs sm:text-sm text-[#5A6C5B] mt-1.5 leading-relaxed">
                    Customer Service kami siap membantu Anda menyelesaikan kendala secara cepat dan ramah.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Card 1: WhatsApp -->
                <div class="bg-white rounded-3xl p-7 border border-[#E1E3D9] shadow-xs flex flex-col justify-between gap-5 hover:border-[#88E7D4] transition">
                    <div class="space-y-3">
                        <div class="w-12 h-12 rounded-2xl bg-[#EFFBF3] text-[#239F50] border border-[#BAEECD] flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-[#1B2724]">Chat WhatsApp Customer Care</h3>
                        <p class="text-xs sm:text-sm text-[#5A6C5B] leading-relaxed">
                            Respon instan untuk kendala saat booking, keterlambatan verifikasi transfer, atau pertanyaan seputar fasilitas venue.
                        </p>
                        <p class="text-xs text-[#0A7660] font-semibold">
                            ● Jam Kerja: Setiap Hari, 08.00 – 22.00 WIB
                        </p>
                    </div>

                    <a
                        href="https://wa.me/6281234567890?text=Halo%20SportPlace,%20saya%20butuh%20bantuan%20terkait%20layanan."
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center justify-center gap-2 w-full py-3.5 px-5 rounded-2xl bg-[#239F50] hover:bg-[#1A7F3F] text-white text-sm font-bold shadow-xs transition"
                    >
                        <span>Mulai Chat via WhatsApp</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                </div>

                <!-- Card 2: Email Support -->
                <div class="bg-white rounded-3xl p-7 border border-[#E1E3D9] shadow-xs flex flex-col justify-between gap-5 hover:border-[#88E7D4] transition">
                    <div class="space-y-3">
                        <div class="w-12 h-12 rounded-2xl bg-[#DDF4EF] text-[#0A7660] border border-[#BBEDE3] flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-[#1B2724]">Email Tiket Bantuan Resmi</h3>
                        <p class="text-xs sm:text-sm text-[#5A6C5B] leading-relaxed">
                            Gunakan email untuk pengaduan resmi, penawaran kerja sama kemitraan, kendala integrasi perbankan, atau saran platform.
                        </p>
                        <p class="text-xs text-[#5A6C5B] font-medium">
                            Respon balasan resmi maksimal dalam 1x24 jam kerja.
                        </p>
                    </div>

                    <a
                        href="mailto:support@sportplace.id?subject=Permohonan%20Bantuan%20SportPlace"
                        class="inline-flex items-center justify-center gap-2 w-full py-3.5 px-5 rounded-2xl bg-white border border-[#E1E3D9] hover:bg-[#FCFBF5] text-[#1B2724] text-sm font-bold shadow-xs transition"
                    >
                        <span>support@sportplace.id</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Client-side Script untuk Filter & Akordeon FAQ -->
    <script>
        let currentCategory = 'all';

        function selectFaqCategory(category, btn) {
            currentCategory = category;
            document.querySelectorAll('.faq-filter-btn').forEach(b => {
                b.className = 'faq-filter-btn px-4 py-2 rounded-full text-xs sm:text-sm font-semibold bg-white border border-[#E1E3D9] text-[#5A6C5B] hover:text-[#1B2724] hover:bg-[#F4F7F5] transition shrink-0 cursor-pointer';
            });
            btn.className = 'faq-filter-btn px-4 py-2 rounded-full text-xs sm:text-sm font-bold bg-[#0A7660] text-white shadow-xs transition shrink-0 cursor-pointer';
            filterFaq();
        }

        function filterFaq() {
            const query = (document.getElementById('faq-search-input')?.value || '').toLowerCase().trim();
            const clearBtn = document.getElementById('faq-clear-btn');
            if (clearBtn) {
                clearBtn.classList.toggle('hidden', query.length === 0);
            }

            const items = document.querySelectorAll('.faq-item');
            let visibleCount = 0;

            items.forEach(item => {
                const itemCat = item.getAttribute('data-category');
                const text = item.textContent.toLowerCase();

                const matchCat = (currentCategory === 'all' || itemCat === currentCategory);
                const matchQuery = (query === '' || text.includes(query));

                if (matchCat && matchQuery) {
                    item.classList.remove('hidden');
                    visibleCount++;
                } else {
                    item.classList.add('hidden');
                }
            });

            const emptyState = document.getElementById('faq-empty-state');
            if (emptyState) {
                emptyState.classList.toggle('hidden', visibleCount > 0);
            }
        }

        function clearFaqSearch() {
            const input = document.getElementById('faq-search-input');
            if (input) {
                input.value = '';
                filterFaq();
                input.focus();
            }
        }

        function toggleFaqAccordion(btn) {
            const content = btn.nextElementSibling;
            const chevron = btn.querySelector('.faq-chevron');
            if (!content) return;

            const isHidden = content.classList.contains('hidden');
            if (isHidden) {
                content.classList.remove('hidden');
                if (chevron) chevron.classList.add('rotate-180');
            } else {
                content.classList.add('hidden');
                if (chevron) chevron.classList.remove('rotate-180');
            }
        }
    </script>

</x-layouts.public>

