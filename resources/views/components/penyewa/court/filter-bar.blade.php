@props([
    'sportCounts'    => [],
    'sportsInDb'     => null,
    'cities'         => null,
    'currentSport'   => 'all',
    'currentCity'    => 'all',
    'currentSurface' => 'all',
    'currentSort'    => 'popular',
    'search'         => '',
    'totalCount'     => 0,
])

@php
    $sportIcons = [
        'all'         => '🏆',
        'futsal'      => '⚽',
        'badminton'   => '🏸',
        'tenis'       => '🎾',
        'basket'      => '🏀',
        'mini-soccer' => '⚽',
    ];

    // Build sports list from database or fallback
    $sportsList = [
        'all' => ['label' => 'Semua Olahraga', 'icon' => '🏆', 'slug' => 'all'],
    ];

    if ($sportsInDb && $sportsInDb->isNotEmpty()) {
        foreach ($sportsInDb as $s) {
            $sportsList[$s->slug] = [
                'label' => $s->nama_olahraga,
                'icon'  => $sportIcons[$s->slug] ?? '🏅',
                'slug'  => $s->slug,
            ];
        }
    } else {
        $sportsList = [
            'all'       => ['label' => 'Semua Olahraga', 'icon' => '🏆', 'slug' => 'all'],
            'futsal'    => ['label' => 'Futsal', 'icon' => '⚽', 'slug' => 'futsal'],
            'badminton' => ['label' => 'Badminton', 'icon' => '🏸', 'slug' => 'badminton'],
            'tenis'     => ['label' => 'Tenis', 'icon' => '🎾', 'slug' => 'tenis'],
            'basket'    => ['label' => 'Basket', 'icon' => '🏀', 'slug' => 'basket'],
        ];
    }

    $hasActiveFilters = !empty($search) || $currentSport !== 'all' || $currentCity !== 'all' || $currentSurface !== 'all' || $currentSort !== 'popular';
@endphp

