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
        :title="'Halo, ' . ($user?->name ?? auth()->user()?->name ?? 'Dimas Pratama')"
        subtitle="Pantau jadwal sewa lapangan dan kelola aktivitas olahragamu di sini."
        action-text="Booking Lapangan Baru"
        :action-url="url('/penyewa/lapangan')"
        action-id="btn-booking-baru"
    />

    {{-- =========================================================
         SECTION 1.5: Onboarding Coach Banner untuk Pengguna Baru (Saran 1 & 5)
    ========================================================= --}}
    <div id="penyewa-onboarding-sim-card" class="p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-[#06231D] via-[#094034] to-[#0A7660] text-white flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-sm border border-emerald-800/80">
        <div class="flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-[#DEEF25] text-[#06231D] flex items-center justify-center shrink-0 shadow-xs font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h3 class="text-sm font-bold text-white">Baru Pertama Kali di SportPlace?</h3>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-[#DEEF25]/20 text-[#DEEF25] border border-[#DEEF25]/30">Onboarding ± 1 Menit</span>
                </div>
                <p class="text-xs text-emerald-100/80 mt-0.5">Coba simulasi interaktif alur sewa lapangan agar Anda memahami proses pemilihan jam hingga tiket QR terbit.</p>
            </div>
        </div>
        <div class="flex items-center gap-2.5 shrink-0">
            <button type="button" onclick="openBookingSimulation()" class="px-4 py-2 rounded-xl bg-[#DEEF25] hover:bg-[#cde01e] text-[#06231D] text-xs font-bold transition shadow-xs cursor-pointer">
                Mulai Simulasi
            </button>
            <button type="button" onclick="dismissOnboardingSimCard()" class="p-2 rounded-xl text-emerald-300 hover:text-white hover:bg-white/10 transition cursor-pointer" title="Tutup pemberitahuan">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (localStorage.getItem('sportplace_sim_banner_dismissed') === 'true') {
                const card = document.getElementById('penyewa-onboarding-sim-card');
                if (card) card.classList.add('hidden');
            }
        });
        function dismissOnboardingSimCard() {
            const card = document.getElementById('penyewa-onboarding-sim-card');
            if (card) card.classList.add('hidden');
            localStorage.setItem('sportplace_sim_banner_dismissed', 'true');
        }
    </script>

    {{-- =========================================================
         SECTION 2: 3 Metrics / Stat Cards Row (Data dari Database)
    ========================================================= --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

        {{-- Stat Card 1: Booking Aktif (Col 1) --}}
        <x-penyewa.stat-card
            label="Booking Aktif"
            :value="$stats['active_count'] ?? 0"
            :badge="$stats['active_badge'] ?? 'Hari ini'"
            badge-type="pill-green"
            :note="$stats['active_note'] ?? 'Tidak ada booking aktif'"
            note-icon="dot"
            icon="flag"
        />

        {{-- Stat Card 2: Booking Mendatang (Col 2) --}}
        <x-penyewa.stat-card
            label="Booking Mendatang"
            :value="$stats['upcoming_count'] ?? 0"
            :badge="$stats['upcoming_badge'] ?? 'Jadwal siap'"
            badge-type="text-gray"
            :note="$stats['upcoming_note'] ?? 'Terjadwal dalam 7 hari ke depan'"
            note-icon="calendar"
            icon="clock"
        />

        {{-- Stat Card 3: Total Booking Bulan Ini (Col 3) --}}
        <x-penyewa.stat-card
            label="Total Booking Bulan Ini"
            :value="$stats['month_count'] ?? 0"
            :badge="$stats['month_growth'] ?? '+0%'"
            badge-type="trend-green"
            :note="$stats['month_note'] ?? '0 booking dibanding bulan lalu'"
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

            <!-- Booking Cards 2x2 Grid (Data dari Database) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                @forelse ($bookings as $booking)
                    @php
                        $header = $booking->header;
                        $field = $booking->field;
                        $venue = $field?->venue;
                        $sportName = strtoupper($field?->sport?->nama_olahraga ?? 'FUTSAL');
                        $tgl = \Carbon\Carbon::parse($booking->tanggal_sewa);
                        $isPending = $header?->status === 'pending';
                        $isToday = $tgl->isToday();

                        // Tentukan status badge
                        if ($isPending) {
                            $statusType = 'pending';
                            $payDeadline = '45 menit lagi';
                            $payUrl = route('penyewa.pembayaran.review', ['id' => $booking->header_id]);
                        } elseif ($isToday) {
                            $statusType = 'ongoing';
                            $payDeadline = null;
                            $payUrl = null;
                        } else {
                            $statusType = 'confirmed';
                            $payDeadline = null;
                            $payUrl = null;
                        }

                        // Format waktu
                        $timeStart = \Carbon\Carbon::parse($booking->jam_mulai)->format('H:i');
                        $timeEnd = \Carbon\Carbon::parse($booking->jam_selesai)->format('H:i');

                        // Lokasi
                        $location = $venue?->alamat_lengkap ?? ($venue?->city?->nama_kota ?? 'Lokasi');

                        // Resolusi Foto Lapangan yang Tepat
                        $primaryPhoto = $field?->photos?->firstWhere('is_primary', 1) ?? $field?->photos?->first();
                        $photoPath = $primaryPhoto?->foto_url;
                        $sportSlug = strtolower($field?->sport?->slug ?? 'futsal');

                        $defaultSportImages = [
                            'futsal'      => asset('images/courts/futsal.jpg'),
                            'badminton'   => asset('images/courts/badminton.jpg'),
                            'tenis'       => asset('images/courts/tennis.jpg'),
                            'basket'      => asset('images/courts/basketball.jpg'),
                            'mini-soccer' => asset('images/courts/futsal.jpg'),
                        ];

                        if ($photoPath) {
                            if (str_starts_with($photoPath, 'http://') || str_starts_with($photoPath, 'https://')) {
                                $imageSrc = $photoPath;
                            } elseif (str_starts_with($photoPath, 'images/')) {
                                $imageSrc = asset($photoPath);
                            } else {
                                $imageSrc = asset('storage/' . $photoPath);
                            }
                        } else {
                            $imageSrc = $defaultSportImages[$sportSlug] ?? asset('images/courts/futsal.jpg');
                        }

                        // Link detail mengarah ke Detail Booking & Tiket Check-in
                        $bookingDetailUrl = route('penyewa.booking.detail', ['id' => $booking->header_id]);
                    @endphp

                    <x-penyewa.booking-card
                        :sport="$sportName"
                        :status="$statusType"
                        :venue-name="$venue?->nama_venue ?? 'Venue Olahraga'"
                        :field-name="$field?->nama_lapangan ?? 'Lapangan'"
                        :date="$tgl"
                        :time-start="$timeStart"
                        :time-end="$timeEnd"
                        :location="$location"
                        :price="$booking->harga_slot ?? $header?->total_tagihan ?? 0"
                        :detail-url="$bookingDetailUrl"
                        :pay-url="$payUrl"
                        :pay-deadline="$payDeadline"
                        :image-src="$imageSrc"
                    />
                @empty
                    <div class="col-span-full bg-white rounded-2xl border border-slate-200/90 p-8 text-center space-y-3">
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h4 class="text-sm font-bold text-slate-900">Belum Ada Booking Aktif atau Mendatang</h4>
                        <p class="text-xs text-slate-500 max-w-sm mx-auto">Mulai cari lapangan favoritmu dan jadwalkan aktivitas olahraga sekarang.</p>
                        <a href="{{ url('/penyewa/lapangan') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#0B7B69] text-white text-xs font-semibold hover:bg-[#086354] transition">
                            Cari & Sewa Lapangan
                        </a>
                    </div>
                @endforelse
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
                        href="{{ url('/penyewa/riwayat?tab=selesai') }}"
                        label="Beri Ulasan Lapangan"
                        icon="star"
                    />
                </div>
            </div>
        </div>

    </div>

</div>
</x-layouts.penyewa>
