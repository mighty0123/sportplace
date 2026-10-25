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
                    'semua'     => 'Semua (' . ($counts['semua'] ?? 12) . ')',
                    'pending'   => 'Pending (' . ($counts['pending'] ?? 1) . ')',
                    'confirmed' => 'Confirmed (' . ($counts['confirmed'] ?? 2) . ')',
                    'completed' => 'Completed (' . ($counts['completed'] ?? 8) . ')',
                    'cancelled' => 'Cancelled (' . ($counts['cancelled'] ?? 1) . ')',
                ];
            @endphp
            @foreach ($tabs as $key => $label)
                <a href="{{ route('penyewa.riwayat', array_merge(request()->query(), ['status' => $key])) }}"
                   class="px-4 py-2 rounded-full text-xs font-semibold whitespace-nowrap transition duration-150 {{ $currentStatus === $key ? 'bg-[#0B7B69] text-white shadow-xs' : 'bg-slate-100/90 hover:bg-slate-200 text-slate-600' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <!-- Month Filter Dropdown -->
        <div class="shrink-0">
            <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white border border-slate-200/90 text-xs font-semibold text-slate-700 shadow-2xs hover:bg-slate-50 transition cursor-pointer">
                <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span>Bulan Ini: Okt 2024</span>
                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
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
            :current-page="1"
            :last-page="2"
            unit="pesanan"
        />
    </div>

</div>
</x-layouts.penyewa>