<div class="space-y-4">
    <!-- 1. Category Pills Strip -->
    <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1">
        @foreach ($sportsList as $slug => $data)
            @php
                $isActive = $currentSport === $slug;
                $count = $sportCounts[$slug] ?? 0;
            @endphp
            <a
                href="{{ route('penyewa.lapangan', array_merge(request()->query(), ['sport' => $slug])) }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-semibold whitespace-nowrap transition duration-150 {{ $isActive ? 'bg-[#0B7B69] text-white shadow-xs' : 'bg-white hover:bg-slate-100 text-slate-700 border border-slate-200/80 shadow-2xs' }}"
            >
                <span>{{ $data['icon'] }}</span>
                <span>{{ $data['label'] }}</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold {{ $isActive ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500' }}">
                    {{ $count }}
                </span>
            </a>
        @endforeach
    </div>

    <!-- 2. Search & Filter Controls Toolbar -->
    <form method="GET" action="{{ route('penyewa.lapangan') }}" class="bg-white rounded-2xl border border-slate-200/90 p-3 sm:p-4 shadow-xs">
        @if ($currentSport !== 'all')
            <input type="hidden" name="sport" value="{{ $currentSport }}">
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-center">
            <!-- Search Keyword (5 cols on lg) -->
            <div class="lg:col-span-5 relative">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Cari nama arena, lapangan, atau lokasi..."
                    class="w-full bg-slate-50 border border-slate-200/80 hover:border-emerald-300 focus:bg-white focus:border-[#0B7B69] focus:ring-2 focus:ring-emerald-500/20 rounded-xl pl-10 pr-4 py-2.5 text-xs text-slate-800 placeholder-slate-400 outline-none transition"
                >
            </div>

            <!-- City Filter (2 cols on lg) -->
            <div class="lg:col-span-2">
                <select
                    name="city"
                    onchange="this.form.submit()"
                    class="w-full bg-slate-50 border border-slate-200/80 hover:border-emerald-300 focus:bg-white focus:border-[#0B7B69] rounded-xl px-3 py-2.5 text-xs text-slate-700 outline-none transition cursor-pointer"
                >
                    <option value="all" {{ $currentCity === 'all' ? 'selected' : '' }}>Semua Lokasi</option>
                    @if ($cities && $cities->isNotEmpty())
                        @foreach ($cities as $c)
                            <option value="{{ strtolower($c->nama_kota) }}" {{ $currentCity === strtolower($c->nama_kota) ? 'selected' : '' }}>
                                {{ $c->nama_kota }}
                            </option>
                        @endforeach
                    @else
                        <option value="jakarta selatan" {{ $currentCity === 'jakarta selatan' ? 'selected' : '' }}>Jakarta Selatan</option>
                        <option value="bandung" {{ $currentCity === 'bandung' ? 'selected' : '' }}>Bandung</option>
                        <option value="surabaya" {{ $currentCity === 'surabaya' ? 'selected' : '' }}>Surabaya</option>
                    @endif
                </select>
            </div>

            <!-- Surface Filter (2 cols on lg) -->
            <div class="lg:col-span-2">
                <select
                    name="surface"
                    onchange="this.form.submit()"
                    class="w-full bg-slate-50 border border-slate-200/80 hover:border-emerald-300 focus:bg-white focus:border-[#0B7B69] rounded-xl px-3 py-2.5 text-xs text-slate-700 outline-none transition cursor-pointer"
                >
                    <option value="all" {{ $currentSurface === 'all' ? 'selected' : '' }}>Semua Lantai</option>
                    <option value="vinyl" {{ $currentSurface === 'vinyl' ? 'selected' : '' }}>Vinyl Mat</option>
                    <option value="karpet" {{ $currentSurface === 'karpet' ? 'selected' : '' }}>Karpet BWF</option>
                    <option value="interlock" {{ $currentSurface === 'interlock' ? 'selected' : '' }}>Interlock</option>
                    <option value="parquet" {{ $currentSurface === 'parquet' ? 'selected' : '' }}>Parquet Kayu</option>
                    <option value="hard_court" {{ $currentSurface === 'hard_court' ? 'selected' : '' }}>Hard Court</option>
                    <option value="sintetis" {{ $currentSurface === 'sintetis' ? 'selected' : '' }}>Rumput Sintetis</option>
                </select>
            </div>

            <!-- Sort By (2 cols on lg) -->
            <div class="lg:col-span-2">
                <select
                    name="sort"
                    onchange="this.form.submit()"
                    class="w-full bg-slate-50 border border-slate-200/80 hover:border-emerald-300 focus:bg-white focus:border-[#0B7B69] rounded-xl px-3 py-2.5 text-xs text-slate-700 outline-none transition cursor-pointer"
                >
                    <option value="popular" {{ $currentSort === 'popular' ? 'selected' : '' }}>Paling Populer</option>
                    <option value="rating" {{ $currentSort === 'rating' ? 'selected' : '' }}>Rating Tertinggi</option>
                    <option value="price_low" {{ $currentSort === 'price_low' ? 'selected' : '' }}>Harga: Terendah</option>
                    <option value="price_high" {{ $currentSort === 'price_high' ? 'selected' : '' }}>Harga: Tertinggi</option>
                </select>
            </div>

            <!-- Submit / Reset (1 col on lg) -->
            <div class="lg:col-span-1 flex items-center gap-1.5">
                <button
                    type="submit"
                    class="w-full py-2.5 rounded-xl bg-[#0B7B69] hover:bg-[#086354] active:scale-95 text-white text-xs font-semibold shadow-xs transition flex items-center justify-center"
                    title="Cari"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>

                @if ($hasActiveFilters)
                    <a
                        href="{{ route('penyewa.lapangan') }}"
                        class="p-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 transition"
                        title="Reset Filter"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>
