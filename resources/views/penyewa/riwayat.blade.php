{{--
    Halaman Riwayat Booking — SportPlace
    Layout  : x-layouts.penyewa (Navbar-based, no sidebar)
    Features: Tab filter status, search arena/kode, data table responsif, status badges, pagination
--}}
<x-layouts.penyewa title="Riwayat Booking — SportPlace" active="riwayat">
<div class="space-y-6">

    {{-- =========================================================
         SECTION 1: Header Halaman & Kolom Pencarian
    ========================================================= --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                Riwayat Booking
            </h1>
            <p class="text-sm text-slate-500 mt-1">
                Kelola dan tinjau seluruh status riwayat pemesanan lapangan Anda
            </p>
        </div>

        {{-- Search Input Toolbar --}}
        <form method="GET" action="{{ route('penyewa.riwayat') }}" class="relative w-full md:w-80 shrink-0">
            <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input
                type="text"
                name="search"
                value="{{ $search ?? '' }}"
                placeholder="Cari berdasarkan nama arena, kode booking..."
                class="w-full bg-white border border-slate-200/90 hover:border-emerald-300 focus:border-[#0B7B69] focus:ring-2 focus:ring-emerald-500/20 rounded-xl pl-10 pr-4 py-2.5 text-xs text-slate-800 placeholder-slate-400 shadow-2xs outline-none transition duration-150"
            >
            @if (!empty($currentStatus) && $currentStatus !== 'semua')
                <input type="hidden" name="status" value="{{ $currentStatus }}">
            @endif
            @if (!empty($selectedMonth) && $selectedMonth !== 'semua')
                <input type="hidden" name="bulan" value="{{ $selectedMonth }}">
            @endif
        </form>
    </div>

    {{-- =========================================================
         SECTION 2: Filter Tabs Status & Pilihan Periode Bulan
    ========================================================= --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <!-- Filter Tabs (Pill Buttons) -->
        <div class="flex items-center gap-1.5 sm:gap-2 overflow-x-auto no-scrollbar pb-1">
            @php
                $tabs = [
                    'semua'     => 'Semua (' . ($counts['semua'] ?? 0) . ')',
                    'pending'   => 'Pending (' . ($counts['pending'] ?? 0) . ')',
                    'confirmed' => 'Confirmed (' . ($counts['confirmed'] ?? 0) . ')',
                    'completed' => 'Completed (' . ($counts['completed'] ?? 0) . ')',
                    'cancelled' => 'Cancelled (' . ($counts['cancelled'] ?? 0) . ')',
                ];
            @endphp
            @foreach ($tabs as $key => $label)
                <a href="{{ route('penyewa.riwayat', array_merge(request()->query(), ['status' => $key, 'page' => 1])) }}"
                   class="px-4 py-2 rounded-full text-xs font-semibold whitespace-nowrap transition duration-150 {{ $currentStatus === $key ? 'bg-[#0B7B69] text-white shadow-xs' : 'bg-slate-100/90 hover:bg-slate-200 text-slate-600' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <!-- Month Filter Dropdown (Vanilla JS) -->
        <div class="relative shrink-0" id="month-filter-container">
            <button
                type="button"
                id="month-filter-btn"
                onclick="toggleMonthFilter(event)"
                class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border text-xs font-semibold shadow-2xs transition cursor-pointer select-none {{ !empty($hasMonthFilter) ? 'bg-emerald-50/70 border-emerald-300 text-[#0B7B69]' : 'bg-white border-slate-200/90 text-slate-700 hover:bg-slate-50' }}"
                aria-haspopup="true"
                aria-expanded="false"
            >
                <svg class="w-4 h-4 {{ !empty($hasMonthFilter) ? 'text-[#0B7B69]' : 'text-slate-500' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span>{{ $monthLabel ?? 'Filter' }}</span>
                <svg id="month-filter-chevron" class="w-3.5 h-3.5 {{ !empty($hasMonthFilter) ? 'text-[#0B7B69]' : 'text-slate-400' }} shrink-0 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <!-- Dropdown Menu -->
            <div
                id="month-filter-menu"
                class="hidden absolute right-0 mt-2 w-52 bg-white rounded-2xl shadow-xl border border-slate-100 py-1.5 z-30 divide-y divide-slate-100/80"
            >
                <div class="py-1">
                    <a href="{{ route('penyewa.riwayat', array_merge(request()->query(), ['bulan' => 'semua', 'page' => 1])) }}"
                       class="flex items-center justify-between px-3.5 py-2 text-xs font-semibold rounded-xl mx-1 transition {{ empty($selectedMonth) || $selectedMonth === 'semua' ? 'text-[#0B7B69] bg-emerald-50/70 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                        <span>Semua</span>
                        @if (empty($selectedMonth) || $selectedMonth === 'semua')
                            <svg class="w-4 h-4 text-[#0B7B69]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        @endif
                    </a>
                </div>

                <div class="py-1 max-h-56 overflow-y-auto">
                    @foreach (($monthOptions ?? []) as $val => $mLabel)
                        <a href="{{ route('penyewa.riwayat', array_merge(request()->query(), ['bulan' => $val, 'page' => 1])) }}"
                           class="flex items-center justify-between px-3.5 py-2 text-xs font-medium rounded-xl mx-1 transition {{ ($selectedMonth ?? '') === $val ? 'text-[#0B7B69] bg-emerald-50/70 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                            <span>{{ $mLabel }}</span>
                            @if (($selectedMonth ?? '') === $val)
                                <svg class="w-4 h-4 text-[#0B7B69]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- =========================================================
         SECTION 3: Tabel Riwayat Pemesanan Lapangan
    ========================================================= --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <!-- Table Header -->
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th scope="col" class="py-3.5 px-5 sm:px-6">LAPANGAN</th>
                        <th scope="col" class="py-3.5 px-4 sm:px-5">TANGGAL & JAM</th>
                        <th scope="col" class="py-3.5 px-4 sm:px-5">TOTAL HARGA</th>
                        <th scope="col" class="py-3.5 px-4 sm:px-5">STATUS</th>
                        <th scope="col" class="py-3.5 px-5 sm:px-6 text-right">AKSI</th>
                    </tr>
                </thead>

                <!-- Table Rows -->
                <tbody class="divide-y divide-slate-100">
                    @forelse ($bookings as $b)
                        <x-penyewa.history-row
                            :sport="$b['sport']"
                            :venue-name="$b['venue_name']"
                            :field-name="$b['field_name']"
                            :booking-code="$b['booking_code']"
                            :location="$b['location']"
                            :date="$b['date']"
                            :time="$b['time']"
                            :price="$b['price']"
                            :price-sub="$b['price_sub']"
                            :price-sub-type="$b['price_sub_type']"
                            :status="$b['status']"
                            :action-type="$b['action_type']"
                            :detail-url="$b['detail_url'] ?? '#'"
                            :pay-url="$b['pay_url'] ?? '#'"
                            :review-url="$b['review_url'] ?? '#'"
                            :image-src="$b['image_src']"
                        />
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 px-6 text-center text-slate-400">
                                <x-empty-state
                                    title="Tidak ada riwayat booking"
                                    description="Tidak ditemukan riwayat pemesanan yang sesuai dengan filter ini."
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- =========================================================
             SECTION 4: Pagination Bar
        ========================================================= --}}
        <x-penyewa.pagination
            :from="$from"
            :to="$to"
            :total="$total"
            :current-page="$currentPage ?? 1"
            :last-page="$lastPage ?? 1"
            unit="pesanan"
        />
    </div>

</div>

<script>
    function toggleMonthFilter(event) {
        if (event) {
            event.stopPropagation();
        }
        const menu = document.getElementById('month-filter-menu');
        const btn = document.getElementById('month-filter-btn');
        const chevron = document.getElementById('month-filter-chevron');
        if (!menu) return;

        const isHidden = menu.classList.contains('hidden');
        if (isHidden) {
            menu.classList.remove('hidden');
            if (btn) btn.setAttribute('aria-expanded', 'true');
            if (chevron) chevron.classList.add('rotate-180');
        } else {
            menu.classList.add('hidden');
            if (btn) btn.setAttribute('aria-expanded', 'false');
            if (chevron) chevron.classList.remove('rotate-180');
        }
    }

    // Tutup dropdown saat klik di luar
    document.addEventListener('click', function (e) {
        const container = document.getElementById('month-filter-container');
        const menu = document.getElementById('month-filter-menu');
        const btn = document.getElementById('month-filter-btn');
        const chevron = document.getElementById('month-filter-chevron');

        if (container && !container.contains(e.target) && menu && !menu.classList.contains('hidden')) {
            menu.classList.add('hidden');
            if (btn) btn.setAttribute('aria-expanded', 'false');
            if (chevron) chevron.classList.remove('rotate-180');
        }
    });

    // Tutup dengan tombol Escape
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            const menu = document.getElementById('month-filter-menu');
            const btn = document.getElementById('month-filter-btn');
            const chevron = document.getElementById('month-filter-chevron');
            if (menu && !menu.classList.contains('hidden')) {
                menu.classList.add('hidden');
                if (btn) btn.setAttribute('aria-expanded', 'false');
                if (chevron) chevron.classList.remove('rotate-180');
            }
        }
    });
</script>
</x-layouts.penyewa>
