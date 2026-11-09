<x-layouts.mitra title="Pengaturan Tarif Sewa & Peak Hours — SportPlace Mitra" heading="Pengaturan Harga">
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

                @if ($selectedField)
                    <div class="flex items-center gap-3 shrink-0">
                        <a
                            href="{{ route('mitra.schedules.index', ['field_id' => $selectedField->id]) }}"
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

            <!-- Field Selector Tabs (Pilih Lapangan) -->
            @if ($allFields->isNotEmpty())
                <div class="mt-6 pt-6 border-t border-slate-100">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">
                            Pilih Lapangan:
                        </span>
                        <span class="text-xs text-slate-400">
                            {{ $allFields->count() }} Lapangan Terdaftar
                        </span>
                    </div>

                    <div class="flex items-center gap-2 overflow-x-auto pb-2 no-scrollbar">
                        @foreach ($allFields as $f)
                            @php
                                $isActive = $selectedField && $selectedField->id === $f->id;
                                $isDynamic = is_array($f->meta_harga) && ($f->meta_harga['tipe_skema'] ?? '') === 'peak_off_peak';
                            @endphp
                            <a
                                href="{{ route('mitra.pricing.index', ['field_id' => $f->id]) }}"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-semibold whitespace-nowrap transition border shrink-0 {{ $isActive ? 'bg-[#06231B] text-white border-[#06231B] shadow-xs' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 border-slate-200' }}"
                            >
                                <span class="w-2 h-2 rounded-full {{ $isActive ? 'bg-[#DEEF25]' : ($isDynamic ? 'bg-amber-400' : 'bg-emerald-500') }}"></span>
                                <span>{{ $f->nama_lapangan }}</span>
                                <span class="text-[10px] opacity-70">({{ $f->venue?->nama_venue ?? 'Venue' }})</span>
                                @if ($isDynamic)
                                    <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-amber-200/40 text-amber-900 border border-amber-300/60">Peak</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        @if ($allFields->isEmpty())
            <!-- Empty State Jika Belum Ada Lapangan -->
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
                    class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-full bg-[#0B7B69] hover:bg-[#086354] text-white text-xs sm:text-sm font-semibold shadow-xs transition"
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
                            <h3 class="text-base font-bold text-slate-900">{{ $selectedField->nama_lapangan }}</h3>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-white text-[#0B7B69] border border-emerald-300">
                                {{ $selectedField->sport?->nama_olahraga ?? 'Umum' }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-600 mt-0.5">
                            Venue: <span class="font-semibold text-slate-800">{{ $selectedField->venue?->nama_venue }}</span> • 
                            Tarif Aktif Saat Ini: <span class="font-bold text-emerald-800">{{ $selectedField->formatted_harga }}/jam</span>
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 self-start sm:self-auto text-xs">
                    <span class="text-slate-500">Skema Terpasang:</span>
                    @if (($metaHarga['tipe_skema'] ?? '') === 'peak_off_peak')
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

            @php
                $currentTipe = old('tipe_skema', $metaHarga['tipe_skema'] ?? 'flat');
                $hargaDasar = old('harga_perjam', $selectedField->harga_perjam ?? 100000);
                $hargaOffPeak = old('harga_off_peak', $metaHarga['harga_off_peak'] ?? $hargaDasar);
                $hargaPeak = old('harga_peak', $metaHarga['harga_peak'] ?? round($hargaDasar * 1.3, -3));
                $peakRules = old('peak_rules', $metaHarga['peak_rules'] ?? []);
            @endphp

            <!-- GRID 2 KOLOM: Form Editor (Kiri) & Live Simulation (Kanan) -->
            <form method="POST" action="{{ route('mitra.fields.pricing.update', $selectedField) }}" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                @csrf
                @method('PUT')

                <!-- KOLOM KIRI (7 Kolom): Formulir Pengaturan Harga -->
                <div class="lg:col-span-7 space-y-6">
                    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-7 shadow-xs space-y-6">
                        
                        <!-- Pilihan Skema Tarif -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-3">
                                Pilih Model Penetapan Tarif <span class="text-rose-500">*</span>
                            </label>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <!-- Option 1: Flat -->
                                <label class="relative flex flex-col p-4 rounded-2xl border-2 transition cursor-pointer select-none {{ $currentTipe === 'flat' ? 'border-[#0B7B69] bg-emerald-50/30' : 'border-slate-200 hover:border-slate-300 bg-white' }}">
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
                                            name="tipe_skema"
                                            value="flat"
                                            onchange="switchPricingScheme('flat')"
                                            {{ $currentTipe === 'flat' ? 'checked' : '' }}
                                            class="w-4 h-4 text-[#0B7B69] focus:ring-emerald-500"
                                        >
                                    </div>
                                    <p class="text-xs text-slate-500 leading-relaxed">
                                        Satu harga tunggal yang berlaku untuk semua hari dan seluruh jam operasional.
                                    </p>
                                </label>

                                <!-- Option 2: Peak / Off-Peak -->
                                <label class="relative flex flex-col p-4 rounded-2xl border-2 transition cursor-pointer select-none {{ $currentTipe === 'peak_off_peak' ? 'border-[#0B7B69] bg-emerald-50/30' : 'border-slate-200 hover:border-slate-300 bg-white' }}">
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
                                            name="tipe_skema"
                                            value="peak_off_peak"
                                            onchange="switchPricingScheme('peak_off_peak')"
                                            {{ $currentTipe === 'peak_off_peak' ? 'checked' : '' }}
                                            class="w-4 h-4 text-[#0B7B69] focus:ring-emerald-500"
                                        >
                                    </div>
                                    <p class="text-xs text-slate-500 leading-relaxed">
                                        Harga dinamis otomatis: tarif lebih tinggi pada jam sibuk/weekend, tarif standar di waktu lainnya.
                                    </p>
                                </label>
                            </div>
                        </div>

                        <!-- SECTION 1: INPUT TARIF DASAR (FLAT) -->
                        <div id="section-flat" class="{{ $currentTipe === 'flat' ? '' : 'hidden' }} space-y-4 pt-4 border-t border-slate-100">
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
                                        id="input-harga-perjam"
                                        name="harga_perjam"
                                        value="{{ $hargaDasar }}"
                                        step="1000"
                                        min="0"
                                        oninput="updatePricingSimulation()"
                                        class="w-full pl-12 pr-4 py-2.5 rounded-2xl border border-slate-300 text-sm font-bold text-slate-900 focus:ring-2 focus:ring-emerald-500 focus:outline-none tabular-nums"
                                    >
                                </div>
                                <p class="text-[11px] text-slate-400 mt-1">Tarif ini berlaku seragam setiap kali penyewa memilih slot lapangan.</p>
                                @error('harga_perjam')
                                    <p class="text-[11px] text-rose-600 font-medium mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- SECTION 2: INPUT TARIF PEAK & OFF-PEAK -->
                        <div id="section-peak" class="{{ $currentTipe === 'peak_off_peak' ? '' : 'hidden' }} space-y-6 pt-4 border-t border-slate-100">
                            
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
                                            id="input-harga-off-peak"
                                            name="harga_off_peak"
                                            value="{{ $hargaOffPeak }}"
                                            step="1000"
                                            min="0"
                                            oninput="syncBasePriceWithOffPeak(); updatePricingSimulation()"
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
                                            id="input-harga-peak"
                                            name="harga_peak"
                                            value="{{ $hargaPeak }}"
                                            step="1000"
                                            min="0"
                                            oninput="updatePricingSimulation()"
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
                                        onclick="applyPreset('weekend')"
                                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-white hover:bg-slate-100 text-slate-700 border border-slate-300 text-xs font-semibold shadow-2xs transition cursor-pointer"
                                    >
                                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span>Weekend Penuh (Sabtu & Minggu)</span>
                                    </button>
                                    <button
                                        type="button"
                                        onclick="applyPreset('night')"
                                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-white hover:bg-slate-100 text-slate-700 border border-slate-300 text-xs font-semibold shadow-2xs transition cursor-pointer"
                                    >
                                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                                        </svg>
                                        <span>Malam Hari (18:00 – 23:00)</span>
                                    </button>
                                    <button
                                        type="button"
                                        onclick="applyPreset('combo')"
                                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-white hover:bg-emerald-50 text-[#0B7B69] border border-emerald-300 text-xs font-semibold shadow-2xs transition cursor-pointer"
                                    >
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                        </svg>
                                        <span>Malam Hari + Weekend Penuh</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Daftar Aturan Jam Peak (T7.1 & T7.2) -->
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                        Aturan Periode Jam Peak <span class="text-rose-500">*</span>
                                    </label>
                                    <button
                                        type="button"
                                        onclick="addPeakRuleRow()"
                                        class="inline-flex items-center gap-1 text-xs font-bold text-[#0B7B69] hover:underline cursor-pointer"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        <span>Tambah Aturan Periode</span>
                                    </button>
                                </div>

                                <div id="peak-rules-container" class="space-y-3">
                                    @forelse ($peakRules as $rIdx => $rule)
                                        @php
                                            $ruleDays = $rule['hari'] ?? [];
                                            $rStart = substr($rule['jam_mulai'] ?? '08:00', 0, 5);
                                            $rEnd = substr($rule['jam_selesai'] ?? '23:00', 0, 5);
                                        @endphp
                                        <div class="peak-rule-item p-4 rounded-2xl border border-slate-200 bg-white space-y-3 shadow-2xs relative" data-index="{{ $rIdx }}">
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs font-bold text-slate-800 rule-title">Aturan Periode #{{ $rIdx + 1 }}</span>
                                                <button type="button" onclick="removePeakRuleRow(this)" class="p-1.5 rounded-full text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Hapus Aturan">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </div>

                                            <!-- Hari Berlaku (Checkbox Pills) -->
                                            <div>
                                                <span class="text-[11px] font-semibold text-slate-600 block mb-1.5">Hari Berlaku:</span>
                                                <div class="flex flex-wrap items-center gap-1.5">
                                                    @foreach ([1 => 'Sen', 2 => 'Sel', 3 => 'Rab', 4 => 'Kam', 5 => 'Jum', 6 => 'Sab', 7 => 'Min'] as $dNum => $dLabel)
                                                        @php
                                                            $checked = in_array($dNum, $ruleDays, false);
                                                        @endphp
                                                        <label class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl border text-xs font-semibold cursor-pointer select-none transition {{ $checked ? 'bg-[#0B7B69] text-white border-[#0B7B69]' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100' }}">
                                                            <input
                                                                type="checkbox"
                                                                name="peak_rules[{{ $rIdx }}][hari][]"
                                                                value="{{ $dNum }}"
                                                                {{ $checked ? 'checked' : '' }}
                                                                onchange="toggleDayPillStyle(this); updatePricingSimulation()"
                                                                class="sr-only"
                                                            >
                                                            <span>{{ $dLabel }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>

                                            <!-- Jam Mulai & Jam Selesai -->
                                            <div class="flex items-center gap-2 pt-1">
                                                <div class="flex-1">
                                                    <span class="text-[11px] font-semibold text-slate-600 block mb-1">Jam Mulai:</span>
                                                    <input
                                                        type="time"
                                                        name="peak_rules[{{ $rIdx }}][jam_mulai]"
                                                        value="{{ $rStart }}"
                                                        oninput="updatePricingSimulation()"
                                                        required
                                                        class="w-full px-3 py-1.5 rounded-xl border border-slate-300 text-xs font-medium text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                                                    >
                                                </div>
                                                <span class="text-xs text-slate-400 mt-5">s/d</span>
                                                <div class="flex-1">
                                                    <span class="text-[11px] font-semibold text-slate-600 block mb-1">Jam Selesai:</span>
                                                    <input
                                                        type="time"
                                                        name="peak_rules[{{ $rIdx }}][jam_selesai]"
                                                        value="{{ $rEnd }}"
                                                        oninput="updatePricingSimulation()"
                                                        required
                                                        class="w-full px-3 py-1.5 rounded-xl border border-slate-300 text-xs font-medium text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                                                    >
                                                </div>
                                            </div>

                                            @error("peak_rules.{$rIdx}.jam_mulai")
                                                <p class="text-[11px] text-rose-600 font-medium mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    @empty
                                        <!-- Template default jika belum ada baris -->
                                        <div class="peak-rule-item p-4 rounded-2xl border border-slate-200 bg-white space-y-3 shadow-2xs relative" data-index="0">
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs font-bold text-slate-800 rule-title">Aturan Periode #1</span>
                                                <button type="button" onclick="removePeakRuleRow(this)" class="p-1.5 rounded-full text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Hapus Aturan">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </div>

                                            <div>
                                                <span class="text-[11px] font-semibold text-slate-600 block mb-1.5">Hari Berlaku:</span>
                                                <div class="flex flex-wrap items-center gap-1.5">
                                                    @foreach ([1 => 'Sen', 2 => 'Sel', 3 => 'Rab', 4 => 'Kam', 5 => 'Jum', 6 => 'Sab', 7 => 'Min'] as $dNum => $dLabel)
                                                        @php
                                                            $checked = in_array($dNum, [6, 7], false);
                                                        @endphp
                                                        <label class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl border text-xs font-semibold cursor-pointer select-none transition {{ $checked ? 'bg-[#0B7B69] text-white border-[#0B7B69]' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100' }}">
                                                            <input
                                                                type="checkbox"
                                                                name="peak_rules[0][hari][]"
                                                                value="{{ $dNum }}"
                                                                {{ $checked ? 'checked' : '' }}
                                                                onchange="toggleDayPillStyle(this); updatePricingSimulation()"
                                                                class="sr-only"
                                                            >
                                                            <span>{{ $dLabel }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-2 pt-1">
                                                <div class="flex-1">
                                                    <span class="text-[11px] font-semibold text-slate-600 block mb-1">Jam Mulai:</span>
                                                    <input
                                                        type="time"
                                                        name="peak_rules[0][jam_mulai]"
                                                        value="08:00"
                                                        oninput="updatePricingSimulation()"
                                                        required
                                                        class="w-full px-3 py-1.5 rounded-xl border border-slate-300 text-xs font-medium text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                                                    >
                                                </div>
                                                <span class="text-xs text-slate-400 mt-5">s/d</span>
                                                <div class="flex-1">
                                                    <span class="text-[11px] font-semibold text-slate-600 block mb-1">Jam Selesai:</span>
                                                    <input
                                                        type="time"
                                                        name="peak_rules[0][jam_selesai]"
                                                        value="23:00"
                                                        oninput="updatePricingSimulation()"
                                                        required
                                                        class="w-full px-3 py-1.5 rounded-xl border border-slate-300 text-xs font-medium text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                                                    >
                                                </div>
                                            </div>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Submit -->
                        <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                            <button
                                type="submit"
                                class="px-7 py-3 rounded-full bg-[#0B7B69] hover:bg-[#086354] text-white text-xs sm:text-sm font-semibold shadow-xs transition duration-150 cursor-pointer min-h-[42px]"
                            >
                                Simpan Pengaturan Tarif Lapangan
                            </button>
                        </div>
                    </div>
                </div>

                <!-- KOLOM KANAN (5 Kolom): Live Pricing Simulator & Proteksi Kebijakan -->
                <div class="lg:col-span-5 space-y-6">
                    
                    <!-- Simulasi Perbandingan Tarif -->
                    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-7 shadow-xs space-y-5">
                        <div class="pb-3 border-b border-slate-100">
                            <h4 class="text-base font-bold text-slate-900">Simulasi Tarif Sewa</h4>
                            <p class="text-xs text-slate-500 mt-0.5">Perkiraan harga per jam yang akan dilihat penyewa pada jam-jam umum.</p>
                        </div>

                        <div class="space-y-3">
                            <!-- Skenario 1: Hari Kerja Siang -->
                            <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50 flex items-center justify-between">
                                <div>
                                    <div class="text-xs font-bold text-slate-800">Senin, 10:00 – 11:00 WIB</div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">Hari Kerja (Siang Hari)</div>
                                </div>
                                <div class="text-right">
                                    <div id="sim-weekday-day" class="text-sm font-bold text-[#0B7B69] tabular-nums">
                                        Rp {{ number_format($currentTipe === 'peak_off_peak' ? $hargaOffPeak : $hargaDasar, 0, ',', '.') }}
                                    </div>
                                    <span id="sim-badge-weekday-day" class="inline-block px-1.5 py-0.2 rounded text-[9px] font-semibold bg-emerald-100 text-[#0B7B69]">
                                        {{ $currentTipe === 'peak_off_peak' ? 'Off-Peak' : 'Reguler' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Skenario 2: Hari Kerja Malam -->
                            <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50 flex items-center justify-between">
                                <div>
                                    <div class="text-xs font-bold text-slate-800">Senin, 19:00 – 20:00 WIB</div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">Hari Kerja (Malam Hari)</div>
                                </div>
                                <div class="text-right">
                                    <div id="sim-weekday-night" class="text-sm font-bold text-slate-900 tabular-nums">
                                        Rp {{ number_format($currentTipe === 'peak_off_peak' ? $hargaOffPeak : $hargaDasar, 0, ',', '.') }}
                                    </div>
                                    <span id="sim-badge-weekday-night" class="inline-block px-1.5 py-0.2 rounded text-[9px] font-semibold bg-slate-200 text-slate-700">
                                        {{ $currentTipe === 'peak_off_peak' ? 'Off-Peak' : 'Reguler' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Skenario 3: Akhir Pekan Siang -->
                            <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50 flex items-center justify-between">
                                <div>
                                    <div class="text-xs font-bold text-slate-800">Sabtu, 10:00 – 11:00 WIB</div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">Weekend (Siang Hari)</div>
                                </div>
                                <div class="text-right">
                                    <div id="sim-weekend-day" class="text-sm font-bold text-amber-900 tabular-nums">
                                        Rp {{ number_format($currentTipe === 'peak_off_peak' ? $hargaPeak : $hargaDasar, 0, ',', '.') }}
                                    </div>
                                    <span id="sim-badge-weekend-day" class="inline-block px-1.5 py-0.2 rounded text-[9px] font-bold bg-amber-100 text-amber-800">
                                        {{ $currentTipe === 'peak_off_peak' ? 'Peak Hour' : 'Reguler' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Skenario 4: Akhir Pekan Malam -->
                            <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50 flex items-center justify-between">
                                <div>
                                    <div class="text-xs font-bold text-slate-800">Sabtu, 20:00 – 21:00 WIB</div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">Weekend (Malam Hari)</div>
                                </div>
                                <div class="text-right">
                                    <div id="sim-weekend-night" class="text-sm font-bold text-amber-900 tabular-nums">
                                        Rp {{ number_format($currentTipe === 'peak_off_peak' ? $hargaPeak : $hargaDasar, 0, ',', '.') }}
                                    </div>
                                    <span id="sim-badge-weekend-night" class="inline-block px-1.5 py-0.2 rounded text-[9px] font-bold bg-amber-100 text-amber-800">
                                        {{ $currentTipe === 'peak_off_peak' ? 'Peak Hour' : 'Reguler' }}
                                    </span>
                                </div>
                            </div>
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

    <!-- JAVASCRIPT LOGIC DYNAMIC PRICING -->
    <script>
        function switchPricingScheme(type) {
            const sectionFlat = document.getElementById('section-flat');
            const sectionPeak = document.getElementById('section-peak');
            const inputBase = document.getElementById('input-harga-perjam');
            const inputOffPeak = document.getElementById('input-harga-off-peak');

            if (type === 'flat') {
                sectionFlat.classList.remove('hidden');
                sectionPeak.classList.add('hidden');
            } else {
                sectionFlat.classList.add('hidden');
                sectionPeak.classList.remove('hidden');
                if (inputBase && inputOffPeak && !inputOffPeak.value) {
                    inputOffPeak.value = inputBase.value;
                }
            }

            updatePricingSimulation();
        }

        function syncBasePriceWithOffPeak() {
            const inputBase = document.getElementById('input-harga-perjam');
            const inputOffPeak = document.getElementById('input-harga-off-peak');
            if (inputBase && inputOffPeak) {
                inputBase.value = inputOffPeak.value;
            }
        }

        function toggleDayPillStyle(checkbox) {
            const label = checkbox.closest('label');
            if (checkbox.checked) {
                label.classList.remove('bg-slate-50', 'text-slate-700', 'border-slate-200');
                label.classList.add('bg-[#0B7B69]', 'text-white', 'border-[#0B7B69]');
            } else {
                label.classList.remove('bg-[#0B7B69]', 'text-white', 'border-[#0B7B69]');
                label.classList.add('bg-slate-50', 'text-slate-700', 'border-slate-200');
            }
        }

        function addPeakRuleRow() {
            const container = document.getElementById('peak-rules-container');
            const items = container.querySelectorAll('.peak-rule-item');
            const nextIndex = items.length;

            const html = `
                <div class="peak-rule-item p-4 rounded-2xl border border-slate-200 bg-white space-y-3 shadow-2xs relative" data-index="${nextIndex}">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-800 rule-title">Aturan Periode #${nextIndex + 1}</span>
                        <button type="button" onclick="removePeakRuleRow(this)" class="p-1.5 rounded-full text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Hapus Aturan">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>

                    <div>
                        <span class="text-[11px] font-semibold text-slate-600 block mb-1.5">Hari Berlaku:</span>
                        <div class="flex flex-wrap items-center gap-1.5">
                            ${[
                                [1, 'Sen'], [2, 'Sel'], [3, 'Rab'], [4, 'Kam'], [5, 'Jum'], [6, 'Sab'], [7, 'Min']
                            ].map(([num, name]) => `
                                <label class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl border text-xs font-semibold cursor-pointer select-none transition bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100">
                                    <input
                                        type="checkbox"
                                        name="peak_rules[${nextIndex}][hari][]"
                                        value="${num}"
                                        onchange="toggleDayPillStyle(this); updatePricingSimulation()"
                                        class="sr-only"
                                    >
                                    <span>${name}</span>
                                </label>
                            `).join('')}
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <div class="flex-1">
                            <span class="text-[11px] font-semibold text-slate-600 block mb-1">Jam Mulai:</span>
                            <input
                                type="time"
                                name="peak_rules[${nextIndex}][jam_mulai]"
                                value="18:00"
                                oninput="updatePricingSimulation()"
                                required
                                class="w-full px-3 py-1.5 rounded-xl border border-slate-300 text-xs font-medium text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                            >
                        </div>
                        <span class="text-xs text-slate-400 mt-5">s/d</span>
                        <div class="flex-1">
                            <span class="text-[11px] font-semibold text-slate-600 block mb-1">Jam Selesai:</span>
                            <input
                                type="time"
                                name="peak_rules[${nextIndex}][jam_selesai]"
                                value="23:00"
                                oninput="updatePricingSimulation()"
                                required
                                class="w-full px-3 py-1.5 rounded-xl border border-slate-300 text-xs font-medium text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                            >
                        </div>
                    </div>
                </div>
            `;

            container.insertAdjacentHTML('beforeend', html);
            updatePricingSimulation();
        }

        function removePeakRuleRow(button) {
            const container = document.getElementById('peak-rules-container');
            const items = container.querySelectorAll('.peak-rule-item');
            if (items.length <= 1) {
                alert('Setidaknya harus menyisakan satu aturan periode peak.');
                return;
            }

            button.closest('.peak-rule-item').remove();

            // Re-index titles
            container.querySelectorAll('.peak-rule-item').forEach((item, idx) => {
                const title = item.querySelector('.rule-title');
                if (title) title.textContent = 'Aturan Periode #' + (idx + 1);
            });

            updatePricingSimulation();
        }

        function applyPreset(presetType) {
            const container = document.getElementById('peak-rules-container');
            container.innerHTML = '';

            if (presetType === 'weekend') {
                // Preset 1: Weekend 08:00–23:00
                addCustomRuleRow(0, [6, 7], '08:00', '23:00');
            } else if (presetType === 'night') {
                // Preset 2: Malam setiap hari 18:00–23:00
                addCustomRuleRow(0, [1, 2, 3, 4, 5, 6, 7], '18:00', '23:00');
            } else if (presetType === 'combo') {
                // Preset 3: Malam hari kerja + Weekend penuh
                addCustomRuleRow(0, [1, 2, 3, 4, 5], '18:00', '23:00');
                addCustomRuleRow(1, [6, 7], '08:00', '23:00');
            }

            updatePricingSimulation();
        }

        function addCustomRuleRow(index, daysArray, startVal, endVal) {
            const container = document.getElementById('peak-rules-container');
            const html = `
                <div class="peak-rule-item p-4 rounded-2xl border border-slate-200 bg-white space-y-3 shadow-2xs relative" data-index="${index}">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-800 rule-title">Aturan Periode #${index + 1}</span>
                        <button type="button" onclick="removePeakRuleRow(this)" class="p-1.5 rounded-full text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Hapus Aturan">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>

                    <div>
                        <span class="text-[11px] font-semibold text-slate-600 block mb-1.5">Hari Berlaku:</span>
                        <div class="flex flex-wrap items-center gap-1.5">
                            ${[
                                [1, 'Sen'], [2, 'Sel'], [3, 'Rab'], [4, 'Kam'], [5, 'Jum'], [6, 'Sab'], [7, 'Min']
                            ].map(([num, name]) => {
                                const isChecked = daysArray.includes(num);
                                return `
                                    <label class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl border text-xs font-semibold cursor-pointer select-none transition ${isChecked ? 'bg-[#0B7B69] text-white border-[#0B7B69]' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'}">
                                        <input
                                            type="checkbox"
                                            name="peak_rules[${index}][hari][]"
                                            value="${num}"
                                            ${isChecked ? 'checked' : ''}
                                            onchange="toggleDayPillStyle(this); updatePricingSimulation()"
                                            class="sr-only"
                                        >
                                        <span>${name}</span>
                                    </label>
                                `;
                            }).join('')}
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <div class="flex-1">
                            <span class="text-[11px] font-semibold text-slate-600 block mb-1">Jam Mulai:</span>
                            <input
                                type="time"
                                name="peak_rules[${index}][jam_mulai]"
                                value="${startVal}"
                                oninput="updatePricingSimulation()"
                                required
                                class="w-full px-3 py-1.5 rounded-xl border border-slate-300 text-xs font-medium text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                            >
                        </div>
                        <span class="text-xs text-slate-400 mt-5">s/d</span>
                        <div class="flex-1">
                            <span class="text-[11px] font-semibold text-slate-600 block mb-1">Jam Selesai:</span>
                            <input
                                type="time"
                                name="peak_rules[${index}][jam_selesai]"
                                value="${endVal}"
                                oninput="updatePricingSimulation()"
                                required
                                class="w-full px-3 py-1.5 rounded-xl border border-slate-300 text-xs font-medium text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                            >
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
        }

        function formatRupiah(number) {
            return 'Rp ' + Number(number).toLocaleString('id-ID');
        }

        function updatePricingSimulation() {
            const isPeakScheme = document.querySelector('input[name="tipe_skema"]:checked')?.value === 'peak_off_peak';
            const basePrice = Number(document.getElementById('input-harga-perjam')?.value || 0);
            const offPeakPrice = Number(document.getElementById('input-harga-off-peak')?.value || basePrice);
            const peakPrice = Number(document.getElementById('input-harga-peak')?.value || basePrice);

            // Cek apakah Senin siang (10:00), Senin malam (19:00), Sabtu siang (10:00), Sabtu malam (20:00) kena peak
            let mondayDayPeak = false;
            let mondayNightPeak = false;
            let saturdayDayPeak = false;
            let saturdayNightPeak = false;

            if (isPeakScheme) {
                const items = document.querySelectorAll('.peak-rule-item');
                items.forEach(item => {
                    const days = Array.from(item.querySelectorAll('input[type="checkbox"]:checked')).map(c => Number(c.value));
                    const start = item.querySelector('input[name$="[jam_mulai]"]')?.value || '00:00';
                    const end = item.querySelector('input[name$="[jam_selesai]"]')?.value || '24:00';

                    if (days.includes(1)) {
                        if ('10:00' >= start && '10:00' < end) mondayDayPeak = true;
                        if ('19:00' >= start && '19:00' < end) mondayNightPeak = true;
                    }
                    if (days.includes(6)) {
                        if ('10:00' >= start && '10:00' < end) saturdayDayPeak = true;
                        if ('20:00' >= start && '20:00' < end) saturdayNightPeak = true;
                    }
                });
            }

            // Update DOM simulation
            updateSimSlot('sim-weekday-day', 'sim-badge-weekday-day', isPeakScheme ? (mondayDayPeak ? peakPrice : offPeakPrice) : basePrice, isPeakScheme ? (mondayDayPeak ? 'Peak Hour' : 'Off-Peak') : 'Reguler', isPeakScheme && mondayDayPeak);
            updateSimSlot('sim-weekday-night', 'sim-badge-weekday-night', isPeakScheme ? (mondayNightPeak ? peakPrice : offPeakPrice) : basePrice, isPeakScheme ? (mondayNightPeak ? 'Peak Hour' : 'Off-Peak') : 'Reguler', isPeakScheme && mondayNightPeak);
            updateSimSlot('sim-weekend-day', 'sim-badge-weekend-day', isPeakScheme ? (saturdayDayPeak ? peakPrice : offPeakPrice) : basePrice, isPeakScheme ? (saturdayDayPeak ? 'Peak Hour' : 'Off-Peak') : 'Reguler', isPeakScheme && saturdayDayPeak);
            updateSimSlot('sim-weekend-night', 'sim-badge-weekend-night', isPeakScheme ? (saturdayNightPeak ? peakPrice : offPeakPrice) : basePrice, isPeakScheme ? (saturdayNightPeak ? 'Peak Hour' : 'Off-Peak') : 'Reguler', isPeakScheme && saturdayNightPeak);
        }

        function updateSimSlot(priceId, badgeId, amount, badgeText, isPeak) {
            const pElem = document.getElementById(priceId);
            const bElem = document.getElementById(badgeId);
            if (pElem) {
                pElem.textContent = formatRupiah(amount);
                pElem.className = 'text-sm font-bold tabular-nums ' + (isPeak ? 'text-amber-900' : 'text-[#0B7B69]');
            }
            if (bElem) {
                bElem.textContent = badgeText;
                bElem.className = 'inline-block px-1.5 py-0.2 rounded text-[9px] font-bold ' + (isPeak ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-[#0B7B69]');
            }
        }

        // Jalankan kalkulasi live awal saat halaman selesai dimuat
        document.addEventListener('DOMContentLoaded', function() {
            updatePricingSimulation();
        });
    </script>
</x-layouts.mitra>

