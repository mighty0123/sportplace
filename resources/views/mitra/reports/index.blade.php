<x-layouts.mitra title="Laporan Pendapatan — SportPlace" heading="Laporan Pendapatan">
    <div class="space-y-6 max-w-full min-w-0">

        <!-- Top Header & Breadcrumb -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-xs">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 text-xs font-medium text-slate-500 mb-2">
                        <span class="text-slate-400">SportPlace Mitra</span>
                        <span class="text-slate-300">/</span>
                        <span class="text-slate-800 font-semibold">Laporan Keuangan</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">
                        Laporan Pendapatan & Transaksi
                    </h1>
                    <p class="text-sm text-slate-500 mt-1 max-w-2xl leading-relaxed">
                        Pantau perolehan sewa lapangan, potongan komisi platform, dan rekap mutasi pendapatan bersih dari sesi bermain yang telah diselesaikan.
                    </p>
                </div>

                <!-- Export CSV Button (T9.4 / FR-REPORT-002) -->
                <div class="flex items-center gap-3 shrink-0 self-start md:self-auto">
                    <a
                        href="{{ route('mitra.reports.export', request()->all()) }}"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-full bg-[#0B7B69] hover:bg-[#086354] text-white text-xs sm:text-sm font-semibold shadow-xs transition min-h-[44px]"
                        title="Unduh laporan dalam format CSV"
                    >
                        <svg class="w-4 h-4 text-emerald-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Unduh CSV</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Edukasi / Status Komisi Platform (OQ-004 / GAP-07) -->
        @if (! $isCommissionConfigured)
            <div class="rounded-3xl bg-blue-50/70 border border-blue-200/80 p-4 sm:p-5 flex items-start gap-3.5 text-blue-950 shadow-xs">
                <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="text-xs space-y-1">
                    <h4 class="font-bold text-blue-900 text-sm">Informasi Komisi Platform: Belum Dikenakan (0%)</h4>
                    <p class="text-blue-800 leading-relaxed">
                        Tarif komisi platform saat ini bernilai <strong>0%</strong> (belum ditetapkan secara definitif). Seluruh pendapatan kotor dari pesanan yang selesai menjadi pendapatan bersih Anda tanpa potongan (BR-016).
                    </p>
                </div>
            </div>
        @endif

        <!-- 4 Finansial Metric Cards Grid (T9.3 & BR-016) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">

            <!-- Card 1: Pendapatan Bersih (Net Revenue) -->
            <div class="bg-white rounded-3xl border border-slate-200/80 p-5 sm:p-6 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-emerald-800 uppercase tracking-wider">Pendapatan Bersih</span>
                    <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-[#0B7B69] flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div>
                    <div class="text-2xl sm:text-3xl font-bold text-slate-900 tabular-nums tracking-tight">
                        Rp {{ number_format($netRevenue, 0, ',', '.') }}
                    </div>
                    <div class="flex items-center gap-1.5 mt-2 text-xs text-slate-500">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>Setelah potongan komisi platform</span>
                    </div>
                </div>
            </div>

            <!-- Card 2: Pendapatan Kotor (Gross) -->
            <div class="bg-white rounded-3xl border border-slate-200/80 p-5 sm:p-6 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pendapatan Kotor</span>
                    <div class="w-10 h-10 rounded-2xl bg-slate-100 text-slate-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
                <div>
                    <div class="text-2xl sm:text-3xl font-bold text-slate-800 tabular-nums tracking-tight">
                        Rp {{ number_format($grossRevenue, 0, ',', '.') }}
                    </div>
                    <div class="flex items-center gap-1.5 mt-2 text-xs text-slate-500">
                        <span>Nilai sewa dari {{ $totalBookings }} sesi pesanan</span>
                    </div>
                </div>
            </div>

            <!-- Card 3: Potongan Komisi -->
            <div class="bg-white rounded-3xl border border-slate-200/80 p-5 sm:p-6 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-amber-700 uppercase tracking-wider">Komisi Platform</span>
                    <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-xs">
                        {{ $ratePercent }}%
                    </div>
                </div>
                <div>
                    <div class="text-2xl sm:text-3xl font-bold text-slate-800 tabular-nums tracking-tight">
                        Rp {{ number_format($commissionAmount, 0, ',', '.') }}
                    </div>
                    <div class="flex items-center gap-1.5 mt-2 text-xs text-slate-500">
                        <span>Tarif platform: {{ $ratePercent }}% per transaksi</span>
                    </div>
                </div>
            </div>

            <!-- Card 4: Sesi Selesai & Rata-rata -->
            <div class="bg-white rounded-3xl border border-slate-200/80 p-5 sm:p-6 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-blue-700 uppercase tracking-wider">Total Sesi Transaksi</span>
                    <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                </div>
                <div>
                    <div class="text-2xl sm:text-3xl font-bold text-slate-900 tabular-nums tracking-tight">
                        {{ $totalBookings }} <span class="text-sm font-medium text-slate-500">Sesi</span>
                    </div>
                    <div class="flex items-center justify-between mt-2 text-xs text-slate-500">
                        <span>Rata-rata: Rp {{ number_format($averagePerBooking, 0, ',', '.') }}/sesi</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Filter Periode & Lapangan (T9.1) -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-5 sm:p-6 shadow-xs">
            <form method="GET" action="{{ route('mitra.reports.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 sm:gap-4 items-end">

                <!-- Mode Periode -->
                <div class="lg:col-span-3">
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Pilihan Periode</label>
                    <div class="flex items-center gap-3 text-xs">
                        <label class="inline-flex items-center gap-1.5 cursor-pointer">
                            <input
                                type="radio"
                                name="period_mode"
                                value="month"
                                onchange="togglePeriodMode(this.value)"
                                {{ $periodMode === 'month' ? 'checked' : '' }}
                                class="text-[#0B7B69] focus:ring-[#0B7B69]"
                            >
                            <span>Bulanan</span>
                        </label>
                        <label class="inline-flex items-center gap-1.5 cursor-pointer">
                            <input
                                type="radio"
                                name="period_mode"
                                value="range"
                                onchange="togglePeriodMode(this.value)"
                                {{ $periodMode === 'range' ? 'checked' : '' }}
                                class="text-[#0B7B69] focus:ring-[#0B7B69]"
                            >
                            <span>Rentang Tanggal</span>
                        </label>
                    </div>

                    <!-- Input Bulan -->
                    <div id="month-input-container" class="mt-2 {{ $periodMode === 'range' ? 'hidden' : '' }}">
                        <input
                            type="month"
                            name="month"
                            value="{{ $selectedMonth }}"
                            class="w-full px-3.5 py-2 text-xs sm:text-sm rounded-full border border-slate-200 focus:outline-hidden focus:border-[#0B7B69] focus:ring-1 focus:ring-[#0B7B69] transition bg-white"
                        >
                    </div>

                    <!-- Input Rentang Tanggal -->
                    <div id="range-input-container" class="mt-2 grid grid-cols-2 gap-2 {{ $periodMode === 'month' ? 'hidden' : '' }}">
                        <input
                            type="date"
                            name="start_date"
                            value="{{ $startDate }}"
                            placeholder="Dari"
                            class="w-full px-3 py-2 text-xs rounded-full border border-slate-200 focus:outline-hidden focus:border-[#0B7B69] transition bg-white"
                        >
                        <input
                            type="date"
                            name="end_date"
                            value="{{ $endDate }}"
                            placeholder="Sampai"
                            class="w-full px-3 py-2 text-xs rounded-full border border-slate-200 focus:outline-hidden focus:border-[#0B7B69] transition bg-white"
                        >
                    </div>
                </div>

                <!-- Filter Lapangan -->
                <div class="lg:col-span-3">
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Fasilitas / Lapangan</label>
                    <select
                        name="field_id"
                        class="w-full px-4 py-2.5 text-xs sm:text-sm rounded-full border border-slate-200 focus:outline-hidden focus:border-[#0B7B69] focus:ring-1 focus:ring-[#0B7B69] transition bg-white"
                    >
                        <option value="">Semua Lapangan Terdaftar</option>
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

                <!-- Filter Status Sesi -->
                <div class="lg:col-span-3">
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Kategori Transaksi</label>
                    <select
                        name="status_filter"
                        class="w-full px-4 py-2.5 text-xs sm:text-sm rounded-full border border-slate-200 focus:outline-hidden focus:border-[#0B7B69] focus:ring-1 focus:ring-[#0B7B69] transition bg-white"
                    >
                        <option value="completed" {{ $statusFilter === 'completed' ? 'selected' : '' }}>
                            Sesi Selesai (Completed - Realisasi)
                        </option>
                        <option value="all_paid" {{ $statusFilter === 'all_paid' ? 'selected' : '' }}>
                            Semua Booking Lunas (Realisasi + Estimasi Ongoing)
                        </option>
                    </select>
                </div>

                <!-- Action Button -->
                <div class="lg:col-span-3 flex items-center gap-2">
                    <button
                        type="submit"
                        class="flex-1 px-5 py-2.5 rounded-full bg-[#0B7B69] hover:bg-[#086354] text-white text-xs sm:text-sm font-semibold transition min-h-[42px] cursor-pointer"
                    >
                        Terapkan Filter
                    </button>
                    @if ($selectedFieldId || request()->query('month') || request()->query('start_date') || request()->query('status_filter'))
                        <a
                            href="{{ route('mitra.reports.index') }}"
                            class="p-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 transition min-h-[42px] flex items-center justify-center shrink-0"
                            title="Reset Filter"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </a>
                    @endif
                </div>

            </form>
        </div>

        <!-- Tabel Rincian Transaksi (T9.1 / MTR-11) -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Rincian Transaksi Pendapatan</h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Menampilkan transaksi sewa lapangan periode {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }} &mdash; {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}.
                    </p>
                </div>
                <div class="text-xs text-slate-500 self-start sm:self-auto font-medium">
                    Total: <strong class="text-slate-900">{{ $transactions->total() }}</strong> Baris Transaksi
                </div>
            </div>

            @if ($transactions->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50/80 text-slate-500 uppercase tracking-wider text-[10px] border-b border-slate-100 font-semibold">
                            <tr>
                                <th class="px-5 py-3.5">ID / No. Booking</th>
                                <th class="px-5 py-3.5">Jadwal Sesi</th>
                                <th class="px-5 py-3.5">Fasilitas</th>
                                <th class="px-5 py-3.5">Penyewa</th>
                                <th class="px-5 py-3.5">Pembayaran</th>
                                <th class="px-5 py-3.5 text-right">Kotor (Rp)</th>
                                <th class="px-5 py-3.5 text-right">Komisi (Rp)</th>
                                <th class="px-5 py-3.5 text-right">Bersih (Rp)</th>
                                <th class="px-5 py-3.5 text-center">Status</th>
                                <th class="px-5 py-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @foreach ($transactions as $detail)
                                @php
                                    $calc = $commissionResolver->calculateForDetail($detail);
                                @endphp
                                <tr class="hover:bg-slate-50/70 transition">
                                    <!-- ID Booking -->
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="font-bold text-slate-900">#{{ $detail->header_id }}</div>
                                        <span class="text-[10px] text-slate-400">Detail #{{ $detail->id }}</span>
                                    </td>

                                    <!-- Jadwal Sesi -->
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="font-semibold text-slate-800">
                                            {{ $detail->tanggal_sewa?->translatedFormat('d M Y') }}
                                        </div>
                                        <div class="text-[11px] text-slate-500 font-mono mt-0.5">
                                            {{ substr($detail->jam_mulai, 0, 5) }} - {{ substr($detail->jam_selesai, 0, 5) }}
                                        </div>
                                    </td>

                                    <!-- Lapangan & Venue -->
                                    <td class="px-5 py-4">
                                        <div class="font-semibold text-slate-900">{{ $detail->field->nama_lapangan }}</div>
                                        <div class="text-[11px] text-slate-500 truncate max-w-[150px]">{{ $detail->field->venue->nama_venue }}</div>
                                    </td>

                                    <!-- Penyewa (Masked NFR-PRIV-001) -->
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="font-semibold text-slate-900">{{ $detail->header?->user?->name ?? 'Penyewa' }}</div>
                                        <div class="text-[11px] text-slate-400 font-mono mt-0.5">{{ $detail->masked_phone }}</div>
                                    </td>

                                    <!-- Metode Pembayaran -->
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="text-slate-800 font-semibold">{{ $detail->header?->payment?->metode_pembayaran ?? 'Online Payment' }}</div>
                                        <div class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $detail->header?->payment?->pg_transaction_id ?? '-' }}</div>
                                    </td>

                                    <!-- Nilai Kotor -->
                                    <td class="px-5 py-4 text-right whitespace-nowrap tabular-nums font-semibold text-slate-700">
                                        {{ number_format($calc['gross'], 0, ',', '.') }}
                                    </td>

                                    <!-- Nilai Komisi -->
                                    <td class="px-5 py-4 text-right whitespace-nowrap tabular-nums text-amber-700">
                                        {{ number_format($calc['commission'], 0, ',', '.') }}
                                    </td>

                                    <!-- Nilai Bersih (AC: REPORT-002-01) -->
                                    <td class="px-5 py-4 text-right whitespace-nowrap tabular-nums font-bold text-[#0B7B69]">
                                        {{ number_format($calc['net'], 0, ',', '.') }}
                                    </td>

                                    <!-- Status Sesi -->
                                    <td class="px-5 py-4 text-center whitespace-nowrap">
                                        @if ($detail->status_item === 'completed')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-700">
                                                Selesai
                                            </span>
                                        @elseif ($detail->status_item === 'no_show')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                                No-Show
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-[#0B7B69] border border-emerald-200">
                                                Ongoing
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Aksi -->
                                    <td class="px-5 py-4 text-right whitespace-nowrap">
                                        <a
                                            href="{{ route('mitra.orders.show', $detail->id) }}"
                                            class="px-3 py-1.5 rounded-full border border-slate-300 hover:bg-slate-100 text-slate-700 text-[11px] font-semibold transition"
                                        >
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-50/90 font-bold text-slate-900 border-t border-slate-200">
                            <tr>
                                <td colspan="5" class="px-5 py-4 text-right text-xs uppercase tracking-wider text-slate-500">
                                    Total Halaman Ini:
                                </td>
                                <td class="px-5 py-4 text-right tabular-nums text-slate-800 text-xs">
                                    Rp {{ number_format($transactions->sum('harga_slot'), 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-4 text-right tabular-nums text-amber-700 text-xs">
                                    Rp {{ number_format($transactions->sum(fn ($t) => $commissionResolver->calculateForDetail($t)['commission']), 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-4 text-right tabular-nums text-[#0B7B69] text-sm">
                                    Rp {{ number_format($transactions->sum(fn ($t) => $commissionResolver->calculateForDetail($t)['net']), 0, ',', '.') }}
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $transactions->links() }}
                </div>
            @else
                <div class="text-center py-16">
                    <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h4 class="font-bold text-slate-800 text-sm">Tidak Ada Data Transaksi</h4>
                    <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                        Belum ada transaksi pendapatan pada periode yang dipilih. Silakan ubah filter bulan atau rentang tanggal.
                    </p>
                </div>
            @endif
        </div>

    </div>

    <script>
        function togglePeriodMode(mode) {
            const monthContainer = document.getElementById('month-input-container');
            const rangeContainer = document.getElementById('range-input-container');

            if (mode === 'range') {
                monthContainer.classList.add('hidden');
                rangeContainer.classList.remove('hidden');
            } else {
                monthContainer.classList.remove('hidden');
                rangeContainer.classList.add('hidden');
            }
        }
    </script>
</x-layouts.mitra>
