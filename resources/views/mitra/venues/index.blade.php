<x-layouts.mitra title="Manajemen Venue — SportPlace Mitra">
    <div class="space-y-6">

        <!-- Penanda Halaman / Breadcrumbs -->
        <nav class="flex items-center gap-2 text-xs font-medium text-slate-500" aria-label="Breadcrumb">
            <a href="{{ route('mitra.dashboard') }}" class="hover:text-emerald-700 transition">SportPlace Mitra</a>
            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-slate-800 font-semibold">Venue & Lapangan</span>
        </nav>

        <!-- Flash Alert Success / Error -->
        @if (session('success'))
            <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4 flex items-center justify-between text-emerald-800 text-sm shadow-xs animate-in fade-in">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 cursor-pointer p-1" aria-label="Tutup notifikasi">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-xl bg-rose-50 border border-rose-200 p-4 flex items-center justify-between text-rose-800 text-sm shadow-xs animate-in fade-in">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-800 cursor-pointer p-1" aria-label="Tutup notifikasi">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        <!-- Header Halaman: Judul, Deskripsi & Tombol Tambah Venue -->
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

            <a href="{{ route('mitra.venues.create') }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-[#0B7B69] hover:bg-[#086354] text-white text-sm font-medium shadow-sm hover:shadow transition duration-150 min-h-[44px] cursor-pointer shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Vanue</span>
            </a>
        </div>

        <!-- Banner Informasi Struktur Hirarki SportPlace -->
        <div id="hierarchy-info-banner" class="rounded-2xl bg-[#E8F5F1] border border-[#B2DFDB] p-4 sm:p-5 flex items-start justify-between gap-4 shadow-xs">
            <div class="flex items-start gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-[#D4ECE6] text-[#0B7B69] flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Informasi Struktur Hirarki SportPlace</h3>
                    <p class="text-xs sm:text-sm text-slate-600 mt-1 leading-relaxed">
                        Setiap <strong class="text-slate-800">Vanue</strong> dapat menaungi beberapa <strong class="text-slate-800">Lapangan</strong> dengan jadwal operasional, fasilitas, dan tarif sewa masing-masing. Klik <strong class="text-[#0B7B69]">"Kelola Lapangan"</strong> untuk mengatur unit lapangan di dalam vanue terkait.
                    </p>
                </div>
            </div>
            <button type="button" onclick="document.getElementById('hierarchy-info-banner').remove()" class="text-slate-400 hover:text-slate-600 transition p-1 cursor-pointer shrink-0" aria-label="Tutup banner informasi">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Metric Stat Cards (4 Kartu) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Vanue -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Venue</span>
                    <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-slate-900 tabular-nums">{{ $totalVenues }}</span>
                    <span class="text-xs font-medium text-slate-500">terdaftar</span>
                </div>
                <div class="mt-4 h-1 w-full bg-[#06231B] rounded-full"></div>
            </div>

            <!-- Vanue Aktif -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Vanue Aktif</span>
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-slate-900 tabular-nums">{{ $activeVenues }}</span>
                    <span class="text-xs font-medium text-emerald-600">beroperasi</span>
                </div>
                <div class="mt-4 h-1 w-full bg-[#0B7B69] rounded-full"></div>
            </div>

            <!-- Menunggu Review -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Menunggu Review</span>
                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-slate-900 tabular-nums">{{ $pendingVenues }}</span>
                    <span class="text-xs font-medium text-blue-600">diajukan</span>
                </div>
                <div class="mt-4 h-1 w-full bg-blue-500 rounded-full"></div>
            </div>

            <!-- Butuh Perbaikan -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Butuh Perbaikan</span>
                    <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-slate-900 tabular-nums">{{ $rejectedVenues }}</span>
                    <span class="text-xs font-medium text-rose-600">ditolak admin</span>
                </div>
                <div class="mt-4 h-1 w-full bg-rose-500 rounded-full"></div>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-xs space-y-4">
            <form method="GET" action="{{ route('mitra.venues.index') }}" class="flex flex-col md:flex-row items-stretch md:items-center gap-3">
                <!-- Text Search -->
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama vanue atau lokasi..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-[#0B7B69] transition min-h-[44px]">
                </div>

                <!-- Dropdown Wilayah/Kota -->
                <div class="relative w-full md:w-64">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <select name="city_id" onchange="this.form.submit()" class="w-full pl-10 pr-8 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-[#0B7B69] transition appearance-none bg-white min-h-[44px] cursor-pointer">
                        <option value="">Semua Wilayah</option>
                        @foreach ($cities as $city)
                            <option value="{{ $city->id }}" {{ request('city_id') == $city->id ? 'selected' : '' }}>
                                {{ $city->nama_kota }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>

                <!-- Submit Button jika form diketik -->
                <button type="submit" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium transition cursor-pointer min-h-[44px]">
                    Cari
                </button>

                @if(request()->hasAny(['search', 'city_id', 'status']))
                    <a href="{{ route('mitra.venues.index') }}" class="px-3 py-2.5 rounded-xl text-slate-500 hover:text-slate-800 text-sm font-medium transition text-center min-h-[44px] flex items-center justify-center">
                        Reset
                    </a>
                @endif
            </form>

            <!-- Status Tabs / Pills -->
            <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pt-1">
                @php
                    $currentStatus = request('status', 'semua');
                @endphp

                <a href="{{ request()->fullUrlWithQuery(['status' => 'semua']) }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition cursor-pointer {{ $currentStatus === 'semua' ? 'bg-[#E8F5F1] text-[#0B7B69] border border-emerald-300' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    <span>Semua</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $currentStatus === 'semua' ? 'bg-[#0B7B69] text-white' : 'bg-slate-200 text-slate-600' }}">{{ $totalVenues }}</span>
                </a>

                <a href="{{ request()->fullUrlWithQuery(['status' => 'aktif']) }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition cursor-pointer {{ $currentStatus === 'aktif' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    <span>Disetujui</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $currentStatus === 'aktif' ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-600' }}">{{ $activeVenues }}</span>
                </a>

                <a href="{{ request()->fullUrlWithQuery(['status' => 'pending_review']) }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition cursor-pointer {{ $currentStatus === 'pending_review' ? 'bg-blue-100 text-blue-800 border border-blue-300' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    <span>Menunggu Verifikasi</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $currentStatus === 'pending_review' ? 'bg-blue-600 text-white' : 'bg-slate-200 text-slate-600' }}">{{ $pendingVenues }}</span>
                </a>

                <a href="{{ request()->fullUrlWithQuery(['status' => 'rejected']) }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition cursor-pointer {{ $currentStatus === 'rejected' ? 'bg-rose-100 text-rose-800 border border-rose-300' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    <span>Ditolak</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $currentStatus === 'rejected' ? 'bg-rose-600 text-white' : 'bg-slate-200 text-slate-600' }}">{{ $rejectedVenues }}</span>
                </a>

                <a href="{{ request()->fullUrlWithQuery(['status' => 'draft']) }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition cursor-pointer {{ $currentStatus === 'draft' ? 'bg-slate-200 text-slate-800 border border-slate-400' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    <span>Draft</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $currentStatus === 'draft' ? 'bg-slate-700 text-white' : 'bg-slate-200 text-slate-600' }}">{{ $draftVenues }}</span>
                </a>

                <a href="{{ request()->fullUrlWithQuery(['status' => 'nonaktif']) }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition cursor-pointer {{ $currentStatus === 'nonaktif' ? 'bg-amber-100 text-amber-800 border border-amber-300' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    <span>Nonaktif</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $currentStatus === 'nonaktif' ? 'bg-amber-600 text-white' : 'bg-slate-200 text-slate-600' }}">{{ $nonaktifVenues }}</span>
                </a>
            </div>
        </div>

        <!-- Venue Cards Grid -->
        @if ($venues->isEmpty())
            <div class="bg-white rounded-2xl p-12 text-center border border-slate-200/90 shadow-xs">
                <div class="mx-auto w-16 h-16 rounded-2xl bg-emerald-50 text-[#0B7B69] flex items-center justify-center mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900">Belum Ada Venue</h3>
                <p class="text-sm text-slate-500 max-w-md mx-auto mt-1 leading-relaxed">
                    @if (request()->hasAny(['search', 'city_id', 'status']))
                        Tidak ada venue yang sesuai dengan filter pencarian Anda. Silakan coba atur ulang filter.
                    @else
                        Mulai kelola bisnis fasilitas olahragamu dengan menambahkan lokasi venue pertama.
                    @endif
                </p>
                <div class="mt-6 flex justify-center gap-3">
                    @if (request()->hasAny(['search', 'city_id', 'status']))
                        <a href="{{ route('mitra.venues.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium transition cursor-pointer min-h-[44px] flex items-center">
                            Reset Filter
                        </a>
                    @else
                        <a href="{{ route('mitra.venues.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#0B7B69] hover:bg-[#086354] text-white text-sm font-medium shadow-sm transition min-h-[44px] cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Tambah Venue Pertama</span>
                        </a>
                    @endif
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($venues as $venue)
                    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs hover:shadow-md transition duration-200 flex flex-col overflow-hidden">
                        
                        <!-- Header Visual Venue (Card Image & Status Overlay) -->
                        <div class="relative h-48 bg-linear-to-tr from-slate-800 to-slate-700 overflow-hidden">
                            <!-- Background Image Mockup / Gradient -->
                            <div class="absolute inset-0 bg-cover bg-center opacity-85" style="background-image: url('https://images.unsplash.com/photo-1574629810360-7efbbe195018?q=80&w=800&auto=format&fit=crop');"></div>
                            <div class="absolute inset-0 bg-linear-to-t from-slate-950/85 via-slate-950/40 to-transparent"></div>

                            <!-- Top Floating Badges -->
                            <div class="absolute top-3 inset-x-3 flex items-center justify-between">
                                <!-- Status Badge -->
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

                                <!-- Placeholder Rating -->
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-black/40 text-white backdrop-blur-xs">
                                    <svg class="w-3.5 h-3.5 text-amber-400 fill-amber-400" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    <span>4.8</span>
                                </span>
                            </div>

                            <!-- Bottom Price Tag on Image -->
                            <div class="absolute bottom-3 inset-x-4 text-white">
                                <span class="text-[11px] font-medium text-slate-300 block">
                                    @if($venue->price_range) Mulai dari @else Jam Operasional @endif
                                </span>
                                <div class="text-base font-bold tracking-tight">
                                    {{ $venue->price_range ? $venue->price_range . ' / jam' : $venue->jam_operasional }}
                                </div>
                            </div>
                        </div>

                        <!-- Card Content -->
                        <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                            <div>
                                <!-- Nama & Alamat Venue -->
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

                                <!-- Tags Cabang Olahraga -->
                                <div class="mt-3 flex items-center gap-1.5 flex-wrap">
                                    @forelse ($venue->sports as $sportName)
                                        <span class="px-2.5 py-0.5 rounded-lg bg-slate-100 text-slate-600 text-xs font-medium">
                                            {{ $sportName }}
                                        </span>
                                    @empty
                                        <span class="text-[11px] text-slate-400 italic">Belum ada unit lapangan</span>
                                    @endforelse
                                </div>

                                <!-- Info Box Status Lapangan -->
                                <div class="mt-4 p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                        </svg>
                                        <span class="text-xs font-semibold text-slate-800">{{ $venue->fields->count() }} Lapangan</span>
                                    </div>
                                    @if ($venue->status_vanue === 'aktif')
                                        <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 text-xs font-medium border border-emerald-200">
                                            {{ $venue->fields->where('status_lapangan', 'aktif')->count() }} Aktif Disewakan
                                        </span>
                                    @elseif ($venue->status_vanue === 'pending_review')
                                        <span class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 text-xs font-medium border border-blue-200">
                                            Menunggu ACC Admin
                                        </span>
                                    @elseif ($venue->status_vanue === 'rejected')
                                        <span class="px-2 py-0.5 rounded-md bg-rose-50 text-rose-700 text-xs font-medium border border-rose-200">
                                            Perlu Tindakan
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-md bg-slate-200 text-slate-700 text-xs font-medium">
                                            {{ ucfirst($venue->status_vanue) }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Box Alasan Penolakan Jika Rejected -->
                                @if ($venue->status_vanue === 'rejected')
                                    <div class="mt-3 p-3 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-800 space-y-1">
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

                                <!-- Info Diajukan Jika Pending -->
                                @if ($venue->status_vanue === 'pending_review')
                                    <div class="mt-3 p-3 rounded-xl bg-blue-50 border border-blue-100 text-xs text-blue-800 flex items-start gap-2">
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
                                    <a href="{{ url('/mitra/venues/' . $venue->id . '/fields') }}" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-[#0B7B69] hover:bg-[#086354] text-white text-sm font-semibold transition cursor-pointer min-h-[44px]">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                        </svg>
                                        <span>Kelola Lapangan</span>
                                    </a>
                                @elseif ($venue->status_vanue === 'rejected')
                                    <a href="{{ route('mitra.venues.edit', $venue) }}" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold transition cursor-pointer min-h-[44px]">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                        <span>Perbaiki & Ajukan Ulang</span>
                                    </a>
                                @elseif ($venue->status_vanue === 'pending_review')
                                    <a href="{{ route('mitra.venues.edit', $venue) }}" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold transition cursor-pointer min-h-[44px]">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <span>Detail Pengajuan</span>
                                    </a>
                                @elseif ($venue->status_vanue === 'draft')
                                    <form method="POST" action="{{ route('mitra.venues.submit', $venue) }}" class="flex-1">
                                        @csrf
                                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-[#0B7B69] hover:bg-[#086354] text-white text-sm font-semibold transition cursor-pointer min-h-[44px]">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                            </svg>
                                            <span>Ajukan Peninjauan</span>
                                        </button>
                                    </form>
                                @elseif ($venue->status_vanue === 'nonaktif')
                                    <form method="POST" action="{{ route('mitra.venues.activate', $venue) }}" class="flex-1">
                                        @csrf
                                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold transition cursor-pointer min-h-[44px]">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            <span>Aktifkan Kembali</span>
                                        </button>
                                    </form>
                                @endif

                                <!-- Dropdown Menu 3 Titik -->
                                <div class="relative" x-data="{ open: false }">
                                    <button type="button" onclick="toggleDropdown('venue-dropdown-{{ $venue->id }}')" class="p-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 hover:text-slate-900 transition cursor-pointer min-h-[44px] min-w-[44px] flex items-center justify-center" aria-label="Menu Opsi Venue">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                        </svg>
                                    </button>

                                    <!-- Dropdown Menu Item List -->
                                    <div id="venue-dropdown-{{ $venue->id }}" class="hidden absolute right-0 bottom-full mb-2 w-48 rounded-xl bg-white border border-slate-200 shadow-xl z-20 py-1.5 text-xs text-slate-700 animate-in fade-in">
                                        <a href="{{ route('mitra.venues.edit', $venue) }}" class="flex items-center gap-2 px-3.5 py-2 hover:bg-slate-100 transition">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            <span>Edit Informasi</span>
                                        </a>

                                        @if ($venue->map_url)
                                            <a href="{{ $venue->map_url }}" target="_blank" rel="noopener noreferrer" class="flex items-center gap-2 px-3.5 py-2 hover:bg-slate-100 transition">
                                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                </svg>
                                                <span>Buka Google Maps</span>
                                            </a>
                                        @endif

                                        @if ($venue->status_vanue === 'aktif')
                                            <button type="button" onclick="confirmDeactivate({{ $venue->id }}, '{{ addslashes($venue->nama_venue) }}')" class="w-full text-left flex items-center gap-2 px-3.5 py-2 text-rose-600 hover:bg-rose-50 transition cursor-pointer">
                                                <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                </svg>
                                                <span>Nonaktifkan Venue</span>
                                            </button>
                                        @endif

                                        @if ($venue->status_vanue === 'nonaktif')
                                            <form method="POST" action="{{ route('mitra.venues.activate', $venue) }}">
                                                @csrf
                                                <button type="submit" class="w-full text-left flex items-center gap-2 px-3.5 py-2 text-emerald-700 hover:bg-emerald-50 transition cursor-pointer">
                                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    <span>Aktifkan Kembali</span>
                                                </button>
                                            </form>
                                        @endif

                                        @if ($venue->status_vanue === 'draft')
                                            <button type="button" onclick="confirmDeleteDraft({{ $venue->id }}, '{{ addslashes($venue->nama_venue) }}')" class="w-full text-left flex items-center gap-2 px-3.5 py-2 text-rose-600 hover:bg-rose-50 transition cursor-pointer">
                                                <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
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

    <!-- Modal Konfirmasi Nonaktifkan Venue (FR-VENUE-004) -->
    <dialog id="deactivate-modal" class="backdrop:bg-slate-900/60 rounded-2xl p-0 shadow-2xl max-w-md w-full border border-slate-200">
        <div class="p-6">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900">Nonaktifkan Venue?</h3>
            <p class="mt-2 text-sm text-slate-600 leading-relaxed">
                Anda akan menonaktifkan <strong id="deactivate-venue-name" class="text-slate-800"></strong>. Seluruh lapangan di dalamnya tidak akan ditampilkan ke publik.
            </p>
            <p class="mt-2 text-xs text-amber-700 bg-amber-50 p-2.5 rounded-lg border border-amber-200">
                Peringatan: Penonaktifan akan ditolak jika masih terdapat pesanan aktif mendatang pada lapangan di venue ini.
            </p>

            <form id="deactivate-form" method="POST" action="" class="mt-6 flex justify-end gap-3">
                @csrf
                <button type="button" onclick="document.getElementById('deactivate-modal').close()" class="px-4 py-2.5 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition min-h-[44px] cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 text-sm font-semibold rounded-xl text-white bg-amber-600 hover:bg-amber-700 transition min-h-[44px] cursor-pointer">
                    Ya, Nonaktifkan
                </button>
            </form>
        </div>
    </dialog>

    <!-- Modal Konfirmasi Hapus Draft Venue -->
    <dialog id="delete-draft-modal" class="backdrop:bg-slate-900/60 rounded-2xl p-0 shadow-2xl max-w-md w-full border border-slate-200">
        <div class="p-6">
            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900">Hapus Draft Venue?</h3>
            <p class="mt-2 text-sm text-slate-600 leading-relaxed">
                Apakah Anda yakin ingin menghapus draft venue <strong id="delete-draft-venue-name" class="text-slate-800"></strong>? Tindakan ini tidak dapat dibatalkan.
            </p>

            <form id="delete-draft-form" method="POST" action="" class="mt-6 flex justify-end gap-3">
                @csrf
                @method('DELETE')
                <button type="button" onclick="document.getElementById('delete-draft-modal').close()" class="px-4 py-2.5 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition min-h-[44px] cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 text-sm font-semibold rounded-xl text-white bg-rose-600 hover:bg-rose-700 transition min-h-[44px] cursor-pointer">
                    Ya, Hapus Draft
                </button>
            </form>
        </div>
    </dialog>

    <!-- Script Interaktif Ringan (Dropdown & Modal Handlers) -->
    <script>
        function toggleDropdown(id) {
            const dropdown = document.getElementById(id);
            // Tutup dropdown lain yang terbuka
            document.querySelectorAll('[id^="venue-dropdown-"]').forEach(el => {
                if (el.id !== id) el.classList.add('hidden');
            });
            dropdown.classList.toggle('hidden');
        }

        // Tutup dropdown saat klik di luar
        document.addEventListener('click', function(e) {
            if (!e.target.closest('[id^="venue-dropdown-"]') && !e.target.closest('button[aria-label="Menu Opsi Venue"]')) {
                document.querySelectorAll('[id^="venue-dropdown-"]').forEach(el => el.classList.add('hidden'));
            }
        });

        function confirmDeactivate(venueId, venueName) {
            document.getElementById('deactivate-venue-name').innerText = venueName;
            document.getElementById('deactivate-form').action = `/mitra/venues/${venueId}/deactivate`;
            document.getElementById('deactivate-modal').showModal();
        }

        function confirmDeleteDraft(venueId, venueName) {
            document.getElementById('delete-draft-venue-name').innerText = venueName;
            document.getElementById('delete-draft-form').action = `/mitra/venues/${venueId}`;
            document.getElementById('delete-draft-modal').showModal();
        }
    </script>
</x-layouts.mitra>

