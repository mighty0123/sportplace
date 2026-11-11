<div class="space-y-6">

    <!-- Breadcrumbs -->
    <nav class="flex items-center gap-2 text-xs font-medium text-slate-500" aria-label="Breadcrumb">
        <a href="{{ route('mitra.dashboard') }}" class="hover:text-emerald-700 transition">SportPlace Mitra</a>
        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <a href="{{ route('mitra.venues.index') }}" class="hover:text-emerald-700 transition">Venue & Lapangan</a>
        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        @if ($venue)
            <span class="text-slate-800 font-semibold truncate max-w-xs">{{ $venue->nama_venue }}</span>
        @else
            <span class="text-slate-800 font-semibold">Semua Lapangan (Lintas Venue)</span>
        @endif
    </nav>

    <!-- Header Halaman -->
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
                Kelola unit lapangan, status operasional, fasilitas, galeri foto, dan tarif sewa per jam secara terpusat.
            </p>
        </div>

        <div class="flex items-center gap-3">
            @if ($venuesOptions->isNotEmpty())
                <button
                    type="button"
                    wire:click="openCreateModal"
                    class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-full bg-[#0B7B69] hover:bg-[#086354] active:scale-95 text-white text-sm font-semibold shadow-sm hover:shadow transition duration-150 min-h-[44px] cursor-pointer shrink-0"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Tambah Lapangan</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Metric Stat Cards (4 Kartu) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
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
                <span class="text-3xl font-bold text-slate-900 tabular-nums">{{ $fieldStats['total'] }}</span>
                <span class="text-xs font-medium text-slate-500">unit</span>
            </div>
        </div>

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
                <span class="text-3xl font-bold text-emerald-700 tabular-nums">{{ $fieldStats['aktif'] }}</span>
                <span class="text-xs font-medium text-emerald-600">unit aktif</span>
            </div>
        </div>

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
                <span class="text-3xl font-bold text-blue-700 tabular-nums">{{ $fieldStats['pending_review'] }}</span>
                <span class="text-xs font-medium text-blue-600">sedang ditinjau</span>
            </div>
        </div>

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
                <span class="text-3xl font-bold text-rose-700 tabular-nums">{{ $fieldStats['rejected'] }}</span>
                <span class="text-xs font-medium text-rose-600">revisi admin</span>
            </div>
        </div>
    </div>

    <!-- Filter Bar & Sport Tabs (Reaktif Livewire) -->
    <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/90 shadow-xs space-y-4">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 flex-1">
                <!-- Input Search -->
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Cari nama lapangan..."
                        class="w-full pl-10 pr-4 py-2.5 text-sm rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#0B7B69] focus:border-transparent min-h-[44px]"
                    >
                </div>

                <!-- Filter Status Dropdown -->
                <div class="w-full sm:w-48">
                    <select
                        wire:model.live="status"
                        class="w-full px-3.5 py-2.5 text-sm rounded-2xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-[#0B7B69] focus:border-transparent min-h-[44px] cursor-pointer"
                    >
                        <option value="semua">Semua Status</option>
                        <option value="aktif">Aktif</option>
                        <option value="pending_review">Menunggu Verifikasi</option>
                        <option value="draft">Draft</option>
                        <option value="nonaktif">Nonaktif</option>
                        <option value="rejected">Perlu Tindakan / Ditolak</option>
                    </select>
                </div>

                @if ($search !== '' || $status !== 'semua' || $sportId !== 'semua')
                    <button
                        type="button"
                        wire:click="resetFilters"
                        class="px-4 py-2.5 rounded-full text-slate-500 hover:text-slate-800 hover:bg-slate-100 text-xs font-semibold flex items-center justify-center transition cursor-pointer min-h-[44px]"
                    >
                        Reset
                    </button>
                @endif
            </div>

            <!-- Toggle Lintas Venue -->
            <div class="flex items-center gap-2 pt-2 lg:pt-0 border-t lg:border-t-0 border-slate-100 shrink-0">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input
                        type="checkbox"
                        class="sr-only peer"
                        wire:click="toggleCrossVenue"
                        {{ $showAllVenues ? 'checked' : '' }}
                    >
                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#0B7B69]"></div>
                    <span class="ml-3 text-xs sm:text-sm font-medium text-slate-700 select-none">Tampilkan semua lapangan (lintas venue)</span>
                </label>
            </div>
        </div>

        <!-- Sport Filter Tabs -->
        @if ($sportsList->count() > 0)
            <div class="flex items-center gap-2 overflow-x-auto pt-2 no-scrollbar border-t border-slate-100 pb-1">
                <span class="text-xs font-semibold text-slate-500 shrink-0 mr-1">Olahraga:</span>
                <button
                    type="button"
                    wire:click="setSportFilter('semua')"
                    class="px-3.5 py-1.5 rounded-full text-xs font-medium transition shrink-0 cursor-pointer {{ $sportId === 'semua' ? 'bg-[#0B7B69] text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                >
                    Semua Olahraga
                </button>
                @foreach ($sportsList as $sport)
                    <button
                        type="button"
                        wire:click="setSportFilter('{{ $sport->id }}')"
                        class="px-3.5 py-1.5 rounded-full text-xs font-medium transition shrink-0 cursor-pointer {{ $sportId == $sport->id ? 'bg-[#0B7B69] text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                    >
                        {{ $sport->nama_olahraga }}
                    </button>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Tabel Daftar Lapangan -->
    <div wire:loading.class="opacity-50 pointer-events-none" wire:target="search,status,sportId,toggleCrossVenue,setSportFilter,resetFilters">
        @if ($fieldsList->isEmpty())
            <div class="bg-white rounded-3xl border border-slate-200/90 p-12 text-center shadow-xs">
                <div class="w-16 h-16 rounded-full bg-emerald-50 text-[#0B7B69] flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900">Belum ada unit lapangan ditemukan</h3>
                <p class="text-sm text-slate-500 max-w-md mx-auto mt-1 mb-6">
                    @if ($search !== '' || $status !== 'semua' || $sportId !== 'semua')
                        Tidak ada lapangan yang cocok dengan kriteria filter Anda. Coba reset filter pencarian.
                    @else
                        Tambahkan unit lapangan olahraga pertama Anda untuk mulai menerima reservasi sewa dari pelanggan.
                    @endif
                </p>
                @if ($search !== '' || $status !== 'semua' || $sportId !== 'semua')
                    <button type="button" wire:click="resetFilters" class="inline-flex items-center justify-center px-5 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold transition cursor-pointer min-h-[44px]">
                        Reset Filter
                    </button>
                @elseif ($venuesOptions->isNotEmpty())
                    <button type="button" wire:click="openCreateModal" class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-full bg-[#0B7B69] hover:bg-[#086354] text-white text-sm font-semibold shadow-sm transition min-h-[44px] cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Tambah Lapangan Baru</span>
                    </button>
                @endif
            </div>
        @else
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
                            @foreach ($fieldsList as $field)
                                <tr wire:key="field-row-{{ $field->id }}" class="hover:bg-slate-50/60 transition group {{ $field->status_lapangan === 'rejected' ? 'bg-rose-50/30' : '' }}">
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
                                                    @if ($showAllVenues && $field->venue)
                                                        <span>•</span>
                                                        <span class="text-emerald-700 font-medium truncate">{{ $field->venue->nama_venue }}</span>
                                                    @endif
                                                </div>
                                                @if ($field->status_lapangan === 'rejected' && $field->alasan_penolakan)
                                                    <p class="text-[11px] text-rose-600 font-medium mt-1 truncate max-w-xs" title="{{ $field->alasan_penolakan }}">
                                                        Revisi: {{ $field->alasan_penolakan }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    <td class="py-4 px-4">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#0B7B69]"></span>
                                            {{ $field->sport?->nama_olahraga ?? 'Umum' }}
                                        </span>
                                    </td>

                                    <td class="py-4 px-4 text-xs text-slate-600">
                                        <div class="font-semibold text-slate-800">{{ $field->dimensi }}</div>
                                        <div class="text-slate-500 mt-0.5">{{ $field->kapasitas ?: 'Standar' }}</div>
                                    </td>

                                    <td class="py-4 px-4 font-bold text-slate-900 whitespace-nowrap tabular-nums">
                                        {{ $field->formatted_harga }}
                                        <span class="text-xs font-normal text-slate-500">/ jam</span>
                                    </td>

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

                                    <td class="py-4 px-4 text-center whitespace-nowrap">
                                        @if (in_array($field->status_lapangan, ['aktif', 'nonaktif'], true))
                                            <button
                                                type="button"
                                                wire:click="confirmActionModal({{ $field->id }}, 'toggle_status')"
                                                class="relative inline-flex items-center h-6 w-11 rounded-full transition-colors cursor-pointer focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 {{ $field->status_lapangan === 'aktif' ? 'bg-[#0B7B69]' : 'bg-slate-300' }}"
                                                aria-label="Ubah Status Operasional Lapangan"
                                            >
                                                <span class="inline-block w-4 h-4 transform transition-transform bg-white rounded-full {{ $field->status_lapangan === 'aktif' ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                            </button>
                                        @else
                                            <span class="text-xs text-slate-400 select-none italic">Belum Disetujui</span>
                                        @endif
                                    </td>

                                    <td class="py-4 px-4 sm:px-6 text-right whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1">
                                            <button
                                                type="button"
                                                wire:click="openEditModal({{ $field->id }})"
                                                class="p-2 rounded-full text-slate-600 hover:text-[#0B7B69] hover:bg-emerald-50 transition cursor-pointer min-h-[44px] min-w-[44px] inline-flex items-center justify-center"
                                                title="Edit Lapangan & Galeri Foto"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>

                                            <!-- Dropdown Menu Tindakan -->
                                            <div class="relative inline-block text-left" x-data="{ open: false }" @click.outside="open = false">
                                                <button
                                                    type="button"
                                                    @click="open = !open"
                                                    class="p-2 rounded-full text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition cursor-pointer min-h-[44px] min-w-[44px] inline-flex items-center justify-center"
                                                    title="Tindakan Lainnya"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01"/>
                                                    </svg>
                                                </button>
                                                <div
                                                    x-show="open"
                                                    x-cloak
                                                    x-transition
                                                    class="absolute right-0 mt-1 w-48 bg-white rounded-2xl shadow-xl border border-slate-200 z-30 py-1.5 text-xs font-medium text-left"
                                                >
                                                    @if (in_array($field->status_lapangan, ['draft', 'rejected'], true))
                                                        <button type="button" @click="open = false; $wire.confirmActionModal({{ $field->id }}, 'submit')" class="w-full text-left px-4 py-2.5 hover:bg-emerald-50 text-[#0B7B69] font-semibold flex items-center gap-2 cursor-pointer">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                                            <span>Ajukan Verifikasi</span>
                                                        </button>
                                                    @endif
                                                    <a href="{{ route('mitra.schedules.index', ['field_id' => $field->id]) }}" class="w-full text-left px-4 py-2.5 hover:bg-slate-50 text-slate-700 flex items-center gap-2">
                                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        <span>Atur Jadwal & Slot</span>
                                                    </a>
                                                    <a href="{{ route('mitra.pricing.index', ['field_id' => $field->id]) }}" class="w-full text-left px-4 py-2.5 hover:bg-slate-50 text-slate-700 flex items-center gap-2">
                                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5a1.99 1.99 0 011.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V5a2 2 0 012-2z"/></svg>
                                                        <span>Pengaturan Harga</span>
                                                    </a>
                                                    @if ($field->status_lapangan === 'draft')
                                                        <button type="button" @click="open = false; $wire.confirmActionModal({{ $field->id }}, 'delete')" class="w-full text-left px-4 py-2.5 hover:bg-rose-50 text-rose-600 flex items-center gap-2 cursor-pointer">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                            <span>Hapus Draft</span>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <!-- MODAL FORM TAMBAH / EDIT LAPANGAN & GALERI FOTO INTERAKTIF (LW-3.2) -->
    @if ($showFormModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="field-modal-title">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" wire:click="closeFormModal"></div>
            <div class="flex min-h-full items-center justify-center p-4 sm:p-6">
                <div class="relative w-full max-w-4xl rounded-3xl bg-white shadow-2xl border border-slate-200 overflow-hidden" @click.stop>
                    <!-- Header Modal -->
                    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
                        <div>
                            <h3 id="field-modal-title" class="text-lg font-bold text-slate-900">
                                {{ $editingFieldId ? 'Edit Unit Lapangan & Galeri Foto' : 'Tambah Unit Lapangan Baru' }}
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Lengkapi spesifikasi lapangan, fasilitas pendukung, dan unggah foto fasilitas.
                            </p>
                        </div>
                        <button type="button" wire:click="closeFormModal" class="p-2 rounded-full text-slate-400 hover:text-slate-700 hover:bg-slate-200/70 transition cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form wire:submit="saveField('draft')" class="p-6 max-h-[82vh] overflow-y-auto space-y-6">
                        <!-- Bagian 1: Identitas & Harga -->
                        <div class="space-y-4">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 border-b border-slate-100 pb-2">1. Informasi Dasar & Tarif</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="f_venue_id" value="Venue Induk" required />
                                    <select
                                        id="f_venue_id"
                                        wire:model.live="form_venue_id"
                                        class="w-full rounded-full border {{ $errors->has('form_venue_id') ? 'border-rose-400' : 'border-slate-300' }} bg-white px-4 py-2 text-sm focus:outline-hidden focus:border-[#0B7B69]"
                                        {{ $editingFieldId ? 'disabled' : '' }}
                                    >
                                        <option value="">-- Pilih Venue --</option>
                                        @foreach ($venuesOptions as $vOpt)
                                            <option value="{{ $vOpt->id }}">{{ $vOpt->nama_venue }} ({{ ucfirst($vOpt->status_vanue) }})</option>
                                        @endforeach
                                    </select>
                                    @error('form_venue_id') <p class="mt-1 text-[11px] text-rose-600 font-medium">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <x-input-label for="f_nama_lapangan" value="Nama Lapangan" required />
                                    <input
                                        type="text"
                                        id="f_nama_lapangan"
                                        wire:model.live.debounce.300ms="nama_lapangan"
                                        placeholder="Contoh: Lapangan A (Sintetis)"
                                        class="w-full rounded-full border {{ $errors->has('nama_lapangan') ? 'border-rose-400' : 'border-slate-300' }} px-4 py-2 text-sm focus:outline-hidden focus:border-[#0B7B69]"
                                    >
                                    @error('nama_lapangan') <p class="mt-1 text-[11px] text-rose-600 font-medium">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <x-input-label for="f_sport_id" value="Cabang Olahraga" required />
                                    <select
                                        id="f_sport_id"
                                        wire:model.live="sport_id"
                                        class="w-full rounded-full border {{ $errors->has('sport_id') ? 'border-rose-400' : 'border-slate-300' }} bg-white px-4 py-2 text-sm focus:outline-hidden focus:border-[#0B7B69]"
                                    >
                                        <option value="">-- Pilih Cabang Olahraga --</option>
                                        @foreach ($sportsList as $s)
                                            <option value="{{ $s->id }}">{{ $s->nama_olahraga }}</option>
                                        @endforeach
                                    </select>
                                    @error('sport_id') <p class="mt-1 text-[11px] text-rose-600 font-medium">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <x-input-label for="f_harga_perjam" value="Tarif Dasar per Jam (Rp)" required />
                                    <input
                                        type="number"
                                        id="f_harga_perjam"
                                        wire:model.live.debounce.300ms="harga_perjam"
                                        placeholder="Contoh: 150000"
                                        min="1000"
                                        step="1000"
                                        class="w-full rounded-full border {{ $errors->has('harga_perjam') ? 'border-rose-400' : 'border-slate-300' }} px-4 py-2 text-sm tabular-nums focus:outline-hidden focus:border-[#0B7B69]"
                                    >
                                    @error('harga_perjam') <p class="mt-1 text-[11px] text-rose-600 font-medium">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Bagian 2: Spesifikasi Fisik & Aturan -->
                        <div class="space-y-4">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 border-b border-slate-100 pb-2">2. Spesifikasi Fisik & Kapasitas</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                                <div class="lg:col-span-1">
                                    <x-input-label for="f_tipe_bangunan" value="Tipe Bangunan" required />
                                    <select id="f_tipe_bangunan" wire:model.live="tipe_bangunan" class="w-full rounded-full border border-slate-300 bg-white px-3.5 py-2 text-sm focus:outline-hidden focus:border-[#0B7B69]">
                                        <option value="indoor">Indoor</option>
                                        <option value="semi_outdoor">Semi-Outdoor</option>
                                        <option value="outdoor">Outdoor</option>
                                    </select>
                                </div>

                                <div class="lg:col-span-2">
                                    <x-input-label for="f_jenis_permukaan" value="Jenis Permukaan" required />
                                    <input
                                        type="text"
                                        id="f_jenis_permukaan"
                                        wire:model.live.debounce.300ms="jenis_permukaan"
                                        placeholder="Contoh: Rumput Sintetis / Vinyl / Parquet"
                                        class="w-full rounded-full border {{ $errors->has('jenis_permukaan') ? 'border-rose-400' : 'border-slate-300' }} px-4 py-2 text-sm focus:outline-hidden focus:border-[#0B7B69]"
                                    >
                                    @error('jenis_permukaan') <p class="mt-1 text-[11px] text-rose-600 font-medium">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <x-input-label for="f_panjang" value="Panjang (m)" />
                                    <input type="number" id="f_panjang" wire:model.live="panjang" placeholder="40" step="0.5" class="w-full rounded-full border border-slate-300 px-3.5 py-2 text-sm tabular-nums focus:outline-hidden focus:border-[#0B7B69]">
                                </div>

                                <div>
                                    <x-input-label for="f_lebar" value="Lebar (m)" />
                                    <input type="number" id="f_lebar" wire:model.live="lebar" placeholder="20" step="0.5" class="w-full rounded-full border border-slate-300 px-3.5 py-2 text-sm tabular-nums focus:outline-hidden focus:border-[#0B7B69]">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="f_kapasitas" value="Kapasitas / Rekomendasi Pemain" />
                                    <input type="text" id="f_kapasitas" wire:model.live="kapasitas" placeholder="Contoh: 5 vs 5 atau Maksimal 12 Orang" class="w-full rounded-full border border-slate-300 px-4 py-2 text-sm focus:outline-hidden focus:border-[#0B7B69]">
                                </div>

                                <div>
                                    <x-input-label for="f_deskripsi" value="Deskripsi Lapangan" />
                                    <input type="text" id="f_deskripsi" wire:model.live="deskripsi" placeholder="Informasi keunggulan atau pencahayaan lapangan..." class="w-full rounded-full border border-slate-300 px-4 py-2 text-sm focus:outline-hidden focus:border-[#0B7B69]">
                                </div>
                            </div>
                        </div>

                        <!-- Bagian 3: Fasilitas Pendukung -->
                        @if ($facilitiesList->isNotEmpty())
                            <div class="space-y-3">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 border-b border-slate-100 pb-2">3. Fasilitas Pendukung</h4>
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($facilitiesList as $fac)
                                        <label class="inline-flex items-center gap-2 px-3.5 py-2 rounded-full border cursor-pointer text-xs font-medium transition {{ in_array((string)$fac->id, $facility_ids) ? 'bg-[#E8F5F1] border-[#0B7B69] text-[#0B7B69] font-semibold' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                                            <input type="checkbox" wire:model.live="facility_ids" value="{{ $fac->id }}" class="sr-only">
                                            @if (in_array((string)$fac->id, $facility_ids))
                                                <svg class="w-3.5 h-3.5 text-[#0B7B69]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            @endif
                                            <span>{{ $fac->nama }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Bagian 4: Galeri Foto Lapangan Interaktif (LW-3.2) -->
                        <div class="space-y-4">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">4. Galeri Foto Lapangan (Maks. 8 Foto)</h4>
                                <span class="text-[11px] font-medium text-slate-500">
                                    {{ count($existingPhotos) + (is_array($photos) ? count($photos) : 0) }} / 8 Foto
                                </span>
                            </div>

                            <!-- Foto Tersimpan Saat Ini (Mode Edit: Hapus & Set Utama Instan Tanpa Reload) -->
                            @if (!empty($existingPhotos))
                                <div>
                                    <p class="text-[11px] font-semibold text-slate-600 mb-2">Foto Terpasang Saat Ini:</p>
                                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                        @foreach ($existingPhotos as $photo)
                                            <div wire:key="existing-photo-{{ $photo['id'] }}" class="relative group rounded-2xl overflow-hidden border-2 {{ $photo['is_primary'] ? 'border-[#0B7B69] shadow-sm' : 'border-slate-200' }} bg-slate-100 aspect-4/3">
                                                <img src="{{ $photo['foto_url'] }}" alt="Foto Lapangan" class="w-full h-full object-cover">

                                                @if ($photo['is_primary'])
                                                    <span class="absolute top-2 left-2 px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#0B7B69] text-white shadow-xs">
                                                        Foto Utama
                                                    </span>
                                                @endif

                                                <!-- Overlay Aksi Hover / Touch -->
                                                <div class="absolute inset-x-0 bottom-0 p-2 bg-linear-to-t from-black/80 via-black/40 to-transparent flex items-center justify-between gap-1">
                                                    @if (!$photo['is_primary'])
                                                        <button
                                                            type="button"
                                                            wire:click="setPrimaryExistingPhoto({{ $photo['id'] }})"
                                                            class="px-2 py-1 rounded-lg bg-white/90 hover:bg-white text-[10px] font-bold text-slate-900 transition cursor-pointer"
                                                            title="Jadikan Foto Utama"
                                                        >
                                                            Set Utama
                                                        </button>
                                                    @else
                                                        <span></span>
                                                    @endif
                                                    <button
                                                        type="button"
                                                        wire:click="deleteExistingPhoto({{ $photo['id'] }})"
                                                        class="p-1.5 rounded-lg bg-rose-600/90 hover:bg-rose-600 text-white transition cursor-pointer"
                                                        title="Hapus Foto Ini"
                                                    >
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Area Drag & Drop Upload Foto Baru -->
                            <div
                                x-data="{ isUploading: false, progress: 0, isDragging: false }"
                                x-on:livewire-upload-start="isUploading = true; progress = 0"
                                x-on:livewire-upload-finish="isUploading = false; progress = 100"
                                x-on:livewire-upload-cancel="isUploading = false"
                                x-on:livewire-upload-error="isUploading = false"
                                x-on:livewire-upload-progress="progress = $event.detail.progress"
                            >
                                <label
                                    @dragover.prevent="isDragging = true"
                                    @dragleave.prevent="isDragging = false"
                                    @drop.prevent="isDragging = false"
                                    class="flex flex-col items-center justify-center w-full p-6 border-2 border-dashed rounded-2xl cursor-pointer transition {{ $errors->has('photos') || $errors->has('photos.*') ? 'border-rose-300 bg-rose-50/30' : 'border-slate-300 hover:border-[#0B7B69] hover:bg-emerald-50/20' }}"
                                    :class="isDragging ? 'border-[#0B7B69] bg-emerald-50/40' : ''"
                                >
                                    <svg class="w-8 h-8 text-[#0B7B69] mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <p class="text-xs sm:text-sm font-semibold text-slate-700">
                                        Klik untuk pilih atau seret foto ke area ini
                                    </p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">
                                        JPG, PNG, WEBP • Maksimal 5 MB per foto (bisa pilih banyak sekaligus)
                                    </p>
                                    <input type="file" wire:model="photos" multiple accept="image/jpeg,image/png,image/webp" class="hidden">
                                </label>

                                <!-- Upload Progress Bar -->
                                <div x-show="isUploading" x-cloak class="mt-3">
                                    <div class="flex items-center justify-between text-xs font-medium text-[#0B7B69] mb-1">
                                        <span>Mengunggah foto ke preview...</span>
                                        <span x-text="progress + '%'" class="tabular-nums"></span>
                                    </div>
                                    <div class="w-full h-1.5 bg-slate-200 rounded-full overflow-hidden">
                                        <div class="h-full bg-[#0B7B69] transition-all" :style="'width: ' + progress + '%'"></div>
                                    </div>
                                </div>

                                @error('photos') <p class="mt-2 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                                @error('photos.*') <p class="mt-2 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                            </div>

                            <!-- Live Thumbnail Preview Foto Baru Sebelum Disimpan -->
                            @if (is_array($photos) && count($photos) > 0)
                                <div>
                                    <p class="text-[11px] font-semibold text-emerald-700 mb-2">Preview Foto Baru (Akan disimpan saat submit):</p>
                                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                        @foreach ($photos as $idx => $newPhoto)
                                            <div wire:key="new-photo-{{ $idx }}" class="relative group rounded-2xl overflow-hidden border-2 {{ empty($existingPhotos) && $idx === $newPrimaryIndex ? 'border-[#0B7B69]' : 'border-emerald-200' }} bg-slate-100 aspect-4/3">
                                                @if (method_exists($newPhoto, 'temporaryUrl'))
                                                    <img src="{{ $newPhoto->temporaryUrl() }}" alt="Preview Foto Baru" class="w-full h-full object-cover">
                                                @endif

                                                @if (empty($existingPhotos) && $idx === $newPrimaryIndex)
                                                    <span class="absolute top-2 left-2 px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#0B7B69] text-white shadow-xs">
                                                        Foto Utama
                                                    </span>
                                                @endif

                                                <div class="absolute inset-x-0 bottom-0 p-2 bg-linear-to-t from-black/80 via-black/40 to-transparent flex items-center justify-between gap-1">
                                                    @if (empty($existingPhotos) && $idx !== $newPrimaryIndex)
                                                        <button
                                                            type="button"
                                                            wire:click="setPrimaryNewPhoto({{ $idx }})"
                                                            class="px-2 py-1 rounded-lg bg-white/90 hover:bg-white text-[10px] font-bold text-slate-900 cursor-pointer"
                                                        >
                                                            Set Utama
                                                        </button>
                                                    @else
                                                        <span></span>
                                                    @endif
                                                    <button
                                                        type="button"
                                                        wire:click="removeNewPhoto({{ $idx }})"
                                                        class="p-1.5 rounded-lg bg-rose-600/90 hover:bg-rose-600 text-white cursor-pointer"
                                                        title="Batal Unggah Foto Ini"
                                                    >
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Modal Footer Buttons -->
                        <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-2.5 pt-4 border-t border-slate-100">
                            <button type="button" wire:click="closeFormModal" class="w-full sm:w-auto px-5 py-2.5 rounded-full border border-slate-300 bg-white hover:bg-slate-50 text-xs font-semibold text-slate-700 cursor-pointer min-h-[42px]">
                                Batal
                            </button>

                            @php
                                $editingField = $editingFieldId ? \App\Models\Field::find($editingFieldId) : null;
                                $canSubmitField = ! $editingField || in_array($editingField->status_lapangan, ['draft', 'rejected'], true);
                            @endphp

                            @if ($canSubmitField)
                                <button
                                    type="button"
                                    wire:click="saveField('draft')"
                                    wire:loading.attr="disabled"
                                    wire:target="saveField,photos"
                                    class="w-full sm:w-auto px-5 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-semibold cursor-pointer min-h-[42px] disabled:opacity-50"
                                >
                                    Simpan Draft
                                </button>
                                <button
                                    type="button"
                                    wire:click="saveField('submit')"
                                    wire:loading.attr="disabled"
                                    wire:target="saveField,photos"
                                    class="w-full sm:w-auto px-6 py-2.5 rounded-full bg-[#0B7B69] hover:bg-[#086354] text-white text-xs sm:text-sm font-semibold shadow-xs cursor-pointer min-h-[42px] disabled:opacity-50"
                                >
                                    Simpan & Ajukan Review
                                </button>
                            @else
                                <button
                                    type="button"
                                    wire:click="saveField('save')"
                                    wire:loading.attr="disabled"
                                    wire:target="saveField,photos"
                                    class="w-full sm:w-auto px-6 py-2.5 rounded-full bg-[#0B7B69] hover:bg-[#086354] text-white text-xs sm:text-sm font-semibold shadow-xs cursor-pointer min-h-[42px] disabled:opacity-50"
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

    <!-- MODAL KONFIRMASI AKSI LAPANGAN -->
    @if ($showConfirmModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" wire:click="closeConfirmModal"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl border border-slate-200 text-center" @click.stop>
                    @php
                        $isDanger = $confirmAction === 'delete' || ($confirmAction === 'toggle_status' && $confirmCurrentStatus === 'aktif');
                    @endphp
                    <div class="w-14 h-14 rounded-full mx-auto mb-4 flex items-center justify-center {{ $isDanger ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-[#0B7B69]' }}">
                        @if ($isDanger)
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        @else
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        @endif
                    </div>

                    <h3 class="text-lg font-bold text-slate-900 mb-2">
                        @if ($confirmAction === 'submit') Ajukan Verifikasi Lapangan?
                        @elseif ($confirmAction === 'toggle_status')
                            {{ $confirmCurrentStatus === 'aktif' ? 'Nonaktifkan Lapangan?' : 'Aktifkan Kembali Lapangan?' }}
                        @elseif ($confirmAction === 'delete') Hapus Draft Lapangan?
                        @endif
                    </h3>

                    <p class="text-xs sm:text-sm text-slate-500 leading-relaxed mb-6">
                        @if ($confirmAction === 'submit')
                            Unit lapangan <strong>{{ $confirmFieldName }}</strong> akan diajukan ke tim moderasi Admin SportPlace. Pastikan foto dan data sudah sesuai.
                        @elseif ($confirmAction === 'toggle_status')
                            @if ($confirmCurrentStatus === 'aktif')
                                Apakah Anda yakin menonaktifkan <strong>{{ $confirmFieldName }}</strong>? Lapangan tidak dapat dipesan sementara waktu.
                            @else
                                Lapangan <strong>{{ $confirmFieldName }}</strong> akan dibuka kembali untuk menerima booking pelanggan.
                            @endif
                        @elseif ($confirmAction === 'delete')
                            Apakah Anda yakin ingin menghapus permanen draft lapangan <strong>{{ $confirmFieldName }}</strong> beserta seluruh fotonya?
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
                            class="flex-1 px-4 py-2.5 rounded-full text-white text-xs font-semibold shadow-xs cursor-pointer min-h-[42px] {{ $isDanger ? 'bg-rose-600 hover:bg-rose-700' : 'bg-[#0B7B69] hover:bg-[#086354]' }}"
                        >
                            Ya, Lanjutkan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
