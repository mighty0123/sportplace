{{--
    Komponen Modal Pop-up Bantuan Cepat — SportPlace
    Menampilkan Tutorial Cepat Booking 4 Langkah, FAQ Populer dengan Akordeon, Banner Tautan ke /bantuan, dan Kontak CS.
    Menggunakan Vanilla JS (bebas dependensi eksternal), mendukung tombol keyboard Escape dan klik backdrop.
--}}
<div id="help-modal" class="fixed inset-0 z-[9999] hidden overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="help-modal-title">
    <!-- Backdrop Blur Overlay -->
    <div id="help-modal-backdrop" class="fixed inset-0 bg-[#06231D]/80 backdrop-blur-sm transition-opacity duration-300 opacity-0"></div>

    <!-- Modal Dialog Positioner -->
    <div class="min-h-full flex items-center justify-center p-3 sm:p-5 text-center">
        <!-- Modal Card Container -->
        <div id="help-modal-card" class="relative w-full max-w-2xl bg-white rounded-3xl border border-[#E1E3D9] text-left shadow-2xl transform transition-all duration-300 scale-95 opacity-0 my-8 overflow-hidden flex flex-col max-h-[92vh]">
            
            {{-- =========================================================
                 MODAL HEADER
            ========================================================= --}}
            <div class="px-5 sm:px-7 pt-6 pb-4 border-b border-[#E1E3D9] bg-[#FCFBF5] flex items-start justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#DEF1E7] border border-[#BBE3D0] text-[#0A7660] text-[11px] font-bold uppercase tracking-wider mb-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#0A7660]"></span>
                        <span>Pusat Bantuan & Panduan</span>
                    </div>
                    <h2 id="help-modal-title" class="text-xl sm:text-2xl font-bold text-[#1B2724] tracking-tight">
                        Ada yang Bisa Kami Bantu?
                    </h2>
                    <p class="text-xs sm:text-sm text-[#5A6C5B] mt-1">
                        Pelajari alur sewa lapangan atau temukan jawaban instan untuk pertanyaan umum.
                    </p>
                </div>

                <!-- Close Button [✕] -->
                <button
                    type="button"
                    onclick="closeHelpModal()"
                    class="w-9 h-9 rounded-full bg-[#F4F7F5] hover:bg-[#E1E3D9] text-[#5A6C5B] hover:text-[#1B2724] flex items-center justify-center transition shrink-0 cursor-pointer"
                    aria-label="Tutup jendela bantuan"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- =========================================================
                 TAB SWITCHER (CARA BOOKING | FAQ KILAT | KONTAK CS)
            ========================================================= --}}
            <div class="px-5 sm:px-7 pt-4 bg-[#FCFBF5] border-b border-[#E1E3D9]">
                <div class="flex items-center gap-2 border-b border-transparent overflow-x-auto no-scrollbar">
                    <button
                        type="button"
                        id="tab-btn-tutorial"
                        onclick="switchHelpTab('tutorial')"
                        class="tab-help-btn px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition flex items-center gap-2 bg-[#0A7660] text-white shadow-xs"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <span>Cara Booking</span>
                    </button>

                    <button
                        type="button"
                        id="tab-btn-faq"
                        onclick="switchHelpTab('faq')"
                        class="tab-help-btn px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition flex items-center gap-2 bg-transparent text-[#5A6C5B] hover:text-[#1B2724] hover:bg-[#F4F7F5]"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Tanya Jawab (FAQ)</span>
                    </button>

                    <button
                        type="button"
                        id="tab-btn-contact"
                        onclick="switchHelpTab('contact')"
                        class="tab-help-btn px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition flex items-center gap-2 bg-transparent text-[#5A6C5B] hover:text-[#1B2724] hover:bg-[#F4F7F5]"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <span>Hubungi CS</span>
                    </button>
                </div>
            </div>

            {{-- =========================================================
                 TAB CONTENT BODY (SCROLLABLE)
            ========================================================= --}}
            <div class="p-5 sm:p-7 overflow-y-auto space-y-6">

                {{-- TAB 1: PANDUAN CARA BOOKING (VISUAL 4 LANGKAH & SIMULASI INTERAKTIF) --}}
                <div id="help-pane-tutorial" class="space-y-6">

                    <!-- Interactive Simulation Banner Callout -->
                    <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-br from-[#06231D] via-[#08362D] to-[#0A4B3E] text-white border border-[#115E4F] shadow-lg flex flex-col sm:flex-row sm:items-center justify-between gap-4 relative overflow-hidden">
                        <div class="absolute -right-8 -bottom-8 w-32 h-32 bg-[#DEEF25]/10 rounded-full blur-2xl pointer-events-none"></div>
                        <div class="space-y-1.5 relative z-10">
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-[#DEEF25]/20 border border-[#DEEF25]/40 text-[#DEEF25] text-[11px] font-bold">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#DEEF25] animate-ping"></span>
                                <span>Simulasi Booking Interaktif</span>
                            </div>
                            <h3 class="text-sm sm:text-base font-bold text-white">
                                Ingin Coba Langsung Alur Pemesanan?
                            </h3>
                            <p class="text-xs text-emerald-100/80 leading-relaxed max-w-md">
                                Coba alur 4 langkah dari memilih lapangan, cek slot jam kosong, hingga tiket digital terbit. <strong>100% simulasi aman tanpa mengubah database ataupun saldo Anda</strong>.
                            </p>
                        </div>
                        <button
                            type="button"
                            onclick="startBookingSimulation()"
                            class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-[#DEEF25] hover:bg-[#cde01e] text-[#06231D] text-xs sm:text-sm font-bold transition shadow-sm hover:scale-[1.02] active:scale-[0.98] shrink-0 cursor-pointer relative z-10"
                        >
                            <svg class="w-4 h-4 text-[#06231D]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Mulai Simulasi Booking</span>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <!-- Step 1 -->
                        <div class="p-4 rounded-2xl bg-[#FCFBF5] border border-[#E1E3D9] flex items-start gap-3.5">
                            <div class="w-8 h-8 rounded-xl bg-[#DDF4EF] text-[#0A7660] font-bold text-xs flex items-center justify-center shrink-0">
                                01
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-xs sm:text-sm font-bold text-[#1B2724]">Cari & Pilih Lapangan</h4>
                                <p class="text-xs text-[#5A6C5B] mt-0.5 leading-relaxed">
                                    Pilih cabang olahraga & kota Anda. Jelajahi foto, jenis lantai, dan fasilitas tanpa wajib login.
                                </p>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div class="p-4 rounded-2xl bg-[#FCFBF5] border border-[#E1E3D9] flex items-start gap-3.5">
                            <div class="w-8 h-8 rounded-xl bg-[#F6F9D8] text-[#939E1A] font-bold text-xs flex items-center justify-center shrink-0">
                                02
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-xs sm:text-sm font-bold text-[#1B2724]">Pilih Jadwal Jam Main</h4>
                                <p class="text-xs text-[#5A6C5B] mt-0.5 leading-relaxed">
                                    Lihat slot jam kosong berwarna hijau secara real-time pada kalender tanggal yang diinginkan.
                                </p>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div class="p-4 rounded-2xl bg-[#FCFBF5] border border-[#E1E3D9] flex items-start gap-3.5">
                            <div class="w-8 h-8 rounded-xl bg-[#DDF4EF] text-[#0A7660] font-bold text-xs flex items-center justify-center shrink-0">
                                03
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-xs sm:text-sm font-bold text-[#1B2724]">Masuk & Bayar Instan</h4>
                                <p class="text-xs text-[#5A6C5B] mt-0.5 leading-relaxed">
                                    Login/daftar akun untuk mengamankan slot, lalu selesaikan pembayaran via QRIS atau Virtual Account.
                                </p>
                            </div>
                        </div>

                        <!-- Step 4 -->
                        <div class="p-4 rounded-2xl bg-[#FCFBF5] border border-[#E1E3D9] flex items-start gap-3.5">
                            <div class="w-8 h-8 rounded-xl bg-[#EFFBF3] text-[#239F50] font-bold text-xs flex items-center justify-center shrink-0">
                                04
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-xs sm:text-sm font-bold text-[#1B2724]">Tiket Digital & Main</h4>
                                <p class="text-xs text-[#5A6C5B] mt-0.5 leading-relaxed">
                                    Tiket QR terbit seketika di akun Anda. Tunjukkan ke pengelola lapangan saat hari pertandingan!
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Action Box: Auth vs Guest -->
                    @auth
                        <div class="p-4 sm:p-5 rounded-2xl bg-[#EFFBF3] border border-[#BAEECD] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <p class="text-xs font-bold text-[#1A7F3F]">Halo, {{ auth()->user()->name }}!</p>
                                <p class="text-xs text-[#146130] mt-0.5">Akun Anda sudah siap untuk melakukan reservasi lapangan.</p>
                            </div>
                            <a
                                href="{{ route('penyewa.lapangan') }}"
                                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-[#0A7660] hover:bg-[#0B604F] text-white text-xs font-bold transition shadow-xs shrink-0"
                            >
                                <span>Mulai Cari Lapangan</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </a>
                        </div>
                    @else
                        <div class="p-4 sm:p-5 rounded-2xl bg-[#F8F7F0] border border-[#E1E3D9] space-y-3.5">
                            <div class="flex items-start gap-2.5 text-xs text-[#5A6C5B]">
                                <svg class="w-4 h-4 text-[#0A7660] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span><strong>Tips Pengunjung:</strong> Anda bebas melihat lapangan dan ketersediaan slot tanpa login. Akun hanya diperlukan saat Anda menekan tombol bayar agar tiket tersimpan di akun Anda.</span>
                            </div>

                            <div class="flex flex-wrap items-center gap-2.5 pt-1">
                                <a
                                    href="{{ route('register') }}"
                                    class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-[#0A7660] hover:bg-[#0B604F] text-white text-xs font-bold transition shadow-xs"
                                >
                                    Daftar Akun Gratis
                                </a>
                                <a
                                    href="{{ route('login') }}"
                                    class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-white border border-[#E1E3D9] hover:bg-[#FCFBF5] text-[#1B2724] text-xs font-semibold transition"
                                >
                                    Masuk / Login
                                </a>
                                <a
                                    href="{{ route('penyewa.lapangan') }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-[#0A7660] hover:underline ml-auto"
                                >
                                    <span>Jelajahi Lapangan Dulu</span>
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
                    <div class="border border-[#E1E3D9] rounded-2xl overflow-hidden transition">
                        <button
                            type="button"
                            onclick="toggleHelpFaq(this)"
                            class="w-full p-4 text-left font-bold text-xs sm:text-sm text-[#1B2724] bg-[#FCFBF5] hover:bg-[#F4F7F5] flex items-center justify-between gap-3 transition"
                        >
                            <span>Apakah jadwal di SportPlace terjamin akurat?</span>
                            <svg class="faq-chevron w-4 h-4 text-[#5A6C5B] transition-transform duration-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div class="faq-body hidden p-4 text-xs text-[#5A6C5B] leading-relaxed border-t border-[#E1E3D9] bg-white">
                            Ya, SportPlace terintegrasi langsung dengan jadwal operasional mitra pengelola venue. Slot berwarna hijau menandakan jam kosong yang dapat langsung Anda pesan tanpa risiko jadwal ganda (*double booking*).
                        </div>
                    </div>

                    <!-- FAQ Item 2 -->
                    <div class="border border-[#E1E3D9] rounded-2xl overflow-hidden transition">
                        <button
                            type="button"
                            onclick="toggleHelpFaq(this)"
                            class="w-full p-4 text-left font-bold text-xs sm:text-sm text-[#1B2724] bg-[#FCFBF5] hover:bg-[#F4F7F5] flex items-center justify-between gap-3 transition"
                        >
                            <span>Berapa batas waktu pembayaran setelah memilih slot jam?</span>
                            <svg class="faq-chevron w-4 h-4 text-[#5A6C5B] transition-transform duration-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div class="faq-body hidden p-4 text-xs text-[#5A6C5B] leading-relaxed border-t border-[#E1E3D9] bg-white">
                            Slot jam yang Anda pilih akan ditahan (*slot lock*) selama 15 menit agar tidak diambil oleh penyewa lain. Jika tidak ada konfirmasi pembayaran dalam rentang waktu tersebut, slot otomatis dilepas kembali ke publik.
                        </div>
                    </div>

                    <!-- FAQ Item 3 -->
                    <div class="border border-[#E1E3D9] rounded-2xl overflow-hidden transition">
                        <button
                            type="button"
                            onclick="toggleHelpFaq(this)"
                            class="w-full p-4 text-left font-bold text-xs sm:text-sm text-[#1B2724] bg-[#FCFBF5] hover:bg-[#F4F7F5] flex items-center justify-between gap-3 transition"
                        >
                            <span>Metode pembayaran apa saja yang didukung?</span>
                            <svg class="faq-chevron w-4 h-4 text-[#5A6C5B] transition-transform duration-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div class="faq-body hidden p-4 text-xs text-[#5A6C5B] leading-relaxed border-t border-[#E1E3D9] bg-white">
                            Kami mendukung QRIS (bisa di-scan menggunakan GoPay, ShopeePay, OVO, Dana, BCA Mobile, Livin, dll.) serta Virtual Account resmi dari Bank BCA, Mandiri, BNI, dan BRI.
                        </div>
                    </div>

                    <!-- FAQ Item 4 -->
                    <div class="border border-[#E1E3D9] rounded-2xl overflow-hidden transition">
                        <button
                            type="button"
                            onclick="toggleHelpFaq(this)"
                            class="w-full p-4 text-left font-bold text-xs sm:text-sm text-[#1B2724] bg-[#FCFBF5] hover:bg-[#F4F7F5] flex items-center justify-between gap-3 transition"
                        >
                            <span>Apakah bisa membatalkan pesanan atau ganti jadwal?</span>
                            <svg class="faq-chevron w-4 h-4 text-[#5A6C5B] transition-transform duration-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div class="faq-body hidden p-4 text-xs text-[#5A6C5B] leading-relaxed border-t border-[#E1E3D9] bg-white">
                            Pengajuan *reschedule* atau pembatalan dapat diajukan maksimal H-1 sebelum jadwal bermain dimulai melalui menu Riwayat Booking di akun Anda sesuai dengan ketentuan mitra venue.
                        </div>
                    </div>

                    <!-- Banner Arahan ke Halaman Lengkap /bantuan -->
                    <div class="pt-2">
                        <div class="p-4 rounded-2xl bg-[#EBF5F0] border border-[#BBE3D0] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="min-w-0">
                                <h5 class="text-xs font-bold text-[#1B2724]">Butuh aturan & kebijakan yang lebih lengkap?</h5>
                                <p class="text-[11px] text-[#5A6C5B] mt-0.5">
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

                {{-- TAB 3: HUBUNGI CUSTOMER SERVICE --}}
                <div id="help-pane-contact" class="space-y-4 hidden">
                    <p class="text-xs sm:text-sm text-[#5A6C5B]">
                        Tim operasional SportPlace siap membantu segala kendala seputar transaksi, ketersediaan venue, maupun pendaftaran mitra.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <!-- WhatsApp Card -->
                        <div class="p-4 rounded-2xl bg-[#FCFBF5] border border-[#E1E3D9] flex flex-col justify-between gap-3">
                            <div>
                                <div class="w-9 h-9 rounded-xl bg-[#EFFBF3] text-[#239F50] flex items-center justify-center mb-2.5">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                    </svg>
                                </div>
                                <h4 class="text-xs sm:text-sm font-bold text-[#1B2724]">WhatsApp Customer Care</h4>
                                <p class="text-[11px] text-[#5A6C5B] mt-0.5">Respon cepat setiap hari pukul 08.00 – 22.00 WIB.</p>
                            </div>
                            <a
                                href="https://wa.me/6281234567890?text=Halo%20SportPlace,%20saya%20butuh%20bantuan%20terkait%20pemesanan%20lapangan."
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center justify-center gap-1.5 w-full py-2 px-3 rounded-xl bg-[#239F50] hover:bg-[#1A7F3F] text-white text-xs font-bold transition shadow-xs"
                            >
                                <span>Chat WhatsApp</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </a>
                        </div>

                        <!-- Email Card -->
                        <div class="p-4 rounded-2xl bg-[#FCFBF5] border border-[#E1E3D9] flex flex-col justify-between gap-3">
                            <div>
                                <div class="w-9 h-9 rounded-xl bg-[#DDF4EF] text-[#0A7660] flex items-center justify-center mb-2.5">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <h4 class="text-xs sm:text-sm font-bold text-[#1B2724]">Email Pengaduan Resmi</h4>
                                <p class="text-[11px] text-[#5A6C5B] mt-0.5">Kirim tiket kendala teknis atau pengajuan kemitraan venue.</p>
                            </div>
                            <a
                                href="mailto:support@sportplace.id?subject=Bantuan%20SportPlace"
                                class="inline-flex items-center justify-center gap-1.5 w-full py-2 px-3 rounded-xl bg-white border border-[#E1E3D9] hover:bg-[#FCFBF5] text-[#1B2724] text-xs font-bold transition"
                            >
                                <span>support@sportplace.id</span>
                            </a>
                        </div>
                    </div>
                </div>

            </div>

            {{-- =========================================================
                 MODAL FOOTER (DISMISS BUTTON)
            ========================================================= --}}
            <div class="px-5 sm:px-7 py-3.5 border-t border-[#E1E3D9] bg-[#FCFBF5] flex items-center justify-between text-xs text-[#5A6C5B]">
                <span>SportPlace Indonesia • Layanan Bantuan</span>
                <button
                    type="button"
                    onclick="closeHelpModal()"
                    class="font-semibold text-[#0A7660] hover:underline cursor-pointer"
                >
                    Tutup
                </button>
            </div>

        </div>
    </div>
</div>

{{-- =========================================================================
     MODAL POP-UP SIMULASI BOOKING INTERAKTIF (100% DEMO CLIENT-SIDE)
     Tidak mengubah database, memberikan simulasi langsung alur sewa lapangan.
========================================================================= --}}
<div id="booking-simulation-modal" class="fixed inset-0 z-[10000] hidden overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="sim-modal-title">
    <!-- Backdrop Blur Overlay -->
    <div id="sim-modal-backdrop" class="fixed inset-0 bg-[#06231D]/80 backdrop-blur-sm transition-opacity duration-300 opacity-0"></div>

    <!-- Modal Positioner -->
    <div class="min-h-full flex items-center justify-center p-3 sm:p-5 text-center">
        <div id="sim-modal-card" class="relative w-full max-w-2xl bg-white rounded-3xl border border-[#E1E3D9] text-left shadow-2xl transform transition-all duration-300 scale-95 opacity-0 my-8 overflow-hidden flex flex-col max-h-[92vh]">
            
            <!-- Top Gamified Header Bar -->
            <div class="px-5 sm:px-7 py-3 bg-[#06231D] text-white flex items-center justify-between border-b border-[#0F4A3F]">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-[#DEEF25]/20 text-[#DEEF25] border border-[#DEEF25]/40 text-[10px] font-bold uppercase tracking-wider">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#DEEF25] animate-ping"></span>
                        <span>Mode Simulasi (Demo)</span>
                    </span>
                    <span class="text-xs text-emerald-200/90 hidden sm:inline">• Tidak Ada Perubahan Database</span>
                </div>
                <button
                    type="button"
                    onclick="closeBookingSimulation()"
                    class="text-xs text-emerald-300 hover:text-white flex items-center gap-1 cursor-pointer transition"
                    aria-label="Tutup simulasi"
                >
                    <span>Tutup</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Stepper Progress Header -->
            <div class="px-5 sm:px-7 pt-5 pb-4 bg-[#FCFBF5] border-b border-[#E1E3D9]">
                <div class="flex items-center justify-between gap-4 mb-3">
                    <div>
                        <h2 id="sim-modal-title" class="text-lg sm:text-xl font-bold text-[#1B2724] tracking-tight">
                            Simulasi Alur Reservasi Lapangan
                        </h2>
                        <p class="text-xs text-[#5A6C5B] mt-0.5">
                            Ikuti langkah demi langkah untuk merasakan alur pemesanan SportPlace secara nyata.
                        </p>
                    </div>
                    <span id="sim-step-badge" class="px-3 py-1 rounded-full bg-[#DEF1E7] border border-[#BBE3D0] text-[#0A7660] text-xs font-bold shrink-0">
                        Langkah 1 dari 4
                    </span>
                </div>

                <!-- 4-Steps Indicator -->
                <div class="grid grid-cols-4 gap-2 pt-2">
                    <!-- Step 1 -->
                    <div id="sim-indicator-1" class="text-left space-y-1">
                        <div class="h-1.5 rounded-full bg-[#0A7660] transition-colors duration-300"></div>
                        <p class="text-[10px] font-bold text-[#0A7660] truncate">1. Lapangan</p>
                    </div>
                    <!-- Step 2 -->
                    <div id="sim-indicator-2" class="text-left space-y-1">
                        <div class="h-1.5 rounded-full bg-[#E1E3D9] transition-colors duration-300"></div>
                        <p class="text-[10px] font-semibold text-[#7D8C78] truncate">2. Jam Main</p>
                    </div>
                    <!-- Step 3 -->
                    <div id="sim-indicator-3" class="text-left space-y-1">
                        <div class="h-1.5 rounded-full bg-[#E1E3D9] transition-colors duration-300"></div>
                        <p class="text-[10px] font-semibold text-[#7D8C78] truncate">3. Bayar</p>
                    </div>
                    <!-- Step 4 -->
                    <div id="sim-indicator-4" class="text-left space-y-1">
                        <div class="h-1.5 rounded-full bg-[#E1E3D9] transition-colors duration-300"></div>
                        <p class="text-[10px] font-semibold text-[#7D8C78] truncate">4. Tiket Terbit</p>
                    </div>
                </div>
            </div>

            <!-- Modal Body (Steps Container) -->
            <div class="p-5 sm:p-7 overflow-y-auto space-y-5 flex-1">

                {{-- =========================================================
                     STEP 1: PILIH LAPANGAN
                ========================================================= --}}
                <div id="sim-step-1" class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-bold text-[#1B2724]">
                            Langkah 1: Tentukan Unit Lapangan Pilihan
                        </h4>
                        <span class="text-xs text-[#0A7660] font-semibold">Pilih salah satu kartu</span>
                    </div>

                    <!-- Court Cards Selection -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <!-- Court 1 -->
                        <div
                            id="sim-court-card-1"
                            onclick="selectSimulationCourt(1, 'Arena Futsal Vinyl Super (Indoor)', 150000)"
                            class="sim-court-card p-4 rounded-2xl border-2 border-[#0A7660] bg-[#DEF1E7]/25 hover:bg-[#DEF1E7]/40 transition cursor-pointer relative"
                        >
                            <div class="flex items-start justify-between gap-2">
                                <span class="px-2 py-0.5 rounded-full bg-[#DDF4EF] text-[#0A7660] text-[10px] font-bold">Futsal</span>
                                <span class="sim-court-check w-5 h-5 rounded-full bg-[#0A7660] text-white flex items-center justify-center text-xs">✓</span>
                            </div>
                            <h5 class="font-bold text-sm text-[#1B2724] mt-2">Arena Futsal Vinyl Super</h5>
                            <p class="text-xs text-[#5A6C5B] mt-0.5">Grand Sport Arena • Jakarta Selatan</p>
                            <div class="mt-3 pt-2.5 border-t border-[#E1E3D9] flex items-center justify-between text-xs">
                                <span class="text-[#5A6C5B]">Tarif / Jam</span>
                                <span class="font-bold text-[#0A7660]">Rp 150.000</span>
                            </div>
                        </div>

                        <!-- Court 2 -->
                        <div
                            id="sim-court-card-2"
                            onclick="selectSimulationCourt(2, 'Badminton Court Karpet BWF (Indoor)', 85000)"
                            class="sim-court-card p-4 rounded-2xl border-2 border-[#E1E3D9] bg-white hover:bg-[#FCFBF5] transition cursor-pointer relative"
                        >
                            <div class="flex items-start justify-between gap-2">
                                <span class="px-2 py-0.5 rounded-full bg-[#F6F9D8] text-[#939E1A] text-[10px] font-bold">Badminton</span>
                                <span class="sim-court-check hidden w-5 h-5 rounded-full bg-[#0A7660] text-white items-center justify-center text-xs">✓</span>
                            </div>
                            <h5 class="font-bold text-sm text-[#1B2724] mt-2">Badminton Court BWF</h5>
                            <p class="text-xs text-[#5A6C5B] mt-0.5">Grand Sport Arena • Jakarta Selatan</p>
                            <div class="mt-3 pt-2.5 border-t border-[#E1E3D9] flex items-center justify-between text-xs">
                                <span class="text-[#5A6C5B]">Tarif / Jam</span>
                                <span class="font-bold text-[#0A7660]">Rp 85.000</span>
                            </div>
                        </div>
                    </div>

                    <!-- Amenities Banner -->
                    <div class="p-3 rounded-xl bg-[#FCFBF5] border border-[#E1E3D9] flex flex-wrap items-center gap-3 text-xs text-[#5A6C5B]">
                        <span class="font-bold text-[#1B2724]">Fasilitas Venue:</span>
                        <span class="inline-flex items-center gap-1">❄️ AC Dingin</span>
                        <span class="inline-flex items-center gap-1">🚿 Kamar Mandi & Shower</span>
                        <span class="inline-flex items-center gap-1">🚗 Parkir Luas</span>
                    </div>

                    <!-- Guidance Tip -->
                    <div class="p-3.5 rounded-xl bg-[#DEF1E7]/40 border border-[#BBE3D0] flex items-center gap-2.5 text-xs text-[#0A7660]">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span><strong>Petunjuk:</strong> Klik salah satu lapangan yang diminati, lalu tekan tombol <em>"Lanjut ke Pilih Jam Main"</em>.</span>
                    </div>
                </div>

                {{-- =========================================================
                     STEP 2: PILIH JADWAL & JAM MAIN
                ========================================================= --}}
                <div id="sim-step-2" class="space-y-4 hidden">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-bold text-[#1B2724]">
                            Langkah 2: Pilih Tanggal & Slot Jam Kosong
                        </h4>
                        <span class="text-xs text-[#5A6C5B]">Slot hijau = Kosong</span>
                    </div>

                    <!-- Date Selector Pills -->
                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            onclick="setSimulationDate('Hari Ini, ' + (new Date()).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }), this)"
                            class="sim-date-btn px-4 py-2 rounded-xl text-xs font-bold bg-[#0A7660] text-white shadow-xs cursor-pointer"
                        >
                            Hari Ini
                        </button>
                        <button
                            type="button"
                            onclick="setSimulationDate('Besok', this)"
                            class="sim-date-btn px-4 py-2 rounded-xl text-xs font-semibold bg-white border border-[#E1E3D9] text-[#5A6C5B] hover:bg-[#FCFBF5] cursor-pointer"
                        >
                            Besok
                        </button>
                    </div>

                    <!-- Time Slots Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                        <!-- Slot 1 (Available) -->
                        <button
                            type="button"
                            onclick="toggleSimulationSlot(this, '16:00 - 17:00')"
                            class="sim-slot-btn p-3 rounded-xl border border-emerald-300 bg-[#EFFBF3] text-left hover:bg-emerald-100 transition cursor-pointer text-xs"
                        >
                            <span class="block font-bold text-[#1B2724]">16:00 - 17:00</span>
                            <span class="text-[11px] text-[#239F50] font-medium">Tersedia</span>
                        </button>

                        <!-- Slot 2 (Booked - Red Disabled) -->
                        <div class="p-3 rounded-xl border border-rose-200 bg-rose-50/70 text-left text-xs opacity-60 cursor-not-allowed">
                            <span class="block font-bold text-slate-600 line-through">17:00 - 18:00</span>
                            <span class="text-[11px] text-rose-600 font-semibold">Sudah Terisi</span>
                        </div>

                        <!-- Slot 3 (Available) -->
                        <button
                            type="button"
                            onclick="toggleSimulationSlot(this, '18:00 - 19:00')"
                            class="sim-slot-btn p-3 rounded-xl border border-emerald-300 bg-[#EFFBF3] text-left hover:bg-emerald-100 transition cursor-pointer text-xs"
                        >
                            <span class="block font-bold text-[#1B2724]">18:00 - 19:00</span>
                            <span class="text-[11px] text-[#239F50] font-medium">Tersedia</span>
                        </button>

                        <!-- Slot 4 (Selected by default) -->
                        <button
                            type="button"
                            id="sim-default-slot"
                            onclick="toggleSimulationSlot(this, '19:00 - 20:00')"
                            class="sim-slot-btn p-3 rounded-xl border-2 border-[#0A7660] bg-[#0A7660] text-white text-left transition cursor-pointer text-xs shadow-xs"
                        >
                            <div class="flex items-center justify-between">
                                <span class="block font-bold">19:00 - 20:00</span>
                                <span class="text-xs">✓</span>
                            </div>
                            <span class="text-[11px] text-emerald-200 font-medium">Terpilih (Favorit)</span>
                        </button>

                        <!-- Slot 5 (Booked - Red Disabled) -->
                        <div class="p-3 rounded-xl border border-rose-200 bg-rose-50/70 text-left text-xs opacity-60 cursor-not-allowed">
                            <span class="block font-bold text-slate-600 line-through">20:00 - 21:00</span>
                            <span class="text-[11px] text-rose-600 font-semibold">Sudah Terisi</span>
                        </div>

                        <!-- Slot 6 (Available) -->
                        <button
                            type="button"
                            onclick="toggleSimulationSlot(this, '21:00 - 22:00')"
                            class="sim-slot-btn p-3 rounded-xl border border-emerald-300 bg-[#EFFBF3] text-left hover:bg-emerald-100 transition cursor-pointer text-xs"
                        >
                            <span class="block font-bold text-[#1B2724]">21:00 - 22:00</span>
                            <span class="text-[11px] text-[#239F50] font-medium">Tersedia</span>
                        </button>
                    </div>

                    <!-- Live Selection Summary -->
                    <div class="p-4 rounded-2xl bg-[#FCFBF5] border border-[#E1E3D9] flex items-center justify-between">
                        <div>
                            <span class="text-[11px] text-[#5A6C5B] uppercase tracking-wider font-semibold block">Ringkasan Slot</span>
                            <span id="sim-selected-slot-text" class="text-xs sm:text-sm font-bold text-[#1B2724]">19:00 - 20:00 WIB (1 Jam)</span>
                        </div>
                        <div class="text-right">
                            <span class="text-[11px] text-[#5A6C5B] uppercase tracking-wider font-semibold block">Subtotal</span>
                            <span id="sim-subtotal-text" class="text-sm sm:text-base font-bold text-[#0A7660]">Rp 150.000</span>
                        </div>
                    </div>
                </div>

                {{-- =========================================================
                     STEP 3: REVIEW & PEMBAYARAN SIMULASI
                ========================================================= --}}
                <div id="sim-step-3" class="space-y-4 hidden">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-bold text-[#1B2724]">
                            Langkah 3: Review Pesanan & Pembayaran Instan
                        </h4>
                        <!-- Simulated Lock Timer -->
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-amber-50 border border-amber-200 text-amber-800 text-[11px] font-bold">
                            <span>⏱️ Slot Terkunci: 14:59</span>
                        </div>
                    </div>

                    <!-- Order Summary Box -->
                    <div class="p-4 rounded-2xl bg-[#FCFBF5] border border-[#E1E3D9] space-y-2.5 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-[#5A6C5B]">Lapangan:</span>
                            <span id="sim-review-court" class="font-bold text-[#1B2724]">Arena Futsal Vinyl Super</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-[#5A6C5B]">Jadwal Main:</span>
                            <span id="sim-review-schedule" class="font-bold text-[#1B2724]">Hari Ini • 19:00 - 20:00 WIB</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-[#5A6C5B]">Penyewa:</span>
                            <span class="font-bold text-[#1B2724]">{{ auth()->user()->name ?? 'Pengunjung Demo (Tamu)' }}</span>
                        </div>
                        <div class="pt-2 border-t border-[#E1E3D9] space-y-1">
                            <div class="flex items-center justify-between text-[#5A6C5B]">
                                <span>Sewa Lapangan (1 Jam):</span>
                                <span id="sim-cost-court">Rp 150.000</span>
                            </div>
                            <div class="flex items-center justify-between text-[#5A6C5B]">
                                <span>Biaya Layanan Platform:</span>
                                <span>Rp 2.500</span>
                            </div>
                            <div class="flex items-center justify-between pt-1 border-t border-dashed border-[#E1E3D9] text-sm font-bold text-[#1B2724]">
                                <span>Total Tagihan:</span>
                                <span id="sim-cost-total" class="text-[#0A7660]">Rp 152.500</span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method Demo Radio -->
                    <div class="space-y-2">
                        <span class="text-xs font-bold text-[#1B2724] block">Metode Pembayaran Demo:</span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            <label class="p-3 rounded-xl border-2 border-[#0A7660] bg-[#DEF1E7]/30 flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="sim_payment_method" value="qris" checked class="text-[#0A7660] focus:ring-[#0A7660]">
                                <div class="text-xs">
                                    <span class="font-bold text-[#1B2724] block">QRIS Instan (Semua E-Wallet)</span>
                                    <span class="text-[11px] text-[#5A6C5B]">GoPay, ShopeePay, OVO, BCA, Livin</span>
                                </div>
                            </label>

                            <label class="p-3 rounded-xl border border-[#E1E3D9] bg-white flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="sim_payment_method" value="va" class="text-[#0A7660] focus:ring-[#0A7660]">
                                <div class="text-xs">
                                    <span class="font-bold text-[#1B2724] block">Virtual Account Bank</span>
                                    <span class="text-[11px] text-[#5A6C5B]">BCA, Mandiri, BNI, BRI</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Safety Notice -->
                    <div class="p-3 rounded-xl bg-amber-50/70 border border-amber-200/80 flex items-start gap-2.5 text-xs text-amber-800">
                        <svg class="w-4 h-4 shrink-0 text-amber-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <span><strong>Catatan Simulasi:</strong> Menekan tombol bayar tidak akan memotong saldo nyata dan tidak menyimpan transaksi ke database.</span>
                    </div>
                </div>

                {{-- =========================================================
                     STEP 4: TIKET DIGITAL SIMULASI TERBIT!
                ========================================================= --}}
                <div id="sim-step-4" class="space-y-5 hidden text-center">
                    <!-- Celebration Banner -->
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#DEF1E7] border border-[#BBE3D0] text-[#0A7660] text-xs font-bold">
                        <span>🎉 Pembayaran Simulasi Sukses!</span>
                    </div>

                    <h4 class="text-lg sm:text-xl font-bold text-[#1B2724]">
                        Tiket Digital Resmi Telah Terbit
                    </h4>
                    <p class="text-xs text-[#5A6C5B] max-w-md mx-auto -mt-2">
                        Ini adalah bentuk tiket digital yang Anda terima saat melakukan pemesanan nyata di SportPlace.
                    </p>

                    <!-- Realistic E-Ticket Card -->
                    <div class="max-w-md mx-auto rounded-3xl border border-[#E1E3D9] bg-white shadow-lg overflow-hidden text-left relative">
                        <!-- Ticket Header -->
                        <div class="p-4 bg-[#06231D] text-white flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg bg-[#0B7B69] flex items-center justify-center text-white font-bold text-xs">
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
                                <span class="text-[10px] text-[#5A6C5B] uppercase font-bold tracking-wider block">Unit Lapangan</span>
                                <h5 id="sim-ticket-court" class="font-bold text-sm text-[#1B2724]">Arena Futsal Vinyl Super</h5>
                                <p class="text-xs text-[#5A6C5B]">Grand Sport Arena • Jakarta Selatan</p>
                            </div>

                            <div class="grid grid-cols-2 gap-3 text-xs pt-2 border-t border-dashed border-[#E1E3D9]">
                                <div>
                                    <span class="text-[10px] text-[#5A6C5B] uppercase font-bold tracking-wider block">Waktu Main</span>
                                    <span id="sim-ticket-time" class="font-bold text-[#1B2724]">19:00 - 20:00 WIB</span>
                                </div>
                                <div>
                                    <span class="text-[10px] text-[#5A6C5B] uppercase font-bold tracking-wider block">Kode Booking</span>
                                    <span class="font-bold text-[#0A7660] font-mono">#SP-SIM-2026-X88</span>
                                </div>
                            </div>

                            <!-- Simulated QR Code -->
                            <div class="pt-3 border-t border-dashed border-[#E1E3D9] flex items-center justify-between gap-4">
                                <div>
                                    <span class="text-[10px] text-[#5A6C5B] uppercase font-bold tracking-wider block">Verifikasi Venue</span>
                                    <p class="text-[11px] text-[#5A6C5B] mt-0.5 leading-tight">Tunjukkan QR code ini ke petugas venue saat kedatangan.</p>
                                </div>
                                <!-- Clean QR SVG Representation -->
                                <div class="w-16 h-16 bg-[#F4F7F5] border border-[#E1E3D9] rounded-xl p-1.5 flex items-center justify-center shrink-0">
                                    <svg class="w-full h-full text-[#06231D]" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M2 2h8v8H2V2zm2 2v4h4V4H4zm10-2h8v8h-8V2zm2 2v4h4V4h-4zM2 14h8v8H2v-8zm2 2v4h4v-4H4zm10 0h2v2h-2v-2zm4 0h4v2h-4v-2zm0 4h2v2h-2v-2zm-4 2h2v2h-2v-2zm6 0h2v2h-2v-2zM6 6h2v2H6V6zm12 0h2v2h-2V6zM6 18h2v2H6v-2z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Ticket Footer Ribbon -->
                        <div class="px-5 py-2.5 bg-[#FCFBF5] border-t border-[#E1E3D9] flex items-center justify-between text-[11px] text-[#5A6C5B]">
                            <span>Status: Konfirmasi Instan</span>
                            <span class="font-bold text-[#0A7660]">SportPlace Official Demo</span>
                        </div>
                    </div>

                    <!-- What happens next box -->
                    <div class="p-4 rounded-2xl bg-[#EFFBF3] border border-[#BAEECD] text-left space-y-1.5 text-xs text-[#146130] max-w-md mx-auto">
                        <p class="font-bold text-[#1A7F3F]">💡 Informasi Alur Pemesanan Asli:</p>
                        <p class="leading-relaxed">
                            1. Slot jam yang Anda bayar otomatis menjadi merah bagi pengguna lain (anti *double booking*).<br>
                            2. Tiket ini langsung tersimpan otomatis di menu <strong>Riwayat Booking</strong> di profil Anda.<br>
                            3. Anda cukup datang ke venue saat jam main dimulai!
                        </p>
                    </div>
                </div>

            </div>

            <!-- Modal Action Footer Buttons -->
            <div class="px-5 sm:px-7 py-4 border-t border-[#E1E3D9] bg-[#FCFBF5] flex items-center justify-between gap-3">
                <!-- Back Button (Steps 2 & 3) -->
                <button
                    type="button"
                    id="sim-back-btn"
                    onclick="prevSimulationStep()"
                    class="hidden px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold text-[#5A6C5B] hover:text-[#1B2724] hover:bg-[#E1E3D9] transition cursor-pointer"
                >
                    ⬅ Kembali
                </button>

                <!-- Close & Return (Step 1) -->
                <button
                    type="button"
                    id="sim-cancel-btn"
                    onclick="closeBookingSimulation()"
                    class="px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold text-[#5A6C5B] hover:text-[#1B2724] transition cursor-pointer"
                >
                    Tutup Simulasi
                </button>

                <!-- Next / Proceed Button -->
                <button
                    type="button"
                    id="sim-next-btn"
                    onclick="nextSimulationStep()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#0A7660] hover:bg-[#0B604F] text-white text-xs sm:text-sm font-bold transition shadow-xs cursor-pointer ml-auto"
                >
                    <span>Lanjut ke Jam Main</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </button>

                <!-- Finish / Action Buttons for Step 4 -->
                <div id="sim-finish-actions" class="hidden flex-wrap items-center gap-2 ml-auto">
                    <button
                        type="button"
                        onclick="resetBookingSimulation()"
                        class="px-4 py-2 rounded-xl bg-white border border-[#E1E3D9] hover:bg-[#FCFBF5] text-[#1B2724] text-xs font-semibold transition cursor-pointer"
                    >
                        🔄 Coba Lagi
                    </button>
                    <a
                        href="{{ route('penyewa.lapangan') }}"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#0A7660] hover:bg-[#0B604F] text-white text-xs font-bold transition shadow-xs"
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

        // Hide all steps
        for (let i = 1; i <= 4; i++) {
            const stepEl = document.getElementById('sim-step-' + i);
            const indicatorEl = document.getElementById('sim-indicator-' + i);
            if (stepEl) stepEl.classList.add('hidden');
            if (indicatorEl) {
                const line = indicatorEl.querySelector('div');
                const text = indicatorEl.querySelector('p');
                if (i <= stepNumber) {
                    line.className = 'h-1.5 rounded-full bg-[#0A7660] transition-colors duration-300';
                    text.className = 'text-[10px] font-bold text-[#0A7660] truncate';
                } else {
                    line.className = 'h-1.5 rounded-full bg-[#E1E3D9] transition-colors duration-300';
                    text.className = 'text-[10px] font-semibold text-[#7D8C78] truncate';
                }
            }
        }

        const activeStepEl = document.getElementById('sim-step-' + stepNumber);
        if (activeStepEl) activeStepEl.classList.remove('hidden');

        // Update badge
        const badge = document.getElementById('sim-step-badge');
        if (badge) badge.textContent = 'Langkah ' + stepNumber + ' dari 4';

        // Update buttons state
        const backBtn = document.getElementById('sim-back-btn');
        const cancelBtn = document.getElementById('sim-cancel-btn');
        const nextBtn = document.getElementById('sim-next-btn');
        const finishActions = document.getElementById('sim-finish-actions');

        if (stepNumber === 1) {
            if (backBtn) backBtn.classList.add('hidden');
            if (cancelBtn) cancelBtn.classList.remove('hidden');
            if (nextBtn) {
                nextBtn.classList.remove('hidden');
                nextBtn.innerHTML = '<span>Lanjut ke Jam Main</span><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>';
            }
            if (finishActions) finishActions.classList.add('hidden');
        } else if (stepNumber === 2) {
            if (backBtn) backBtn.classList.remove('hidden');
            if (cancelBtn) cancelBtn.classList.add('hidden');
            if (nextBtn) {
                nextBtn.classList.remove('hidden');
                nextBtn.innerHTML = '<span>Lanjut ke Pembayaran</span><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>';
            }
            if (finishActions) finishActions.classList.add('hidden');
        } else if (stepNumber === 3) {
            if (backBtn) backBtn.classList.remove('hidden');
            if (cancelBtn) cancelBtn.classList.add('hidden');
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
            if (cancelBtn) cancelBtn.classList.add('hidden');
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
            // Simulasi proses bayar dengan spinner kecil
            const nextBtn = document.getElementById('sim-next-btn');
            if (nextBtn) {
                nextBtn.disabled = true;
                nextBtn.innerHTML = '<span class="inline-block animate-spin mr-1">⌛</span> Memproses Tiket...';
            }
            setTimeout(() => {
                if (nextBtn) nextBtn.disabled = false;
                window.setSimulationStep(4);
            }, 600);
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

    // Pilih Lapangan pada Step 1
    window.selectSimulationCourt = function (id, name, price) {
        window.simState.courtId = id;
        window.simState.courtName = name;
        window.simState.courtPrice = price;

        for (let i = 1; i <= 2; i++) {
            const card = document.getElementById('sim-court-card-' + i);
            if (!card) continue;
            const check = card.querySelector('.sim-court-check');
            if (i === id) {
                card.className = 'sim-court-card p-4 rounded-2xl border-2 border-[#0A7660] bg-[#DEF1E7]/25 hover:bg-[#DEF1E7]/40 transition cursor-pointer relative';
                if (check) check.classList.remove('hidden');
            } else {
                card.className = 'sim-court-card p-4 rounded-2xl border-2 border-[#E1E3D9] bg-white hover:bg-[#FCFBF5] transition cursor-pointer relative';
                if (check) check.classList.add('hidden');
            }
        }

        // Update harga di step 2
        const subtotal = document.getElementById('sim-subtotal-text');
        if (subtotal) subtotal.textContent = 'Rp ' + price.toLocaleString('id-ID');
    };

    // Ganti Tanggal pada Step 2
    window.setSimulationDate = function (dateLabel, btn) {
        window.simState.dateText = dateLabel;
        document.querySelectorAll('.sim-date-btn').forEach(b => {
            b.className = 'sim-date-btn px-4 py-2 rounded-xl text-xs font-semibold bg-white border border-[#E1E3D9] text-[#5A6C5B] hover:bg-[#FCFBF5] cursor-pointer';
        });
        if (btn) {
            btn.className = 'sim-date-btn px-4 py-2 rounded-xl text-xs font-bold bg-[#0A7660] text-white shadow-xs cursor-pointer';
        }
    };

    // Toggle Slot Jam pada Step 2
    window.toggleSimulationSlot = function (btn, timeRange) {
        window.simState.slotTime = timeRange;
        document.querySelectorAll('.sim-slot-btn').forEach(b => {
            b.className = 'sim-slot-btn p-3 rounded-xl border border-emerald-300 bg-[#EFFBF3] text-left hover:bg-emerald-100 transition cursor-pointer text-xs';
            const check = b.querySelector('.sim-slot-check');
            if (check) check.remove();
        });

        btn.className = 'sim-slot-btn p-3 rounded-xl border-2 border-[#0A7660] bg-[#0A7660] text-white text-left transition cursor-pointer text-xs shadow-xs';
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

        // Transisi halus fade-in
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

    // Pindah Tab di dalam Modal
    window.switchHelpTab = function (tabName) {
        const tabs = ['tutorial', 'faq', 'contact'];
        tabs.forEach(t => {
            const pane = document.getElementById('help-pane-' + t);
            const btn = document.getElementById('tab-btn-' + t);
            if (pane) pane.classList.add('hidden');
            if (btn) {
                btn.className = 'tab-help-btn px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition flex items-center gap-2 bg-transparent text-[#5A6C5B] hover:text-[#1B2724] hover:bg-[#F4F7F5] cursor-pointer';
            }
        });

        const activePane = document.getElementById('help-pane-' + tabName);
        const activeBtn = document.getElementById('tab-btn-' + tabName);
        if (activePane) activePane.classList.remove('hidden');
        if (activeBtn) {
            activeBtn.className = 'tab-help-btn px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition flex items-center gap-2 bg-[#0A7660] text-white shadow-xs cursor-pointer';
        }
    };

    // Akordeon FAQ Toggle
    window.toggleHelpFaq = function (btn) {
        const body = btn.nextElementSibling;
        const chevron = btn.querySelector('.faq-chevron');
        if (!body) return;

        const isHidden = body.classList.contains('hidden');
        // Tutup item lain
        document.querySelectorAll('.faq-body').forEach(b => b.classList.add('hidden'));
        document.querySelectorAll('.faq-chevron').forEach(c => c.classList.remove('rotate-180'));

        if (isHidden) {
            body.classList.remove('hidden');
            if (chevron) chevron.classList.add('rotate-180');
        }
    };

    // Keyboard listener Escape & Backdrop click
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

    // Event listener delegasi untuk tombol apa saja yang memiliki atribut data-open-help-modal
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
