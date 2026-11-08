@php
    $first = \Illuminate\Support\Str::before(auth()->user()->name ?? 'Admin', ' ');
    $hour = now()->hour;
    $greeting = match (true) {
        $hour < 11 => 'Selamat pagi',
        $hour < 15 => 'Selamat siang',
        $hour < 19 => 'Selamat sore',
        default => 'Selamat malam',
    };
    $toneClass = [
        'success' => 'bg-success-100 text-success-800',
        'danger'  => 'bg-danger-100 text-danger-700',
        'info'    => 'bg-info-100 text-info-700',
        'warning' => 'bg-warning-100 text-warning-800',
        'neutral' => 'bg-surface-sunken text-ink-muted',
    ];
    $toneText = [
        'success' => 'text-success-800',
        'danger'  => 'text-danger-700',
        'info'    => 'text-info-700',
        'warning' => 'text-warning-800',
        'neutral' => 'text-ink-muted',
    ];
    $mitra = $queues['mitra'];
    $moderasi = $queues['moderasi'];
    $waiting = $mitra['count'] + $moderasi['count'];
    $href = fn ($name) => \Illuminate\Support\Facades\Route::has($name) ? route($name) : '#';
    $summary = $waiting > 0
        ? "{$mitra['count']} pendaftaran mitra dan {$moderasi['count']} listing menunggu tinjauan."
        : 'Semua antrian sudah ditinjau.';
@endphp

<x-layouts.admin title="Dashboard">
    <x-slot:hero>
        <x-admin.page-header
            tone="dark"
            :title="$greeting . ', ' . $first"
            :description="now()->locale('id')->translatedFormat('l, j F Y') . '. ' . $summary" />
    </x-slot:hero>

    <section aria-label="Ringkasan platform"
        class="grid grid-cols-1 divide-y divide-line overflow-hidden rounded-md border border-line bg-surface
            md:grid-cols-3 md:gap-4 md:divide-y-0 md:overflow-visible md:rounded-none md:border-0 md:bg-transparent">
        <x-admin.kpi-card label="Mitra terverifikasi" icon="store"
            caption="Akun mitra berstatus disetujui"
            :value="$kpis['mitra']['value']" :delta="$kpis['mitra']['delta']" :trend="$kpis['mitra']['trend']" />
        <x-admin.kpi-card label="Lapangan tayang" icon="layers"
            caption="Lapangan berstatus aktif"
            :value="$kpis['lapangan']['value']" :delta="$kpis['lapangan']['delta']" :trend="$kpis['lapangan']['trend']" />
        <x-admin.kpi-card label="Transaksi 30 hari terakhir" icon="wallet"
            caption="Pembayaran berhasil"
            :value="$kpis['transaksi']['value']" :delta="$kpis['transaksi']['delta']" :trend="$kpis['transaksi']['trend']" />
    </section>

    <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-3">
        {{-- Grafik --}}
        <x-ui.card class="min-w-0 lg:col-span-2">
            <div x-data="revenueChart(@js($chart))">
                <div>
                    <h2 class="text-h4 text-ink">Tren transaksi</h2>
                    <p class="mt-0.5 text-body-sm text-ink-muted">Total nilai booking dengan pembayaran berhasil, bukan pendapatan komisi.</p>
                </div>

                <div class="mt-4 flex flex-wrap items-center gap-2">
                    <x-ui.segmented label="Ukuran" model="metric" :options="['nilai' => 'Nilai (Rp)', 'jumlah' => 'Jumlah']" />
                    <x-ui.segmented label="Periode" model="range" :options="['7d' => '7 hari', '30d' => '30 hari', '6m' => '6 bulan']" />
                </div>

                <div class="relative mt-5 h-64 md:h-72">
                    <canvas x-ref="canvas" role="img" :aria-label="summary"></canvas>
                    <p x-show="total === 0" x-cloak class="absolute inset-0 grid place-items-center text-body-sm text-ink-muted">
                        Belum ada pembayaran berhasil pada periode ini.
                    </p>
                </div>

                <div class="mt-4 flex flex-col gap-2 border-t border-line pt-3 text-caption text-ink-muted md:flex-row md:items-center md:justify-between">
                    <p class="flex flex-wrap items-center gap-x-4 gap-y-1">
                        <span class="inline-flex items-center gap-1.5"><span class="h-0.5 w-5 bg-primary-600"></span>Periode selesai</span>
                        <span class="inline-flex items-center gap-1.5"><span class="w-5 border-t-2 border-dashed border-primary-600"></span><span x-text="partialText"></span></span>
                    </p>
                    <p class="text-ink-strong"><span x-text="totalLabel"></span>: <strong class="font-semibold" x-text="totalText"></strong></p>
                </div>
            </div>
        </x-ui.card>

        {{-- Kolom kanan --}}
        <div class="grid min-w-0 grid-cols-1 content-start gap-4">
            <x-ui.card class="min-w-0 border-l-4 border-l-warning-500">
                <h2 class="text-h4 text-ink">Perlu tindakan admin</h2>

                @if ($waiting === 0)
                    <div class="mt-4 flex items-center gap-3 text-body-md text-ink-muted">
                        <span class="grid size-9 place-items-center rounded-full bg-success-100 text-success-800"><x-ui.icon name="check-circle" /></span>
                        Tidak ada antrian. Semua sudah ditinjau.
                    </div>
                @else
                    <div class="mt-4 divide-y divide-line">
                        @if ($mitra['count'])
                            <x-admin.action-item :count="$mitra['count']" title="Pendaftaran mitra menunggu verifikasi"
                                :hint="$mitra['since']" cta="Tinjau pendaftaran" :href="$href('admin.verifikasi.index')" />
                        @endif
                        @if ($moderasi['count'])
                            <x-admin.action-item :count="$moderasi['count']" title="Listing menunggu moderasi"
                                :hint="$moderasi['since']" cta="Tinjau listing" :href="$href('admin.moderasi.index')" />
                        @endif
                    </div>
                @endif
            </x-ui.card>

            <x-ui.card class="min-w-0 ">
                <h2 class="text-h5 text-ink">Aktivitas terbaru</h2>
            
                <ol class="mt-3">
                    @forelse ($activity as $row)
                        <li class="flex gap-3 border-t border-line py-3 first:border-t-0 first:pt-0 last:pb-0">
                            <span class="grid size-8 shrink-0 place-items-center rounded-full {{ $toneClass[$row['tone']] }}">
                                <x-ui.icon :name="$row['icon']" />
                            </span>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-baseline justify-between gap-3">
                                    <p class="truncate text-body-sm font-semibold text-ink" title="{{ $row['subject'] }}">{{ $row['subject'] }}</p>
                                    <p class="shrink-0 text-caption text-ink-muted">{{ $row['time'] }}</p>
                                </div>
                                <p class="text-caption text-ink-muted">
                                    <span class="font-semibold {{ $toneText[$row['tone']] }}">{{ ucfirst($row['verb']) }}</span>
                                    oleh {{ $row['actor'] }}
                                </p>
                                @if ($row['note'])
                                    <p class="mt-0.5 truncate text-caption text-ink-muted">{{ $row['note'] }}</p>
                                @endif
                            </div>
                        </li>
                    @empty
                        <li class="text-body-sm text-ink-muted">Belum ada aktivitas. Tindakan verifikasi dan moderasi akan muncul di sini.</li>
                    @endforelse
                </ol>
            </x-ui.card>
        </div>
    </div>
</x-layouts.admin>