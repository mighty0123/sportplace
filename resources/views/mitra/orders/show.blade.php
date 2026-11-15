<x-layouts.mitra title="Detail Pesanan #{{ $order->id }} — SportPlace" heading="Detail Pesanan">
    <div class="space-y-6 max-w-5xl">

        <!-- Top Navigation & Action Header -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-xs">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 text-xs font-medium text-slate-500 mb-2">
                        <a href="{{ url('/mitra/pesanan') }}" class="text-slate-400 hover:text-[#0B7B69] transition">Pesanan Masuk</a>
                        <span class="text-slate-300">/</span>
                        <span class="text-slate-800 font-semibold">Detail Sesi #{{ $order->id }}</span>
                    </div>
                    <div class="flex items-center gap-3 flex-wrap">
                        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">
                            Pesanan #{{ $order->header_id }}
                        </h1>

                        <!-- Status Sesi Badge -->
                        @if ($order->status_item === 'completed')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Selesai (Completed)</span>
                            </span>
                        @elseif ($order->status_item === 'no_show')
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
                        @if ($order->isPaid())
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-[#0B7B69] border border-emerald-200">
                                Lunas Terbayar
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                Menunggu Pembayaran
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500 mt-1">
                        Dipesan pada {{ $order->created_at?->translatedFormat('d F Y H:i') ?? '-' }} &bull; Item Detail ID: #{{ $order->id }}
                    </p>
                </div>

                <a
                    href="{{ url('/mitra/pesanan') }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs sm:text-sm font-semibold transition shrink-0 self-start md:self-auto min-h-[42px]"
                >
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Daftar</span>
                </a>
            </div>
        </div>

        <!-- 2-Column Grid: Sesi Detail & Tindakan -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Left 2 Cols: Main Info -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Card 1: Informasi Lapangan & Jadwal Sesi -->
                <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-emerald-50 text-[#0B7B69] flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <h2 class="text-sm font-bold text-slate-900">Jadwal Sesi & Fasilitas</h2>
                        </div>
                        <span class="text-xs font-semibold text-slate-500">{{ $order->field->sport?->nama_olahraga }}</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div>
                            <span class="text-slate-400 block mb-1">Venue</span>
                            <span class="font-bold text-slate-800 text-sm">{{ $order->field->venue->nama_venue }}</span>
                            <span class="text-slate-500 block mt-0.5">{{ $order->field->venue->alamat_lengkap }}, {{ $order->field->venue->city?->nama_kota }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block mb-1">Lapangan</span>
                            <span class="font-bold text-slate-800 text-sm">{{ $order->field->nama_lapangan }}</span>
                            <span class="text-slate-500 block mt-0.5">Dimensi: {{ $order->field->dimensi }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block mb-1">Tanggal Sewa</span>
                            <span class="font-bold text-slate-900 text-sm">{{ $order->tanggal_sewa?->translatedFormat('l, d F Y') }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block mb-1">Jam Bermain</span>
                            <span class="font-bold text-[#0B7B69] text-sm font-mono">
                                {{ substr($order->jam_mulai, 0, 5) }} - {{ substr($order->jam_selesai, 0, 5) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Informasi Penyewa (Protected NFR-PRIV-001) -->
                <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <h2 class="text-sm font-bold text-slate-900">Data Penyewa</h2>
                        </div>
                        <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-slate-400 bg-slate-100 px-2.5 py-0.5 rounded-full">
                            <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Privasi Dilindungi (NFR-PRIV-001)
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                        <div>
                            <span class="text-slate-400 block mb-1">Nama Penyewa</span>
                            <span class="font-bold text-slate-800 text-sm">{{ $order->header?->user?->name ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block mb-1">Nomor Handphone</span>
                            <span class="font-bold text-slate-800 text-sm font-mono">{{ $order->masked_phone }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block mb-1">Alamat Email</span>
                            <span class="font-bold text-slate-800 text-sm font-mono">{{ $order->masked_email }}</span>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Rincian Pembayaran & Finansial -->
                <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <h2 class="text-sm font-bold text-slate-900">Rincian Pembayaran</h2>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div>
                            <span class="text-slate-400 block mb-1">Tarif Slot Sesi Ini</span>
                            <span class="text-xl font-bold text-slate-900">{{ $order->formatted_harga }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block mb-1">Total Tagihan Booking Header</span>
                            <span class="text-xl font-bold text-slate-900">{{ $order->header?->formatted_total_tagihan }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block mb-1">Metode Pembayaran</span>
                            <span class="font-semibold text-slate-800">{{ $order->header?->payment?->metode_pembayaran ?? 'Payment Gateway / QRIS' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block mb-1">Kode Transaksi PG</span>
                            <span class="font-mono text-slate-700">{{ $order->header?->payment?->pg_transaction_id ?? '-' }}</span>
                        </div>
                        <div class="sm:col-span-2">
                            <span class="text-slate-400 block mb-1">Waktu Pelunasan</span>
                            <span class="font-semibold text-slate-800">
                                {{ $order->header?->payment?->paid_at?->translatedFormat('d F Y H:i:s') ?? 'Menunggu pelunasan' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Riwayat Audit Log (T8.7 / NFR-LOG-001) -->
                <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs">
                    <h3 class="text-sm font-bold text-slate-900 mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Riwayat Log Aktivitas Pesanan (Audit Trail)</span>
                    </h3>

                    @if ($auditLogs->isNotEmpty())
                        <div class="flow-root">
                            <ul role="list" class="-mb-8">
                                @foreach ($auditLogs as $log)
                                    <li>
                                        <div class="relative pb-8">
                                            @if (! $loop->last)
                                                <span class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-slate-200" aria-hidden="true"></span>
                                            @endif
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    <span class="h-8 w-8 rounded-full bg-slate-100 flex items-center justify-center ring-8 ring-white text-slate-600 text-xs font-bold">
                                                        {{ $loop->iteration }}
                                                    </span>
                                                </div>
                                                <div class="flex-1 min-w-0 pt-1.5 flex justify-between space-x-4">
                                                    <div>
                                                        <p class="text-xs font-bold text-slate-900">
                                                            {{ str_replace('_', ' ', strtoupper($log->aksi)) }}
                                                        </p>
                                                        <p class="text-xs text-slate-500 mt-0.5">{{ $log->catatan }}</p>
                                                    </div>
                                                    <div class="text-right text-[11px] whitespace-nowrap text-slate-400">
                                                        <time datetime="{{ $log->created_at }}">{{ $log->created_at?->translatedFormat('d M Y H:i') }}</time>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <p class="text-xs text-slate-400 py-3">Belum ada catatan aktivitas perubahan status untuk pesanan ini.</p>
                    @endif
                </div>

            </div>

            <!-- Right 1 Col: Operational Actions -->
            <div class="space-y-6">

                <!-- Operational Actions Card -->
                <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-5">
                    <h3 class="text-sm font-bold text-slate-900 pb-3 border-b border-slate-100">
                        Tindakan Operasional
                    </h3>

                    @if ($order->status_item === 'completed')
                        <div class="rounded-2xl bg-slate-50 border border-slate-200 p-4 text-center">
                            <div class="w-10 h-10 rounded-full bg-slate-200 text-slate-600 mx-auto flex items-center justify-center mb-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <h4 class="font-bold text-slate-800 text-xs">Pesanan Selesai</h4>
                            <p class="text-[11px] text-slate-500 mt-1">
                                Sesi bermain ini telah diselesaikan. Hak ulasan bagi penyewa sudah aktif.
                            </p>
                        </div>
                    @elseif ($order->status_item === 'no_show')
                        <div class="rounded-2xl bg-rose-50 border border-rose-200 p-4 text-center">
                            <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-600 mx-auto flex items-center justify-center mb-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </div>
                            <h4 class="font-bold text-rose-900 text-xs">Penyewa Tidak Hadir</h4>
                            <p class="text-[11px] text-rose-700 mt-1">
                                Pesanan ini telah ditandai No-Show (absen) di lapangan.
                            </p>
                        </div>
                    @else
                        <!-- Action 1: Selesaikan Pesanan (FR-ORDER-003) -->
                        <div class="space-y-2">
                            <form method="POST" action="{{ route('mitra.orders.complete', $order->id) }}">
                                @csrf
                                <button
                                    type="submit"
                                    {{ ! $order->isReadyToComplete() ? 'disabled' : '' }}
                                    onclick="return confirm('Apakah Anda yakin ingin menyelesaikan sesi bermain ini?')"
                                    class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-full text-xs sm:text-sm font-semibold transition min-h-[44px] {{ $order->isReadyToComplete() ? 'bg-[#0B7B69] hover:bg-[#086354] text-white shadow-xs cursor-pointer' : 'bg-slate-100 text-slate-400 cursor-not-allowed' }}"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span>Selesaikan Sesi (Completed)</span>
                                </button>
                            </form>
                            @if (! $order->isReadyToComplete())
                                <p class="text-[11px] text-slate-400 leading-relaxed text-center">
                                    Tombol aktif setelah jam berakhir ({{ substr($order->jam_selesai, 0, 5) }}) dan pembayaran lunas.
                                </p>
                            @endif
                        </div>

                        <!-- Action 2: Konfirmasi Kehadiran -->
                        <div class="pt-3 border-t border-slate-100 space-y-2">
                            <form method="POST" action="{{ route('mitra.orders.confirm-attendance', $order->id) }}">
                                @csrf
                                <button
                                    type="submit"
                                    class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-full bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs sm:text-sm font-semibold transition min-h-[44px]"
                                >
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>Konfirmasi Kehadiran</span>
                                </button>
                            </form>
                            <p class="text-[11px] text-slate-400 text-center">
                                Verifikasi bahwa penyewa sudah tiba dan check-in di lokasi.
                            </p>
                        </div>

                        <!-- Action 3: Tandai No-Show (FR-ORDER-002) -->
                        <div class="pt-3 border-t border-slate-100 space-y-2">
                            <form method="POST" action="{{ route('mitra.orders.no-show', $order->id) }}">
                                @csrf
                                <button
                                    type="submit"
                                    {{ ! $order->canMarkNoShow() ? 'disabled' : '' }}
                                    onclick="return confirm('Apakah Anda yakin penyewa tidak hadir (No-Show)? Tindakan ini akan dicatat.')"
                                    class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-full text-xs sm:text-sm font-semibold transition min-h-[44px] {{ $order->canMarkNoShow() ? 'bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-300 cursor-pointer' : 'bg-slate-100 text-slate-400 cursor-not-allowed' }}"
                                >
                                    <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    <span>Tandai Tidak Hadir (No-Show)</span>
                                </button>
                            </form>
                            @if (! $order->canMarkNoShow())
                                <p class="text-[11px] text-slate-400 leading-relaxed text-center">
                                    No-Show hanya dapat ditandai setelah jam mulai ({{ substr($order->jam_mulai, 0, 5) }}).
                                </p>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Panduan & Ketentuan Operasional -->
                <div class="bg-emerald-50/50 rounded-3xl border border-emerald-100 p-5 text-xs text-emerald-950 space-y-2 shadow-xs">
                    <h4 class="font-bold text-[#0B7B69] flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-[#0B7B69]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Ketentuan Operasional Pesanan</span>
                    </h4>
                    <p class="text-[11px] text-slate-600 leading-relaxed">
                        &bull; <strong>Selesaikan Sesi:</strong> Hanya dapat dilakukan jika jam bermain telah berakhir dan pembayaran telah lunas (BR-013).
                    </p>
                    <p class="text-[11px] text-slate-600 leading-relaxed">
                        &bull; <strong>No-Show:</strong> Hanya dapat ditandai jika sesi bermain telah dimulai namun penyewa tidak hadir (BR-015).
                    </p>
                    <p class="text-[11px] text-slate-600 leading-relaxed">
                        &bull; <strong>Audit Log:</strong> Seluruh tindakan perubahan status dicatat dalam audit trail sistem untuk akuntabilitas.
                    </p>
                </div>

            </div>

        </div>

    </div>
</x-layouts.mitra>

