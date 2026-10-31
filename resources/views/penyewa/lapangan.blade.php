{{--
    Halaman Eksplorasi & Daftar Lapangan — SportPlace
    Layout  : x-layouts.penyewa (Top navbar-based)
    Features: Category strip, multi-facet filtering (sport, city, surface, sort, search), responsive court grid, pagination
--}}
<x-layouts.penyewa title="Cari & Booking Lapangan — SportPlace" active="lapangan">
<div class="space-y-6">

    {{-- =========================================================
         SECTION 1: Header Halaman
    ========================================================= --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                Daftar Lapangan Olahraga
            </h1>
            <p class="text-sm text-slate-500 mt-1">
                Pilih lapangan favorit Anda untuk melihat detail fasilitas, tarif, dan memilih jadwal booking.
            </p>
        </div>
    </div>

    {{-- =========================================================
         SECTION 2: Filter Toolbar (Sport, City, Surface, Sort, Search)
    ========================================================= --}}
    <x-penyewa.court.filter-bar
        :sport-counts="$sportCounts"
        :sports-in-db="$sportsInDb"
        :cities="$cities"
        :current-sport="$currentSport"
        :current-city="$currentCity"
        :current-surface="$currentSurface"
        :current-sort="$currentSort"
        :search="$search"
        :total-count="$totalCount"
    />

    {{-- =========================================================
         SECTION 3: Status Hasil & Grid Lapangan
    ========================================================= --}}
    <div class="space-y-4">
        <!-- Results Counter -->
        <div class="flex items-center justify-between text-xs text-slate-500 font-medium">
            <p>
                Menampilkan <span class="font-bold text-slate-900">{{ $totalCount }}</span> lapangan olahraga
                @if (!empty($search))
                    untuk kata kunci "<span class="font-bold text-emerald-700">{{ $search }}</span>"
                @endif
            </p>
        </div>

        <!-- Court Cards Grid (Responsive 1/2/3/4 cols) -->
        @if ($courts->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach ($courts as $c)
                    <x-penyewa.court.card
                        :id="$c['id']"
                        :venue-name="$c['venue_name']"
                        :field-name="$c['field_name']"
                        :sport="$c['sport']"
                        :court-type="$c['court_type']"
                        :location="$c['location']"
                        :rating="$c['rating']"
                        :reviews-count="$c['reviews_count']"
                        :price-per-hour="$c['price_per_hour']"
                        :facilities="$c['facilities']"
                        :open-hours="$c['open_hours']"
                        :image-src="$c['image_src']"
                        :booking-url="url('/penyewa/lapangan/' . $c['id'])"
                    />
                @endforeach
            </div>

            <!-- Pagination Bar -->
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden mt-6">
                <x-penyewa.pagination
                    :from="1"
                    :to="$totalCount"
                    :total="$totalCount"
                    :current-page="1"
                    :last-page="1"
                    unit="lapangan"
                />
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-2xl border border-slate-200/90 p-12 text-center shadow-xs">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-emerald-50 text-[#0B7B69] flex items-center justify-center mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-800">Tidak ada lapangan yang cocok</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    Coba ubah kata kunci pencarian, ganti kategori olahraga, atau klik reset filter untuk melihat semua lapangan.
                </p>
                <div class="mt-5">
                    <a
                        href="{{ route('penyewa.lapangan') }}"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#0B7B69] hover:bg-[#086354] text-white text-xs font-semibold shadow-xs transition"
                    >
                        <span>Reset Semua Filter</span>
                    </a>
                </div>
            </div>
        @endif
    </div>

</div>
</x-layouts.penyewa>
