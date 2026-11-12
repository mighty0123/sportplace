@php
    use App\Services\Admin\DashboardStats;
    use App\Services\Admin\TransactionMonitor as TM;

    $f       = $this->f;
    $rows    = $this->rows;
    $counts  = $this->counts;
    $s       = $this->summary;
    $methodLabels = DashboardStats::PAYMENT_LABELS;
    $methodLabel  = fn ($m) => $m ? ($methodLabels[$m] ?? \Illuminate\Support\Str::headline($m)) : null;

    $rp      = fn ($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');
    $compact = fn ($n) => 'Rp ' . ($n >= 1e9 ? number_format($n / 1e9, 1, ',', '.') . ' M' : ($n >= 1e6 ? number_format($n / 1e6, 1, ',', '.') . ' jt' : number_format($n, 0, ',', '.')));
    $initialsOf = fn ($name) => \Illuminate\Support\Str::of($name ?: '?')->explode(' ')->filter()->take(2)
        ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');

    $stateStatus = ['lunas' => 'succes', 'menunggu' => 'pending', 'gagal' => 'failed', 'belum' => 'draft'];
    $statusTabs  = ['' => 'Semua'] + TM::BOOKING_STATUS;
    $totalCount  = array_sum($counts);
    $periodNames = ['hari-ini' => 'Hari ini', '7' => '7 hari terakhir', '30' => '30 hari terakhir', '90' => '90 hari terakhir', 'semua' => 'Semua waktu', 'kustom' => 'Rentang pilihan'];
@endphp

<div class="space-y-4">
    <x-admin.summary-strip :items="[
        ['label' => 'Booking', 'value' => number_format($s['count'], 0, ',', '.'), 'note' => $periodNames[$f['period']] ?? ''],
        ['label' => 'Nilai terbayar', 'value' => $compact($s['paid']), 'note' => $rp($s['paid'])],
        ['label' => 'Refund selesai', 'value' => $compact($s['refund']), 'note' => $rp($s['refund'])],
        ['label' => 'Perlu dicek', 'value' => number_format($s['flagged'], 0, ',', '.'), 'alert' => $s['flagged'] > 0, 'note' => 'Data yang saling bertentangan'],
    ]" />

    {{-- Tab status booking dan pencarian --}}
    <div class="flex flex-col gap-3 xl:flex-row xl:items-end xl:justify-between">
        <nav aria-label="Filter status booking"
            class="flex gap-5 overflow-x-auto overflow-y-hidden border-b border-line-strong pb-px xl:flex-1 xl:overflow-visible">
            @foreach ($statusTabs as $key => $label)
                <button type="button" wire:click="setStatus('{{ $key }}')" wire:key="tab-{{ $key ?: 'semua' }}"
                        @if ($f['status'] === (string) $key) aria-current="true" @endif
                        @class([
                            '-mb-px min-h-11 shrink-0 whitespace-nowrap border-b-2 text-label-lg transition-colors motion-reduce:transition-none',
                            'border-brand font-semibold text-brand-link' => $f['status'] === (string) $key,
                            'border-transparent text-ink-body hover:text-ink' => $f['status'] !== (string) $key,
                        ])>
                    {{ $label }}
                    <span class="ml-1 tabular-nums">{{ $key === '' ? $totalCount : ($counts[$key] ?? 0) }}</span>
                </button>
            @endforeach
        </nav>

        <div class="relative xl:w-80 xl:flex-none">
            <label for="cari-trx" class="sr-only">Cari booking</label>
            <x-ui.icon name="search" class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-ink-muted" />
            <input id="cari-trx" type="search" maxlength="100" autocomplete="off" wire:model.live.debounce.300ms="search"
                placeholder="Kode booking, penyewa, atau ID transaksi"
                class="min-h-11 w-full rounded-md border border-line-strong bg-surface pl-10 pr-3 text-body-sm text-ink placeholder:text-ink-muted focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-brand lg:min-h-10">
        </div>
    </div>

    {{-- Filter --}}
    <div class="grid gap-3 sm:grid-cols-3">
        <x-ui.select compact name="period" wire:model.live="period" label="Periode" :options="TM::PERIODS" />
        <x-ui.select compact name="method" wire:model.live="method" label="Metode pembayaran"
                    :options="['' => 'Semua'] + collect($this->methods)->mapWithKeys(fn ($m) => [$m => $methodLabel($m)])->all()" />
        <x-ui.select compact name="sort" wire:model.live="sort" label="Urutan" :options="TM::SORTS" />
    </div>

    @if ($f['period'] === 'kustom')
        <div class="grid gap-3 sm:grid-cols-2 lg:max-w-md">
            <x-ui.input name="from" type="date" wire:model.live="from" label="Dari tanggal" />
            <x-ui.input name="to" type="date" wire:model.live="to" label="Sampai tanggal" />
        </div>
    @endif

    @if ($rangeError)
        <x-ui.alert type="error">{{ $rangeError }}</x-ui.alert>
    @endif

    <div class="flex flex-wrap items-center justify-between gap-3">
        <label class="inline-flex min-h-10 cursor-pointer items-center gap-2.5 text-body-sm text-ink">
            <input type="checkbox" wire:model.live="flagged" class="size-4.5 accent-primary-500">
            Hanya yang perlu dicek
        </label>
        @if ($this->hasActiveFilters())
            <x-ui.button variant="ghost" size="sm" type="button" wire:click="clearFilters">Reset filter</x-ui.button>
        @endif
    </div>

    <div wire:loading.class="opacity-60"
        wire:target="search,setStatus,period,from,to,method,sort,flagged,clearFilters,clearSearch,nextPage,previousPage,gotoPage,perPage"
        class="space-y-4 transition-opacity motion-reduce:transition-none">
        <p class="sr-only" role="status">{{ $rows->total() }} booking ditemukan</p>

        @if ($rows->isEmpty())
            <x-ui.card>
                @if ($f['search'] !== '')
                    <x-ui.empty-state icon="search" title="Tidak ada hasil untuk “{{ $f['search'] }}”"
                                    description="Cari dengan ID booking (misalnya BK-000123), nama atau email penyewa, atau ID transaksi gateway.">
                        <x-ui.button variant="outline" type="button" wire:click="clearSearch">Hapus pencarian</x-ui.button>
                    </x-ui.empty-state>
                @elseif ($this->hasActiveFilters() || $f['period'] !== '30')
                    <x-ui.empty-state icon="search" title="Tidak ada transaksi yang cocok"
                                    description="Longgarkan filter, atau perluas periode ke “Semua waktu”.">
                        <x-ui.button variant="outline" type="button" wire:click="clearFilters">Reset filter</x-ui.button>
                    </x-ui.empty-state>
                @else
                    <x-ui.empty-state title="Belum ada transaksi dalam 30 hari terakhir"
                                    description="Booking baru dari penyewa akan muncul di sini.">
                        <x-ui.button variant="outline" type="button" wire:click="$set('period', 'semua')">Lihat semua waktu</x-ui.button>
                    </x-ui.empty-state>
                @endif
            </x-ui.card>
        @else
            {{-- Tabel (xl ke atas): satu status per baris --}}
            <div class="hidden overflow-x-auto rounded-md border border-line bg-surface xl:block">
                <table class="w-full text-left text-body-sm">
                    <caption class="sr-only">Daftar booking</caption>
                    <thead class="bg-surface-muted text-label-sm text-ink-body">
                        <tr>
                            <th scope="col" class="px-3 py-3 font-medium">Booking</th>
                            <th scope="col" class="px-3 py-3 font-medium">Penyewa</th>
                            <th scope="col" class="px-3 py-3 font-medium">Lapangan</th>
                            <th scope="col" class="px-3 py-3 text-right font-medium">Total</th>
                            <th scope="col" class="px-3 py-3 font-medium">Status</th>
                            <th scope="col" class="px-2 py-3"><span class="sr-only">Lihat</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @foreach ($rows as $r)
                            @php
                                $url        = route('admin.transaksi.show', $r->id);
                                $flags      = $monitor->flagsFor($r);
                                $flagLabels = $monitor->flagLabels($r);
                                $made       = \Carbon\CarbonImmutable::parse($r->created_at)->locale('id');
                            @endphp
                            <tr wire:key="row-{{ $r->id }}" class="align-top">
                                <td class="px-3 py-3.5">
                                    <a href="{{ $url }}" class="whitespace-nowrap font-semibold tabular-nums text-ink hover:underline">{{ TM::code($r->id, $r->kode_booking) }}</a>
                                    <p class="whitespace-nowrap text-caption text-ink-muted">{{ $made->translatedFormat($made->isCurrentYear() ? 'j M, H.i' : 'j M Y, H.i') }}</p>
                                </td>
                                <td class="px-3 py-3.5">
                                    <p class="max-w-28 truncate text-ink" title="{{ $r->customer_name }}">{{ $r->customer_name }}</p>
                                </td>
                                <td class="px-3 py-3.5 text-ink-body">
                                    @if ($r->item_count > 0)
                                        <p class="max-w-32 truncate" title="{{ $r->nama_lapangan }}, {{ $r->nama_venue }}">{{ $r->nama_lapangan }}</p>
                                        @if ($r->item_count > 1)
                                            <p class="text-caption text-ink-muted">{{ $r->item_count }} slot</p>
                                        @endif
                                    @else
                                        <span class="text-ink-muted">Tanpa rincian</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-3 py-3.5 text-right">
                                    <p class="tabular-nums text-ink">{{ $rp($r->total_tagihan) }}</p>
                                    @if ($r->pay_succes > 0 && $methodLabel($r->method))
                                        <p class="text-caption text-ink-muted">{{ $methodLabel($r->method) }}</p>
                                    @endif
                                </td>
                                <td class="px-3 py-3.5">
                                    <x-admin.status-badge :status="$r->status" :label="TM::BOOKING_STATUS[$r->status] ?? null" />

                                    @if ($r->status === 'pending')
                                        @php
                                            $deadline = $r->expires_at
                                                ? \Carbon\CarbonImmutable::parse($r->expires_at)
                                                : \Carbon\CarbonImmutable::parse($r->created_at)->addMinutes((int) config('booking.payment_window_minutes', 15));
                                        @endphp
                                        <p class="mt-1 text-caption tabular-nums text-ink-muted">Batas bayar {{ $deadline->format('H.i') }}</p>
                                    @endif
                                        
                                    @if ($r->refund_open > 0)
                                        <p class="mt-1 text-caption text-ink-muted">Refund diproses</p>
                                    @elseif ($r->refund_done > 0)
                                        <p class="mt-1 text-caption text-ink-muted">Refund selesai</p>
                                    @endif
                                        
                                    @if ($flagLabels)
                                        <p class="mt-1.5 flex max-w-52 items-start gap-1 text-caption text-warning-800 [&>svg]:mt-0.5 [&>svg]:size-3.5"
                                        title="{{ implode(' ', $flags) }}">
                                            <x-ui.icon name="alert-circle" />
                                            <span>
                                                {{ $flagLabels[0] }}
                                                @if (count($flagLabels) > 1)
                                                    <span class="text-ink-muted">(+{{ count($flagLabels) - 1 }})</span>
                                                @endif
                                            </span>
                                        </p>
                                    @endif
                                </td>
                                <td class="px-2 py-3.5 text-right">
                                    <a href="{{ $url }}" aria-label="Lihat {{ TM::code($r->id, $r->kode_booking) }}"
                                    class="inline-grid size-9 place-items-center rounded-md border border-line-strong text-ink-body hover:bg-surface-muted focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand">
                                        <x-ui.icon name="chevron-right" />
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Daftar kartu (di bawah xl) --}}
            <ul class="space-y-3 xl:hidden">
                @foreach ($rows as $r)
                    @php
                        $url        = route('admin.transaksi.show', $r->id);
                        $flags      = $monitor->flagsFor($r);
                        $flagLabels = $monitor->flagLabels($r);
                    @endphp
                    <li wire:key="card-{{ $r->id }}"
                        @class([
                            'relative rounded-md border border-l-4 border-line bg-surface p-4 transition-colors active:bg-surface-muted motion-reduce:transition-none',
                            'border-l-warning-500' => (bool) $flags,
                            'border-l-line-strong' => ! $flags,
                        ])>
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <a href="{{ $url }}" class="block text-label-lg font-semibold tabular-nums text-ink after:absolute after:inset-0 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand">
                                    <span class="sr-only">Lihat </span>{{ TM::code($r->id, $r->kode_booking) }}
                                </a>
                                <p class="text-caption text-ink-muted">{{ \Carbon\CarbonImmutable::parse($r->created_at)->locale('id')->translatedFormat('j M Y, H.i') }}</p>
                            </div>
                            <div class="shrink-0 text-right">
                                <p class="text-label-lg tabular-nums text-ink">{{ $rp($r->total_tagihan) }}</p>
                                @if ($r->pay_succes > 0 && $methodLabel($r->method))
                                    <p class="text-caption text-ink-muted">{{ $methodLabel($r->method) }}</p>
                                @endif
                            </div>
                        </div>

                        <p class="mt-2 truncate text-body-sm text-ink">{{ $r->customer_name }}</p>
                        <p class="truncate text-caption text-ink-muted">
                            {{ $r->item_count > 0 ? $r->nama_lapangan . ', ' . $r->nama_venue . ($r->item_count > 1 ? ', ' . $r->item_count . ' slot' : '') : 'Tanpa rincian slot' }}
                        </p>

                        <div class="mt-3 flex flex-wrap items-center gap-2">
                            <x-admin.status-badge :status="$r->status" :label="TM::BOOKING_STATUS[$r->status] ?? null" />
                            @if ($r->refund_open > 0)
                                <span class="text-caption text-ink-muted">Refund diproses</span>
                            @elseif ($r->refund_done > 0)
                                <span class="text-caption text-ink-muted">Refund selesai</span>
                            @endif
                        </div>

                        @if ($flagLabels)
                            <p class="mt-2 flex items-start gap-1 text-caption text-warning-800 [&>svg]:mt-0.5 [&>svg]:size-3.5">
                                <x-ui.icon name="alert-circle" />
                                <span>{{ implode(', ', $flagLabels) }}</span>
                            </p>
                        @endif
                    </li>
                @endforeach
            </ul>

            <x-ui.pagination :rows="$rows" :pages="$this->pageWindow()" :per-page-options="$perPageOptions" noun="booking" />
        @endif
    </div>
</div>