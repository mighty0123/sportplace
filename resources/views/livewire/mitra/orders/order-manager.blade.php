<div class="space-y-6 max-w-full min-w-0" x-data="{}" @keydown.window.escape="$wire.showCompleteConfirmModal ? $wire.cancelCompleteOrder() : ($wire.showDetailModal ? $wire.closeDetail() : ($wire.showBulkConfirmModal ? $wire.closeBulkModal() : null))">

    <!-- Top Header & Breadcrumb -->
    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-xs">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-medium text-slate-500 mb-2">
                    <span class="text-slate-400">SportPlace Mitra</span>
                    <span class="text-slate-300">/</span>
                    <span class="text-slate-800 font-semibold">Pesanan Masuk</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">
                    Pesanan & Kalender Reservasi
                </h1>
                <p class="text-sm text-slate-500 mt-1 max-w-2xl leading-relaxed">
                    Pantau daftar reservasi penyewa, periksa kehadiran di lapangan, dan selesaikan pesanan setelah jam bermain berakhir secara real-time.
                </p>
            </div>

            <!-- View Switch: Tabel vs Kalender -->
            <div class="inline-flex p-1.5 bg-slate-100 rounded-full shrink-0 self-start md:self-auto border border-slate-200/70">
                <button
                    type="button"
                    wire:click="setViewMode('table')"
                    class="inline-flex items-center gap-2 px-5 py-2 rounded-full text-xs sm:text-sm font-semibold transition cursor-pointer {{ $viewMode === 'table' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}"
                >
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                    <span>Daftar Pesanan</span>
                </button>
                <button
                    type="button"
                    wire:click="setViewMode('calendar')"
                    class="inline-flex items-center gap-2 px-5 py-2 rounded-full text-xs sm:text-sm font-semibold transition cursor-pointer {{ $viewMode === 'calendar' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}"
                >
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>Kalender Agenda</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Metric Summary Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2.5 sm:gap-3">
        <div class="bg-white rounded-2xl border border-slate-200/80 p-3 sm:p-4 shadow-xs">
            <span class="text-[10px] sm:text-[11px] font-semibold text-slate-500 uppercase tracking-wider block mb-0.5 truncate">Total Reservasi</span>
            <span class="text-xl sm:text-2xl font-bold text-slate-900 tabular-nums">{{ $metrics['total'] }}</span>
            <span class="text-[10px] text-slate-400 block mt-0.5 truncate">Seluruh sesi tercatat</span>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200/80 p-3 sm:p-4 shadow-xs">
            <span class="text-[10px] sm:text-[11px] font-semibold text-emerald-700 uppercase tracking-wider block mb-0.5 truncate">Berlangsung</span>
            <span class="text-xl sm:text-2xl font-bold text-[#0B7B69] tabular-nums">{{ $metrics['ongoing'] }}</span>
            <span class="text-[10px] text-slate-400 block mt-0.5 truncate">Sesi aktif berjalan</span>
        </div>
        <button
            type="button"
            wire:click="toggleReadyToCompleteFilter"
            class="bg-white rounded-2xl border p-3 sm:p-4 shadow-xs text-left transition cursor-pointer {{ $filter === 'ready_to_complete' ? 'border-[#0B7B69] ring-2 ring-[#0B7B69]/20 bg-amber-50/40' : 'border-amber-200/80 bg-amber-50/20 hover:border-amber-300' }}"
            title="Klik untuk memfilter sesi siap selesai"
        >
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-[11px] font-semibold text-amber-700 uppercase tracking-wider block mb-0.5 truncate">Siap Selesai</span>
                @if ($filter === 'ready_to_complete')
                    <span class="w-2 h-2 rounded-full bg-[#0B7B69]"></span>
                @endif
            </div>
            <span class="text-xl sm:text-2xl font-bold text-amber-700 tabular-nums">{{ $metrics['ready_to_complete'] }}</span>
            <span class="text-[10px] text-amber-600 block mt-0.5 truncate">Jam sewa berakhir</span>
        </button>
        <div class="bg-white rounded-2xl border border-slate-200/80 p-3 sm:p-4 shadow-xs">
            <span class="text-[10px] sm:text-[11px] font-semibold text-slate-700 uppercase tracking-wider block mb-0.5 truncate">Selesai</span>
            <span class="text-xl sm:text-2xl font-bold text-slate-800 tabular-nums">{{ $metrics['completed'] }}</span>
            <span class="text-[10px] text-slate-400 block mt-0.5 truncate">Hak ulasan aktif</span>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200/80 p-3 sm:p-4 shadow-xs col-span-2 sm:col-span-1">
            <span class="text-[10px] sm:text-[11px] font-semibold text-rose-700 uppercase tracking-wider block mb-0.5 truncate">No-Show</span>
            <span class="text-xl sm:text-2xl font-bold text-rose-600 tabular-nums">{{ $metrics['no_show'] }}</span>
            <span class="text-[10px] text-slate-400 block mt-0.5 truncate">Penyewa absen</span>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white rounded-3xl border border-slate-200/80 p-4 sm:p-5 shadow-xs">
        <div class="space-y-3.5">
            <!-- 5 Kolom Input Proporsional -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-2.5 sm:gap-3">
                <!-- 1. Search -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Pencarian</label>
                    <div class="relative">
                        <input
                            type="text"
                            wire:model.live.debounce.350ms="search"
                            placeholder="No. booking / nama / ID..."
                            class="w-full px-3 py-2 pl-8 text-xs sm:text-sm rounded-full border border-slate-200 focus:outline-hidden focus:border-[#0B7B69] focus:ring-1 focus:ring-[#0B7B69] transition min-h-[38px]"
                        >
                        <svg class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>

                <!-- 2. Lapangan & Venue -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Pilih Lapangan</label>
                    <select
                        wire:model.live="fieldId"
                        class="w-full px-3 py-2 text-xs sm:text-sm rounded-full border border-slate-200 focus:outline-hidden focus:border-[#0B7B69] focus:ring-1 focus:ring-[#0B7B69] transition bg-white truncate min-h-[38px]"
                    >
                        <option value="">Semua Lapangan</option>
                        @foreach ($venues as $v)
                            <optgroup label="{{ $v->nama_venue }}">
                                @foreach ($v->fields as $f)
                                    <option value="{{ $f->id }}">
                                        {{ $f->nama_lapangan }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>

                <!-- 3. Status Sesi -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Status Sesi</label>
                    <select
                        wire:model.live="status"
                        class="w-full px-3 py-2 text-xs sm:text-sm rounded-full border border-slate-200 focus:outline-hidden focus:border-[#0B7B69] focus:ring-1 focus:ring-[#0B7B69] transition bg-white min-h-[38px]"
                    >
                        <option value="">Semua Status Sesi</option>
                        <option value="ongoing">Ongoing (Aktif)</option>
                        <option value="completed">Completed (Selesai)</option>
                        <option value="no_show">No-Show (Tidak Hadir)</option>
                    </select>
                </div>

                <!-- 4. Status Bayar -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Status Bayar</label>
                    <select
                        wire:model.live="paymentStatus"
                        class="w-full px-3 py-2 text-xs sm:text-sm rounded-full border border-slate-200 focus:outline-hidden focus:border-[#0B7B69] focus:ring-1 focus:ring-[#0B7B69] transition bg-white min-h-[38px]"
                    >
                        <option value="">Semua Pembayaran</option>
                        <option value="payment">Lunas (Payment)</option>
                        <option value="pending">Pending (Menunggu)</option>
                        <option value="canceled">Dibatalkan</option>
                    </select>
                </div>

                <!-- 5. Tanggal Sesi -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Tanggal Sesi</label>
                    <input
                        type="date"
                        wire:model.live="date"
                        class="w-full px-3 py-2 text-xs sm:text-sm rounded-full border border-slate-200 focus:outline-hidden focus:border-[#0B7B69] focus:ring-1 focus:ring-[#0B7B69] transition bg-white min-h-[38px]"
                    >
                </div>
            </div>

            <!-- Tombol Aksi Filter & Reset -->
            <div class="flex items-center justify-between pt-2.5 border-t border-slate-100">
                <div class="flex items-center gap-2">
                    <span wire:loading class="text-xs text-[#0B7B69] font-medium inline-flex items-center gap-1.5">
                        <svg class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span>Memperbarui data...</span>
                    </span>
                </div>

                <div class="flex items-center gap-2">
                    @if ($search || $fieldId || $venueId || $status || $paymentStatus || $date || $filter)
                        <button
                            type="button"
                            wire:click="resetFilters"
                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold transition cursor-pointer min-h-[36px]"
                            title="Reset Semua Filter"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span>Reset Filter</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if ($viewMode === 'calendar')
        <!-- ================= CALENDAR / AGENDA VIEW ================= -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-6">
            <!-- Date Bar Navigation -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        wire:click="prevDay"
                        class="p-2.5 rounded-full border border-slate-200 hover:bg-slate-50 text-slate-600 transition cursor-pointer min-h-[44px] min-w-[44px] flex items-center justify-center"
                        title="Hari Sebelumnya"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <div>
                        <h3 class="text-base sm:text-lg font-bold text-slate-900">
                            {{ $selectedDate->translatedFormat('l, d F Y') }}
                        </h3>
                        <span class="text-xs text-slate-500">
                            {{ $calendarBookings->count() }} sesi reservasi pada tanggal ini
                        </span>
                    </div>
                    <button
                        type="button"
                        wire:click="nextDay"
                        class="p-2.5 rounded-full border border-slate-200 hover:bg-slate-50 text-slate-600 transition cursor-pointer min-h-[44px] min-w-[44px] flex items-center justify-center"
                        title="Hari Berikutnya"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>

                <button
                    type="button"
                    wire:click="goToToday"
                    class="px-4 py-2 rounded-full border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition self-start sm:self-auto cursor-pointer min-h-[40px]"
                >
                    Hari Ini
                </button>
            </div>

            <!-- Timeline Sesi Harian -->
            @if ($calendarBookings->isNotEmpty())
                <div class="space-y-3">
                    @foreach ($calendarBookings as $slot)
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 rounded-2xl border transition {{ $slot->status_item === 'completed' ? 'bg-slate-50 border-slate-200/80 text-slate-600' : ($slot->status_item === 'no_show' ? 'bg-rose-50/50 border-rose-200' : 'bg-white border-slate-200 hover:border-[#0B7B69]/40') }} gap-4">
                            <div class="flex items-start sm:items-center gap-4">
                                <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-[#0B7B69] flex flex-col items-center justify-center shrink-0 border border-emerald-100">
                                    <span class="text-xs font-bold">{{ substr($slot->jam_mulai, 0, 5) }}</span>
                                    <span class="text-[10px] text-slate-400">s/d</span>
                                    <span class="text-xs font-bold">{{ substr($slot->jam_selesai, 0, 5) }}</span>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h4 class="font-bold text-slate-900 text-sm">
                                            {{ $slot->field->nama_lapangan }}
                                        </h4>
                                        <span class="text-xs text-slate-400">&bull;</span>
                                        <span class="text-xs text-slate-500">{{ $slot->field->venue->nama_venue }}</span>

                                        <!-- Badge Status Sesi -->
                                        @if ($slot->status_item === 'completed')
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-slate-200 text-slate-700">Selesai</span>
                                        @elseif ($slot->status_item === 'no_show')
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-rose-100 text-rose-700">No-Show</span>
                                        @else
                                            @if ($slot->isReadyToComplete())
                                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-800 border border-amber-300">Siap Selesai</span>
                                            @else
                                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800">Ongoing</span>
                                            @endif
                                        @endif

                                        <!-- Badge Pembayaran -->
                                        @if ($slot->isPaid())
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-[#0B7B69] border border-emerald-200">Lunas</span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">Pending</span>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-3 text-xs text-slate-500 mt-1">
                                        <span>Penyewa: <strong class="text-slate-700">{{ $slot->header?->user?->name ?? 'Penyewa' }}</strong></span>
                                        <span>&bull;</span>
                                        <span>Tarif: <strong class="text-slate-800">{{ $slot->formatted_harga }}</strong></span>
                                        <span>&bull;</span>
                                        <span class="text-slate-400">#{{ $slot->header_id }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 self-end sm:self-center shrink-0">
                                @if ($slot->status_item === 'ongoing' && $slot->isReadyToComplete())
                                    <button
                                        type="button"
                                        wire:click="confirmCompleteOrder({{ $slot->id }})"
                                        class="px-4 py-2 rounded-full bg-[#0B7B69] hover:bg-[#086354] text-white text-xs font-semibold shadow-xs transition cursor-pointer min-h-[44px]"
                                    >
                                        Selesaikan
                                    </button>
                                @endif

                                <button
                                    type="button"
                                    wire:click="openDetail({{ $slot->id }})"
                                    class="px-4 py-2 rounded-full border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-semibold transition cursor-pointer min-h-[44px]"
                                >
                                    Detail
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h4 class="font-bold text-slate-800 text-sm">Tidak Ada Jadwal Reservasi</h4>
                    <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                        Belum ada pesanan masuk untuk tanggal {{ $selectedDate->translatedFormat('d F Y') }}.
                    </p>
                </div>
            @endif
        </div>

    @else
        <!-- ================= TABLE VIEW WITH BULK COMPLETION ================= -->
        <div>
            <!-- Floating Bulk Action Bar -->
            @if (count($selectedOrderIds) > 0)
                <div
                    class="sticky top-4 z-20 mb-4 bg-slate-900 text-white rounded-2xl p-4 shadow-xl border border-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-3 animate-fade-in"
                >
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-emerald-500/20 text-[#DEEF25] flex items-center justify-center font-bold text-xs">
                            <span>{{ count($selectedOrderIds) }}</span>
                        </div>
                        <span class="text-xs sm:text-sm font-semibold">
                            Pesanan terpilih siap diproses secara massal
                        </span>
                    </div>

                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            wire:click="resetBulkSelection"
                            class="px-4 py-2 rounded-full bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition cursor-pointer min-h-[44px]"
                        >
                            Batal
                        </button>
                        <button
                            type="button"
                            wire:click="openBulkModal"
                            class="px-5 py-2 rounded-full bg-[#0B7B69] hover:bg-[#086354] text-white text-xs sm:text-sm font-semibold shadow-xs transition inline-flex items-center gap-1.5 cursor-pointer min-h-[44px]"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Selesaikan Pesanan Terpilih</span>
                        </button>
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
                @if ($orders && $orders->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50/80 text-slate-500 uppercase tracking-wider text-[10px] border-b border-slate-100 font-semibold">
                                <tr>
                                    <th class="px-5 py-3.5 w-10 text-center">
                                        <button
                                            type="button"
                                            wire:click="selectAllOnPage([{{ $orders->pluck('id')->implode(',') }}])"
                                            class="p-1 rounded text-slate-400 hover:text-slate-700 transition"
                                            title="Pilih Semua di Halaman Ini"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </button>
                                    </th>
                                    <th class="px-5 py-3.5">ID / No. Booking</th>
                                    <th class="px-5 py-3.5">Jadwal Sesi</th>
                                    <th class="px-5 py-3.5">Fasilitas</th>
                                    <th class="px-5 py-3.5">Penyewa</th>
                                    <th class="px-5 py-3.5">Tarif & Pembayaran</th>
                                    <th class="px-5 py-3.5">Status Sesi</th>
                                    <th class="px-5 py-3.5 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium">
                                @foreach ($orders as $order)
                                    <tr class="hover:bg-slate-50/70 transition {{ in_array($order->id, $selectedOrderIds, true) ? 'bg-emerald-50/20' : '' }}">
                                        <!-- Checkbox Bulk -->
                                        <td class="px-5 py-4 text-center">
                                            <input
                                                type="checkbox"
                                                value="{{ $order->id }}"
                                                wire:model.live="selectedOrderIds"
                                                class="rounded border-slate-300 text-[#0B7B69] focus:ring-[#0B7B69] w-4 h-4 cursor-pointer"
                                            >
                                        </td>

                                        <!-- ID / No Booking -->
                                        <td class="px-5 py-4 whitespace-nowrap">
                                            <div class="font-bold text-slate-900">#{{ $order->header_id }}</div>
                                            <span class="text-[10px] text-slate-400">Detail #{{ $order->id }}</span>
                                        </td>

                                        <!-- Jadwal Sesi -->
                                        <td class="px-5 py-4 whitespace-nowrap">
                                            <div class="font-semibold text-slate-800">
                                                {{ $order->tanggal_sewa?->translatedFormat('d M Y') }}
                                            </div>
                                            <div class="text-[11px] text-slate-500 font-mono mt-0.5">
                                                {{ substr($order->jam_mulai, 0, 5) }} - {{ substr($order->jam_selesai, 0, 5) }}
                                            </div>
                                        </td>

                                        <!-- Lapangan & Venue -->
                                        <td class="px-5 py-4">
                                            <div class="font-semibold text-slate-900">{{ $order->field->nama_lapangan }}</div>
                                            <div class="text-[11px] text-slate-500 truncate max-w-[160px]">{{ $order->field->venue->nama_venue }}</div>
                                        </td>

                                        <!-- Penyewa (Masked NFR-PRIV-001) -->
                                        <td class="px-5 py-4">
                                            <div class="font-semibold text-slate-900">{{ $order->header?->user?->name ?? 'Penyewa' }}</div>
                                            <div class="text-[11px] text-slate-400 font-mono mt-0.5">{{ $order->masked_phone }}</div>
                                        </td>

                                        <!-- Tarif & Bayar -->
                                        <td class="px-5 py-4 whitespace-nowrap">
                                            <div class="font-bold text-slate-900">{{ $order->formatted_harga }}</div>
                                            <div class="mt-1">
                                                @if ($order->isPaid())
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-[#0B7B69] border border-emerald-200">
                                                        Lunas
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                                        Menunggu Bayar
                                                    </span>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- Status Sesi -->
                                        <td class="px-5 py-4 whitespace-nowrap">
                                            @if ($order->status_item === 'completed')
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                                                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    <span>Selesai</span>
                                                </span>
                                            @elseif ($order->status_item === 'no_show')
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                                    <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                    <span>No-Show</span>
                                                </span>
                                            @else
                                                @if ($order->isReadyToComplete())
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                        <span>Siap Selesai</span>
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-[#0B7B69] border border-emerald-200">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                        <span>Ongoing</span>
                                                    </span>
                                                @endif
                                            @endif
                                        </td>

                                        <!-- Aksi Cepat & Detail -->
                                        <td class="px-5 py-4 text-right whitespace-nowrap">
                                            <div class="flex items-center justify-end gap-1.5">
                                                @if ($order->status_item === 'ongoing' && $order->isReadyToComplete())
                                                    <button
                                                        type="button"
                                                        wire:click="confirmCompleteOrder({{ $order->id }})"
                                                        class="px-3 py-1.5 rounded-full bg-[#0B7B69] hover:bg-[#086354] text-white text-[11px] font-semibold shadow-xs transition cursor-pointer min-h-[44px]"
                                                        title="Selesaikan Sesi"
                                                    >
                                                        Selesaikan
                                                    </button>
                                                @endif

                                                <button
                                                    type="button"
                                                    wire:click="openDetail({{ $order->id }})"
                                                    class="px-3 py-1.5 rounded-full border border-slate-300 hover:bg-slate-100 text-slate-700 text-[11px] font-semibold transition cursor-pointer min-h-[44px]"
                                                >
                                                    Detail
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="px-6 py-4 border-t border-slate-100">
                        {{ $orders->links() }}
                    </div>
                @else
                    <div class="text-center py-16">
                        <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <h4 class="font-bold text-slate-800 text-sm">Tidak Ada Pesanan Ditemukan</h4>
                        <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                            Belum ada pesanan masuk yang sesuai dengan kriteria filter Anda. Coba reset filter untuk melihat semua data.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- ================= MODAL DETAIL PESANAN (LW-6.2 / MTR-09) ================= -->
    @if ($showDetailModal && $viewingOrder)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <!-- Backdrop -->
            <div
                class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
                wire:click="closeDetail"
            ></div>

            <div class="flex min-h-full items-center justify-center p-4 sm:p-6 text-center">
                <div class="relative w-full max-w-4xl transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all border border-slate-200">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5 bg-slate-50/50">
                        <div>
                            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                                <span>Pesanan Masuk</span>
                                <span>/</span>
                                <span class="font-semibold text-slate-800">Detail Sesi #{{ $viewingOrder->id }}</span>
                            </div>
                            <div class="flex items-center gap-3 flex-wrap">
                                <h3 class="text-xl font-bold text-slate-900">
                                    Pesanan #{{ $viewingOrder->header_id }}
                                </h3>

                                <!-- Status Sesi Badge -->
                                @if ($viewingOrder->status_item === 'completed')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        <span>Selesai (Completed)</span>
                                    </span>
                                @elseif ($viewingOrder->status_item === 'no_show')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        <span>Tidak Hadir (No-Show)</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-[#0B7B69] border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <span>Berlangsung (Ongoing)</span>
                                    </span>
                                @endif

                                <!-- Status Bayar Badge -->
                                @if ($viewingOrder->isPaid())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-[#0B7B69] border border-emerald-200">
                                        Lunas Terbayar
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        Menunggu Pembayaran
                                    </span>
                                @endif
                            </div>
                        </div>

                        <button
                            type="button"
                            wire:click="closeDetail"
                            class="rounded-full p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition cursor-pointer min-h-[44px] min-w-[44px] flex items-center justify-center"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="px-6 py-6 max-h-[75vh] overflow-y-auto space-y-6">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <!-- Left 2 Cols: Data -->
                            <div class="lg:col-span-2 space-y-5">
                                <!-- Lapangan & Jadwal -->
                                <div class="p-5 rounded-2xl border border-slate-200/80 bg-slate-50/40 space-y-3">
                                    <div class="flex items-center justify-between pb-2 border-b border-slate-200/60">
                                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-600 flex items-center gap-1.5">
                                            <svg class="w-4 h-4 text-[#0B7B69]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <span>Jadwal Sesi & Fasilitas</span>
                                        </h4>
                                        <span class="text-xs font-semibold text-slate-500">{{ $viewingOrder->field->sport?->nama_olahraga }}</span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3 text-xs">
                                        <div>
                                            <span class="text-slate-400 block mb-0.5">Venue</span>
                                            <span class="font-bold text-slate-800">{{ $viewingOrder->field->venue->nama_venue }}</span>
                                            <span class="text-slate-500 block text-[11px]">{{ $viewingOrder->field->venue->alamat_lengkap }}, {{ $viewingOrder->field->venue->city?->nama_kota }}</span>
                                        </div>
                                        <div>
                                            <span class="text-slate-400 block mb-0.5">Lapangan</span>
                                            <span class="font-bold text-slate-800">{{ $viewingOrder->field->nama_lapangan }}</span>
                                            <span class="text-slate-500 block text-[11px]">Dimensi: {{ $viewingOrder->field->dimensi }}</span>
                                        </div>
                                        <div>
                                            <span class="text-slate-400 block mb-0.5">Tanggal Sewa</span>
                                            <span class="font-bold text-slate-900">{{ $viewingOrder->tanggal_sewa?->translatedFormat('l, d F Y') }}</span>
                                        </div>
                                        <div>
                                            <span class="text-slate-400 block mb-0.5">Jam Bermain</span>
                                            <span class="font-bold text-[#0B7B69] font-mono">
                                                {{ substr($viewingOrder->jam_mulai, 0, 5) }} - {{ substr($viewingOrder->jam_selesai, 0, 5) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Penyewa (Protected NFR-PRIV-001) -->
                                <div class="p-5 rounded-2xl border border-slate-200/80 bg-slate-50/40 space-y-3">
                                    <div class="flex items-center justify-between pb-2 border-b border-slate-200/60">
                                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-600 flex items-center gap-1.5">
                                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                            <span>Data Penyewa</span>
                                        </h4>
                                        <span class="text-[10px] font-semibold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">
                                            Privasi Dilindungi (NFR-PRIV-001)
                                        </span>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                                        <div>
                                            <span class="text-slate-400 block mb-0.5">Nama Penyewa</span>
                                            <span class="font-bold text-slate-800">{{ $viewingOrder->header?->user?->name ?? '-' }}</span>
                                        </div>
                                        <div>
                                            <span class="text-slate-400 block mb-0.5">Nomor Handphone</span>
                                            <span class="font-bold text-slate-800 font-mono">{{ $viewingOrder->masked_phone }}</span>
                                        </div>
                                        <div>
                                            <span class="text-slate-400 block mb-0.5">Alamat Email</span>
                                            <span class="font-bold text-slate-800 font-mono">{{ $viewingOrder->masked_email }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Pembayaran -->
                                <div class="p-5 rounded-2xl border border-slate-200/80 bg-slate-50/40 space-y-3">
                                    <div class="flex items-center justify-between pb-2 border-b border-slate-200/60">
                                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-600 flex items-center gap-1.5">
                                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span>Rincian Pembayaran</span>
                                        </h4>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3 text-xs">
                                        <div>
                                            <span class="text-slate-400 block mb-0.5">Tarif Slot Sesi Ini</span>
                                            <span class="text-base font-bold text-slate-900">{{ $viewingOrder->formatted_harga }}</span>
                                        </div>
                                        <div>
                                            <span class="text-slate-400 block mb-0.5">Total Tagihan Booking</span>
                                            <span class="text-base font-bold text-slate-900">{{ $viewingOrder->header?->formatted_total_tagihan }}</span>
                                        </div>
                                        <div>
                                            <span class="text-slate-400 block mb-0.5">Metode Pembayaran</span>
                                            <span class="font-semibold text-slate-800">{{ $viewingOrder->header?->payment?->metode_pembayaran ?? 'Payment Gateway' }}</span>
                                        </div>
                                        <div>
                                            <span class="text-slate-400 block mb-0.5">Kode Transaksi PG</span>
                                            <span class="font-mono text-slate-700">{{ $viewingOrder->header?->payment?->pg_transaction_id ?? '-' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Audit Trail -->
                                <div class="p-5 rounded-2xl border border-slate-200/80 bg-slate-50/40 space-y-3">
                                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-600 flex items-center gap-1.5 pb-2 border-b border-slate-200/60">
                                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>Riwayat Aktivitas Pesanan (Audit Trail)</span>
                                    </h4>

                                    @if ($auditLogs->isNotEmpty())
                                        <div class="space-y-2">
                                            @foreach ($auditLogs as $log)
                                                <div class="flex items-start justify-between text-xs py-1.5 border-b border-slate-100 last:border-b-0">
                                                    <div>
                                                        <span class="font-bold text-slate-800">{{ str_replace('_', ' ', strtoupper($log->aksi)) }}</span>
                                                        <p class="text-slate-500 text-[11px]">{{ $log->catatan }}</p>
                                                    </div>
                                                    <span class="text-[10px] text-slate-400 whitespace-nowrap">{{ $log->created_at?->translatedFormat('d M Y H:i') }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-xs text-slate-400">Belum ada riwayat aktivitas untuk pesanan ini.</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Right 1 Col: Actions -->
                            <div class="space-y-4">
                                <div class="p-5 rounded-2xl border border-slate-200 bg-white shadow-xs space-y-4">
                                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 pb-2 border-b border-slate-100">
                                        Tindakan Operasional
                                    </h4>

                                    @if ($viewingOrder->status_item === 'completed')
                                        <div class="rounded-xl bg-slate-50 border border-slate-200 p-3 text-center">
                                            <svg class="w-5 h-5 text-emerald-600 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            <span class="text-xs font-bold text-slate-800">Selesai</span>
                                            <p class="text-[11px] text-slate-500 mt-0.5">Sesi telah selesai dan hak ulasan telah aktif.</p>
                                        </div>
                                    @elseif ($viewingOrder->status_item === 'no_show')
                                        <div class="rounded-xl bg-rose-50 border border-rose-200 p-3 text-center">
                                            <svg class="w-5 h-5 text-rose-600 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                            <span class="text-xs font-bold text-rose-800">Tidak Hadir (No-Show)</span>
                                            <p class="text-[11px] text-rose-600 mt-0.5">Penyewa ditandai absen di lapangan.</p>
                                        </div>
                                    @else
                                        <!-- Selesaikan Sesi (T8.5) -->
                                        <div>
                                            <button
                                                type="button"
                                                wire:click="confirmCompleteOrder({{ $viewingOrder->id }})"
                                                {{ ! $viewingOrder->isReadyToComplete() ? 'disabled' : '' }}
                                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-full text-xs font-semibold transition min-h-[44px] {{ $viewingOrder->isReadyToComplete() ? 'bg-[#0B7B69] hover:bg-[#086354] text-white shadow-xs cursor-pointer' : 'bg-slate-100 text-slate-400 cursor-not-allowed' }}"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                <span>Selesaikan Sesi</span>
                                            </button>
                                            @if (! $viewingOrder->isReadyToComplete())
                                                <p class="text-[10px] text-slate-400 mt-1 text-center">
                                                    Aktif setelah jam sewa berakhir ({{ substr($viewingOrder->jam_selesai, 0, 5) }}) dan lunas.
                                                </p>
                                            @endif
                                        </div>

                                        <!-- Konfirmasi Kehadiran (T8.4) -->
                                        <div class="pt-3 border-t border-slate-100">
                                            <button
                                                type="button"
                                                wire:click="confirmAttendance({{ $viewingOrder->id }})"
                                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-full bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-semibold transition cursor-pointer min-h-[44px]"
                                            >
                                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <span>Konfirmasi Kehadiran</span>
                                            </button>
                                            <p class="text-[10px] text-slate-400 mt-1 text-center">
                                                Verifikasi penyewa sudah berada di lokasi.
                                            </p>
                                        </div>

                                        <!-- Tandai No-Show (T8.4) -->
                                        <div class="pt-3 border-t border-slate-100">
                                            <button
                                                type="button"
                                                wire:click="markNoShow({{ $viewingOrder->id }})"
                                                wire:confirm="Tandai penyewa tidak hadir (No-Show)? Tindakan ini akan dicatat."
                                                {{ ! $viewingOrder->canMarkNoShow() ? 'disabled' : '' }}
                                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-full text-xs font-semibold transition min-h-[44px] {{ $viewingOrder->canMarkNoShow() ? 'bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-300 cursor-pointer' : 'bg-slate-100 text-slate-400 cursor-not-allowed' }}"
                                            >
                                                <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                                <span>Tandai No-Show</span>
                                            </button>
                                            @if (! $viewingOrder->canMarkNoShow())
                                                <p class="text-[10px] text-slate-400 mt-1 text-center">
                                                    Hanya dapat ditandai setelah jam mulai ({{ substr($viewingOrder->jam_mulai, 0, 5) }}).
                                                </p>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="border-t border-slate-100 px-6 py-4 bg-slate-50/50 flex justify-end">
                        <button
                            type="button"
                            wire:click="closeDetail"
                            class="px-5 py-2.5 rounded-full border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs sm:text-sm font-semibold transition cursor-pointer min-h-[44px]"
                        >
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- ================= MODAL KONFIRMASI SELESAIKAN SESI (SINGLE) ================= -->
    @if ($showCompleteConfirmModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="complete-modal-title">
            <!-- Backdrop -->
            <div
                class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
                wire:click="cancelCompleteOrder"
            ></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center">
                <div class="relative w-full max-w-sm sm:max-w-md transform overflow-hidden rounded-3xl bg-white p-6 sm:p-7 text-left shadow-2xl transition-all border border-slate-200">
                    <div class="flex items-center gap-3.5 mb-4">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-[#0B7B69] flex items-center justify-center shrink-0 border border-emerald-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div>
                            <h3 id="complete-modal-title" class="text-base sm:text-lg font-bold text-slate-900">
                                Selesaikan Sesi
                            </h3>
                            @if ($completingOrderId)
                                <span class="text-xs text-slate-400">Detail Sesi #{{ $completingOrderId }}</span>
                            @endif
                        </div>
                    </div>

                    <p class="text-sm sm:text-base text-slate-700 font-medium leading-relaxed">
                        Selesaikan sesi bermain ini?
                    </p>

                    <div class="mt-6 flex items-center justify-end gap-3 pt-1">
                        <button
                            type="button"
                            wire:click="cancelCompleteOrder"
                            class="px-5 py-2.5 rounded-full border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs sm:text-sm font-semibold transition cursor-pointer min-h-[44px]"
                        >
                            Batal
                        </button>
                        <button
                            type="button"
                            wire:click="executeCompleteOrder"
                            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-full bg-[#0B7B69] hover:bg-[#086354] text-white text-xs sm:text-sm font-semibold shadow-xs transition cursor-pointer min-h-[44px]"
                        >
                            <span wire:loading.remove wire:target="executeCompleteOrder">Selesaikan</span>
                            <span wire:loading wire:target="executeCompleteOrder" class="inline-flex items-center gap-1.5">
                                <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                                <span>Memproses...</span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- ================= MODAL KONFIRMASI BULK COMPLETION ================= -->
    @if ($showBulkConfirmModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <!-- Backdrop -->
            <div
                class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
                wire:click="closeBulkModal"
            ></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center">
                <div class="relative w-full max-w-md transform overflow-hidden rounded-3xl bg-white p-6 text-left shadow-2xl transition-all border border-slate-200">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-[#0B7B69] flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>

                    <h3 class="text-lg font-bold text-slate-900">
                        Selesaikan {{ count($selectedOrderIds) }} Pesanan Terpilih?
                    </h3>

                    <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                        Sistem akan memverifikasi setiap pesanan. Hanya pesanan yang <strong>sudah lunas</strong> dan <strong>jam sewa telah berakhir</strong> yang akan diubah statusnya menjadi <em>Completed</em>.
                    </p>

                    <div class="mt-6 flex items-center justify-end gap-3">
                        <button
                            type="button"
                            wire:click="closeBulkModal"
                            class="px-4 py-2.5 rounded-full border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold transition cursor-pointer min-h-[44px]"
                        >
                            Batal
                        </button>
                        <button
                            type="button"
                            wire:click="bulkComplete"
                            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-full bg-[#0B7B69] hover:bg-[#086354] text-white text-xs font-semibold shadow-xs transition cursor-pointer min-h-[44px]"
                        >
                            <span wire:loading.remove wire:target="bulkComplete">Konfirmasi & Selesaikan</span>
                            <span wire:loading wire:target="bulkComplete" class="inline-flex items-center gap-1.5">
                                <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                                <span>Memproses...</span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
