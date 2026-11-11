<div class="space-y-6">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs font-medium text-slate-500" aria-label="Breadcrumb">
        <a href="{{ route('mitra.dashboard') }}" class="hover:text-emerald-700 transition">SportPlace Mitra</a>
        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-slate-800 font-semibold">Venue & Lapangan</span>
    </nav>

    <!-- Header Halaman -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-1.5 text-xs font-bold tracking-wider text-emerald-800 uppercase mb-1">
                <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <span>Venue & Lapangan</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Manajemen Venue</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola lokasi usahamu dan lapangan olahraga di dalamnya secara terpusat.</p>
        </div>

        <button
            type="button"
            wire:click="openCreateModal"
            class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-full bg-[#0B7B69] hover:bg-[#086354] active:scale-95 text-white text-sm font-semibold shadow-sm hover:shadow transition duration-150 min-h-[44px] cursor-pointer shrink-0"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Tambah Venue</span>
        </button>
    </div>

    <!-- Banner Informasi Struktur Hirarki -->
    <div x-data="{ showBanner: true }" x-show="showBanner" class="rounded-3xl bg-[#E8F5F1] border border-[#B2DFDB] p-5 sm:p-6 flex items-start justify-between gap-4 shadow-xs">
        <div class="flex items-start gap-3.5">
            <div class="w-11 h-11 rounded-2xl bg-[#D4ECE6] text-[#0B7B69] flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-slate-900">Informasi Struktur Hirarki SportPlace</h3>
                <p class="text-xs sm:text-sm text-slate-600 mt-1 leading-relaxed">
                    Setiap <strong class="text-slate-800">Venue</strong> dapat menaungi beberapa <strong class="text-slate-800">Lapangan</strong> dengan jadwal operasional, fasilitas, dan tarif sewa masing-masing. Klik <strong class="text-[#0B7B69]">"Kelola Lapangan"</strong> untuk mengatur unit lapangan di dalam venue terkait.
                </p>
            </div>
        </div>
        <button type="button" @click="showBanner = false" class="text-slate-400 hover:text-slate-600 transition p-1 cursor-pointer shrink-0" aria-label="Tutup banner informasi">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <!-- Metric Stat Cards (4 Kartu) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/90 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Venue</span>
                <div class="w-10 h-10 rounded-2xl bg-slate-100 text-slate-700 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-bold text-slate-900 tabular-nums">{{ $venueStats['total'] }}</span>
                <span class="text-xs font-medium text-slate-500">terdaftar</span>
            </div>
            <div class="mt-4 h-1 w-full bg-[#06231B] rounded-full"></div>
        </div>

        <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/90 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Venue Aktif</span>
                <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-bold text-slate-900 tabular-nums">{{ $venueStats['aktif'] }}</span>
                <span class="text-xs font-medium text-emerald-600">beroperasi</span>
            </div>
            <div class="mt-4 h-1 w-full bg-[#0B7B69] rounded-full"></div>
        </div>

        <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/90 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Menunggu Review</span>
                <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-bold text-slate-900 tabular-nums">{{ $venueStats['pending_review'] }}</span>
                <span class="text-xs font-medium text-blue-600">diajukan</span>
            </div>
            <div class="mt-4 h-1 w-full bg-blue-500 rounded-full"></div>
        </div>

        <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/90 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Butuh Perbaikan</span>
                <div class="w-10 h-10 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-bold text-slate-900 tabular-nums">{{ $venueStats['rejected'] }}</span>
                <span class="text-xs font-medium text-rose-600">ditolak admin</span>
            </div>
            <div class="mt-4 h-1 w-full bg-rose-500 rounded-full"></div>
        </div>
    </div>

    <!-- Filter & Search Bar (Reaktif Livewire) -->
    <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/90 shadow-xs space-y-4">
        <div class="flex flex-col md:flex-row items-stretch md:items-center gap-3">
            <!-- Text Search -->
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari nama venue atau lokasi..."
                    class="w-full pl-10 pr-4 py-2.5 rounded-2xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-[#0B7B69] transition min-h-[44px]"
                >
            </div>

            <!-- Dropdown Wilayah/Kota -->
            <div class="relative w-full md:w-64">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <select
                    wire:model.live="cityId"
                    class="w-full pl-10 pr-8 py-2.5 rounded-2xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-[#0B7B69] transition appearance-none bg-white min-h-[44px] cursor-pointer"
                >
                    <option value="">Semua Wilayah</option>
                    @foreach ($citiesList as $city)
                        <option value="{{ $city->id }}">{{ $city->nama_kota }}</option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </div>

            @if ($search !== '' || $cityId !== '' || $status !== 'semua')
                <button
                    type="button"
                    wire:click="resetFilters"
                    class="px-4 py-2.5 rounded-full text-slate-500 hover:text-slate-800 hover:bg-slate-100 text-sm font-semibold transition text-center min-h-[44px] flex items-center justify-center cursor-pointer"
                >
                    Reset
                </button>
            @endif
        </div>

        <!-- Status Tabs / Pills -->
        <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pt-1">
            @php
                $tabItems = [
                    'semua' => ['label' => 'Semua', 'count' => $venueStats['total'], 'activeClass' => 'bg-[#E8F5F1] text-[#0B7B69] border border-emerald-300', 'badgeClass' => 'bg-[#0B7B69] text-white'],
                    'aktif' => ['label' => 'Disetujui', 'count' => $venueStats['aktif'], 'activeClass' => 'bg-emerald-100 text-emerald-800 border border-emerald-300', 'badgeClass' => 'bg-emerald-600 text-white'],
                    'pending_review' => ['label' => 'Menunggu Verifikasi', 'count' => $venueStats['pending_review'], 'activeClass' => 'bg-blue-100 text-blue-800 border border-blue-300', 'badgeClass' => 'bg-blue-600 text-white'],
                    'rejected' => ['label' => 'Ditolak', 'count' => $venueStats['rejected'], 'activeClass' => 'bg-rose-100 text-rose-800 border border-rose-300', 'badgeClass' => 'bg-rose-600 text-white'],
                    'draft' => ['label' => 'Draft', 'count' => $venueStats['draft'], 'activeClass' => 'bg-slate-200 text-slate-800 border border-slate-400', 'badgeClass' => 'bg-slate-700 text-white'],
                    'nonaktif' => ['label' => 'Nonaktif', 'count' => $venueStats['nonaktif'], 'activeClass' => 'bg-amber-100 text-amber-800 border border-amber-300', 'badgeClass' => 'bg-amber-600 text-white'],
                ];
            @endphp

            @foreach ($tabItems as $tabKey => $tab)
                <button
                    type="button"
                    wire:click="setStatusFilter('{{ $tabKey }}')"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-semibold whitespace-nowrap transition cursor-pointer {{ $status === $tabKey ? $tab['activeClass'] : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                >
                    <span>{{ $tab['label'] }}</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] {{ $status === $tabKey ? $tab['badgeClass'] : 'bg-slate-200 text-slate-600' }}">{{ $tab['count'] }}</span>
                </button>
            @endforeach
        </div>
    </div>

    <!-- Venue Cards Grid -->
    <div wire:loading.class="opacity-50 pointer-events-none" wire:target="search,cityId,status,setStatusFilter,resetFilters">
        @if ($venuesList->isEmpty())
            <div class="bg-white rounded-3xl p-12 text-center border border-slate-200/90 shadow-xs">
                <div class="mx-auto w-16 h-16 rounded-full bg-emerald-50 text-[#0B7B69] flex items-center justify-center mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900">Belum Ada Venue</h3>
                <p class="text-sm text-slate-500 max-w-md mx-auto mt-1 leading-relaxed">
                    @if ($search !== '' || $cityId !== '' || $status !== 'semua')
                        Tidak ada venue yang sesuai dengan filter pencarian Anda. Silakan coba atur ulang filter.
                    @else
                        Mulai kelola bisnis fasilitas olahragamu dengan menambahkan lokasi venue pertama.
                    @endif
                </p>
                <div class="mt-6 flex justify-center gap-3">
                    @if ($search !== '' || $cityId !== '' || $status !== 'semua')
                        <button type="button" wire:click="resetFilters" class="px-5 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold transition cursor-pointer min-h-[44px]">
                            Reset Filter
                        </button>
                    @else
                        <button type="button" wire:click="openCreateModal" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-[#0B7B69] hover:bg-[#086354] text-white text-sm font-semibold shadow-sm transition min-h-[44px] cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Tambah Venue Pertama</span>
                        </button>
                    @endif
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($venuesList as $venue)
                    <div wire:key="venue-{{ $venue->id }}" class="bg-white rounded-3xl border border-slate-200/90 shadow-xs hover:shadow-md transition duration-200 flex flex-col overflow-hidden">
                        
                        <!-- Header Visual Venue -->
                        <div class="relative h-48 bg-linear-to-tr from-slate-800 to-slate-700 overflow-hidden">
                            <div class="absolute inset-0 bg-cover bg-center opacity-85" style="background-image: url('https://images.unsplash.com/photo-1574629810360-7efbbe195018?q=80&w=800&auto=format&fit=crop');"></div>
                            <div class="absolute inset-0 bg-linear-to-t from-slate-950/85 via-slate-950/40 to-transparent"></div>

                            <!-- Status Badge -->
                            <div class="absolute top-3 inset-x-3 flex items-center justify-between">
                                @if ($venue->status_vanue === 'aktif')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-white/95 text-emerald-800 shadow-xs border border-emerald-100 backdrop-blur-xs">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        <span>Disetujui & Aktif</span>
                                    </span>
                                @elseif ($venue->status_vanue === 'pending_review')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-white/95 text-blue-800 shadow-xs border border-blue-100 backdrop-blur-xs">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>Menunggu Verifikasi</span>
                                    </span>
                                @elseif ($venue->status_vanue === 'rejected')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-white/95 text-rose-800 shadow-xs border border-rose-100 backdrop-blur-xs">
                                        <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        <span>Ditolak</span>
                                    </span>
                                @elseif ($venue->status_vanue === 'draft')
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-white/95 text-slate-700 shadow-xs border border-slate-200 backdrop-blur-xs">
                                        <span>Draft</span>
                                    </span>
                                @elseif ($venue->status_vanue === 'nonaktif')
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-white/95 text-amber-800 shadow-xs border border-amber-200 backdrop-blur-xs">
                                        <span>Nonaktif</span>
                                    </span>
                                @endif

                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-black/40 text-white backdrop-blur-xs">
                                    <svg class="w-3.5 h-3.5 text-amber-400 fill-amber-400" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    <span>4.8</span>
                                </span>
                            </div>

                            <!-- Bottom Tag -->
                            <div class="absolute bottom-3 inset-x-4 text-white">
                                <span class="text-[11px] font-medium text-slate-300 block">
                                    @if($venue->price_range) Mulai dari @else Jam Operasional @endif
                                </span>
                                <div class="text-base font-bold tracking-tight tabular-nums">
                                    {{ $venue->price_range ? $venue->price_range . ' / jam' : $venue->jam_operasional }}
                                </div>
                            </div>
                        </div>

                        <!-- Card Content -->
                        <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 line-clamp-1" title="{{ $venue->nama_venue }}">
                                    {{ $venue->nama_venue }}
                                </h3>

                                <div class="mt-1 flex items-start gap-1.5 text-xs text-slate-500">
                                    <svg class="w-4 h-4 text-slate-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span class="line-clamp-1" title="{{ $venue->alamat_lengkap }}, {{ $venue->city?->nama_kota }}">
                                        {{ $venue->alamat_lengkap }}, {{ $venue->city?->nama_kota }}
                                    </span>
                                </div>

                                <div class="mt-3 flex items-center gap-1.5 flex-wrap">
                                    @forelse ($venue->sports as $sportName)
                                        <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-medium">
                                            {{ $sportName }}
                                        </span>
                                    @empty
                                        <span class="text-[11px] text-slate-400 italic">Belum ada unit lapangan</span>
                                    @endforelse
                                </div>

                                <div class="mt-4 p-3.5 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                        </svg>
                                        <span class="text-xs font-semibold text-slate-800">{{ $venue->fields->count() }} Lapangan</span>
                                    </div>
                                    @if ($venue->status_vanue === 'aktif')
                                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-xs font-medium border border-emerald-200">
                                            {{ $venue->fields->where('status_lapangan', 'aktif')->count() }} Aktif Disewakan
                                        </span>
                                    @elseif ($venue->status_vanue === 'pending_review')
                                        <span class="px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 text-xs font-medium border border-blue-200">
                                            Menunggu ACC Admin
                                        </span>
                                    @elseif ($venue->status_vanue === 'rejected')
                                        <span class="px-2.5 py-0.5 rounded-full bg-rose-50 text-rose-700 text-xs font-medium border border-rose-200">
                                            Perlu Tindakan
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full bg-slate-200 text-slate-700 text-xs font-medium">
                                            {{ ucfirst($venue->status_vanue) }}
                                        </span>
                                    @endif
                                </div>

                                @if ($venue->status_vanue === 'rejected')
                                    <div class="mt-3 p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-xs text-rose-800 space-y-1">
                                        <div class="font-bold flex items-center gap-1.5">
                                            <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                            </svg>
                                            <span>Alasan Penolakan Admin:</span>
                                        </div>
                                        <p class="leading-relaxed text-rose-700 pl-5">
                                            {{ $venue->alasan_penolakan ?? 'Perbaiki berkas atau data venue untuk peninjauan ulang.' }}
                                        </p>
                                    </div>
                                @endif

                                @if ($venue->status_vanue === 'pending_review')
                                    <div class="mt-3 p-3.5 rounded-2xl bg-blue-50 border border-blue-100 text-xs text-blue-800 flex items-start gap-2">
                                        <svg class="w-4 h-4 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <p class="leading-relaxed">
                                            Diajukan {{ $venue->updated_at ? $venue->updated_at->translatedFormat('d M Y') : 'hari ini' }}. Estimasi verifikasi tim SportPlace adalah <strong>1x24 jam kerja</strong>.
                                        </p>
                                    </div>
                                @endif
                            </div>

                            <!-- Action Buttons -->
                            <div class="pt-2 flex items-center gap-2 border-t border-slate-100">
                                @if ($venue->status_vanue === 'aktif')
                                    <a href="{{ route('mitra.venues.fields.index', $venue) }}" class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-full bg-[#0B7B69] hover:bg-[#086354] text-white text-sm font-semibold transition cursor-pointer min-h-[44px]">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                        </svg>
                                        <span>Kelola Lapangan</span>
                                    </a>
                                @elseif ($venue->status_vanue === 'rejected')
                                    <button type="button" wire:click="openEditModal({{ $venue->id }})" class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-full bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold transition cursor-pointer min-h-[44px]">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                        <span>Perbaiki & Ajukan Ulang</span>
                                    </button>
                                @elseif ($venue->status_vanue === 'pending_review')
                                    <button type="button" wire:click="openEditModal({{ $venue->id }})" class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold transition cursor-pointer min-h-[44px]">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <span>Detail & Edit</span>
                                    </button>
                                @elseif ($venue->status_vanue === 'draft')
                                    <button type="button" wire:click="confirmActionModal({{ $venue->id }}, 'submit')" class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-full bg-[#0B7B69] hover:bg-[#086354] text-white text-sm font-semibold transition cursor-pointer min-h-[44px]">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                        </svg>
                                        <span>Ajukan Peninjauan</span>
                                    </button>
                                @elseif ($venue->status_vanue === 'nonaktif')
                                    <button type="button" wire:click="confirmActionModal({{ $venue->id }}, 'activate')" class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold transition cursor-pointer min-h-[44px]">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        <span>Aktifkan Kembali</span>
                                    </button>
                                @endif

                                <!-- Dropdown 3 Titik -->
                                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                                    <button
                                        type="button"
                                        @click="open = !open"
                                        class="p-2.5 rounded-full border border-slate-200 hover:bg-slate-100 text-slate-600 transition min-h-[44px] min-w-[44px] flex items-center justify-center cursor-pointer"
                                        aria-label="Opsi Venue"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01"/>
                                        </svg>
                                    </button>
                                    <div
                                        x-show="open"
                                        x-cloak
                                        x-transition
                                        class="absolute right-0 bottom-12 w-48 bg-white rounded-2xl shadow-xl border border-slate-200 z-30 py-1.5 text-xs font-medium"
                                    >
                                        <button type="button" @click="open = false; $wire.openEditModal({{ $venue->id }})" class="w-full text-left px-4 py-2.5 hover:bg-slate-50 text-slate-700 flex items-center gap-2 cursor-pointer">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            <span>Edit Profil Venue</span>
                                        </button>

                                        @if ($venue->status_vanue === 'aktif')
                                            <button type="button" @click="open = false; $wire.confirmActionModal({{ $venue->id }}, 'deactivate')" class="w-full text-left px-4 py-2.5 hover:bg-amber-50 text-amber-700 flex items-center gap-2 cursor-pointer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                                <span>Nonaktifkan Venue</span>
                                            </button>
                                        @endif

                                        @if ($venue->status_vanue === 'draft')
                                            <button type="button" @click="open = false; $wire.confirmActionModal({{ $venue->id }}, 'delete')" class="w-full text-left px-4 py-2.5 hover:bg-rose-50 text-rose-600 flex items-center gap-2 cursor-pointer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                <span>Hapus Draft</span>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- MODAL TAMBAH / EDIT VENUE -->
    @if ($showFormModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="venue-modal-title">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" wire:click="closeFormModal"></div>
            <div class="flex min-h-full items-center justify-center p-4 sm:p-6">
                <div class="relative w-full max-w-2xl rounded-3xl bg-white shadow-2xl border border-slate-200 overflow-hidden" @click.stop>
                    <!-- Header -->
                    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
                        <div>
                            <h3 id="venue-modal-title" class="text-lg font-bold text-slate-900">
                                {{ $editingVenueId ? 'Edit Profil Venue' : 'Tambah Venue Baru' }}
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Lengkapi lokasi dan jam operasional fasilitas olahraga Anda.
                            </p>
                        </div>
                        <button type="button" wire:click="closeFormModal" class="p-2 rounded-full text-slate-400 hover:text-slate-700 hover:bg-slate-200/70 transition cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- Form Body -->
                    <form wire:submit="saveVenue('draft')" class="p-6 max-h-[80vh] overflow-y-auto space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="v_nama_venue" value="Nama Venue" required />
                                <input
                                    type="text"
                                    id="v_nama_venue"
                                    wire:model.live.debounce.300ms="nama_venue"
                                    placeholder="Contoh: Champion Futsal Arena"
                                    class="w-full rounded-full border {{ $errors->has('nama_venue') ? 'border-rose-400' : 'border-slate-300' }} px-4 py-2 text-sm focus:outline-hidden focus:border-[#0B7B69]"
                                >
                                @error('nama_venue') <p class="mt-1 text-[11px] text-rose-600 font-medium">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <x-input-label for="v_city_id" value="Kota / Wilayah" required />
                                <select
                                    id="v_city_id"
                                    wire:model.live="city_id"
                                    class="w-full rounded-full border {{ $errors->has('city_id') ? 'border-rose-400' : 'border-slate-300' }} bg-white px-4 py-2 text-sm focus:outline-hidden focus:border-[#0B7B69]"
                                >
                                    <option value="">-- Pilih Kota --</option>
                                    @foreach ($citiesList as $city)
                                        <option value="{{ $city->id }}">{{ $city->nama_kota }}</option>
                                    @endforeach
                                </select>
                                @error('city_id') <p class="mt-1 text-[11px] text-rose-600 font-medium">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <x-input-label for="v_alamat_lengkap" value="Alamat Lengkap Venue" required />
                            <textarea
                                id="v_alamat_lengkap"
                                wire:model.live.debounce.300ms="alamat_lengkap"
                                rows="2"
                                placeholder="Contoh: Jl. Gatot Subroto Barat No. 88"
                                class="w-full rounded-2xl border {{ $errors->has('alamat_lengkap') ? 'border-rose-400' : 'border-slate-300' }} px-4 py-2 text-sm focus:outline-hidden focus:border-[#0B7B69]"
                            ></textarea>
                            @error('alamat_lengkap') <p class="mt-1 text-[11px] text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <x-input-label for="v_jam_buka" value="Jam Buka" required />
                                <input
                                    type="time"
                                    id="v_jam_buka"
                                    wire:model.live="jam_buka"
                                    class="w-full rounded-full border {{ $errors->has('jam_buka') ? 'border-rose-400' : 'border-slate-300' }} px-4 py-2 text-sm tabular-nums focus:outline-hidden focus:border-[#0B7B69]"
                                >
                                @error('jam_buka') <p class="mt-1 text-[11px] text-rose-600 font-medium">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <x-input-label for="v_jam_tutup" value="Jam Tutup" required />
                                <input
                                    type="time"
                                    id="v_jam_tutup"
                                    wire:model.live="jam_tutup"
                                    class="w-full rounded-full border {{ $errors->has('jam_tutup') ? 'border-rose-400' : 'border-slate-300' }} px-4 py-2 text-sm tabular-nums focus:outline-hidden focus:border-[#0B7B69]"
                                >
                                @error('jam_tutup') <p class="mt-1 text-[11px] text-rose-600 font-medium">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <x-input-label for="v_map_url" value="URL Google Maps (Opsional)" />
                                <input
                                    type="url"
                                    id="v_map_url"
                                    wire:model.live.debounce.300ms="map_url"
                                    placeholder="https://maps.google.com/..."
                                    class="w-full rounded-full border {{ $errors->has('map_url') ? 'border-rose-400' : 'border-slate-300' }} px-4 py-2 text-sm focus:outline-hidden focus:border-[#0B7B69]"
                                >
                                @error('map_url') <p class="mt-1 text-[11px] text-rose-600 font-medium">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <x-input-label for="v_deskripsi" value="Deskripsi & Keunggulan Venue" />
                            <textarea
                                id="v_deskripsi"
                                wire:model.live.debounce.300ms="deskripsi"
                                rows="3"
                                placeholder="Jelaskan fasilitas umum (parkir luas, mushola, kantin) dan akses menuju lokasi..."
                                class="w-full rounded-2xl border border-slate-300 px-4 py-2 text-sm focus:outline-hidden focus:border-[#0B7B69]"
                            ></textarea>
                            @error('deskripsi') <p class="mt-1 text-[11px] text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <!-- Footer Buttons -->
                        <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-2.5 pt-4 border-t border-slate-100">
                            <button type="button" wire:click="closeFormModal" class="w-full sm:w-auto px-5 py-2.5 rounded-full border border-slate-300 bg-white hover:bg-slate-50 text-xs font-semibold text-slate-700 cursor-pointer min-h-[42px]">
                                Batal
                            </button>

                            @php
                                $editingVenue = $editingVenueId ? \App\Models\Venue::find($editingVenueId) : null;
                                $canSubmitReview = ! $editingVenue || in_array($editingVenue->status_vanue, ['draft', 'rejected'], true);
                            @endphp

                            @if ($canSubmitReview)
                                <button
                                    type="button"
                                    wire:click="saveVenue('draft')"
                                    wire:loading.attr="disabled"
                                    class="w-full sm:w-auto px-5 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-semibold cursor-pointer min-h-[42px]"
                                >
                                    Simpan Draft
                                </button>
                                <button
                                    type="button"
                                    wire:click="saveVenue('submit')"
                                    wire:loading.attr="disabled"
                                    class="w-full sm:w-auto px-6 py-2.5 rounded-full bg-[#0B7B69] hover:bg-[#086354] text-white text-xs sm:text-sm font-semibold shadow-xs cursor-pointer min-h-[42px]"
                                >
                                    Simpan & Ajukan Review
                                </button>
                            @else
                                <button
                                    type="button"
                                    wire:click="saveVenue('save')"
                                    wire:loading.attr="disabled"
                                    class="w-full sm:w-auto px-6 py-2.5 rounded-full bg-[#0B7B69] hover:bg-[#086354] text-white text-xs sm:text-sm font-semibold shadow-xs cursor-pointer min-h-[42px]"
                                >
                                    Simpan Perubahan
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL KONFIRMASI AKSI -->
    @if ($showConfirmModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" wire:click="closeConfirmModal"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl border border-slate-200 text-center" @click.stop>
                    <div class="w-14 h-14 rounded-full mx-auto mb-4 flex items-center justify-center {{ in_array($confirmAction, ['delete', 'deactivate']) ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-[#0B7B69]' }}">
                        @if (in_array($confirmAction, ['delete', 'deactivate']))
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        @else
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        @endif
                    </div>

                    <h3 class="text-lg font-bold text-slate-900 mb-2">
                        @if ($confirmAction === 'submit') Ajukan Peninjauan Venue?
                        @elseif ($confirmAction === 'deactivate') Nonaktifkan Venue?
                        @elseif ($confirmAction === 'activate') Aktifkan Kembali Venue?
                        @elseif ($confirmAction === 'delete') Hapus Draft Venue?
                        @endif
                    </h3>

                    <p class="text-xs sm:text-sm text-slate-500 leading-relaxed mb-6">
                        @if ($confirmAction === 'submit')
                            Venue <strong>{{ $confirmVenueName }}</strong> akan diajukan ke tim moderasi Admin SportPlace untuk diverifikasi.
                        @elseif ($confirmAction === 'deactivate')
                            Venue <strong>{{ $confirmVenueName }}</strong> dan seluruh lapangannya tidak akan muncul pada pencarian publik sampai diaktifkan kembali.
                        @elseif ($confirmAction === 'activate')
                            Venue <strong>{{ $confirmVenueName }}</strong> akan kembali aktif dan siap menerima pemesanan dari penyewa.
                        @elseif ($confirmAction === 'delete')
                            Apakah Anda yakin ingin menghapus permanen draft venue <strong>{{ $confirmVenueName }}</strong>?
                        @endif
                    </p>

                    <div class="flex items-center justify-center gap-3">
                        <button type="button" wire:click="closeConfirmModal" class="flex-1 px-4 py-2.5 rounded-full border border-slate-300 bg-white hover:bg-slate-50 text-xs font-semibold text-slate-700 cursor-pointer min-h-[42px]">
                            Batal
                        </button>
                        <button
                            type="button"
                            wire:click="executeConfirmedAction"
                            wire:loading.attr="disabled"
                            class="flex-1 px-4 py-2.5 rounded-full text-white text-xs font-semibold shadow-xs cursor-pointer min-h-[42px] {{ in_array($confirmAction, ['delete', 'deactivate']) ? 'bg-rose-600 hover:bg-rose-700' : 'bg-[#0B7B69] hover:bg-[#086354]' }}"
                        >
                            Ya, Lanjutkan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
