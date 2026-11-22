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
                    <span class="text-slate-900 font-semibold">Jadwal & Slot</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">
                    Jadwal & Slot Operasional
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-2xl leading-relaxed">
                    Kelola jam buka mingguan per lapangan dan atur blokir slot waktu untuk pemeliharaan, turnamen, atau kegiatan internal.
                </p>
            </div>

            @if ($currentField)
                <div class="flex items-center gap-3 shrink-0">
                    <button
                        type="button"
                        wire:click="openBlockModal"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-full bg-[#0B7B69] hover:bg-[#086354] active:scale-95 text-white text-xs sm:text-sm font-semibold shadow-xs transition min-h-[44px] cursor-pointer"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <span>+ Tambah Blokir / Maintenance</span>
                    </button>
                </div>
            @endif
        </div>

        <!-- Selector Dropdown Venue & Lapangan (Reaktif Livewire — Tanpa Reload, Rata Kiri-Kanan) -->
        @if ($allFieldsList->isNotEmpty())
            <div class="mt-6 pt-6 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 w-full">
                <!-- Dropdown Venue (Kiri) -->
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

                <!-- Dropdown Pilih Lapangan (Kanan) -->
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
                                <option value="{{ $f->id }}">
                                    {{ $f->nama_lapangan }} ({{ $f->sport?->nama_olahraga ?? 'Umum' }})
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <h3 class="text-base font-bold text-slate-900 mb-1">Belum Ada Lapangan yang Terdaftar</h3>
            <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto mb-6">
                Untuk mengatur jadwal operasional dan jam booking, Anda perlu menambahkan setidaknya satu venue dan lapangan olahraga terlebih dahulu.
            </p>
            <a
                href="{{ route('mitra.venues.index') }}"
                class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-full bg-[#0B7B69] hover:bg-[#086354] text-white text-xs sm:text-sm font-semibold shadow-xs transition min-h-[44px]"
            >
                <span>Kelola Venue & Lapangan &rarr;</span>
            </a>
        </div>
    @else
        <!-- Selected Field Header & Venue Notice -->
        <div class="bg-emerald-50/70 border border-emerald-200/80 rounded-3xl p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-2xl bg-white text-[#0B7B69] border border-emerald-200 flex items-center justify-center shrink-0 shadow-xs">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
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
                        Jam Operasional Venue Induk: <span class="font-semibold text-emerald-800">{{ substr($currentField->venue?->jam_buka ?? '06:00', 0, 5) }} – {{ substr($currentField->venue?->jam_tutup ?? '23:00', 0, 5) }} WIB</span>
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 self-start sm:self-auto text-xs">
                <span class="text-slate-500">Tarif Dasar:</span>
                <span class="px-3 py-1 rounded-full bg-white border border-emerald-300 font-bold text-[#0B7B69] tabular-nums">
                    {{ $currentField->formatted_harga }} / jam
                </span>
            </div>
        </div>

        <!-- MAIN WORKSPACE GRID -->
        <div wire:loading.class="opacity-60 pointer-events-none" wire:target="venueId,fieldId" class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- KOLOM KIRI (7 Kolom): Formulir Jadwal Mingguan & Daftar Blokir -->
            <div class="lg:col-span-7 space-y-6">
                <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-7 shadow-xs">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 gap-3">
                        <div>
                            <h4 class="text-base font-bold text-slate-900">Jadwal Operasional Mingguan</h4>
                            <p class="text-xs text-slate-500 mt-0.5">Tentukan jam buka, jam tutup, dan durasi slot untuk tiap hari.</p>
                        </div>
                        <button
                            type="button"
                            wire:click="copyMondayToAll"
                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-700 text-xs font-semibold transition cursor-pointer self-start sm:self-auto"
                            title="Salin jam buka, tutup, dan durasi dari hari Senin ke Selasa s.d. Minggu"
                        >
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            <span>Salin Senin ke Semua Hari</span>
                        </button>
                    </div>

                    <form wire:submit="saveSchedules" class="mt-5 space-y-4">
                        <div class="space-y-3">
                            @foreach ($schedules as $day => $sched)
                                @php
                                    $isOpen = (bool) ($sched['is_open'] ?? false);
                                @endphp
                                <div wire:key="sched-day-{{ $day }}" class="p-4 rounded-2xl border transition duration-150 {{ $isOpen ? 'bg-white border-slate-200' : 'bg-slate-50 border-slate-200/60 opacity-80' }}">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                        <!-- Nama Hari & Toggle Status Buka/Tutup -->
                                        <div class="flex items-center justify-between sm:justify-start gap-3 sm:w-44">
                                            <span class="font-bold text-sm text-slate-900 w-20">{{ $sched['nama_hari'] }}</span>
                                            <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                                                <input
                                                    type="checkbox"
                                                    wire:model.live="schedules.{{ $day }}.is_open"
                                                    class="sr-only peer"
                                                >
                                                <div class="w-9 h-5 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#0B7B69] relative"></div>
                                                <span class="text-xs font-semibold {{ $isOpen ? 'text-emerald-700' : 'text-slate-400' }}">
                                                    {{ $isOpen ? 'Buka' : 'Tutup' }}
                                                </span>
                                            </label>
                                        </div>

                                        <!-- Jam Buka, Jam Tutup, dan Durasi Slot -->
                                        <div class="flex items-center gap-2 flex-1 justify-end flex-wrap {{ $isOpen ? '' : 'pointer-events-none opacity-40' }}">
                                            <div class="flex items-center gap-1.5">
                                                <input
                                                    type="time"
                                                    wire:model="schedules.{{ $day }}.jam_buka"
                                                    class="px-2.5 py-1.5 rounded-xl border border-slate-300 text-xs font-medium text-slate-800 tabular-nums focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                                                    {{ $isOpen ? '' : 'disabled' }}
                                                >
                                                <span class="text-xs text-slate-400">s/d</span>
                                                <input
                                                    type="time"
                                                    wire:model="schedules.{{ $day }}.jam_tutup"
                                                    class="px-2.5 py-1.5 rounded-xl border border-slate-300 text-xs font-medium text-slate-800 tabular-nums focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                                                    {{ $isOpen ? '' : 'disabled' }}
                                                >
                                            </div>

                                            <div class="flex items-center gap-1.5">
                                                <span class="text-[11px] text-slate-400">Durasi:</span>
                                                <select
                                                    wire:model="schedules.{{ $day }}.durasi"
                                                    class="px-2.5 py-1.5 rounded-xl border border-slate-300 text-xs font-medium text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                                                    {{ $isOpen ? '' : 'disabled' }}
                                                >
                                                    <option value="00:30">30 Menit</option>
                                                    <option value="01:00">60 Menit (1 Jam)</option>
                                                    <option value="01:30">90 Menit</option>
                                                    <option value="02:00">120 Menit (2 Jam)</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    @error("schedules.{$day}.jam_buka")
                                        <p class="text-[11px] text-rose-600 font-medium mt-1.5">{{ $message }}</p>
                                    @enderror
                                    @error("schedules.{$day}.jam_tutup")
                                        <p class="text-[11px] text-rose-600 font-medium mt-1.5">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endforeach
                        </div>

                        <div class="pt-4 flex items-center justify-end gap-3">
                            <button
                                type="submit"
                                wire:loading.attr="disabled"
                                wire:target="saveSchedules"
                                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-[#0B7B69] hover:bg-[#086354] active:scale-95 text-white text-xs sm:text-sm font-semibold shadow-xs transition duration-150 cursor-pointer min-h-[42px] disabled:opacity-60"
                            >
                                <span wire:loading.remove wire:target="saveSchedules">Simpan Perubahan Jadwal Mingguan</span>
                                <span wire:loading wire:target="saveSchedules" class="inline-flex items-center gap-1.5">
                                    <svg class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg>
                                    <span>Menyimpan...</span>
                                </span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- DAFTAR BLOKIR AKTIF / MENDATANG -->
                <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-7 shadow-xs">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div>
                            <h4 class="text-base font-bold text-slate-900">Daftar Blokir & Pemeliharaan Aktif</h4>
                            <p class="text-xs text-slate-500 mt-0.5">Jadwal yang sedang diblokir tidak dapat dipesan oleh penyewa.</p>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 tabular-nums">
                            {{ $activeBlocks->count() }} Terdaftar
                        </span>
                    </div>

                    <div class="mt-4">
                        @if ($activeBlocks->isEmpty())
                            <div class="py-8 text-center text-slate-400">
                                <svg class="w-10 h-10 mx-auto mb-2 opacity-60 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-xs">Tidak ada pemblokiran jadwal yang sedang aktif.</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">Semua slot beroperasi normal sesuai jadwal mingguan.</p>
                            </div>
                        @else
                            <div class="space-y-3">
                                @foreach ($activeBlocks as $block)
                                    <div wire:key="block-item-{{ $block->id }}" class="p-4 rounded-2xl border border-amber-200 bg-amber-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                        <div>
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-200 text-amber-900">
                                                    Maintenance
                                                </span>
                                                <span class="text-xs font-bold text-slate-900 tabular-nums">
                                                    {{ \Carbon\Carbon::parse($block->tanggal_mulai)->translatedFormat('d M Y') }}
                                                    @if ($block->tanggal_mulai != $block->tanggal_selesai)
                                                        – {{ \Carbon\Carbon::parse($block->tanggal_selesai)->translatedFormat('d M Y') }}
                                                    @endif
                                                </span>
                                                <span class="text-xs text-slate-500 tabular-nums">
                                                    ({{ substr($block->jam_mulai, 0, 5) }} – {{ substr($block->jam_selesai, 0, 5) }} WIB)
                                                </span>
                                            </div>
                                            <p class="text-xs text-amber-900 mt-1">
                                                Alasan: <span class="font-medium">{{ $block->alasan_pemblokiran ?: 'Pemeliharaan Rutin Lapangan' }}</span>
                                            </p>
                                        </div>

                                        <button
                                            type="button"
                                            wire:click="confirmDeleteBlock({{ $block->id }})"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white border border-rose-300 hover:bg-rose-50 text-rose-700 text-xs font-semibold shadow-xs transition cursor-pointer shrink-0 self-start sm:self-center"
                                        >
                                            <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            <span>Hapus Blokir</span>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN (5 Kolom): Pratinjau Slot Real-Time (SlotGenerator) -->
            <div class="lg:col-span-5 space-y-6">
                <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-7 shadow-xs">
                    <div class="pb-4 border-b border-slate-100">
                        <h4 class="text-base font-bold text-slate-900">Pratinjau Slot Kalender</h4>
                        <p class="text-xs text-slate-500 mt-0.5">Simulasi ketersediaan slot waktu yang tampil di sisi penyewa secara instan.</p>
                    </div>

                    <!-- Date Navigation Bar (Reaktif Tanpa Reload) -->
                    <div class="mt-4 p-3 bg-slate-50 rounded-2xl border border-slate-200 flex items-center justify-between gap-2">
                        @php
                            $currentCarbon = \Carbon\Carbon::parse($selectedDate ?: now()->toDateString());
                        @endphp

                        <button
                            type="button"
                            wire:click="previousDate"
                            class="p-2 rounded-full hover:bg-white text-slate-600 transition cursor-pointer"
                            title="Hari Sebelumnya"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>

                        <div class="text-center">
                            <div class="text-xs font-bold text-slate-900">
                                {{ $currentCarbon->translatedFormat('l, d F Y') }}
                            </div>
                            <div class="flex items-center justify-center gap-1.5 mt-0.5">
                                @if ($currentCarbon->isToday())
                                    <span class="inline-block px-2 py-0.2 rounded-full bg-emerald-100 text-emerald-800 font-semibold text-[10px]">Hari Ini</span>
                                @endif
                                @if ($previewData)
                                    <span class="text-[10px] text-slate-400 tabular-nums">
                                        {{ $previewData['is_open'] ? $previewData['available_slots'] . ' dari ' . $previewData['total_slots'] . ' Slot Tersedia' : 'Lapangan Tutup' }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <button
                            type="button"
                            wire:click="nextDate"
                            class="p-2 rounded-full hover:bg-white text-slate-600 transition cursor-pointer"
                            title="Hari Berikutnya"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Date Picker Dropdown Quick Jump -->
                    <div class="mt-3 flex items-center gap-2">
                        <input
                            type="date"
                            wire:model.live="selectedDate"
                            class="w-full px-3 py-1.5 rounded-xl border border-slate-300 text-xs font-medium text-slate-700 tabular-nums focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                        >
                        @if (!$currentCarbon->isToday())
                            <button
                                type="button"
                                wire:click="todayDate"
                                class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold whitespace-nowrap transition cursor-pointer"
                            >
                                Hari Ini
                            </button>
                        @endif
                    </div>

                    <!-- Legend Status Slot -->
                    <div class="mt-4 pt-3 border-t border-slate-100 flex flex-wrap items-center gap-3 text-[11px] text-slate-600">
                        <div class="flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded bg-emerald-50 border border-emerald-400"></span>
                            <span>Tersedia</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded bg-slate-100 border border-slate-300"></span>
                            <span>Terisi</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded pattern-diagonal-blocked border border-amber-300"></span>
                            <span>Blokir / Maintenance</span>
                        </div>
                    </div>

                    <!-- GRID SLOT WAKTU -->
                    <div class="mt-4" wire:loading.class="opacity-50" wire:target="selectedDate,previousDate,nextDate,todayDate">
                        @if (!$previewData || !$previewData['is_open'])
                            <div class="py-10 text-center p-4 rounded-2xl bg-slate-50 border border-slate-200">
                                <div class="w-10 h-10 rounded-full bg-slate-200 text-slate-400 flex items-center justify-center mx-auto mb-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                    </svg>
                                </div>
                                <h5 class="text-xs font-bold text-slate-700">Lapangan Tutup Pada Hari Ini</h5>
                                <p class="text-[11px] text-slate-500 mt-0.5 max-w-xs mx-auto">
                                    Sesuai jadwal mingguan, hari {{ $previewData['day_name'] ?? 'ini' }} diatur sebagai hari libur operasional.
                                </p>
                            </div>
                        @elseif (empty($previewData['slots']))
                            <div class="py-8 text-center text-slate-400 text-xs">
                                Tidak ada slot yang terbentuk dari rentang jam operasional.
                            </div>
                        @else
                            <div class="grid grid-cols-2 gap-2.5 max-h-[480px] overflow-y-auto pr-1 no-scrollbar">
                                @foreach ($previewData['slots'] as $slot)
                                    @if ($slot['status'] === 'blocked')
                                        <!-- State Blocked / Maintenance -->
                                        <div class="pattern-diagonal-blocked border border-amber-300 rounded-2xl p-2.5 flex flex-col justify-between min-h-[72px] shadow-2xs">
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs font-bold text-amber-950 tabular-nums">{{ $slot['label'] }}</span>
                                                <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-amber-400 text-amber-950 uppercase">Blokir</span>
                                            </div>
                                            <div class="text-[10px] text-amber-900 font-medium truncate mt-1" title="{{ $slot['reason'] }}">
                                                {{ $slot['reason'] ?: 'Pemeliharaan' }}
                                            </div>
                                        </div>
                                    @elseif ($slot['status'] === 'booked')
                                        <!-- State Booked -->
                                        <div class="bg-slate-100 border border-slate-200 rounded-2xl p-2.5 flex flex-col justify-between min-h-[72px] shadow-2xs">
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs font-bold text-slate-800 tabular-nums">{{ $slot['label'] }}</span>
                                                <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-slate-200 text-slate-700">Terisi</span>
                                            </div>
                                            <div class="text-[10px] text-slate-500 truncate mt-1" title="Dipesan oleh {{ $slot['penyewa_name'] }}">
                                                {{ $slot['penyewa_name'] ? 'Penyewa: ' . $slot['penyewa_name'] : 'Sudah Dipesan' }}
                                            </div>
                                        </div>
                                    @elseif ($slot['status'] === 'past')
                                        <!-- State Past -->
                                        <div class="bg-slate-50/70 border border-slate-200/50 rounded-2xl p-2.5 flex flex-col justify-between min-h-[72px] opacity-60">
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs font-semibold text-slate-400 tabular-nums">{{ $slot['label'] }}</span>
                                                <span class="text-[9px] text-slate-400">Lewat</span>
                                            </div>
                                            <div class="text-[10px] text-slate-400 italic mt-1">
                                                Selesai
                                            </div>
                                        </div>
                                    @else
                                        <!-- State Available -->
                                        <div class="bg-white hover:bg-emerald-50/40 border border-emerald-200/80 rounded-2xl p-2.5 flex flex-col justify-between min-h-[72px] transition shadow-2xs group">
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs font-bold text-slate-900 group-hover:text-emerald-800 tabular-nums">{{ $slot['label'] }}</span>
                                                <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-emerald-100 text-[#0B7B69]">Tersedia</span>
                                            </div>
                                            <div class="text-[10px] font-bold text-[#0B7B69] tabular-nums mt-1">
                                                Rp {{ number_format($slot['price'], 0, ',', '.') }}
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    @endif

    <!-- MODAL TAMBAH BLOKIR LAPANGAN (Reaktif Livewire + Proteksi Konflik Booking) -->
    @if ($showBlockModal && $currentField)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" wire:click="closeBlockModal"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-lg rounded-3xl bg-white border border-slate-200 shadow-2xl overflow-hidden" @click.stop>
                    <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h4 class="text-base font-bold text-slate-900">Tambah Blokir Jadwal</h4>
                            <p class="text-xs text-slate-500 mt-0.5">Blokir slot waktu pada {{ $currentField->nama_lapangan }}</p>
                        </div>
                        <button type="button" wire:click="closeBlockModal" class="p-2 rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <form wire:submit="storeBlock" class="p-6 space-y-4">
                        <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-xs text-amber-900 flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="leading-relaxed">
                                Sistem akan otomatis menolak pemblokiran jika terdapat reservasi penyewa aktif/terkonfirmasi pada rentang jam tersebut.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggal Mulai <span class="text-rose-500">*</span></label>
                                <input
                                    type="date"
                                    wire:model="block_tanggal_mulai"
                                    min="{{ now()->toDateString() }}"
                                    required
                                    class="w-full px-3.5 py-2.5 rounded-2xl border border-slate-300 text-xs font-medium text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                                >
                                @error('block_tanggal_mulai')
                                    <p class="text-[11px] text-rose-600 font-medium mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggal Selesai <span class="text-rose-500">*</span></label>
                                <input
                                    type="date"
                                    wire:model="block_tanggal_selesai"
                                    min="{{ now()->toDateString() }}"
                                    required
                                    class="w-full px-3.5 py-2.5 rounded-2xl border border-slate-300 text-xs font-medium text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                                >
                                @error('block_tanggal_selesai')
                                    <p class="text-[11px] text-rose-600 font-medium mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Jam Mulai <span class="text-rose-500">*</span></label>
                                <input
                                    type="time"
                                    wire:model="block_jam_mulai"
                                    required
                                    class="w-full px-3.5 py-2.5 rounded-2xl border border-slate-300 text-xs font-medium text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                                >
                                @error('block_jam_mulai')
                                    <p class="text-[11px] text-rose-600 font-medium mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Jam Selesai <span class="text-rose-500">*</span></label>
                                <input
                                    type="time"
                                    wire:model="block_jam_selesai"
                                    required
                                    class="w-full px-3.5 py-2.5 rounded-2xl border border-slate-300 text-xs font-medium text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                                >
                                @error('block_jam_selesai')
                                    <p class="text-[11px] text-rose-600 font-medium mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Alasan Pemblokiran</label>
                            <input
                                type="text"
                                wire:model="block_alasan"
                                placeholder="Contoh: Perawatan rumput sintetis, turnamen internal..."
                                maxlength="255"
                                class="w-full px-3.5 py-2.5 rounded-2xl border border-slate-300 text-xs text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                            >
                            @error('block_alasan')
                                <p class="text-[11px] text-rose-600 font-medium mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2.5">
                            <button type="button" wire:click="closeBlockModal" class="px-5 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition cursor-pointer min-h-[40px]">
                                Batal
                            </button>
                            <button
                                type="submit"
                                wire:loading.attr="disabled"
                                wire:target="storeBlock"
                                class="px-6 py-2.5 rounded-full bg-[#0B7B69] hover:bg-[#086354] text-white text-xs font-semibold shadow-xs transition cursor-pointer min-h-[40px] disabled:opacity-60"
                            >
                                <span wire:loading.remove wire:target="storeBlock">Simpan Blokir Slot</span>
                                <span wire:loading wire:target="storeBlock">Menyimpan...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL KONFIRMASI HAPUS BLOKIR -->
    @if ($showDeleteBlockModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" wire:click="closeDeleteBlockModal"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl border border-slate-200 text-center" @click.stop>
                    <div class="w-14 h-14 rounded-full mx-auto mb-4 flex items-center justify-center bg-rose-50 text-rose-600">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Hapus Blokir Jadwal?</h3>
                    <p class="text-xs sm:text-sm text-slate-500 leading-relaxed mb-6">
                        Apakah Anda yakin ingin membatalkan blokir jadwal pada <strong>{{ $confirmDeleteBlockInfo }}</strong>? Slot waktu akan kembali terbuka untuk dipesan pelanggan.
                    </p>
                    <div class="flex items-center justify-center gap-3">
                        <button type="button" wire:click="closeDeleteBlockModal" class="flex-1 px-4 py-2.5 rounded-full border border-slate-300 bg-white hover:bg-slate-50 text-xs font-semibold text-slate-700 cursor-pointer min-h-[42px]">
                            Batal
                        </button>
                        <button
                            type="button"
                            wire:click="executeDeleteBlock"
                            wire:loading.attr="disabled"
                            class="flex-1 px-4 py-2.5 rounded-full bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold shadow-xs cursor-pointer min-h-[42px]"
                        >
                            Ya, Hapus Blokir
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
