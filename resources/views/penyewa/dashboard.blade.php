{{--
    Dashboard Penyewa — SportPlace
    Layout  : x-layouts.penyewa (Navbar-based replacing sidebar)
    Design  : Sesuai referensi Figma (Top Navbar, Greeting + CTA, 3 Stat Cards, 3-Col Desktop Grid aligning Booking Cards & Aksi Cepat)
--}}
<x-layouts.penyewa title="Dashboard — SportPlace" active="dashboard">
<div class="space-y-8">

    {{-- =========================================================
         SECTION 1: Greeting Header & Primary CTA Button
    ========================================================= --}}
    <x-penyewa.page-header
        :title="'Halo, ' . (auth()->user()?->name ?? 'Dimas Pratama') . ' 👋'"
        subtitle="Pantau jadwal sewa lapangan dan kelola aktivitas olahragamu di sini."
        action-text="Booking Lapangan Baru"
        :action-url="url('/penyewa/lapangan')"
        action-id="btn-booking-baru"
    />

    {{-- =========================================================
         SECTION 2: 3 Metrics / Stat Cards Row
    ========================================================= --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

        {{-- Stat Card 1: Booking Aktif (Col 1) --}}
        <x-penyewa.stat-card
            label="Booking Aktif"
            value="2"
            badge="Hari ini"
            badge-type="pill-green"
            note="1 lapangan sedang berlangsung"
            note-icon="dot"
            icon="flag"
        />

        {{-- Stat Card 2: Booking Mendatang (Col 2) --}}
        <x-penyewa.stat-card
            label="Booking Mendatang"
            value="4"
            badge="jadwal siap"
            badge-type="text-gray"
            note="Terjadwal dalam 7 hari ke depan"
            note-icon="calendar"
            icon="clock"
        />

        {{-- Stat Card 3: Total Booking Bulan Ini (Col 3) --}}
        <x-penyewa.stat-card
            label="Total Booking Bulan Ini"
            value="11"
            badge="+37%"
            badge-type="trend-green"
            note="+3 booking dibanding bulan lalu"
            note-icon="check"
            icon="trophy"
        />

    </div>

    {{-- =========================================================
         SECTION 3: Main Content (3 Columns Grid on Desktop matching Stat Cards)
    ========================================================= --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

        {{-- ---- Left 2 Columns: Booking Aktif & Mendatang (Col 1 & Col 2) ---- --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="flex items-center justify-between mb-4 h-7">
                <h2 class="text-base sm:text-lg font-bold text-slate-900">Booking Aktif & Mendatang</h2>
                <a href="{{ url('/penyewa/riwayat') }}"
                   class="text-xs font-semibold text-[#0B7B69] hover:text-[#086354] hover:underline inline-flex items-center gap-1 transition">
                    Lihat semua
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            <!-- Booking Cards 2x2 Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                {{-- Booking 1: FUTSAL — Sedang Berlangsung --}}
                <x-penyewa.booking-card
                    sport="FUTSAL"
                    status="ongoing"
                    venue-name="Viva Arena Futsal"
                    field-name="Lapangan Vip"
                    date="Hari ini, 24 Okt 2024"
                    time-start="19:00"
                    time-end="21:00"
                    location="Renon, Denpasar"
                    :price="450000"
                    detail-url="{{ url('/penyewa/booking/1') }}"
                    :image-src="asset('images/courts/futsal.jpg')"
                />

                {{-- Booking 2: BADMINTON — Terkonfirmasi --}}
                <x-penyewa.booking-card
                    sport="BADMINTON"
                    status="confirmed"
                    venue-name="Smash Center Arena"
                    field-name="Lapangan A"
                    date="Sabtu, 26 Okt 2024"
                    time-start="08:00"
                    time-end="10:00"
                    location="Goa Gong, Jimbaran"
                    :price="240000"
                    detail-url="{{ url('/penyewa/booking/2') }}"
                    :image-src="asset('images/courts/badminton.jpg')"
                />

                {{-- Booking 3: TENIS — Pending Bayar --}}
                <x-penyewa.booking-card
                    sport="TENIS"
                    status="pending"
                    venue-name="Champion Tennis Club"
                    field-name="Court 2"
                    pay-deadline="45 menit lagi"
                    location="Kediri, Tabanan"
                    :price="350000"
                    pay-url="{{ url('/penyewa/booking/3/bayar') }}"
                    detail-url="{{ url('/penyewa/booking/3') }}"
                    :image-src="asset('images/courts/tennis.jpg')"
                />

                {{-- Booking 4: BASKET — Terkonfirmasi --}}
                <x-penyewa.booking-card
                    sport="BASKET"
                    status="confirmed"
                    venue-name="Hoops Hall Jakarta"
                    field-name="Full Court"
                    date="Selasa, 29 Okt 2024"
                    time-start="20:00"
                    time-end="22:00"
                    location="Kedonganan, Kuta Selatan"
                    :price="600000"
                    detail-url="{{ url('/penyewa/booking/4') }}"
                    :image-src="asset('images/courts/basketball.jpg')"
                />

            </div>
        </div>

        {{-- ---- Right 1 Column: Aksi Cepat (Col 3) ---- --}}
        <div class="lg:col-span-1 space-y-4">
            <!-- Header Spacer so Aksi Cepat card aligns with top of booking cards on desktop -->
            <div class="hidden lg:block h-7 mb-4"></div>

            <div class="bg-white rounded-2xl border border-slate-200/90 p-5 sm:p-6 shadow-xs sticky top-24">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-slate-900">Aksi Cepat</h3>
                    <div class="text-slate-400">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                </div>

                <div class="space-y-3">
                    <x-penyewa.quick-action
                        href="{{ url('/penyewa/lapangan') }}"
                        label="Cari & Sewa Lapangan"
                        icon="search"
                    />
                    <x-penyewa.quick-action
                        href="{{ url('/penyewa/riwayat') }}"
                        label="Riwayat Pemesanan"
                        icon="receipt"
                    />
                    <x-penyewa.quick-action
                        href="{{ url('/penyewa/ulasan') }}"
                        label="Beri Ulasan Lapangan"
                        icon="star"
                    />
                </div>

                {{-- Tip Box --}}
                <div class="mt-5 pt-4 border-t border-slate-100">
                    <div class="flex items-start gap-2.5 p-3 rounded-xl bg-emerald-50/60 border border-emerald-100 text-xs text-emerald-900">
                        <span class="text-base shrink-0 leading-none">💡</span>
                        <p class="leading-relaxed">
                            Booking lebih awal atau sewa multi-jam untuk mendapatkan promo dan slot favorit tanpa antre.
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
</x-layouts.penyewa>
