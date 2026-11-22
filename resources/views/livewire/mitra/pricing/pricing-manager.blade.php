<div class="space-y-6">

    <!-- Top Breadcrumbs & Page Header -->
    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-xs">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-medium text-slate-500 mb-2">
                    <a href="{{ route('mitra.dashboard') }}" class="hover:text-emerald-700 transition">SportPlace Mitra</a>
                    <span class="text-slate-300">/</span>
                    <span>Operasional</span>
                    <span class="text-slate-300">/</span>
                    <span class="text-slate-900 font-semibold">Pengaturan Harga</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">
                    Pengaturan Tarif Sewa & Peak Hours
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-2xl leading-relaxed">
                    Tentukan tarif sewa dasar per jam atau aktifkan tarif dinamis otomatis pada jam sibuk (<em>Peak Hours</em>) seperti malam hari dan akhir pekan.
                </p>
            </div>

            @if ($currentField)
                <div class="flex items-center gap-3 shrink-0">
                    <a
                        href="{{ route('mitra.schedules.index', ['field_id' => $currentField->id]) }}"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs sm:text-sm font-semibold transition cursor-pointer min-h-[42px]"
                    >
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>Lihat Kalender & Slot</span>
                    </a>
                </div>
            @endif
        </div>

        <!-- Selector Dropdown Venue & Lapangan (Reaktif Livewire — Tanpa Reload, Rata Kiri-Kanan) -->
        @if ($allFieldsList->isNotEmpty())
            <div class="mt-6 pt-6 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 w-full">
                <!-- Dropdown Venue (Di Kiri) -->
                <div class="w-full">
                    <label for="select-venue" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-[#0B7B69]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span>Pilih Venue</span>
                    </label>
                    <div class="relative">
                        <select
                            id="select-venue"
                            wire:model.live="venueId"
                            class="w-full pl-4 pr-10 py-2.5 text-xs sm:text-sm font-semibold rounded-full border border-slate-200 bg-white hover:border-[#0B7B69]/50 focus:outline-hidden focus:border-[#0B7B69] focus:ring-1 focus:ring-[#0B7B69] transition shadow-xs cursor-pointer appearance-none min-h-[44px] text-slate-800"
                        >
                            @foreach ($venuesList as $v)
                                <option value="{{ $v->id }}">{{ $v->nama_venue }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Dropdown Pilih Lapangan (Di Kanan - Full ke Kanan) -->
                <div class="w-full">
                    <label for="select-field" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5 flex items-center justify-between">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-[#0B7B69]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>Pilih Lapangan</span>
                        </span>
                        <span class="text-[11px] font-normal text-slate-400">
                            {{ $venueFieldsList->count() }} Lapangan
                        </span>
                    </label>
                    <div class="relative">
                        <select
                            id="select-field"
                            wire:model.live="fieldId"
                            class="w-full pl-4 pr-10 py-2.5 text-xs sm:text-sm font-semibold rounded-full border border-slate-200 bg-white hover:border-[#0B7B69]/50 focus:outline-hidden focus:border-[#0B7B69] focus:ring-1 focus:ring-[#0B7B69] transition shadow-xs cursor-pointer appearance-none min-h-[44px] text-slate-800"
                        >
                            @forelse ($venueFieldsList as $f)
                                @php
                                    $isDynamic = is_array($f->meta_harga) && ($f->meta_harga['tipe_skema'] ?? '') === 'peak_off_peak';
                                @endphp
                                <option value="{{ $f->id }}">
                                    {{ $f->nama_lapangan }} ({{ $f->sport?->nama_olahraga ?? 'Umum' }}) {{ $isDynamic ? '— [Peak Hours]' : '— [Tarif Reguler]' }}
                                </option>
                            @empty
                                <option value="">Belum ada lapangan di venue ini</option>
                            @endforelse
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @if ($allFieldsList->isEmpty() || !$currentField)
        <!-- Empty State -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-12 text-center shadow-xs">
            <div class="w-16 h-16 rounded-full bg-emerald-50 text-[#0B7B69] flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 7h.01M7 3h5a1.99 1.99 0 011.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V5a2 2 0 012-2z"/>
                </svg>
            </div>
            <h3 class="text-base font-bold text-slate-900 mb-1">Belum Ada Lapangan</h3>
            <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto mb-6">
                Tambahkan lapangan terlebih dahulu untuk mulai mengatur harga sewa reguler maupun jam sibuk (peak).
            </p>
            <a
                href="{{ route('mitra.venues.index') }}"
                class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-full bg-[#0B7B69] hover:bg-[#086354] text-white text-xs sm:text-sm font-semibold shadow-xs transition min-h-[44px]"
            >
                <span>Kelola Venue & Lapangan &rarr;</span>
            </a>
        </div>
    @else
        <!-- Selected Field Identity Banner -->
        <div class="bg-emerald-50/70 border border-emerald-200/80 rounded-3xl p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-2xl bg-white text-[#0B7B69] border border-emerald-200 flex items-center justify-center shrink-0 shadow-xs">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-base font-bold text-slate-900">{{ $currentField->nama_lapangan }}</h3>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-white text-[#0B7B69] border border-emerald-300">
                            {{ $currentField->sport?->nama_olahraga ?? 'Umum' }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-600 mt-0.5">
                        Venue: <span class="font-semibold text-slate-800">{{ $currentField->venue?->nama_venue }}</span> • 
                        Tarif Aktif Saat Ini: <span class="font-bold text-emerald-800 tabular-nums">{{ $currentField->formatted_harga }}/jam</span>
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 self-start sm:self-auto text-xs">
                <span class="text-slate-500">Skema Terpasang:</span>
                @if ($tipe_skema === 'peak_off_peak')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white border border-amber-300 font-bold text-amber-800">
                        <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <span>Dinamis (Peak / Off-Peak)</span>
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white border border-emerald-300 font-bold text-[#0B7B69]">
                        <svg class="w-3.5 h-3.5 text-[#0B7B69]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5a1.99 1.99 0 011.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V5a2 2 0 012-2z"/>
                        </svg>
                        <span>Tarif Flat</span>
                    </span>
                @endif
            </div>
        </div>

        <!-- MAIN WORKSPACE GRID -->
        <form wire:submit="savePricing" wire:loading.class="opacity-70 pointer-events-none" wire:target="venueId,fieldId" class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- KOLOM KIRI (7 Kolom): Formulir Pengaturan Harga -->
            <div class="lg:col-span-7 space-y-6">
                <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-7 shadow-xs space-y-6">
                    
                    <!-- Pilihan Skema Tarif (Reaktif Livewire) -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-3">
                            Pilih Model Penetapan Tarif <span class="text-rose-500">*</span>
                        </label>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <!-- Option 1: Flat -->
                            <label class="relative flex flex-col p-4 rounded-2xl border-2 transition cursor-pointer select-none {{ $tipe_skema === 'flat' ? 'border-[#0B7B69] bg-emerald-50/30' : 'border-slate-200 hover:border-slate-300 bg-white' }}">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center shrink-0 border border-slate-200/80">
                                            <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 7h.01M7 3h5a1.99 1.99 0 011.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V5a2 2 0 012-2z"/>
                                            </svg>
                                        </div>
                                        <span class="font-bold text-sm text-slate-900">Tarif Flat (Tetap)</span>
                                    </div>
                                    <input
                                        type="radio"
                                        wire:model.live="tipe_skema"
                                        value="flat"
                                        class="w-4 h-4 text-[#0B7B69] focus:ring-emerald-500"
                                    >
                                </div>
                                <p class="text-xs text-slate-500 leading-relaxed">
                                    Satu harga tunggal yang berlaku untuk semua hari dan seluruh jam operasional.
                                </p>
                            </label>

                            <!-- Option 2: Peak / Off-Peak -->
                            <label class="relative flex flex-col p-4 rounded-2xl border-2 transition cursor-pointer select-none {{ $tipe_skema === 'peak_off_peak' ? 'border-[#0B7B69] bg-emerald-50/30' : 'border-slate-200 hover:border-slate-300 bg-white' }}">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 border border-amber-200">
                                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                            </svg>
                                        </div>
                                        <span class="font-bold text-sm text-slate-900">Peak / Off-Peak</span>
                                    </div>
                                    <input
                                        type="radio"
                                        wire:model.live="tipe_skema"
                                        value="peak_off_peak"
                                        class="w-4 h-4 text-[#0B7B69] focus:ring-emerald-500"
                                    >
                                </div>
                                <p class="text-xs text-slate-500 leading-relaxed">
                                    Harga dinamis otomatis: tarif lebih tinggi pada jam sibuk/weekend, tarif standar di waktu lainnya.
                                </p>
                            </label>
                        </div>
                    </div>

                    @if ($tipe_skema === 'flat')
                        <!-- SECTION 1: INPUT TARIF DASAR (FLAT) -->
                        <div class="space-y-4 pt-4 border-t border-slate-100">
                            <div>
                                <label class="block text-xs font-bold text-slate-800 mb-1.5">
                                    Tarif Sewa Reguler per Jam (Rp) <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-xs font-bold text-slate-400 select-none">
                                        Rp
                                    </span>
                                    <input
                                        type="number"
                                        wire:model.live.debounce.250ms="harga_perjam"
                                        step="1000"
                                        min="0"
                                        class="w-full pl-12 pr-4 py-2.5 rounded-2xl border border-slate-300 text-sm font-bold text-slate-900 focus:ring-2 focus:ring-emerald-500 focus:outline-none tabular-nums"
                                    >
                                </div>
                                <p class="text-[11px] text-slate-400 mt-1">Tarif ini berlaku seragam setiap kali penyewa memilih slot lapangan.</p>
                                @error('harga_perjam')
                                    <p class="text-[11px] text-rose-600 font-medium mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    @else
                        <!-- SECTION 2: INPUT TARIF PEAK & OFF-PEAK (REAKTIF LIVEWIRE) -->
                        <div class="space-y-6 pt-4 border-t border-slate-100">
                            
                            <!-- Input Nominal Off-Peak & Peak -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-800 mb-1.5">
                                        Tarif Standar / Off-Peak (Rp) <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-xs font-bold text-slate-400 select-none">
                                            Rp
                                        </span>
                                        <input
                                            type="number"
                                            wire:model.live.debounce.250ms="harga_off_peak"
                                            step="1000"
                                            min="0"
                                            class="w-full pl-12 pr-4 py-2.5 rounded-2xl border border-slate-300 text-sm font-bold text-slate-900 focus:ring-2 focus:ring-emerald-500 focus:outline-none tabular-nums"
                                        >
                                    </div>
                                    <p class="text-[10px] text-slate-400 mt-1">Berlaku di luar jam peak (hari kerja siang).</p>
                                    @error('harga_off_peak')
                                        <p class="text-[11px] text-rose-600 font-medium mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-800 mb-1.5">
                                        Tarif Jam Sibuk / Peak Hours (Rp) <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-xs font-bold text-amber-600 select-none">
                                            Rp
                                        </span>
                                        <input
                                            type="number"
                                            wire:model.live.debounce.250ms="harga_peak"
                                            step="1000"
                                            min="0"
                                            class="w-full pl-12 pr-4 py-2.5 rounded-2xl border border-amber-300 bg-amber-50/20 text-sm font-bold text-amber-950 focus:ring-2 focus:ring-amber-500 focus:outline-none tabular-nums"
                                        >
                                    </div>
                                    <p class="text-[10px] text-slate-400 mt-1">Berlaku selama periode aturan jam peak.</p>
                                    @error('harga_peak')
                                        <p class="text-[11px] text-rose-600 font-medium mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Preset Cepat Aturan Peak -->
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                                <span class="text-xs font-bold text-slate-800 block mb-2">Preset Aturan Cepat:</span>
                                <div class="flex flex-wrap items-center gap-2">
                                    <button
                                        type="button"
                                        wire:click="applyPreset('weekend')"
                                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-white hover:bg-slate-100 active:scale-95 text-slate-700 border border-slate-300 text-xs font-semibold shadow-2xs transition cursor-pointer"
                                    >
                                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span>Weekend Penuh (Sabtu & Minggu)</span>
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="applyPreset('night')"
                                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-white hover:bg-slate-100 active:scale-95 text-slate-700 border border-slate-300 text-xs font-semibold shadow-2xs transition cursor-pointer"
                                    >
                                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                                        </svg>
                                        <span>Malam Hari (18:00 – 23:00)</span>
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="applyPreset('combo')"
                                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-white hover:bg-emerald-50 active:scale-95 text-[#0B7B69] border border-emerald-300 text-xs font-semibold shadow-2xs transition cursor-pointer"
                                    >
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                        </svg>
                                        <span>Malam Hari + Weekend Penuh</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Dynamic Peak Rules Repeater (Pure PHP Livewire - Tanpa JS Kustom) -->
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                        Aturan Periode Jam Peak <span class="text-rose-500">*</span>
                                    </label>
                                    <button
                                        type="button"
                                        wire:click="addPeakRule"
                                        class="inline-flex items-center gap-1 text-xs font-bold text-[#0B7B69] hover:underline cursor-pointer"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        <span>+ Tambah Aturan Peak</span>
                                    </button>
                                </div>

                                <div class="space-y-3">
                                    @foreach ($peak_rules as $rIdx => $rule)
                                        @php
                                            $ruleDays = $rule['hari'] ?? [];
                                        @endphp
                                        <div wire:key="peak-rule-{{ $rIdx }}" class="p-4 rounded-2xl border border-slate-200 bg-white space-y-3 shadow-2xs relative">
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs font-bold text-slate-800">Aturan Periode #{{ $rIdx + 1 }}</span>
                                                <button
                                                    type="button"
                                                    wire:click="removePeakRule({{ $rIdx }})"
                                                    class="p-1.5 rounded-full text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer"
                                                    title="Hapus Aturan"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </div>

                                            <!-- Hari Berlaku (Interactive Pills) -->
                                            <div>
                                                <span class="text-[11px] font-semibold text-slate-600 block mb-1.5">Hari Berlaku:</span>
                                                <div class="flex flex-wrap items-center gap-1.5">
                                                    @foreach ([1 => 'Sen', 2 => 'Sel', 3 => 'Rab', 4 => 'Kam', 5 => 'Jum', 6 => 'Sab', 7 => 'Min'] as $dNum => $dLabel)
                                                        @php
                                                            $isChecked = in_array($dNum, $ruleDays, true);
                                                        @endphp
                                                        <button
                                                            type="button"
                                                            wire:click="toggleRuleDay({{ $rIdx }}, {{ $dNum }})"
                                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl border text-xs font-semibold cursor-pointer select-none transition {{ $isChecked ? 'bg-[#0B7B69] text-white border-[#0B7B69]' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100' }}"
                                                        >
                                                            <span>{{ $dLabel }}</span>
                                                        </button>
                                                    @endforeach
                                                </div>
                                                @error("peak_rules.{$rIdx}.hari")
                                                    <p class="text-[11px] text-rose-600 font-medium mt-1">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <!-- Jam Mulai & Jam Selesai -->
                                            <div class="flex items-center gap-2 pt-1">
                                                <div class="flex-1">
                                                    <span class="text-[11px] font-semibold text-slate-600 block mb-1">Jam Mulai:</span>
                                                    <input
                                                        type="time"
                                                        wire:model.live="peak_rules.{{ $rIdx }}.jam_mulai"
                                                        class="w-full px-3 py-1.5 rounded-xl border border-slate-300 text-xs font-medium text-slate-800 tabular-nums focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                                                    >
                                                </div>
                                                <span class="text-xs text-slate-400 mt-5">s/d</span>
                                                <div class="flex-1">
                                                    <span class="text-[11px] font-semibold text-slate-600 block mb-1">Jam Selesai:</span>
                                                    <input
                                                        type="time"
                                                        wire:model.live="peak_rules.{{ $rIdx }}.jam_selesai"
                                                        class="w-full px-3 py-1.5 rounded-xl border border-slate-300 text-xs font-medium text-slate-800 tabular-nums focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                                                    >
                                                </div>
                                            </div>

                                            @error("peak_rules.{$rIdx}.jam_mulai")
                                                <p class="text-[11px] text-rose-600 font-medium mt-1">{{ $message }}</p>
                                            @enderror
                                            @error("peak_rules.{$rIdx}.jam_selesai")
                                                <p class="text-[11px] text-rose-600 font-medium mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Tombol Submit -->
                    <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="savePricing"
                            class="inline-flex items-center gap-2 px-7 py-3 rounded-full bg-[#0B7B69] hover:bg-[#086354] active:scale-95 text-white text-xs sm:text-sm font-semibold shadow-xs transition duration-150 cursor-pointer min-h-[42px] disabled:opacity-60"
                        >
                            <span wire:loading.remove wire:target="savePricing">Simpan Pengaturan Tarif Lapangan</span>
                            <span wire:loading wire:target="savePricing" class="inline-flex items-center gap-1.5">
                                <svg class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg>
                                <span>Menyimpan...</span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN (5 Kolom): Live Pricing Simulator (Computed PHP Property) & Safeguard Policy -->
            <div class="lg:col-span-5 space-y-6">
                
                <!-- Simulasi Perbandingan Tarif (Real-Time dari PHP Computed) -->
                <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-7 shadow-xs space-y-5">
                    <div class="pb-3 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h4 class="text-base font-bold text-slate-900">Simulasi Tarif Sewa</h4>
                            <p class="text-xs text-slate-500 mt-0.5">Perkiraan harga per jam yang akan dilihat penyewa.</p>
                        </div>
                        <span wire:loading wire:target="tipe_skema,harga_perjam,harga_off_peak,harga_peak,peak_rules,toggleRuleDay,applyPreset" class="text-[10px] font-semibold text-[#0B7B69] animate-pulse">
                            Menghitung...
                        </span>
                    </div>

                    <div class="space-y-3">
                        @foreach ($simResults as $simKey => $sim)
                            <div wire:key="sim-card-{{ $simKey }}" class="p-3.5 rounded-2xl border {{ $sim['is_peak'] ? 'border-amber-200 bg-amber-50/30' : 'border-slate-200 bg-slate-50/50' }} flex items-center justify-between transition">
                                <div>
                                    <div class="text-xs font-bold text-slate-800">{{ $sim['title'] }}</div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">{{ $sim['subtitle'] }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-bold tabular-nums {{ $sim['is_peak'] ? 'text-amber-900' : 'text-[#0B7B69]' }}">
                                        {{ $sim['formatted_price'] }}
                                    </div>
                                    <span class="inline-block px-1.5 py-0.2 rounded text-[9px] font-bold {{ $sim['is_peak'] ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-[#0B7B69]' }}">
                                        {{ $sim['badge'] }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Safeguard Policy Callout (BR-008 & OQ-005) -->
                <div class="rounded-3xl bg-[#06231B] text-slate-200 p-6 shadow-xs space-y-3">
                    <div class="flex items-center gap-2 text-emerald-400 font-bold text-xs uppercase tracking-wider">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Kebijakan Harga SportPlace</span>
                    </div>
                    <h4 class="text-sm font-bold text-white">Transparansi Tarif & Proteksi Pesanan</h4>
                    <ul class="text-xs text-emerald-100/70 space-y-2 leading-relaxed">
                        <li class="flex items-start gap-1.5">
                            <span class="text-[#DEEF25] font-bold">•</span>
                            <span>Perubahan tarif yang Anda simpan <strong>tidak akan mengubah harga</strong> reservasi lama yang sudah dipesan penyewa sebelumnya.</span>
                        </li>
                        <li class="flex items-start gap-1.5">
                            <span class="text-[#DEEF25] font-bold">•</span>
                            <span>Penyewa akan selalu melihat rincian harga per slot sebelum melakukan checkout pembayaran.</span>
                        </li>
                    </ul>
                </div>

            </div>
        </form>
    @endif

</div>
