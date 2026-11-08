<x-layouts.mitra title="Jadwal & Blokir Operasional — SportPlace Mitra" heading="Jadwal & Slot">
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

                @if ($selectedField)
                    <div class="flex items-center gap-3 shrink-0">
                        <button
                            type="button"
                            onclick="openBlockModal()"
                            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-full bg-[#0B7B69] hover:bg-[#086354] text-white text-xs sm:text-sm font-semibold shadow-xs transition duration-150 cursor-pointer min-h-[42px]"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span>+ Tambah Blokir / Maintenance</span>
                        </button>
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
                            @endphp
                            <a
                                href="{{ route('mitra.schedules.index', ['field_id' => $f->id, 'date' => $selectedDate]) }}"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-semibold whitespace-nowrap transition border shrink-0 {{ $isActive ? 'bg-[#06231B] text-white border-[#06231B] shadow-xs' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 border-slate-200' }}"
                            >
                                <span class="w-2 h-2 rounded-full {{ $isActive ? 'bg-[#DEEF25]' : ($f->status_lapangan === 'aktif' ? 'bg-emerald-500' : 'bg-slate-400') }}"></span>
                                <span>{{ $f->nama_lapangan }}</span>
                                <span class="text-[10px] opacity-70">({{ $f->venue?->nama_venue ?? 'Venue' }})</span>
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-900 mb-1">Belum Ada Lapangan yang Terdaftar</h3>
                <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto mb-6">
                    Untuk mengatur jadwal operasional dan jam booking, Anda perlu menambahkan setidaknya satu venue dan lapangan olahraga terlebih dahulu.
                </p>
                <a
                    href="{{ route('mitra.venues.index') }}"
                    class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-full bg-[#0B7B69] hover:bg-[#086354] text-white text-xs sm:text-sm font-semibold shadow-xs transition"
                >
                    <span>Kelola Venue & Lapangan &rarr;</span>
                </a>
            </div>
        @else
            <!-- Selected Field Header & Venue Guard Notice -->
            <div class="bg-emerald-50/70 border border-emerald-200/80 rounded-3xl p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-white text-[#0B7B69] border border-emerald-200 flex items-center justify-center shrink-0 shadow-xs">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
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
                            Jam Operasional Venue Induk: <span class="font-semibold text-emerald-800">{{ substr($selectedField->venue?->jam_buka ?? '06:00', 0, 5) }} – {{ substr($selectedField->venue?->jam_tutup ?? '23:00', 0, 5) }} WIB</span>
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 self-start sm:self-auto text-xs">
                    <span class="text-slate-500">Tarif Dasar:</span>
                    <span class="px-3 py-1 rounded-full bg-white border border-emerald-300 font-bold text-[#0B7B69]">
                        {{ $selectedField->formatted_harga }} / jam
                    </span>
                </div>
            </div>

            <!-- TABS SECTION: Jadwal Mingguan VS Pratinjau Slot Real-Time -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                <!-- KOLOM KIRI (7 Kolom): Formulir Jadwal Mingguan (T6.1 / FR-SCHED-001) -->
                <div class="lg:col-span-7 space-y-6">
                    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-7 shadow-xs">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 gap-3">
                            <div>
                                <h4 class="text-base font-bold text-slate-900">Jadwal Operasional Mingguan</h4>
                                <p class="text-xs text-slate-500 mt-0.5">Tentukan jam buka, jam tutup, dan durasi slot untuk tiap hari.</p>
                            </div>
                            <button
                                type="button"
                                onclick="copyMondayScheduleToAll()"
                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition cursor-pointer self-start sm:self-auto"
                                title="Salin jam buka, tutup, dan durasi dari hari Senin ke Selasa s.d. Minggu"
                            >
                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                <span>Salin Senin ke Semua Hari</span>
                            </button>
                        </div>

                        <!-- Form Update Jadwal Mingguan -->
                        <form method="POST" action="{{ route('mitra.fields.schedule.update', $selectedField) }}" class="mt-5 space-y-4">
                            @csrf
                            @method('PUT')

                            <div class="space-y-3">
                                @foreach ($schedules as $index => $sched)
                                    @php
                                        $hariNum = $sched->hari;
                                        $namaHari = $sched->nama_hari;
                                        $isOpen = old("schedules.{$index}.status_hari", $sched->status_hari) === 'buka';
                                        $bukaVal = old("schedules.{$index}.jam_buka", substr($sched->jam_buka ?? '08:00', 0, 5));
                                        $tutupVal = old("schedules.{$index}.jam_tutup", substr($sched->jam_tutup ?? '22:00', 0, 5));
                                        $durasiVal = old("schedules.{$index}.durasi", substr($sched->durasi ?? '01:00', 0, 5));
                                    @endphp

                                    <div id="day-row-{{ $hariNum }}" class="p-4 rounded-2xl border transition duration-150 {{ $isOpen ? 'bg-white border-slate-200' : 'bg-slate-50 border-slate-200/60 opacity-80' }}">
                                        <input type="hidden" name="schedules[{{ $index }}][hari]" value="{{ $hariNum }}">

                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                            <!-- Nama Hari & Toggle Status Buka/Tutup -->
                                            <div class="flex items-center justify-between sm:justify-start gap-3 sm:w-44">
                                                <span class="font-bold text-sm text-slate-900 w-20">{{ $namaHari }}</span>
                                                <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                                                    <input
                                                        type="checkbox"
                                                        name="schedules[{{ $index }}][status_hari]"
                                                        value="buka"
                                                        onchange="toggleDayInputs({{ $hariNum }}, this.checked)"
                                                        {{ $isOpen ? 'checked' : '' }}
                                                        class="sr-only peer"
                                                    >
                                                    <div class="w-9 h-5 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#0B7B69] relative"></div>
                                                    <span id="status-label-{{ $hariNum }}" class="text-xs font-semibold {{ $isOpen ? 'text-emerald-700' : 'text-slate-400' }}">
                                                        {{ $isOpen ? 'Buka' : 'Tutup' }}
                                                    </span>
                                                </label>
                                            </div>

                                            <!-- Jam Buka, Jam Tutup, dan Durasi Slot -->
                                            <div id="day-inputs-{{ $hariNum }}" class="flex items-center gap-2 flex-1 justify-end flex-wrap {{ $isOpen ? '' : 'pointer-events-none opacity-40' }}">
                                                <div class="flex items-center gap-1.5">
                                                    <input
                                                        type="time"
                                                        id="open-{{ $hariNum }}"
                                                        name="schedules[{{ $index }}][jam_buka]"
                                                        value="{{ $bukaVal }}"
                                                        class="px-2.5 py-1.5 rounded-xl border border-slate-300 text-xs font-medium text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                                                        {{ $isOpen ? '' : 'disabled' }}
                                                    >
                                                    <span class="text-xs text-slate-400">s/d</span>
                                                    <input
                                                        type="time"
                                                        id="close-{{ $hariNum }}"
                                                        name="schedules[{{ $index }}][jam_tutup]"
                                                        value="{{ $tutupVal }}"
                                                        class="px-2.5 py-1.5 rounded-xl border border-slate-300 text-xs font-medium text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                                                        {{ $isOpen ? '' : 'disabled' }}
                                                    >
                                                </div>

                                                <div class="flex items-center gap-1.5">
                                                    <span class="text-[11px] text-slate-400">Durasi:</span>
                                                    <select
                                                        id="durasi-{{ $hariNum }}"
                                                        name="schedules[{{ $index }}][durasi]"
                                                        class="px-2.5 py-1.5 rounded-xl border border-slate-300 text-xs font-medium text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                                                        {{ $isOpen ? '' : 'disabled' }}
                                                    >
                                                        <option value="00:30" {{ $durasiVal === '00:30' ? 'selected' : '' }}>30 Menit</option>
                                                        <option value="01:00" {{ $durasiVal === '01:00' ? 'selected' : '' }}>60 Menit (1 Jam)</option>
                                                        <option value="01:30" {{ $durasiVal === '01:30' ? 'selected' : '' }}>90 Menit</option>
                                                        <option value="02:00" {{ $durasiVal === '02:00' ? 'selected' : '' }}>120 Menit (2 Jam)</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        @error("schedules.{$index}.jam_buka")
                                            <p class="text-[11px] text-rose-600 font-medium mt-1.5">{{ $message }}</p>
                                        @enderror
                                        @error("schedules.{$index}.jam_tutup")
                                            <p class="text-[11px] text-rose-600 font-medium mt-1.5">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @endforeach
                            </div>

                            <div class="pt-4 flex items-center justify-end gap-3">
                                <button
                                    type="submit"
                                    class="px-6 py-2.5 rounded-full bg-[#0B7B69] hover:bg-[#086354] text-white text-xs sm:text-sm font-semibold shadow-xs transition duration-150 cursor-pointer min-h-[40px]"
                                >
                                    Simpan Perubahan Jadwal Mingguan
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- DAFTAR BLOKIR AKTIF / MENDATANG (T6.3) -->
                    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-7 shadow-xs">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                            <div>
                                <h4 class="text-base font-bold text-slate-900">Daftar Blokir & Pemeliharaan Aktif</h4>
                                <p class="text-xs text-slate-500 mt-0.5">Jadwal yang sedang diblokir tidak dapat dipesan oleh penyewa.</p>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                {{ $blocks->count() }} Terdaftar
                            </span>
                        </div>

                        <div class="mt-4">
                            @if ($blocks->isEmpty())
                                <div class="py-8 text-center text-slate-400">
                                    <svg class="w-10 h-10 mx-auto mb-2 opacity-60 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <p class="text-xs">Tidak ada pemblokiran jadwal yang sedang aktif.</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Semua slot beroperasi normal sesuai jadwal mingguan.</p>
                                </div>
                            @else
                                <div class="space-y-3">
                                    @foreach ($blocks as $block)
                                        <div class="p-4 rounded-2xl border border-amber-200 bg-amber-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-200 text-amber-900">
                                                        Maintenance
                                                    </span>
                                                    <span class="text-xs font-bold text-slate-900">
                                                        {{ \Carbon\Carbon::parse($block->tanggal_mulai)->translatedFormat('d M Y') }}
                                                        @if ($block->tanggal_mulai != $block->tanggal_selesai)
                                                            – {{ \Carbon\Carbon::parse($block->tanggal_selesai)->translatedFormat('d M Y') }}
                                                        @endif
                                                    </span>
                                                    <span class="text-xs text-slate-500">
                                                        ({{ substr($block->jam_mulai, 0, 5) }} – {{ substr($block->jam_selesai, 0, 5) }} WIB)
                                                    </span>
                                                </div>
                                                <p class="text-xs text-amber-900 mt-1">
                                                    Alasan: <span class="font-medium">{{ $block->alasan_pemblokiran ?: 'Pemeliharaan Rutin Lapangan' }}</span>
                                                </p>
                                            </div>

                                            <form method="POST" action="{{ route('mitra.fields.blocks.destroy', ['field' => $selectedField, 'block' => $block]) }}" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan blokir ini? Slot waktu akan kembali terbuka untuk reservasi.')">
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white border border-rose-300 hover:bg-rose-50 text-rose-700 text-xs font-semibold shadow-xs transition cursor-pointer"
                                                >
                                                    <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                    <span>Hapus Blokir</span>
                                                </button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- KOLOM KANAN (5 Kolom): Pratinjau Slot Real-Time (T6.2, T6.5 / SlotGenerator) -->
                <div class="lg:col-span-5 space-y-6">
                    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-7 shadow-xs">
                        <div class="pb-4 border-b border-slate-100">
                            <h4 class="text-base font-bold text-slate-900">Pratinjau Slot Kalender</h4>
                            <p class="text-xs text-slate-500 mt-0.5">Simulasi ketersediaan slot waktu yang tampil di sisi penyewa.</p>
                        </div>

                        <!-- Date Navigation Bar -->
                        <div class="mt-4 p-3 bg-slate-50 rounded-2xl border border-slate-200 flex items-center justify-between gap-2">
                            @php
                                $currentCarbon = \Carbon\Carbon::parse($selectedDate);
                                $prevDate = $currentCarbon->copy()->subDay()->toDateString();
                                $nextDate = $currentCarbon->copy()->addDay()->toDateString();
                            @endphp

                            <a
                                href="{{ route('mitra.schedules.index', ['field_id' => $selectedField->id, 'date' => $prevDate]) }}"
                                class="p-2 rounded-full hover:bg-white text-slate-600 transition"
                                title="Hari Sebelumnya"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </a>

                            <div class="text-center">
                                <div class="text-xs font-bold text-slate-900">
                                    {{ $currentCarbon->translatedFormat('l, d F Y') }}
                                </div>
                                <div class="flex items-center justify-center gap-1.5 mt-0.5">
                                    @if ($currentCarbon->isToday())
                                        <span class="inline-block px-2 py-0.2 rounded-full bg-emerald-100 text-emerald-800 font-semibold text-[10px]">Hari Ini</span>
                                    @endif
                                    <span class="text-[10px] text-slate-400">
                                        {{ $slotPreview['is_open'] ? $slotPreview['available_slots'] . ' dari ' . $slotPreview['total_slots'] . ' Slot Tersedia' : 'Lapangan Tutup' }}
                                    </span>
                                </div>
                            </div>

                            <a
                                href="{{ route('mitra.schedules.index', ['field_id' => $selectedField->id, 'date' => $nextDate]) }}"
                                class="p-2 rounded-full hover:bg-white text-slate-600 transition"
                                title="Hari Berikutnya"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>

                        <!-- Date Picker Dropdown Quick Jump -->
                        <form method="GET" action="{{ route('mitra.schedules.index') }}" class="mt-3 flex items-center gap-2">
                            <input type="hidden" name="field_id" value="{{ $selectedField->id }}">
                            <input
                                type="date"
                                name="date"
                                value="{{ $selectedDate }}"
                                onchange="this.form.submit()"
                                class="w-full px-3 py-1.5 rounded-xl border border-slate-300 text-xs font-medium text-slate-700 focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                            >
                            @if (!$currentCarbon->isToday())
                                <a
                                    href="{{ route('mitra.schedules.index', ['field_id' => $selectedField->id, 'date' => now()->toDateString()]) }}"
                                    class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold whitespace-nowrap transition"
                                >
                                    Hari Ini
                                </a>
                            @endif
                        </form>

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

                        <!-- GRID SLOT WAKTU (Desain System §10 & T6.5) -->
                        <div class="mt-4">
                            @if (!$slotPreview['is_open'])
                                <div class="py-10 text-center p-4 rounded-2xl bg-slate-50 border border-slate-200">
                                    <div class="w-10 h-10 rounded-full bg-slate-200 text-slate-400 flex items-center justify-center mx-auto mb-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                        </svg>
                                    </div>
                                    <h5 class="text-xs font-bold text-slate-700">Lapangan Tutup Pada Hari Ini</h5>
                                    <p class="text-[11px] text-slate-500 mt-0.5 max-w-xs mx-auto">
                                        Sesuai jadwal mingguan, hari {{ $slotPreview['day_name'] }} diatur sebagai hari libur operasional.
                                    </p>
                                </div>
                            @elseif (empty($slotPreview['slots']))
                                <div class="py-8 text-center text-slate-400 text-xs">
                                    Tidak ada slot yang terbentuk dari rentang jam operasional.
                                </div>
                            @else
                                <div class="grid grid-cols-2 gap-2.5 max-h-[480px] overflow-y-auto pr-1 no-scrollbar">
                                    @foreach ($slotPreview['slots'] as $slot)
                                        @if ($slot['status'] === 'blocked')
                                            <!-- State Blocked / Maintenance (Pola Garis Diagonal Kuning Sesuai Desain System §10) -->
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
                                            <!-- State Booked (Terisi Reservasi Aktif) -->
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
                                            <!-- State Past (Waktu telah berlalu) -->
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
                                            <!-- State Available (Tersedia untuk Booking) -->
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

    </div>

    <!-- MODAL TAMBAH BLOKIR LAPANGAN (T6.3 & T6.4) -->
    @if ($selectedField)
        <div id="block-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 hidden backdrop-blur-xs transition-opacity">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl w-full max-w-lg overflow-hidden animate-in fade-in zoom-in-95 duration-150">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h4 class="text-base font-bold text-slate-900">Tambah Blokir Jadwal</h4>
                        <p class="text-xs text-slate-500 mt-0.5">Blokir slot waktu pada {{ $selectedField->nama_lapangan }}</p>
                    </div>
                    <button type="button" onclick="closeBlockModal()" class="p-2 rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('mitra.fields.blocks.store', $selectedField) }}" class="p-6 space-y-4">
                    @csrf

                    <!-- Proteksi Notice -->
                    <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-xs text-amber-900 flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="leading-relaxed">
                            Sistem akan otomatis menolak pemblokiran jika terdapat reservasi penyewa aktif/terkonfirmasi pada rentang jam tersebut.
                        </p>
                    </div>

                    <!-- Tanggal Mulai & Tanggal Selesai -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggal Mulai <span class="text-rose-500">*</span></label>
                            <input
                                type="date"
                                name="tanggal_mulai"
                                value="{{ old('tanggal_mulai', $selectedDate) }}"
                                min="{{ now()->toDateString() }}"
                                required
                                class="w-full px-3.5 py-2.5 rounded-2xl border border-slate-300 text-xs font-medium text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                            >
                            @error('tanggal_mulai')
                                <p class="text-[11px] text-rose-600 font-medium mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggal Selesai <span class="text-rose-500">*</span></label>
                            <input
                                type="date"
                                name="tanggal_selesai"
                                value="{{ old('tanggal_selesai', $selectedDate) }}"
                                min="{{ now()->toDateString() }}"
                                required
                                class="w-full px-3.5 py-2.5 rounded-2xl border border-slate-300 text-xs font-medium text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                            >
                            @error('tanggal_selesai')
                                <p class="text-[11px] text-rose-600 font-medium mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Jam Mulai & Jam Selesai -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Jam Mulai <span class="text-rose-500">*</span></label>
                            <input
                                type="time"
                                name="jam_mulai"
                                value="{{ old('jam_mulai', '08:00') }}"
                                required
                                class="w-full px-3.5 py-2.5 rounded-2xl border border-slate-300 text-xs font-medium text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                            >
                            @error('jam_mulai')
                                <p class="text-[11px] text-rose-600 font-medium mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Jam Selesai <span class="text-rose-500">*</span></label>
                            <input
                                type="time"
                                name="jam_selesai"
                                value="{{ old('jam_selesai', '12:00') }}"
                                required
                                class="w-full px-3.5 py-2.5 rounded-2xl border border-slate-300 text-xs font-medium text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                            >
                            @error('jam_selesai')
                                <p class="text-[11px] text-rose-600 font-medium mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Alasan Pemblokiran -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Alasan Pemblokiran / Kegiatan</label>
                        <input
                            type="text"
                            name="alasan_pemblokiran"
                            value="{{ old('alasan_pemblokiran') }}"
                            placeholder="Contoh: Perawatan Rumput, Turnamen Internal, Renovasi Atap"
                            maxlength="255"
                            class="w-full px-3.5 py-2.5 rounded-2xl border border-slate-300 text-xs font-medium text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none placeholder:text-slate-400"
                        >
                        <p class="text-[10px] text-slate-400 mt-1">Alasan ini akan ditampilkan sebagai informasi slot pemeliharaan.</p>
                        @error('alasan_pemblokiran')
                            <p class="text-[11px] text-rose-600 font-medium mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                        <button
                            type="button"
                            onclick="closeBlockModal()"
                            class="px-5 py-2.5 rounded-full border border-slate-300 text-slate-700 hover:bg-slate-50 text-xs font-semibold transition cursor-pointer min-h-[40px]"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="px-6 py-2.5 rounded-full bg-[#0B7B69] hover:bg-[#086354] text-white text-xs font-semibold shadow-xs transition cursor-pointer min-h-[40px]"
                        >
                            Simpan Blokir
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- JAVASCRIPT LOGIC INTERAKTIF -->
    <script>
        function toggleDayInputs(dayNumber, isChecked) {
            const row = document.getElementById('day-row-' + dayNumber);
            const container = document.getElementById('day-inputs-' + dayNumber);
            const label = document.getElementById('status-label-' + dayNumber);
            const openInput = document.getElementById('open-' + dayNumber);
            const closeInput = document.getElementById('close-' + dayNumber);
            const durasiSelect = document.getElementById('durasi-' + dayNumber);

            if (isChecked) {
                row.classList.remove('bg-slate-50', 'border-slate-200/60', 'opacity-80');
                row.classList.add('bg-white', 'border-slate-200');
                container.classList.remove('pointer-events-none', 'opacity-40');
                label.textContent = 'Buka';
                label.classList.remove('text-slate-400');
                label.classList.add('text-emerald-700');
                openInput.disabled = false;
                closeInput.disabled = false;
                durasiSelect.disabled = false;
            } else {
                row.classList.remove('bg-white', 'border-slate-200');
                row.classList.add('bg-slate-50', 'border-slate-200/60', 'opacity-80');
                container.classList.add('pointer-events-none', 'opacity-40');
                label.textContent = 'Tutup';
                label.classList.remove('text-emerald-700');
                label.classList.add('text-slate-400');
                openInput.disabled = true;
                closeInput.disabled = true;
                durasiSelect.disabled = true;
            }
        }

        function copyMondayScheduleToAll() {
            const mondayOpen = document.getElementById('open-1');
            const mondayClose = document.getElementById('close-1');
            const mondayDurasi = document.getElementById('durasi-1');

            if (!mondayOpen || !mondayClose) return;

            const openVal = mondayOpen.value;
            const closeVal = mondayClose.value;
            const durasiVal = mondayDurasi.value;

            for (let day = 2; day <= 7; day++) {
                const targetOpen = document.getElementById('open-' + day);
                const targetClose = document.getElementById('close-' + day);
                const targetDurasi = document.getElementById('durasi-' + day);
                const targetCheckbox = document.querySelector('input[name="schedules[' + (day - 1) + '][status_hari]"]');

                if (targetOpen && targetClose && targetDurasi) {
                    targetOpen.value = openVal;
                    targetClose.value = closeVal;
                    targetDurasi.value = durasiVal;

                    if (targetCheckbox && !targetCheckbox.checked) {
                        targetCheckbox.checked = true;
                        toggleDayInputs(day, true);
                    }
                }
            }

            alert('Pengaturan jam buka (' + openVal + ' - ' + closeVal + ') dan durasi dari hari Senin berhasil disalin ke Selasa s.d. Minggu.');
        }

        function openBlockModal() {
            const modal = document.getElementById('block-modal');
            if (modal) modal.classList.remove('hidden');
        }

        function closeBlockModal() {
            const modal = document.getElementById('block-modal');
            if (modal) modal.classList.add('hidden');
        }

        // Tutup modal dengan tombol Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeBlockModal();
        });
    </script>
</x-layouts.mitra>

