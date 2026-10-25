<x-layouts.mitra title="Dashboard Mitra — SportPlace" heading="Dashboard Mitra">
    <div class="space-y-6">

        <!-- Welcome Banner & Business Identity Card -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-xs">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div>
                    <!-- Penanda Halaman (Breadcrumb) -->
                    <div class="flex items-center gap-2 text-xs font-medium text-slate-500 mb-2">
                        <span class="text-slate-400">SportPlace Mitra</span>
                        <span class="text-slate-300">/</span>
                        <span class="text-slate-800 font-semibold">Dashboard Mitra</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">
                        Halo, {{ auth()->user()->name }}! 👋
                    </h1>
                    {{-- <p class="text-sm text-slate-500 mt-1 max-w-2xl leading-relaxed">
                        Pantau jadwal reservasi lapangan hari ini, ringkasan pendapatan sewa, dan status operasional fasilitas olahragamu dalam satu tempat.
                    </p> --}}
                </div>

                <!-- Quick Action Buttons: Rata Kanan dan 1 Baris -->
                <div class="flex flex-row items-center justify-end gap-3 shrink-0">
                    <a
                        href="{{ url('/mitra/venues') }}"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs sm:text-sm font-semibold shadow-xs transition duration-150 whitespace-nowrap min-h-[42px]"
                    >
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span>Kelola Lapangan</span>
                    </a>

                    <a
                        href="{{ url('/mitra/pesanan') }}"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-[#0B7B69] hover:bg-[#086354] text-white text-xs sm:text-sm font-semibold shadow-xs transition duration-150 whitespace-nowrap min-h-[42px]"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>Kalender Booking</span>
                    </a>
                </div>
            </div>
            <div class="hidden lg:block flex-1">
                <p class="text-sm text-slate-500 mt-1 max-w-2xl leading-relaxed">
                    Pantau jadwal reservasi lapangan hari ini, ringkasan pendapatan sewa, dan status operasional fasilitas olahragamu dalam satu tempat.
                </p>
            </div>
        </div>

        <!-- T3.5: Action Shortcuts & Operational Alerts (Jika Ada Tindakan Tertunda) -->
        @if ($rejectedVenues->isNotEmpty() || $rejectedFields->isNotEmpty())
            <div class="rounded-2xl bg-rose-50 border-2 border-rose-200 p-5 text-sm text-rose-900 shadow-xs">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div class="flex-1">
                        <h4 class="font-bold text-rose-950 mb-1">Perhatian: Terdapat Fasilitas yang Memerlukan Revisi</h4>
                        <p class="text-xs text-rose-800 leading-relaxed mb-3">
                            Sebagian venue atau lapangan Anda ditolak oleh admin pada tahap peninjauan. Silakan tinjau catatan alasan penolakan dan perbaiki informasinya.
                        </p>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($rejectedVenues as $rv)
                                <a href="{{ url('/mitra/venues/' . $rv->id . '/edit') }}" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-white border border-rose-300 text-xs font-semibold text-rose-700 hover:bg-rose-100 transition">
                                    <span>Venue: {{ $rv->nama_venue }}</span>
                                    <span class="text-[10px] text-rose-500 font-normal">({{ $rv->alasan_penolakan ?? 'Perlu revisi' }})</span>
                                </a>
                            @endforeach
                            @foreach ($rejectedFields as $rf)
                                <a href="{{ url('/mitra/fields/' . $rf->id . '/edit') }}" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-white border border-rose-300 text-xs font-semibold text-rose-700 hover:bg-rose-100 transition">
                                    <span>Lapangan: {{ $rf->nama_lapangan }}</span>
                                    <span class="text-[10px] text-rose-500 font-normal">({{ $rf->alasan_penolakan ?? 'Perlu revisi' }})</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if ($pendingBookingsCount > 0)
            <div class="rounded-2xl bg-amber-50 border border-amber-200 p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-amber-900 shadow-xs">
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-amber-100 text-amber-700 shrink-0 font-bold text-xs">
                        {{ $pendingBookingsCount }}
                    </span>
                    <div>
                        <h5 class="text-xs sm:text-sm font-bold text-amber-950">Pesanan Baru Menunggu Verifikasi Pembayaran</h5>
                        <p class="text-xs text-amber-700">Terdapat {{ $pendingBookingsCount }} reservasi transfer manual yang menunggu peninjauan bukti transfer.</p>
                    </div>
                </div>
                <a href="{{ url('/mitra/pesanan?status=pending') }}" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold shadow-xs transition shrink-0 min-h-[38px]">
                    <span>Tinjau Pesanan</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </a>
            </div>
        @endif

        @if ($readyToCompleteCount > 0)
            <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-emerald-900 shadow-xs">
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-emerald-100 text-[#0B7B69] shrink-0 font-bold text-xs">
                        {{ $readyToCompleteCount }}
                    </span>
                    <div>
                        <h5 class="text-xs sm:text-sm font-bold text-emerald-950">Sesi Bermain Siap Diselesaikan</h5>
                        <p class="text-xs text-emerald-700">Terdapat {{ $readyToCompleteCount }} sesi yang jam sewanya telah selesai dan dapat ditandai selesai (Completed).</p>
                    </div>
                </div>
                <a href="{{ url('/mitra/pesanan?filter=ready_to_complete') }}" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl bg-[#0B7B69] hover:bg-[#086354] text-white text-xs font-semibold shadow-xs transition shrink-0 min-h-[38px]">
                    <span>Tandai Selesai</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </a>
            </div>
        @endif

        <!-- 4 Metric Cards Grid (Sesuai DS II & FR-REPORT-001) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">

            <!-- Card 1: Booking Hari Ini (T3.2, AC-FR-REPORT-001-01) -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Booking Hari Ini</span>
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 text-[#0B7B69] flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-slate-900 tabular-nums">
                        {{ $todayBookingsCount }}
                    </div>
                    <div class="flex items-center gap-1.5 mt-2 text-xs text-slate-500">
                        <span class="inline-block w-2 h-2 rounded-full {{ $todayBookingsCount > 0 ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                        <span>{{ $todayBookingsCount > 0 ? $todayBookingsCount . ' sesi reservasi aktif' : 'Belum ada reservasi hari ini' }}</span>
                    </div>
                </div>
            </div>

            <!-- Card 2: Ringkasan Pendapatan (T3.3) -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Pendapatan</span>
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 text-[#0B7B69] flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div>
                    <div class="text-2xl sm:text-3xl font-bold text-slate-900 tabular-nums tracking-tight">
                        Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                    </div>
                    <div class="flex items-center justify-between mt-2 text-xs text-slate-500">
                        <span>Hari Ini: Rp {{ number_format($todayRevenue, 0, ',', '.') }}</span>
                        @if ($saldoAktif > 0)
                            <span class="font-semibold text-emerald-700">Wallet: Rp {{ number_format($saldoAktif, 0, ',', '.') }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Card 3: Rating Rata-rata (T3.4, GAP-01 / OQ-019) -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Rating Venue</span>
                    <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                    </div>
                </div>
                <div>
                    <div class="text-lg sm:text-xl font-bold text-slate-700">
                        Belum Ada Ulasan
                    </div>
                    <p class="text-xs text-slate-400 mt-2">
                        Ulasan aktif setelah penyewa menyelesaikan pesanan.
                    </p>
                </div>
            </div>

            <!-- Card 4: Fasilitas Operasional (Venue & Lapangan) -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Fasilitas Aktif</span>
                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                </div>
                <div>
                    <div class="text-2xl sm:text-3xl font-bold text-slate-900 tabular-nums">
                        {{ $fieldsCount }} <span class="text-base font-medium text-slate-500">Lapangan</span>
                    </div>
                    <div class="flex items-center justify-between mt-2 text-xs text-slate-500">
                        <span>{{ $venuesCount }} Venue terdaftar</span>
                        <a href="{{ url('/mitra/venues') }}" class="font-semibold text-[#0B7B69] hover:underline">Kelola &rarr;</a>
                    </div>
                </div>
            </div>

        </div>

        <!-- Section: Jadwal Booking Hari Ini (MTR-03 / Kalender Booking Masuk) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-base font-bold text-slate-900">Jadwal Booking Hari Ini</h3>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-[#0B7B69] border border-emerald-200">
                            {{ \Carbon\Carbon::today()->translatedFormat('d F Y') }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">Daftar sesi bermain dan kehadiran penyewa untuk hari ini.</p>
                </div>

                <a
                    href="{{ url('/mitra/pesanan') }}"
                    class="text-xs font-semibold text-[#0B7B69] hover:underline inline-flex items-center gap-1 self-start sm:self-auto"
                >
                    <span>Lihat Kalender Lengkap</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            @if ($todayBookings->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50/80 text-slate-500 uppercase tracking-wider text-[10px] border-b border-slate-100 font-semibold">
                            <tr>
                                <th class="px-6 py-3.5">Waktu Sewa</th>
                                <th class="px-6 py-3.5">Venue & Lapangan</th>
                                <th class="px-6 py-3.5">Penyewa</th>
                                <th class="px-6 py-3.5">Tarif Slot</th>
                                <th class="px-6 py-3.5">Status Sesi</th>
                                <th class="px-6 py-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @foreach ($todayBookings as $b)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-slate-900 tabular-nums font-bold">
                                        {{ substr($b->jam_mulai, 0, 5) }} – {{ substr($b->jam_selesai, 0, 5) }} WIB
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-slate-900">{{ $b->field?->nama_lapangan ?? 'Lapangan' }}</div>
                                        <div class="text-[11px] text-slate-400">{{ $b->field?->venue?->nama_venue ?? 'Venue' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-slate-800">{{ $b->header?->user?->name ?? 'Penyewa' }}</div>
                                        <div class="text-[11px] text-slate-400">{{ $b->header?->user?->no_hp ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-slate-900 tabular-nums font-bold">
                                        Rp {{ number_format($b->harga_slot, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($b->status_item === 'completed')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                                Selesai
                                            </span>
                                        @elseif ($b->status_item === 'no_show')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                                                No-Show
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                                                Ongoing
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <a href="{{ url('/mitra/pesanan/' . $b->id) }}" class="text-xs font-semibold text-[#0B7B69] hover:underline">
                                            Detail &rarr;
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <!-- Empty State (Sesuai Desain Figma / AC-FR-REPORT-001-01) -->
                <div class="py-12 px-6 text-center">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h4 class="text-sm font-bold text-slate-800">Belum Ada Booking Hari Ini</h4>
                    <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto leading-relaxed">
                        Tidak ada sesi bermain yang dijadwalkan pada hari ini. Lapangan Anda tetap siap dan terbuka untuk reservasi mendatang.
                    </p>
                    <div class="mt-5 flex flex-wrap items-center justify-center gap-2">
                        <a
                            href="{{ url('/mitra/pesanan') }}"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white border border-slate-300 hover:bg-slate-50 text-xs font-semibold text-slate-700 transition"
                        >
                            <span>Cek Jadwal Mendatang</span>
                        </a>
                        <a
                            href="{{ url('/mitra/jadwal') }}"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-xs font-semibold text-[#0B7B69] transition"
                        >
                            <span>Atur Jam Buka & Slot</span>
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Guidance Banner for Next Operations (Fase 4 - 6) -->
        <div class="rounded-2xl bg-[#06231B] text-slate-200 p-6 sm:p-7 shadow-xs">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-5">
                <div>
                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-400 uppercase tracking-wider mb-1">
                        Langkah Selanjutnya • Operasional Fasilitas
                    </span>
                    <h3 class="text-lg font-bold text-white">Lengkapi Venue & Lapangan untuk Mulai Menerima Booking</h3>
                    <p class="text-xs text-emerald-100/70 mt-1 max-w-2xl leading-relaxed">
                        Pastikan Anda telah mengisi foto lapangan berkualitas, memilih fasilitas yang tersedia, dan mengatur tarif sewa per jam agar penyewa dapat menemukan venue Anda di SportPlace.
                    </p>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    <a
                        href="{{ url('/mitra/venues') }}"
                        class="px-5 py-2.5 rounded-xl bg-[#0B7B69] hover:bg-[#086354] text-white text-xs font-semibold shadow-xs transition duration-150 inline-flex items-center gap-2 min-h-[40px]"
                    >
                        <span>Kelola Venue & Lapangan</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

    </div>
</x-layouts.mitra>
