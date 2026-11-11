@php
    use App\Services\Admin\DashboardStats;
    use App\Services\Admin\TransactionMonitor as TM;
    use Carbon\CarbonImmutable;

    $code    = TM::code($trx->id, $trx->kode_booking);
    $rp      = fn ($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');
    $dt      = fn ($d, $fmt = 'j F Y, H.i') => $d ? CarbonImmutable::parse($d)->locale('id')->translatedFormat($fmt) : null;
    $labels  = DashboardStats::PAYMENT_LABELS;
    $method  = fn ($m) => $labels[$m] ?? \Illuminate\Support\Str::headline((string) $m);
    $initials = \Illuminate\Support\Str::of($trx->customer_name)->explode(' ')->filter()->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
    $stateStatus = ['lunas' => 'succes', 'menunggu' => 'pending', 'gagal' => 'failed', 'belum' => 'draft'];
    $backUrl = route('admin.transaksi.index');
    $itemsTotal = (float) $items->sum('harga_slot');
@endphp

<x-layouts.admin :title="$code" hero-size="compact">
    <x-slot:hero>
        <nav aria-label="Breadcrumb" class="mb-3 text-caption text-primary-200">
            <a href="{{ $backUrl }}" class="underline hover:text-white">Monitoring Transaksi</a>
            <span aria-hidden="true" class="mx-1">/</span>
            <span aria-current="page">{{ $code }}</span>
        </nav>

        <x-admin.page-header tone="dark" :title="'Booking ' . $code"
                            :description="'Dibuat ' . $dt($trx->created_at)">
            <x-slot:actions>
                <div class="flex flex-wrap items-center gap-2">
                    @if ($flags)<x-ui.badge tone="warning" icon="alert-circle">Perlu dicek</x-ui.badge>@endif
                    <x-admin.status-badge :status="$trx->status" :label="TM::BOOKING_STATUS[$trx->status] ?? null" />
                </div>
            </x-slot:actions>
        </x-admin.page-header>
    </x-slot:hero>

    {{-- Ringkasan --}}
    <x-ui.card>
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex min-w-0 items-center gap-4">
                <div class="grid size-14 shrink-0 place-items-center rounded-full bg-primary-100 text-h5 text-primary-700" aria-hidden="true">{{ $initials }}</div>
                <div class="min-w-0">
                    <p class="truncate text-h5 text-ink">{{ $trx->customer_name }}</p>
                    <p class="text-body-sm text-ink-muted"><span class="break-all">{{ $trx->customer_email }}</span>{{ $trx->customer_phone ? ', ' . $trx->customer_phone : '' }}</p>
                </div>
            </div>
            <div class="flex items-center gap-4 lg:text-right">
                <div>
                    <p class="text-caption text-ink-muted">Total tagihan</p>
                    <p class="text-metric tabular-nums text-ink">{{ $rp($trx->total_tagihan) }}</p>
                </div>
                <x-admin.status-badge :status="$stateStatus[$state]" :label="TM::PAYMENT_STATE[$state]" />
            </div>
        </div>
    </x-ui.card>

    @if ($flags)
        <x-ui.alert type="info" class="mt-4">
            <span class="font-semibold">Perlu dicek.</span>
            <ul class="mt-1 list-disc pl-5">
                @foreach ($flags as $msg)<li>{{ $msg }}</li>@endforeach
            </ul>
        </x-ui.alert>
    @endif

    <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-[minmax(0,1fr)_22rem] lg:items-start">
        <div class="grid min-w-0 grid-cols-1 gap-4">

            {{-- Rincian slot --}}
            <x-ui.card class="min-w-0">
                <h2 class="text-h6 text-ink">Rincian slot</h2>
                @if ($items->isEmpty())
                    <p class="mt-2 text-body-sm text-ink-muted">Booking ini tidak memiliki rincian slot.</p>
                @else
                    <ul class="mt-1 divide-y divide-line">
                        @foreach ($items as $it)
                            <li class="flex flex-col gap-1 py-3 sm:flex-row sm:items-center sm:justify-between sm:gap-4">
                                <div class="min-w-0">
                                    <a href="{{ route('admin.moderasi.show', ['jenis' => 'lapangan', 'id' => $it->field_id]) }}" class="text-body-sm font-semibold text-ink hover:underline">{{ $it->nama_lapangan }}</a>
                                    <p class="text-caption text-ink-muted">
                                        {{ $it->nama_venue }}, <a href="{{ route('admin.verifikasi.show', $it->mitra_id) }}" class="underline">{{ $it->nama_usaha ?: 'Mitra #' . $it->mitra_id }}</a>
                                    </p>
                                    <p class="text-caption tabular-nums text-ink-body">
                                        {{ $dt($it->tanggal_sewa, 'l, j F Y') }}, {{ substr($it->jam_mulai, 0, 5) }} sampai {{ substr($it->jam_selesai, 0, 5) }}
                                    </p>
                                </div>
                                <div class="flex items-center gap-3 sm:flex-col sm:items-end sm:gap-1">
                                    <p class="text-body-sm tabular-nums text-ink">{{ $rp($it->harga_slot) }}</p>
                                    <x-admin.status-badge :status="$it->status_item" />
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    <p class="mt-1 flex justify-between border-t border-line pt-3 text-body-sm">
                        <span class="text-ink-muted">Jumlah harga slot</span>
                        <span class="tabular-nums text-ink">{{ $rp($itemsTotal) }}</span>
                    </p>
                @endif
            </x-ui.card>

            {{-- Pembayaran --}}
            <x-ui.card class="min-w-0">
                <h2 class="text-h6 text-ink">Pembayaran</h2>
                @if ($payments->isEmpty())
                    <p class="mt-2 text-body-sm text-ink-muted">Belum ada upaya pembayaran untuk booking ini.</p>
                @else
                    <ul class="mt-1 divide-y divide-line">
                        @foreach ($payments as $p)
                            <li class="flex flex-col gap-1 py-3 sm:flex-row sm:items-center sm:justify-between sm:gap-4">
                                <div class="min-w-0">
                                    <p class="text-body-sm text-ink">{{ $method($p->metode_pembayaran) }}</p>
                                    <p class="break-all text-caption text-ink-muted">{{ $p->pg_transaction_id }}</p>
                                    <p class="text-caption text-ink-muted">
                                        {{ $p->paid_at ? 'Dibayar ' . $dt($p->paid_at) : ($p->created_at ? 'Dibuat ' . $dt($p->created_at) : 'Waktu tidak tercatat') }}
                                    </p>
                                </div>
                                <div class="flex items-center gap-3 sm:flex-col sm:items-end sm:gap-1">
                                    <p class="text-body-sm tabular-nums text-ink">{{ $rp($p->nominal_bayar) }}</p>
                                    <x-admin.status-badge :status="$p->status_pembayaran" />
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-ui.card>

            {{-- Refund --}}
            <x-ui.card class="min-w-0">
                <h2 class="text-h6 text-ink">Refund</h2>
                @if ($refunds->isEmpty())
                    <p class="mt-2 text-body-sm text-ink-muted">Tidak ada permintaan refund untuk booking ini.</p>
                @else
                    <ul class="mt-1 divide-y divide-line">
                        @foreach ($refunds as $rf)
                            <li class="py-3">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="min-w-0">
                                        <p class="text-body-sm tabular-nums text-ink">{{ $rp($rf->nominal_refund) }}</p>
                                        <p class="text-caption text-ink-muted">
                                            {{ $rf->nama_lapangan ? $rf->nama_lapangan . ', ' . $dt($rf->tanggal_sewa, 'j M Y') . ', ' . substr($rf->jam_mulai, 0, 5) : 'Slot tidak ditemukan' }}
                                        </p>
                                    </div>
                                    <x-admin.status-badge :status="$rf->status_refund" />
                                </div>
                                <p class="mt-2 whitespace-pre-line wrap-break-word text-body-sm text-ink-body">{{ $rf->alasan_refund }}</p>
                                <dl class="mt-2 grid grid-cols-[7rem_minmax(0,1fr)] gap-x-3 gap-y-0.5 text-caption">
                                    <dt class="text-ink-muted">Metode</dt><dd class="text-ink-body">{{ $method($rf->metode_refund) }}</dd>
                                    <dt class="text-ink-muted">Rekening tujuan</dt><dd class="break-all text-ink-body">{{ TM::maskAccount($rf->rekening_tujuan) }}</dd>
                                    @if ($rf->created_at)
                                        <dt class="text-ink-muted">Diajukan</dt><dd class="text-ink-body">{{ $dt($rf->created_at) }}</dd>
                                    @endif
                                    @if ($rf->status_refund === 'rejected' && $rf->alasan_penolakan)
                                        <dt class="text-ink-muted">Alasan ditolak</dt><dd class="wrap-break-word text-ink-body">{{ $rf->alasan_penolakan }}</dd>
                                    @endif
                                    <dt class="text-ink-muted">Diproses oleh</dt><dd class="text-ink-body">{{ $rf->processor ?? 'Belum diproses' }}</dd>
                                    @if ($rf->complated_at)
                                        <dt class="text-ink-muted">Selesai</dt><dd class="text-ink-body">{{ $dt($rf->complated_at) }}</dd>
                                    @endif
                                </dl>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-ui.card>
        </div>

        <div class="grid min-w-0 grid-cols-1 content-start gap-4">
            {{-- Rekonsiliasi --}}
            <x-ui.card class="min-w-0">
                <h2 class="text-h6 text-ink">Rekonsiliasi uang</h2>
                <dl class="mt-2 space-y-2 text-body-sm">
                    <div class="flex justify-between gap-3"><dt class="text-ink-muted">Total tagihan</dt><dd class="tabular-nums text-ink">{{ $rp($ledger['tagihan']) }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-ink-muted">Berhasil dibayar</dt><dd class="tabular-nums text-ink">{{ $rp($ledger['dibayar']) }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-ink-muted">Refund selesai</dt><dd class="tabular-nums text-ink">{{ $rp($ledger['refund_selesai']) }}</dd></div>
                    @if ($ledger['refund_berjalan'] > 0)
                        <div class="flex justify-between gap-3"><dt class="text-ink-muted">Refund berjalan</dt><dd class="tabular-nums text-warning-800">{{ $rp($ledger['refund_berjalan']) }}</dd></div>
                    @endif
                    <div class="flex justify-between gap-3 border-t border-line pt-2"><dt class="font-semibold text-ink">Diterima bersih</dt><dd class="font-semibold tabular-nums text-ink">{{ $rp($ledger['bersih']) }}</dd></div>
                </dl>
                <p class="mt-3 text-caption text-ink-muted">Dihitung dari tabel pembayaran dan refund. Bukan pendapatan platform; komisi belum diatur.</p>
            </x-ui.card>

            <x-ui.card class="min-w-0">
                <h2 class="text-h6 text-ink">Data booking</h2>
                <dl class="mt-1">
                    <x-admin.info-row label="Kode" :value="$code" />

                    <x-admin.info-row label="Status">
                        <x-admin.status-badge :status="$trx->status" :label="TM::BOOKING_STATUS[$trx->status] ?? null" />
                    </x-admin.info-row>

                    <x-admin.info-row label="Dibuat" :value="$dt($trx->created_at)" />

                    @if ($trx->expires_at)
                        <x-admin.info-row label="Batas bayar" :value="$dt($trx->expires_at)" />
                    @endif
                    @unless ($trx->kode_booking)
                        <p class="mt-2 text-caption text-ink-muted">Booking ini dibuat sebelum kode acak ada, jadi kodenya diturunkan dari nomor booking.</p>
                    @endunless
                    <x-admin.info-row label="Jumlah slot" :value="(string) $items->count()" />
                </dl>
                <p class="mt-2 text-caption text-ink-muted">Kode diturunkan dari nomor booking, belum ada kolom kode tersendiri di database.</p>
            </x-ui.card>
        </div>
    </div>
</x-layouts.admin>