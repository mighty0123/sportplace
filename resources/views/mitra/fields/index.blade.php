<x-layouts.mitra :title="($venue ? 'Daftar Lapangan — ' . $venue->nama_venue : 'Semua Lapangan') . ' — SportPlace Mitra'">
    <div class="space-y-6">

        <!-- Penanda Halaman / Breadcrumbs -->
        <nav class="flex items-center gap-2 text-xs font-medium text-slate-500" aria-label="Breadcrumb">
            <a href="{{ route('mitra.dashboard') }}" class="hover:text-emerald-700 transition">SportPlace Mitra</a>
            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <a href="{{ route('mitra.venues.index') }}" class="hover:text-emerald-700 transition">Venue & Lapangan</a>
            @if ($venue)
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-slate-800 font-semibold truncate max-w-xs">{{ $venue->nama_venue }}</span>
            @else
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-slate-800 font-semibold">Semua Lapangan (Lintas Venue)</span>
            @endif
        </nav>

        <!-- Flash Alert Success / Error -->
        @if (session('success'))
            <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4 sm:p-5 flex items-center justify-between text-emerald-800 text-sm shadow-xs animate-in fade-in">
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
            <div class="rounded-2xl bg-rose-50 border border-rose-200 p-4 sm:p-5 flex items-center justify-between text-rose-800 text-sm shadow-xs animate-in fade-in">
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

        <!-- Header Halaman: Detail Venue & Tombol Tambah Lapangan -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 text-xs font-semibold text-emerald-800 uppercase tracking-wider mb-1">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800">
                        {{ $venue ? 'Venue Induk' : 'Lintas Venue' }}
                    </span>
                    @if ($venue)
                        <span class="text-slate-400">•</span>
                        <span class="text-slate-600 font-medium normal-case flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{ $venue->city?->nama_kota ?? 'Lokasi belum diatur' }}
                        </span>
                    @endif
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">
                    {{ $venue ? $venue->nama_venue : 'Semua Unit Lapangan' }}
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    Kelola unit lapangan, status operasional, fasilitas, dan tarif sewa per jam secara terpusat.
                </p>
            </div>

            <div class="flex items-center gap-3">
                @if ($venue)
                    <a href="{{ route('mitra.venues.fields.create', $venue) }}" class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-full bg-[#0B7B69] hover:bg-[#086354] text-white text-sm font-semibold shadow-sm hover:shadow transition duration-150 min-h-[44px] cursor-pointer shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Tambah Lapangan</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- Metric Stat Cards (4 Kartu dari Mockup Figma) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Lapangan -->
            <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/90 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Lapangan</span>
                    <div class="w-10 h-10 rounded-2xl bg-slate-100 text-slate-700 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-slate-900 tabular-nums">{{ $totalFields }}</span>
                    <span class="text-xs font-medium text-slate-500">unit</span>
                </div>
            </div>

            <!-- Aktif Disewakan -->
            <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/90 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-emerald-700 uppercase tracking-wider">Aktif Disewakan</span>
                    <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-emerald-700 tabular-nums">{{ $activeFields }}</span>
                    <span class="text-xs font-medium text-emerald-600">unit aktif</span>
                </div>
            </div>

            <!-- Menunggu Verifikasi -->
            <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/90 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-blue-700 uppercase tracking-wider">Menunggu Verifikasi</span>
                    <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-700 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-blue-700 tabular-nums">{{ $pendingFields }}</span>
                    <span class="text-xs font-medium text-blue-600">sedang ditinjau</span>
                </div>
            </div>

            <!-- Perlu Tindakan / Ditolak -->
            <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/90 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-rose-700 uppercase tracking-wider">Perlu Tindakan / Ditolak</span>
                    <div class="w-10 h-10 rounded-2xl bg-rose-50 text-rose-700 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-rose-700 tabular-nums">{{ $rejectedFields }}</span>
                    <span class="text-xs font-medium text-rose-600">revisi admin</span>
                </div>
            </div>
        </div>

        <!-- Filter Bar & Sport Tabs -->
        <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/90 shadow-xs space-y-4">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                
                <!-- Search & Status Dropdown Form -->
                <form method="GET" action="{{ $venue ? route('mitra.venues.fields.index', $venue) : route('mitra.fields.all') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 flex-1">
                    @if (request('sport_id'))
                        <input type="hidden" name="sport_id" value="{{ request('sport_id') }}">
                    @endif

                    <!-- Input Search -->
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama lapangan..." class="w-full pl-10 pr-4 py-2.5 text-sm rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#0B7B69] focus:border-transparent min-h-[44px]">
                    </div>

                    <!-- Filter Status Dropdown -->
                    <div class="w-full sm:w-48">
                        <select name="status" onchange="this.form.submit()" class="w-full px-3.5 py-2.5 text-sm rounded-2xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-[#0B7B69] focus:border-transparent min-h-[44px] cursor-pointer">
                            <option value="semua" {{ request('status', 'semua') === 'semua' ? 'selected' : '' }}>Semua Status</option>
                            <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="pending_review" {{ request('status') === 'pending_review' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Perlu Tindakan / Ditolak</option>
                        </select>
                    </div>

                    <button type="submit" class="px-5 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold transition cursor-pointer min-h-[44px] shrink-0">
                        Cari
                    </button>
                    @if (request()->hasAny(['search', 'status', 'sport_id']))
                        <a href="{{ $venue ? route('mitra.venues.fields.index', $venue) : route('mitra.fields.all') }}" class="px-4 py-2.5 rounded-full text-slate-500 hover:text-slate-800 text-xs font-semibold flex items-center justify-center transition cursor-pointer min-h-[44px]">
                            Reset
                        </a>
                    @endif
                </form>

                <!-- Toggle Lintas Venue -->
                <div class="flex items-center gap-2 pt-2 lg:pt-0 border-t lg:border-t-0 border-slate-100 shrink-0">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="toggle-all-venues" class="sr-only peer" {{ empty($venue) ? 'checked' : '' }} onchange="window.location.href = this.checked ? '{{ route('mitra.fields.all') }}' : '{{ route('mitra.venues.index') }}'">
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#0B7B69]"></div>
                        <span class="ml-3 text-xs sm:text-sm font-medium text-slate-700 select-none">Tampilkan semua lapangan milik saya (lintas venue)</span>
                    </label>
                </div>
            </div>

            <!-- Sport Filter Tabs (Semua, Futsal, Badminton, Basket, dll) -->
            @if ($sports->count() > 0)
                <div class="flex items-center gap-2 overflow-x-auto pt-2 no-scrollbar border-t border-slate-100 pb-1">
                    <span class="text-xs font-semibold text-slate-500 shrink-0 mr-1">Olahraga:</span>
                    <a href="{{ request()->fullUrlWithQuery(['sport_id' => 'semua']) }}" class="px-3.5 py-1.5 rounded-full text-xs font-medium transition shrink-0 cursor-pointer {{ !request('sport_id') || request('sport_id') === 'semua' ? 'bg-[#0B7B69] text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Semua Olahraga
                    </a>
                    @foreach ($sports as $sport)
                        <a href="{{ request()->fullUrlWithQuery(['sport_id' => $sport->id]) }}" class="px-3.5 py-1.5 rounded-full text-xs font-medium transition shrink-0 cursor-pointer {{ request('sport_id') == $sport->id ? 'bg-[#0B7B69] text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            {{ $sport->nama_olahraga }}
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Tabel Daftar Lapangan -->
        @if ($fields->isEmpty())
            <!-- Empty State -->
            <div class="bg-white rounded-3xl border border-slate-200/90 p-12 text-center shadow-xs">
                <div class="w-16 h-16 rounded-full bg-emerald-50 text-[#0B7B69] flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900">Belum ada unit lapangan ditemukan</h3>
                <p class="text-sm text-slate-500 max-w-md mx-auto mt-1 mb-6">
                    @if (request()->hasAny(['search', 'status', 'sport_id']))
                        Tidak ada lapangan yang cocok dengan kriteria filter Anda. Coba reset filter pencarian.
                    @else
                        Tambahkan unit lapangan olahraga pertama Anda di venue ini untuk mulai menerima reservasi sewa dari pelanggan.
                    @endif
                </p>
                @if (request()->hasAny(['search', 'status', 'sport_id']))
                    <a href="{{ $venue ? route('mitra.venues.fields.index', $venue) : route('mitra.fields.all') }}" class="inline-flex items-center justify-center px-5 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold transition cursor-pointer min-h-[44px]">
                        Reset Filter
                    </a>
                @elseif ($venue)
                    <a href="{{ route('mitra.venues.fields.create', $venue) }}" class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-full bg-[#0B7B69] hover:bg-[#086354] text-white text-sm font-semibold shadow-sm transition min-h-[44px] cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Tambah Lapangan Baru</span>
                    </a>
                @endif
            </div>
        @else
            <!-- Desktop / Tablet Table View -->
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                <th class="py-3.5 px-4 sm:px-6">Lapangan</th>
                                <th class="py-3.5 px-4">Olahraga</th>
                                <th class="py-3.5 px-4">Kapasitas & Ukuran</th>
                                <th class="py-3.5 px-4">Tarif Sewa</th>
                                <th class="py-3.5 px-4 text-center">Status Pengajuan</th>
                                <th class="py-3.5 px-4 text-center">Operasional</th>
                                <th class="py-3.5 px-4 sm:px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @foreach ($fields as $field)
                                <tr class="hover:bg-slate-50/60 transition group {{ $field->status_lapangan === 'rejected' ? 'bg-rose-50/30' : '' }}">
                                    <!-- Kolom Lapangan: Thumbnail, Nama, Info Bangunan -->
                                    <td class="py-4 px-4 sm:px-6">
                                        <div class="flex items-center gap-3.5">
                                            <div class="relative w-16 h-12 rounded-2xl overflow-hidden bg-slate-100 shrink-0 border border-slate-200">
                                                <img src="{{ $field->thumbnail_url }}" alt="{{ $field->nama_lapangan }}" class="w-full h-full object-cover">
                                                @if ($field->photos->count() > 1)
                                                    <span class="absolute bottom-1 right-1 px-1 py-0.2 rounded bg-black/60 text-white text-[9px] font-semibold">
                                                        {{ $field->photos->count() }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="min-w-0">
                                                <div class="font-bold text-slate-900 group-hover:text-emerald-700 transition truncate">
                                                    {{ $field->nama_lapangan }}
                                                </div>
                                                <div class="text-xs text-slate-500 flex items-center gap-1.5 mt-0.5">
                                                    <span class="capitalize">{{ str_replace('_', ' ', $field->tipe_bangunan ?? 'Indoor') }}</span>
                                                    <span>•</span>
                                                    <span class="truncate">{{ $field->jenis_permukaan ?? 'Standar' }}</span>
                                                    @if (!$venue && $field->venue)
                                                        <span>•</span>
                                                        <span class="text-emerald-700 font-medium truncate">{{ $field->venue->nama_venue }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Kolom Olahraga -->
                                    <td class="py-4 px-4">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#0B7B69]"></span>
                                            {{ $field->sport?->nama_olahraga ?? 'Umum' }}
                                        </span>
                                    </td>

                                    <!-- Kolom Kapasitas & Ukuran -->
                                    <td class="py-4 px-4 text-xs text-slate-600">
                                        <div class="font-semibold text-slate-800">{{ $field->dimensi }}</div>
                                        <div class="text-slate-500 mt-0.5">{{ $field->kapasitas ?: 'Standar' }}</div>
                                    </td>

                                    <!-- Kolom Tarif Sewa -->
                                    <td class="py-4 px-4 font-bold text-slate-900 whitespace-nowrap">
                                        {{ $field->formatted_harga }}
                                        <span class="text-xs font-normal text-slate-500">/ jam</span>
                                    </td>

                                    <!-- Kolom Status Pengajuan -->
                                    <td class="py-4 px-4 text-center whitespace-nowrap">
                                        @if ($field->status_lapangan === 'aktif')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                                Aktif
                                            </span>
                                        @elseif ($field->status_lapangan === 'pending_review')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                                                Menunggu Verifikasi
                                            </span>
                                        @elseif ($field->status_lapangan === 'rejected')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-800 border border-rose-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                                                Ditolak / Revisi
                                            </span>
                                        @elseif ($field->status_lapangan === 'draft')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                                Draft
                                            </span>
                                        @elseif ($field->status_lapangan === 'nonaktif')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                                Nonaktif
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Kolom Operasional (Toggle Switch Aktif/Nonaktif) -->
                                    <td class="py-4 px-4 text-center whitespace-nowrap">
                                        @if (in_array($field->status_lapangan, ['aktif', 'nonaktif'], true))
                                            <button type="button" 
                                                    onclick="toggleFieldStatus({{ $field->id }}, '{{ $field->status_lapangan }}', '{{ addslashes($field->nama_lapangan) }}')"
                                                    class="relative inline-flex items-center h-6 w-11 rounded-full transition-colors cursor-pointer focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 {{ $field->status_lapangan === 'aktif' ? 'bg-[#0B7B69]' : 'bg-slate-300' }}"
                                                    aria-label="Ubah Status Operasional Lapangan">
                                                <span class="inline-block w-4 h-4 transform transition-transform bg-white rounded-full {{ $field->status_lapangan === 'aktif' ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                            </button>
                                        @else
                                            <span class="text-xs text-slate-400 select-none italic" title="Hanya lapangan yang telah disetujui admin yang dapat diatur status operasionalnya.">
                                                Belum Disetujui
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Kolom Aksi -->
                                    <td class="py-4 px-4 sm:px-6 text-right whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1">
                                            <!-- Tombol Edit -->
                                            <a href="{{ route('mitra.fields.edit', $field) }}" class="p-2 rounded-full text-slate-600 hover:text-[#0B7B69] hover:bg-emerald-50 transition cursor-pointer min-h-[44px] min-w-[44px] inline-flex items-center justify-center" title="Edit Lapangan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>

                                            <!-- Dropdown Menu Tindakan -->
                                            <div class="relative">
                                                <button type="button" onclick="toggleFieldDropdown('field-dropdown-{{ $field->id }}')" class="p-2 rounded-full text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition cursor-pointer min-h-[44px] min-w-[44px] inline-flex items-center justify-center" aria-label="Menu Opsi">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                                    </svg>
                                                </button>

                                                <!-- Dropdown Items -->
                                                <div id="field-dropdown-{{ $field->id }}" class="hidden absolute right-0 mt-1 w-48 rounded-2xl bg-white border border-slate-200 shadow-xl z-20 py-2 text-xs text-slate-700 animate-in fade-in">
                                                    <!-- Atur Jadwal & Slot (FASE 6) -->
                                                    <a href="{{ url('/mitra/jadwal?field_id=' . $field->id) }}" class="flex items-center gap-2 px-3.5 py-2 hover:bg-slate-100 transition">
                                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                        </svg>
                                                        <span>Atur Jadwal & Slot</span>
                                                    </a>

                                                    <!-- Atur Harga Peak/Off-Peak (FASE 7) -->
                                                    <a href="{{ url('/mitra/harga?field_id=' . $field->id) }}" class="flex items-center gap-2 px-3.5 py-2 hover:bg-slate-100 transition">
                                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5a1.99 1.99 0 011.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V5a2 2 0 012-2z"/>
                                                        </svg>
                                                        <span>Pengaturan Tarif Peak</span>
                                                    </a>

                                                    <!-- Ajukan Peninjauan jika draft atau rejected -->
                                                    @if (in_array($field->status_lapangan, ['draft', 'rejected'], true))
                                                        <form method="POST" action="{{ route('mitra.fields.submit', $field) }}">
                                                            @csrf
                                                            <button type="submit" class="w-full text-left flex items-center gap-2 px-3.5 py-2 text-[#0B7B69] hover:bg-emerald-50 transition cursor-pointer">
                                                                <svg class="w-4 h-4 text-[#0B7B69]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                                                </svg>
                                                                <span>Ajukan Peninjauan</span>
                                                            </button>
                                                        </form>
                                                    @endif

                                                    <!-- Hapus Lapangan jika draft -->
                                                    @if ($field->status_lapangan === 'draft')
                                                        <button type="button" onclick="confirmDeleteDraftField({{ $field->id }}, '{{ addslashes($field->nama_lapangan) }}')" class="w-full text-left flex items-center gap-2 px-3.5 py-2 text-rose-600 hover:bg-rose-50 transition cursor-pointer">
                                                            <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                            </svg>
                                                            <span>Hapus Draft</span>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Baris Tambahan Khusus untuk Status Ditolak (Sesuai Mockup Figma) -->
                                @if ($field->status_lapangan === 'rejected')
                                    <tr class="bg-rose-50/50 border-b border-rose-100">
                                        <td colspan="7" class="px-4 sm:px-6 py-3">
                                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                                                <div class="flex items-start gap-2.5 text-rose-800">
                                                    <svg class="w-4 h-4 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                    </svg>
                                                    <div>
                                                        <span class="font-bold">Catatan Penolakan dari Admin:</span>
                                                        <span class="text-rose-700 ml-1">{{ $field->alasan_penolakan ?: 'Terdapat spesifikasi atau foto yang belum sesuai standar. Silakan periksa kembali.' }}</span>
                                                    </div>
                                                </div>
                                                <a href="{{ route('mitra.fields.edit', $field) }}" class="inline-flex items-center gap-1 font-semibold text-rose-700 hover:text-rose-900 underline shrink-0 cursor-pointer">
                                                    <span>Perbaiki & Ajukan Ulang</span>
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                    </svg>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

    </div>

    <!-- Form Tersembunyi untuk Operasional Toggle Status -->
    <form id="toggle-field-status-form" method="POST" action="" class="hidden">
        @csrf
    </form>

    <!-- Modal Konfirmasi Nonaktifkan Lapangan (Safeguard OQ-012) -->
    <div id="modal-deactivate-field" class="fixed inset-0 bg-slate-900/60 z-50 hidden flex items-center justify-center p-4 backdrop-blur-xs">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl animate-in fade-in zoom-in-95">
            <div class="w-12 h-12 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900">Nonaktifkan Lapangan?</h3>
            <p class="text-sm text-slate-500 mt-2 leading-relaxed">
                Anda akan menonaktifkan operasional lapangan <strong id="deactivate-field-name" class="text-slate-800"></strong>.
            </p>
            <div class="mt-3 p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-xs text-amber-800">
                <strong>Catatan Perlindungan Jadwal (OQ-012):</strong> Lapangan yang memiliki pesanan mendatang yang sudah dikonfirmasi atau dibayar tidak dapat dinonaktifkan sampai pesanan selesai.
            </div>
            <div class="mt-6 flex items-center justify-end gap-3">
                <button type="button" onclick="closeDeactivateModal()" class="px-5 py-2.5 rounded-full border border-slate-200 text-slate-700 hover:bg-slate-50 text-sm font-semibold transition cursor-pointer min-h-[44px]">
                    Batal
                </button>
                <button type="button" id="confirm-deactivate-btn" class="px-6 py-2.5 rounded-full bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold transition cursor-pointer min-h-[44px]">
                    Ya, Nonaktifkan
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus Draft Lapangan -->
    <div id="modal-delete-draft-field" class="fixed inset-0 bg-slate-900/60 z-50 hidden flex items-center justify-center p-4 backdrop-blur-xs">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl animate-in fade-in zoom-in-95">
            <div class="w-12 h-12 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900">Hapus Draft Lapangan?</h3>
            <p class="text-sm text-slate-500 mt-2 leading-relaxed">
                Apakah Anda yakin ingin menghapus draft lapangan <strong id="delete-draft-field-name" class="text-slate-800"></strong>? Tindakan ini tidak dapat dibatalkan.
            </p>
            <form id="delete-draft-field-form" method="POST" action="" class="mt-6 flex items-center justify-end gap-3">
                @csrf
                @method('DELETE')
                <button type="button" onclick="closeDeleteDraftModal()" class="px-5 py-2.5 rounded-full border border-slate-200 text-slate-700 hover:bg-slate-50 text-sm font-semibold transition cursor-pointer min-h-[44px]">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2.5 rounded-full bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold transition cursor-pointer min-h-[44px]">
                    Ya, Hapus Draft
                </button>
            </form>
        </div>
    </div>

    <!-- Script Interaksi Dropdown & Modal -->
    <script>
        function toggleFieldDropdown(id) {
            const dropdown = document.getElementById(id);
            const isHidden = dropdown.classList.contains('hidden');
            
            // Tutup dropdown lain yang terbuka
            document.querySelectorAll('[id^="field-dropdown-"]').forEach(el => {
                if (el.id !== id) el.classList.add('hidden');
            });

            if (isHidden) {
                dropdown.classList.remove('hidden');
            } else {
                dropdown.classList.add('hidden');
            }
        }

        // Tutup dropdown ketika mengklik di luar
        window.addEventListener('click', function(e) {
            if (!e.target.closest('.relative')) {
                document.querySelectorAll('[id^="field-dropdown-"]').forEach(el => {
                    el.classList.add('hidden');
                });
            }
        });

        function toggleFieldStatus(fieldId, currentStatus, fieldName) {
            const form = document.getElementById('toggle-field-status-form');
            form.action = `/mitra/fields/${fieldId}/toggle-status`;

            if (currentStatus === 'aktif') {
                document.getElementById('deactivate-field-name').textContent = fieldName;
                document.getElementById('confirm-deactivate-btn').onclick = function() {
                    form.submit();
                };
                document.getElementById('modal-deactivate-field').classList.remove('hidden');
            } else {
                // Aktifkan kembali secara langsung
                form.submit();
            }
        }

        function closeDeactivateModal() {
            document.getElementById('modal-deactivate-field').classList.add('hidden');
        }

        function confirmDeleteDraftField(fieldId, fieldName) {
            const form = document.getElementById('delete-draft-field-form');
            form.action = `/mitra/fields/${fieldId}`;
            document.getElementById('delete-draft-field-name').textContent = fieldName;
            document.getElementById('modal-delete-draft-field').classList.remove('hidden');
        }

        function closeDeleteDraftModal() {
            document.getElementById('modal-delete-draft-field').classList.add('hidden');
        }
    </script>
</x-layouts.mitra>

