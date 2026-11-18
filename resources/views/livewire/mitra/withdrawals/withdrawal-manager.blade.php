<div
    class="space-y-6 max-w-full min-w-0"
    @keydown.window.escape="if ($wire.showConfirmModal) { $wire.cancelConfirm() } else if ($wire.showWithdrawModal) { $wire.closeWithdrawModal() }"
>

    <!-- Penanda Halaman (Breadcrumb) -->
    <nav class="flex items-center gap-2 text-xs font-medium text-slate-500" aria-label="Breadcrumb">
        <a href="{{ route('mitra.dashboard') }}" wire:navigate class="hover:text-emerald-700 transition">SportPlace Mitra</a>
        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-slate-400">Keuangan</span>
        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-slate-800 font-semibold">Penarikan Dana</span>
    </nav>

    <!-- Header Halaman & Tombol Aksi Utama -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-1.5 text-xs font-bold tracking-wider text-emerald-800 uppercase mb-1">
                <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span>Dompet & Saldo Mitra</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Penarikan Dana</h1>
            <p class="text-sm text-slate-500 mt-1">Cairkan pendapatan bersih hasil sewa lapangan yang sudah selesai ke rekening bank Anda.</p>
        </div>

        <button
            type="button"
            wire:click="openWithdrawModal"
            class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-full bg-[#0B7B69] hover:bg-[#086354] active:scale-95 text-white text-sm font-semibold shadow-sm hover:shadow transition duration-150 min-h-[44px] cursor-pointer shrink-0"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Tarik Dana</span>
        </button>
    </div>

    <!-- 4 Kartu Metrik Saldo (DS II) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">

        <!-- Card 1: Saldo Siap Ditarik (Highlight Utama) -->
        <div class="bg-gradient-to-br from-[#06231B] to-[#0A3025] rounded-3xl p-6 text-white shadow-xs flex flex-col justify-between border border-[#0d3f31]">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold uppercase tracking-wider text-emerald-300">Saldo Siap Ditarik</span>
                    <div class="w-9 h-9 rounded-2xl bg-emerald-500/20 text-emerald-300 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-bold tracking-tight tabular-nums text-white">
                    Rp {{ number_format($stats['saldo_aktif'], 0, ',', '.') }}
                </div>
                <p class="text-xs text-emerald-200/70 mt-1 leading-relaxed">
                    Dana bersih dari pesanan berstatus selesai (Completed).
                </p>
            </div>
            <div class="mt-4 pt-3 border-t border-emerald-900/60 flex items-center justify-between">
                <span class="text-[11px] text-emerald-300/80">Min. penarikan Rp 10.000</span>
                @if ($stats['saldo_aktif'] >= 10000)
                    <button
                        type="button"
                        wire:click="openWithdrawModal"
                        class="text-xs font-semibold text-emerald-300 hover:text-white hover:underline transition cursor-pointer"
                    >
                        Tarik Sekarang &rarr;
                    </button>
                @endif
            </div>
        </div>

        <!-- Card 2: Saldo Tertahan (Pesanan Sedang Berlangsung) -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Saldo Tertahan</span>
                    <div class="w-9 h-9 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-bold text-slate-900 tabular-nums tracking-tight">
                    Rp {{ number_format($stats['saldo_tertahan'], 0, ',', '.') }}
                </div>
                <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                    Pesanan lunas yang sesi bermainnya masih berlangsung.
                </p>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 text-[11px] text-slate-400">
                Pindah ke saldo aktif setelah sesi diselesaikan.
            </div>
        </div>

        <!-- Card 3: Total Berhasil Dicairkan -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Telah Dicairkan</span>
                    <div class="w-9 h-9 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-bold text-slate-900 tabular-nums tracking-tight">
                    Rp {{ number_format($stats['total_withdrawn'], 0, ',', '.') }}
                </div>
                <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                    Total dana yang telah berhasil ditransfer ke rekening Anda.
                </p>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 text-[11px] text-slate-400">
                Riwayat kumulatif penarikan berhasil.
            </div>
        </div>

        <!-- Card 4: Penarikan Dalam Proses -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Sedang Diproses</span>
                    <div class="w-9 h-9 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-bold text-slate-900 tabular-nums tracking-tight">
                    Rp {{ number_format($stats['pending_withdrawn'], 0, ',', '.') }}
                </div>
                <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                    Pengajuan penarikan yang menunggu proses transfer bank.
                </p>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 text-[11px] text-slate-400">
                Estimasi pencairan 1×24 jam kerja.
            </div>
        </div>

    </div>

    <!-- Bagian Konten Utama: Tabs & Tabel -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        
        <!-- Header Konten & Tab Navigasi -->
        <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            
            <!-- Tab Switcher -->
            <div class="inline-flex items-center gap-1.5 p-1 rounded-full bg-slate-100 text-xs font-semibold">
                <button
                    type="button"
                    wire:click="setTab('withdrawals')"
                    class="px-5 py-2 rounded-full transition cursor-pointer min-h-[36px] {{ $activeTab === 'withdrawals' ? 'bg-[#0B7B69] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}"
                >
                    Riwayat Penarikan
                </button>
                <button
                    type="button"
                    wire:click="setTab('transactions')"
                    class="px-5 py-2 rounded-full transition cursor-pointer min-h-[36px] {{ $activeTab === 'transactions' ? 'bg-[#0B7B69] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}"
                >
                    Mutasi Dompet
                </button>
            </div>

            <!-- Filter Status (Hanya pada Tab Riwayat Penarikan) -->
            @if ($activeTab === 'withdrawals')
                <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar">
                    @php
                        $statusFilters = [
                            'semua' => 'Semua',
                            'pending' => 'Menunggu',
                            'processing' => 'Diproses',
                            'completed' => 'Berhasil',
                            'failed' => 'Gagal',
                        ];
                    @endphp
                    @foreach ($statusFilters as $key => $label)
                        <button
                            type="button"
                            wire:click="setStatusFilter('{{ $key }}')"
                            class="px-3 py-1.5 rounded-full text-xs font-medium whitespace-nowrap transition cursor-pointer min-h-[36px] {{ $statusFilter === $key ? 'bg-emerald-50 text-[#0B7B69] border border-emerald-200 font-semibold' : 'text-slate-500 hover:bg-slate-100' }}"
                        >
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            @endif

        </div>

        <!-- Tab 1: Riwayat Penarikan Dana -->
        @if ($activeTab === 'withdrawals')
            @if ($withdrawalsList->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50/80 text-slate-500 uppercase tracking-wider text-[10px] border-b border-slate-100 font-semibold">
                            <tr>
                                <th class="px-6 py-3.5">ID / Referensi</th>
                                <th class="px-6 py-3.5">Tanggal Pengajuan</th>
                                <th class="px-6 py-3.5">Rekening Tujuan</th>
                                <th class="px-6 py-3.5">Nominal Penarikan</th>
                                <th class="px-6 py-3.5">Biaya Admin</th>
                                <th class="px-6 py-3.5">Status</th>
                                <th class="px-6 py-3.5 text-right">Waktu Selesai</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @foreach ($withdrawalsList as $w)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="px-6 py-4 whitespace-nowrap font-mono text-slate-700 font-semibold">
                                        {{ $w->pg_disbursement_id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-slate-600">
                                        <div>{{ $w->created_at ? $w->created_at->format('d M Y') : '-' }}</div>
                                        <div class="text-[10px] text-slate-400">{{ $w->created_at ? $w->created_at->format('H:i') . ' WIB' : '' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-slate-900">{{ $w->bank_kode }} • {{ $w->nomor_rekening }}</div>
                                        <div class="text-[11px] text-slate-500">a.n {{ $w->nama_pemilik_rek }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap font-bold text-slate-900 tabular-nums">
                                        {{ $w->formatted_nominal }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-slate-500 tabular-nums">
                                        Rp {{ number_format($w->biaya_admin_bank ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($w->status === 'completed')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                <span>Berhasil</span>
                                            </span>
                                        @elseif ($w->status === 'processing')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-800 border border-blue-200">
                                                <svg class="w-3.5 h-3.5 text-blue-500 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                                <span>Diproses</span>
                                            </span>
                                        @elseif ($w->status === 'failed')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-800 border border-rose-200" title="{{ $w->alasan_gagal ?? 'Penarikan gagal' }}">
                                                <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                <span>Gagal</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                <span>Menunggu</span>
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-slate-500">
                                        {{ $w->complated_at ? $w->complated_at->format('d M Y H:i') . ' WIB' : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $withdrawalsList->links() }}
                </div>
            @else
                <!-- Empty State Riwayat Penarikan -->
                <div class="py-16 px-6 text-center">
                    <div class="w-14 h-14 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <h4 class="text-sm font-bold text-slate-800">Belum Ada Riwayat Penarikan</h4>
                    <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto leading-relaxed">
                        @if ($statusFilter !== 'semua')
                            Tidak ada data penarikan dengan status "{{ $statusFilter }}".
                        @else
                            Anda belum pernah mengajukan penarikan dana. Saldo aktif hasil sewa lapangan dapat dicairkan kapan saja.
                        @endif
                    </p>
                    @if ($stats['saldo_aktif'] >= 10000)
                        <div class="mt-4">
                            <button
                                type="button"
                                wire:click="openWithdrawModal"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-[#0B7B69] hover:bg-[#086354] text-white text-xs font-semibold shadow-xs transition cursor-pointer min-h-[40px]"
                            >
                                <span>Ajukan Penarikan Pertama</span>
                            </button>
                        </div>
                    @endif
                </div>
            @endif
        @endif

        <!-- Tab 2: Mutasi Saldo Dompet -->
        @if ($activeTab === 'transactions')
            @if ($transactionsList->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50/80 text-slate-500 uppercase tracking-wider text-[10px] border-b border-slate-100 font-semibold">
                            <tr>
                                <th class="px-6 py-3.5">Waktu Transaksi</th>
                                <th class="px-6 py-3.5">Kategori</th>
                                <th class="px-6 py-3.5">Keterangan</th>
                                <th class="px-6 py-3.5">Tipe Mutasi</th>
                                <th class="px-6 py-3.5 text-right">Nominal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @foreach ($transactionsList as $tx)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-slate-600">
                                        <div>{{ $tx->created_at ? $tx->created_at->format('d M Y') : '-' }}</div>
                                        <div class="text-[10px] text-slate-400">{{ $tx->created_at ? $tx->created_at->format('H:i') . ' WIB' : '' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($tx->kategori_transaksi === 'booking_revenue')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                Pendapatan Sewa
                                            </span>
                                        @elseif ($tx->kategori_transaksi === 'withdrawal')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-800 border border-blue-200">
                                                Penarikan Dana
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-700">
                                                {{ ucfirst(str_replace('_', ' ', $tx->kategori_transaksi)) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-slate-800">
                                        {{ $tx->deskripsi ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($tx->jenis_mutasi === 'kredit')
                                            <span class="inline-flex items-center gap-1 text-emerald-700 font-semibold">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                                                <span>Kredit (Masuk)</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-rose-700 font-semibold">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                                                <span>Debit (Keluar)</span>
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right font-bold tabular-nums {{ $tx->jenis_mutasi === 'kredit' ? 'text-emerald-700' : 'text-rose-700' }}">
                                        {{ $tx->formatted_nominal }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $transactionsList->links() }}
                </div>
            @else
                <!-- Empty State Mutasi -->
                <div class="py-16 px-6 text-center">
                    <div class="w-14 h-14 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <h4 class="text-sm font-bold text-slate-800">Belum Ada Mutasi Saldo</h4>
                    <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto leading-relaxed">
                        Mutasi saldo akan tercatat otomatis saat ada pesanan sewa lapangan yang diselesaikan atau penarikan dana diajukan.
                    </p>
                </div>
            @endif
        @endif

    </div>

    <!-- ================= MODAL FORM PENARIKAN DANA ================= -->
    @if ($showWithdrawModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="withdraw-modal-title">
            <!-- Backdrop -->
            <div
                class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
                wire:click="closeWithdrawModal"
            ></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center">
                <div class="relative w-full max-w-lg transform overflow-hidden rounded-3xl bg-white p-6 sm:p-7 text-left shadow-2xl transition-all border border-slate-200">
                    
                    <!-- Header Modal -->
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-[#0B7B69] flex items-center justify-center shrink-0 border border-emerald-100">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 id="withdraw-modal-title" class="text-base sm:text-lg font-bold text-slate-900">
                                    Formulir Penarikan Dana
                                </h3>
                                <p class="text-xs text-slate-500">Cairkan saldo aktif ke rekening bank terdaftar Anda.</p>
                            </div>
                        </div>
                        <button
                            type="button"
                            wire:click="closeWithdrawModal"
                            class="text-slate-400 hover:text-slate-600 transition p-1 cursor-pointer"
                            aria-label="Tutup modal"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- Saldo Aktif Banner -->
                    <div class="rounded-2xl bg-[#E8F5F1] border border-[#B2DFDB] p-4 mb-5 flex items-center justify-between">
                        <div>
                            <span class="text-[11px] font-semibold text-emerald-800 uppercase tracking-wider block">Saldo Siap Ditarik</span>
                            <span class="text-lg sm:text-xl font-bold text-slate-900 tabular-nums">
                                Rp {{ number_format($stats['saldo_aktif'], 0, ',', '.') }}
                            </span>
                        </div>
                        <button
                            type="button"
                            wire:click="setMaxAmount"
                            class="px-3.5 py-1.5 rounded-full bg-white border border-[#0B7B69] text-[#0B7B69] hover:bg-emerald-50 text-xs font-bold transition shadow-xs cursor-pointer min-h-[36px]"
                        >
                            Tarik Semua
                        </button>
                    </div>

                    <form wire:submit.prevent="proceedToConfirm" class="space-y-4">
                        
                        <!-- Input Nominal Penarikan -->
                        <div>
                            <label for="nominal_tarik" class="block text-xs font-bold text-slate-700 mb-1.5">
                                Nominal Penarikan (Rp) <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-sm font-bold text-slate-400">
                                    Rp
                                </span>
                                <input
                                    type="number"
                                    id="nominal_tarik"
                                    wire:model="nominal_tarik"
                                    placeholder="Contoh: 100000"
                                    min="10000"
                                    max="{{ $stats['saldo_aktif'] }}"
                                    step="1000"
                                    class="w-full pl-12 pr-4 py-2.5 rounded-2xl border text-sm font-semibold text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-[#0B7B69] transition min-h-[44px] {{ $errors->has('nominal_tarik') ? 'border-rose-300 bg-rose-50/30' : 'border-slate-200 bg-white' }}"
                                />
                            </div>
                            @error('nominal_tarik')
                                <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                            @enderror

                            <!-- Quick Amount Chips -->
                            <div class="flex items-center gap-1.5 mt-2 flex-wrap">
                                @foreach ([50000, 100000, 250000, 500000] as $chip)
                                    @if ($stats['saldo_aktif'] >= $chip)
                                        <button
                                            type="button"
                                            wire:click="setQuickAmount({{ $chip }})"
                                            class="px-2.5 py-1 rounded-full bg-slate-100 hover:bg-slate-200 text-[11px] font-semibold text-slate-700 transition cursor-pointer"
                                        >
                                            +Rp {{ number_format($chip / 1000, 0) }}rb
                                        </button>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <!-- Data Rekening Bank Tujuan -->
                        <div class="pt-2 border-t border-slate-100 space-y-3">
                            <span class="text-xs font-bold text-slate-900 block">Rekening Bank Tujuan</span>

                            <!-- Dropdown Bank -->
                            <div>
                                <label for="bank_kode" class="block text-xs font-medium text-slate-600 mb-1">
                                    Bank Tujuan <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <select
                                        id="bank_kode"
                                        wire:model="bank_kode"
                                        class="w-full pl-4 pr-11 py-2.5 rounded-2xl border border-slate-200 bg-white text-sm font-medium text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-[#0B7B69] transition appearance-none min-h-[44px] cursor-pointer"
                                    >
                                        @foreach ($availableBanks as $kode => $nama)
                                            <option value="{{ $kode }}">{{ $nama }}</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </div>
                                @error('bank_kode')
                                    <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Nomor Rekening -->
                            <div>
                                <label for="nomor_rekening" class="block text-xs font-medium text-slate-600 mb-1">
                                    Nomor Rekening <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="nomor_rekening"
                                    wire:model="nomor_rekening"
                                    placeholder="Contoh: 1234567890"
                                    class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 bg-white text-sm font-medium text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-[#0B7B69] transition min-h-[44px]"
                                />
                                @error('nomor_rekening')
                                    <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Nama Pemilik Rekening -->
                            <div>
                                <label for="nama_pemilik_rek" class="block text-xs font-medium text-slate-600 mb-1">
                                    Nama Pemilik Rekening <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="nama_pemilik_rek"
                                    wire:model="nama_pemilik_rek"
                                    placeholder="Harus sesuai nama buku tabungan"
                                    class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 bg-white text-sm font-medium text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-[#0B7B69] transition min-h-[44px]"
                                />
                                @error('nama_pemilik_rek')
                                    <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Catatan Biaya Admin -->
                        <div class="rounded-2xl bg-slate-50 p-3.5 text-xs text-slate-500 flex items-center justify-between border border-slate-100">
                            <span>Biaya Transfer Bank:</span>
                            <span class="font-bold text-emerald-700">Gratis (Rp 0)</span>
                        </div>

                        <!-- Tombol Aksi Modal Form -->
                        <div class="pt-3 flex items-center justify-end gap-3">
                            <button
                                type="button"
                                wire:click="closeWithdrawModal"
                                class="px-5 py-2.5 rounded-full border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs sm:text-sm font-semibold transition cursor-pointer min-h-[44px]"
                            >
                                Batal
                            </button>
                            <button
                                type="submit"
                                class="px-6 py-2.5 rounded-full bg-[#0B7B69] hover:bg-[#086354] active:scale-95 text-white text-xs sm:text-sm font-semibold shadow-xs transition cursor-pointer min-h-[44px]"
                            >
                                Lanjutkan Penarikan &rarr;
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    @endif

    <!-- ================= MODAL KONFIRMASI PENARIKAN ================= -->
    @if ($showConfirmModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="confirm-withdraw-title">
            <!-- Backdrop -->
            <div
                class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
                wire:click="cancelConfirm"
            ></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center">
                <div class="relative w-full max-w-md transform overflow-hidden rounded-3xl bg-white p-6 sm:p-7 text-left shadow-2xl transition-all border border-slate-200">
                    
                    <div class="flex items-center gap-3.5 mb-4">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-[#0B7B69] flex items-center justify-center shrink-0 border border-emerald-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 id="confirm-withdraw-title" class="text-base sm:text-lg font-bold text-slate-900">
                                Konfirmasi Penarikan Dana
                            </h3>
                            <span class="text-xs text-slate-400">Pastikan rincian rekening tujuan sudah benar</span>
                        </div>
                    </div>

                    <!-- Ringkasan Penarikan -->
                    <div class="rounded-2xl bg-slate-50 border border-slate-200/80 p-4 space-y-2.5 my-4 text-xs">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                            <span class="text-slate-500">Nominal Penarikan:</span>
                            <span class="font-bold text-slate-900 text-sm tabular-nums">
                                Rp {{ number_format((float) $nominal_tarik, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Bank Tujuan:</span>
                            <span class="font-bold text-slate-800">{{ $bank_kode }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Nomor Rekening:</span>
                            <span class="font-mono font-bold text-slate-800">{{ $nomor_rekening }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Nama Penerima:</span>
                            <span class="font-bold text-slate-800">{{ $nama_pemilik_rek }}</span>
                        </div>
                        <div class="flex items-center justify-between pt-2 border-t border-slate-200 text-emerald-800">
                            <span class="font-semibold">Estimasi Bersih Diterima:</span>
                            <span class="font-bold text-sm tabular-nums">
                                Rp {{ number_format((float) $nominal_tarik, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <p class="text-xs text-slate-500 leading-relaxed">
                        Dana akan dipotong dari saldo aktif Anda dan ditransfer ke rekening bank tujuan dalam waktu maksimal 1×24 jam kerja.
                    </p>

                    <!-- Tombol Aksi Konfirmasi -->
                    <div class="mt-6 flex items-center justify-end gap-3 pt-1">
                        <button
                            type="button"
                            wire:click="cancelConfirm"
                            class="px-5 py-2.5 rounded-full border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs sm:text-sm font-semibold transition cursor-pointer min-h-[44px]"
                        >
                            Periksa Kembali
                        </button>
                        <button
                            type="button"
                            wire:click="executeWithdrawal"
                            class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-full bg-[#0B7B69] hover:bg-[#086354] active:scale-95 text-white text-xs sm:text-sm font-semibold shadow-xs transition cursor-pointer min-h-[44px]"
                        >
                            <span wire:loading.remove wire:target="executeWithdrawal">Konfirmasi & Tarik</span>
                            <span wire:loading wire:target="executeWithdrawal" class="inline-flex items-center gap-1.5">
                                <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                <span>Memproses...</span>
                            </span>
                        </button>
                    </div>

                </div>
            </div>
        </div>
    @endif

</div>
