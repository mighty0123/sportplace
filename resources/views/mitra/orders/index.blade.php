<x-layouts.mitra title="Pesanan & Kalender Booking — SportPlace" heading="Pesanan Masuk">
    <div class="space-y-6 max-w-full min-w-0">

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
                        Pantau daftar reservasi penyewa, periksa kehadiran di lapangan, dan selesaikan pesanan setelah jam bermain berakhir.
                    </p>
                </div>

                <!-- View Switch: Tabel vs Kalender -->
                <div class="inline-flex p-1.5 bg-slate-100 rounded-full shrink-0 self-start md:self-auto border border-slate-200/70">
                    <a
                        href="{{ request()->fullUrlWithQuery(['view_mode' => 'table']) }}"
                        class="inline-flex items-center gap-2 px-5 py-2 rounded-full text-xs sm:text-sm font-semibold transition {{ $viewMode === 'table' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}"
                    >
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                        <span>Daftar Pesanan</span>
                    </a>
                    <a
                        href="{{ request()->fullUrlWithQuery(['view_mode' => 'calendar']) }}"
                        class="inline-flex items-center gap-2 px-5 py-2 rounded-full text-xs sm:text-sm font-semibold transition {{ $viewMode === 'calendar' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}"
                    >
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>Kalender Agenda</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Metric Summary Cards (Lebih Compact agar Muat Sempurna dalam Frame) -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2.5 sm:gap-3">
            <div class="bg-white rounded-2xl border border-slate-200/80 p-3 sm:p-4 shadow-xs">
                <span class="text-[10px] sm:text-[11px] font-semibold text-slate-500 uppercase tracking-wider block mb-0.5 truncate">Total Reservasi</span>
                <span class="text-xl sm:text-2xl font-bold text-slate-900 tabular-nums">{{ $totalOrdersCount }}</span>
                <span class="text-[10px] text-slate-400 block mt-0.5 truncate">Seluruh sesi tercatat</span>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200/80 p-3 sm:p-4 shadow-xs">
                <span class="text-[10px] sm:text-[11px] font-semibold text-emerald-700 uppercase tracking-wider block mb-0.5 truncate">Berlangsung</span>
                <span class="text-xl sm:text-2xl font-bold text-[#0B7B69] tabular-nums">{{ $ongoingCount }}</span>
                <span class="text-[10px] text-slate-400 block mt-0.5 truncate">Sesi aktif berjalan</span>
            </div>
            <div class="bg-white rounded-2xl border border-amber-200/80 bg-amber-50/20 p-3 sm:p-4 shadow-xs">
                <span class="text-[10px] sm:text-[11px] font-semibold text-amber-700 uppercase tracking-wider block mb-0.5 truncate">Siap Selesai</span>
                <span class="text-xl sm:text-2xl font-bold text-amber-700 tabular-nums">{{ $readyToCompleteCount }}</span>
                <span class="text-[10px] text-amber-600 block mt-0.5 truncate">Jam sewa berakhir</span>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200/80 p-3 sm:p-4 shadow-xs">
                <span class="text-[10px] sm:text-[11px] font-semibold text-slate-700 uppercase tracking-wider block mb-0.5 truncate">Selesai</span>
                <span class="text-xl sm:text-2xl font-bold text-slate-800 tabular-nums">{{ $completedCount }}</span>
                <span class="text-[10px] text-slate-400 block mt-0.5 truncate">Hak ulasan aktif</span>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200/80 p-3 sm:p-4 shadow-xs col-span-2 sm:col-span-1">
                <span class="text-[10px] sm:text-[11px] font-semibold text-rose-700 uppercase tracking-wider block mb-0.5 truncate">No-Show</span>
                <span class="text-xl sm:text-2xl font-bold text-rose-600 tabular-nums">{{ $noShowCount }}</span>
                <span class="text-[10px] text-slate-400 block mt-0.5 truncate">Penyewa absen</span>
            </div>
        </div>

        <!-- Filter & Search Bar (Rapi & Tidak Overflow Melebihi Card) -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-4 sm:p-5 shadow-xs">
            <form method="GET" action="{{ url('/mitra/pesanan') }}" class="space-y-3.5">
                <input type="hidden" name="view_mode" value="{{ $viewMode }}">

                <!-- 5 Kolom Input Proporsional -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-2.5 sm:gap-3">
                    <!-- 1. Search -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Pencarian</label>
                        <div class="relative">
                            <input
                                type="text"
                                name="search"
                                value="{{ $search }}"
                                placeholder="No. booking / nama / ID..."
                                class="w-full px-3 py-2 pl-8 text-xs sm:text-sm rounded-full border border-slate-200 focus:outline-hidden focus:border-[#0B7B69] focus:ring-1 focus:ring-[#0B7B69] transition"
                            >
                            <svg class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>

                    <!-- 2. Lapangan -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Pilih Lapangan</label>
                        <select
                            name="field_id"
                            class="w-full px-3 py-2 text-xs sm:text-sm rounded-full border border-slate-200 focus:outline-hidden focus:border-[#0B7B69] focus:ring-1 focus:ring-[#0B7B69] transition bg-white truncate"
                        >
                            <option value="">Semua Lapangan</option>
                            @foreach ($venues as $v)
                                <optgroup label="{{ $v->nama_venue }}">
                                    @foreach ($v->fields as $f)
                                        <option value="{{ $f->id }}" {{ (string)$selectedFieldId === (string)$f->id ? 'selected' : '' }}>
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
                            name="status"
                            class="w-full px-3 py-2 text-xs sm:text-sm rounded-full border border-slate-200 focus:outline-hidden focus:border-[#0B7B69] focus:ring-1 focus:ring-[#0B7B69] transition bg-white"
                        >
                            <option value="">Semua Status</option>
                            <option value="ongoing" {{ $selectedStatus === 'ongoing' ? 'selected' : '' }}>Ongoing (Aktif)</option>
                            <option value="completed" {{ $selectedStatus === 'completed' ? 'selected' : '' }}>Completed (Selesai)</option>
                            <option value="no_show" {{ $selectedStatus === 'no_show' ? 'selected' : '' }}>No-Show (Tidak Hadir)</option>
                        </select>
                    </div>

                    <!-- 4. Status Bayar -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Status Bayar</label>
                        <select
                            name="payment_status"
                            class="w-full px-3 py-2 text-xs sm:text-sm rounded-full border border-slate-200 focus:outline-hidden focus:border-[#0B7B69] focus:ring-1 focus:ring-[#0B7B69] transition bg-white"
                        >
                            <option value="">Semua Pembayaran</option>
                            <option value="payment" {{ $selectedPaymentStatus === 'payment' ? 'selected' : '' }}>Lunas (Payment)</option>
                            <option value="pending" {{ $selectedPaymentStatus === 'pending' ? 'selected' : '' }}>Pending (Menunggu)</option>
                        </select>
                    </div>

                    <!-- 5. Tanggal Sesi -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Tanggal Sesi</label>
                        <input
                            type="date"
                            name="date"
                            value="{{ $viewMode === 'calendar' ? $selectedDate->format('Y-m-d') : ($selectedDate ?? '') }}"
                            class="w-full px-3 py-2 text-xs sm:text-sm rounded-full border border-slate-200 focus:outline-hidden focus:border-[#0B7B69] focus:ring-1 focus:ring-[#0B7B69] transition bg-white"
                        >
                    </div>
                </div>

                <!-- Tombol Aksi Filter & Reset (Rata Kanan di Dalam Card) -->
                <div class="flex items-center justify-end gap-2 pt-2.5 border-t border-slate-100">
                    @if ($search || $selectedFieldId || $selectedStatus || $selectedPaymentStatus || request()->query('date') || request()->query('filter'))
                        <a
                            href="{{ url('/mitra/pesanan?view_mode=' . $viewMode) }}"
                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold transition"
                            title="Reset Semua Filter"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span>Reset</span>
                        </a>
                    @endif

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2 rounded-full bg-[#0B7B69] hover:bg-[#086354] text-white text-xs sm:text-sm font-semibold shadow-xs transition min-h-[38px] cursor-pointer"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        <span>Terapkan Filter</span>
                    </button>
                </div>
            </form>
        </div>

        @if ($viewMode === 'calendar')
            <!-- ================= CALENDAR / AGENDA VIEW ================= -->
            <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-6">
                <!-- Date Bar Navigation -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <a
                            href="{{ request()->fullUrlWithQuery(['date' => $selectedDate->copy()->subDay()->format('Y-m-d')]) }}"
                            class="p-2.5 rounded-full border border-slate-200 hover:bg-slate-50 text-slate-600 transition"
                            title="Hari Sebelumnya"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>
                        <div>
                            <h3 class="text-base sm:text-lg font-bold text-slate-900">
                                {{ $selectedDate->translatedFormat('l, d F Y') }}
                            </h3>
                            <span class="text-xs text-slate-500">
                                {{ $calendarBookings->count() }} sesi reservasi pada tanggal ini
                            </span>
                        </div>
                        <a
                            href="{{ request()->fullUrlWithQuery(['date' => $selectedDate->copy()->addDay()->format('Y-m-d')]) }}"
                            class="p-2.5 rounded-full border border-slate-200 hover:bg-slate-50 text-slate-600 transition"
                            title="Hari Berikutnya"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>

                    <a
                        href="{{ request()->fullUrlWithQuery(['date' => \Carbon\Carbon::today()->format('Y-m-d')]) }}"
                        class="px-4 py-2 rounded-full border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition self-start sm:self-auto"
                    >
                        Hari Ini
                    </a>
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
                                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800">Ongoing</span>
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
                                        <form method="POST" action="{{ route('mitra.orders.complete', $slot->id) }}">
                                            @csrf
                                            <button
                                                type="submit"
                                                onclick="return confirm('Selesaikan sesi bermain ini? Hak ulasan penyewa akan aktif.')"
                                                class="px-4 py-2 rounded-full bg-[#0B7B69] hover:bg-[#086354] text-white text-xs font-semibold shadow-xs transition"
                                            >
                                                Selesaikan
                                            </button>
                                        </form>
                                    @endif

                                    <a
                                        href="{{ route('mitra.orders.show', $slot->id) }}"
                                        class="px-4 py-2 rounded-full border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-semibold transition"
                                    >
                                        Detail
                                    </a>
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
            <form id="bulk-complete-form" method="POST" action="{{ route('mitra.orders.bulk-complete') }}">
                @csrf

                <!-- Floating Bulk Action Bar (Aktif ketika ada checkbox yang dicentang) -->
                <div
                    id="bulk-bar"
                    class="hidden sticky top-4 z-20 mb-4 bg-slate-900 text-white rounded-2xl p-4 shadow-xl border border-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-3 animate-fade-in"
                >
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-emerald-500/20 text-[#DEEF25] flex items-center justify-center font-bold text-xs">
                            <span id="selected-count">0</span>
                        </div>
                        <span class="text-xs sm:text-sm font-semibold">
                            Pesanan terpilih siap diproses
                        </span>
                    </div>

                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            onclick="cancelBulkSelection()"
                            class="px-4 py-2 rounded-full bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            onclick="return confirm('Apakah Anda yakin ingin menyelesaikan semua pesanan terpilih secara massal?')"
                            class="px-5 py-2 rounded-full bg-[#0B7B69] hover:bg-[#086354] text-white text-xs sm:text-sm font-semibold shadow-xs transition inline-flex items-center gap-1.5"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Selesaikan Pesanan Terpilih</span>
                        </button>
                    </div>
                </div>

                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
                    @if ($orders->isNotEmpty())
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-slate-50/80 text-slate-500 uppercase tracking-wider text-[10px] border-b border-slate-100 font-semibold">
                                    <tr>
                                        <th class="px-5 py-3.5 w-10 text-center">
                                            <input
                                                type="checkbox"
                                                id="check-all"
                                                onclick="toggleAll(this)"
                                                class="rounded border-slate-300 text-[#0B7B69] focus:ring-[#0B7B69] w-4 h-4 cursor-pointer"
                                            >
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
                                        <tr class="hover:bg-slate-50/70 transition">
                                            <!-- Checkbox Bulk -->
                                            <td class="px-5 py-4 text-center">
                                                <input
                                                    type="checkbox"
                                                    name="detail_ids[]"
                                                    value="{{ $order->id }}"
                                                    onchange="updateBulkBar()"
                                                    class="order-checkbox rounded border-slate-300 text-[#0B7B69] focus:ring-[#0B7B69] w-4 h-4 cursor-pointer"
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
                                                            formaction="{{ route('mitra.orders.complete', $order->id) }}"
                                                            formmethod="POST"
                                                            onclick="submitSingleAction(this)"
                                                            class="px-3 py-1.5 rounded-full bg-[#0B7B69] hover:bg-[#086354] text-white text-[11px] font-semibold shadow-xs transition"
                                                            title="Selesaikan Sesi"
                                                        >
                                                            Selesaikan
                                                        </button>
                                                    @endif

                                                    <a
                                                        href="{{ route('mitra.orders.show', $order->id) }}"
                                                        class="px-3 py-1.5 rounded-full border border-slate-300 hover:bg-slate-100 text-slate-700 text-[11px] font-semibold transition"
                                                    >
                                                        Detail
                                                    </a>
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
            </form>
        @endif

    </div>

    <!-- Hidden Form for Single Actions -->
    <form id="single-action-form" method="POST" class="hidden">
        @csrf
    </form>

    <script>
        function toggleAll(masterCheckbox) {
            const checkboxes = document.querySelectorAll('.order-checkbox');
            checkboxes.forEach(cb => cb.checked = masterCheckbox.checked);
            updateBulkBar();
        }

        function updateBulkBar() {
            const checkedBoxes = document.querySelectorAll('.order-checkbox:checked');
            const bulkBar = document.getElementById('bulk-bar');
            const countLabel = document.getElementById('selected-count');

            if (checkedBoxes.length > 0) {
                bulkBar.classList.remove('hidden');
                countLabel.textContent = checkedBoxes.length;
            } else {
                bulkBar.classList.add('hidden');
                const checkAll = document.getElementById('check-all');
                if (checkAll) checkAll.checked = false;
            }
        }

        function cancelBulkSelection() {
            const checkboxes = document.querySelectorAll('.order-checkbox');
            checkboxes.forEach(cb => cb.checked = false);
            const checkAll = document.getElementById('check-all');
            if (checkAll) checkAll.checked = false;
            updateBulkBar();
        }

        function submitSingleAction(button) {
            if (!confirm('Selesaikan pesanan bermain ini? Hak ulasan penyewa akan aktif.')) {
                return;
            }
            const form = document.getElementById('single-action-form');
            form.action = button.getAttribute('formaction');
            form.submit();
        }
    </script>
</x-layouts.mitra>

