@php
    use App\Services\Admin\DashboardStats;
    use App\Services\Admin\Reports\ReportService as RS;
    use App\Services\Admin\TransactionMonitor as TM;

    $rep   = $this->report;
    $f     = $this->filters;
    $max   = config('reports.max_rows');
    $cols  = collect($rep->columns)->where('preview', true)->values();
    $methodLabels = DashboardStats::PAYMENT_LABELS;

    $fmt = fn (string $kind, $v) => match ($kind) {
        'money'    => number_format((float) $v, 0, ',', '.'),
        'int'      => number_format((int) $v, 0, ',', '.'),
        'date'     => $v ? $v->locale('id')->translatedFormat('j M Y') : '-',
        'datetime' => $v ? $v->locale('id')->translatedFormat('j M Y, H.i') : '-',
        'bool'     => $v ? 'Ya' : 'Tidak',
        default    => (string) ($v ?? '-'),
    };

    $noData   = $rep->total === 0 || $rep->rangeError;
    $xlsxOver = $rep->total > $max['xlsx'];
    $pdfOver  = $rep->total > $max['pdf'];
    $tiles    = collect($rep->summary)->take(4)->map(fn ($s) => [
        'label' => $s[0], 'value' => $fmt($s[2], $s[1]), 'compact' => $s[2] === 'money',
    ])->all();
@endphp

<div class="space-y-5">
    {{-- Jenis laporan --}}
    <div role="radiogroup" aria-label="Jenis laporan" class="grid gap-3 md:grid-cols-3">
        @foreach ($types as $key => $t)
            <button type="button" role="radio" aria-checked="{{ $type === $key ? 'true' : 'false' }}"
                    wire:click="setType('{{ $key }}')" wire:key="type-{{ $key }}"
                    @class([
                        'rounded-md border p-4 text-left transition-colors motion-reduce:transition-none',
                        'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand',
                        'border-brand bg-brand-soft' => $type === $key,
                        'border-line bg-surface hover:bg-surface-muted' => $type !== $key,
                    ])>
                <span class="flex items-center justify-between gap-2">
                    <span class="text-label-lg font-semibold text-ink">{{ $t['label'] }}</span>
                    @if ($type === $key)<x-ui.icon name="check-circle" class="text-primary-700" />@endif
                </span>
                <span class="mt-1 block text-caption text-ink-body">{{ $t['desc'] }}</span>
            </button>
        @endforeach
    </div>

    {{-- Filter --}}
    <x-ui.card>
        <h2 class="text-h6 text-ink">Filter</h2>
        <div class="mt-3 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <x-ui.select compact name="period" wire:model.live="period"
                        :label="$type === 'transaksi' ? 'Periode dibuat' : ($type === 'mitra' ? 'Periode diajukan' : 'Periode daftar')"
                        :options="TM::PERIODS" />

            @if ($type === 'transaksi')
                <x-ui.select compact name="status" wire:model.live="status" label="Status booking" :options="$svc->statusOptions('transaksi')" />
                <x-ui.select compact name="method" wire:model.live="method" label="Metode pembayaran"
                            :options="['' => 'Semua'] + collect($this->methods)->mapWithKeys(fn ($m) => [$m => $methodLabels[$m] ?? \Illuminate\Support\Str::headline($m)])->all()" />
            @elseif ($type === 'mitra')
                <x-ui.select compact name="status" wire:model.live="status" label="Status verifikasi" :options="$svc->statusOptions('mitra')" />
            @else
                <x-ui.select compact name="role" wire:model.live="role" label="Peran" :options="['' => 'Semua peran'] + RS::ROLES" />
                <x-ui.select compact name="account" wire:model.live="account" label="Status akun" :options="['' => 'Semua status'] + RS::ACCOUNT_STATUS" />
            @endif
        </div>

        @if ($period === 'kustom')
            <div class="mt-3 grid gap-3 sm:grid-cols-2 lg:max-w-md">
                <x-ui.input name="from" type="date" wire:model.live="from" label="Dari tanggal" />
                <x-ui.input name="to" type="date" wire:model.live="to" label="Sampai tanggal" />
            </div>
        @endif

        @if ($rep->rangeError)
            <x-ui.alert type="error" class="mt-3">{{ $rep->rangeError }}</x-ui.alert>
        @endif
    </x-ui.card>

    {{-- Pratinjau --}}
    <div wire:loading.class="opacity-60" wire:target="setType,period,from,to,status,method,role,account"
        class="space-y-4 transition-opacity motion-reduce:transition-none">
        @if (! $rep->rangeError)
            <x-admin.summary-strip :items="$tiles" />
        @endif

        <x-ui.card flush class="overflow-hidden">
            <div class="flex items-center justify-between gap-3 px-4 pb-2 pt-4 lg:px-5">
                <h2 class="text-h6 text-ink">Pratinjau</h2>
                <p class="text-caption text-ink-muted">{{ $rep->period }}</p>
            </div>

            @if ($noData)
                <x-ui.empty-state icon="search" title="Tidak ada data untuk laporan ini"
                                description="Ubah periode atau filter. Unduhan baru tersedia bila ada data." />
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-body-sm">
                        <caption class="sr-only">{{ $rep->rows->count() }} baris pertama dari {{ $rep->total }}</caption>
                        <thead class="bg-surface-muted text-label-sm text-ink-body">
                            <tr>
                                @foreach ($cols as $c)
                                    <th scope="col" class="whitespace-nowrap px-4 py-2.5 font-medium {{ in_array($c['kind'], ['money', 'int']) ? 'text-right' : '' }}">{{ $c['label'] }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line">
                            @foreach ($rep->rows as $i => $row)
                                <tr wire:key="prev-{{ $type }}-{{ $i }}">
                                    @foreach ($cols as $c)
                                        <td class="whitespace-nowrap px-4 py-2.5 text-ink-body {{ in_array($c['kind'], ['money', 'int']) ? 'text-right tabular-nums' : '' }}">
                                            <span class="block max-w-52 truncate">{{ $fmt($c['kind'], $row[$c['key']] ?? null) }}</span>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p class="border-t border-line px-4 py-3 text-caption text-ink-muted lg:px-5">
                    Menampilkan {{ $rep->rows->count() }} baris pertama dari {{ number_format($rep->total, 0, ',', '.') }}.
                    Berkas yang diunduh memuat semua baris dan kolom lengkap.
                </p>
            @endif
        </x-ui.card>

        {{-- Unduh --}}
        <x-ui.card>
            <form method="POST" action="{{ route('admin.laporan.download') }}" class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                @csrf
                <input type="hidden" name="type" value="{{ $type }}">
                <input type="hidden" name="period" value="{{ $f['period'] }}">
                <input type="hidden" name="from" value="{{ $f['from'] }}">
                <input type="hidden" name="to" value="{{ $f['to'] }}">
                <input type="hidden" name="status" value="{{ $f['status'] }}">
                <input type="hidden" name="method" value="{{ $f['method'] }}">
                <input type="hidden" name="role" value="{{ $f['role'] }}">
                <input type="hidden" name="account" value="{{ $f['account'] }}">

                <div class="text-body-sm text-ink-body">
                    @if ($noData)
                        Pilih filter yang menghasilkan data untuk mengunduh.
                    @elseif ($xlsxOver)
                        <span class="text-danger-700">Laporan memuat {{ number_format($rep->total, 0, ',', '.') }} baris, melebihi batas {{ number_format($max['xlsx'], 0, ',', '.') }}. Persempit periode atau filter.</span>
                    @elseif ($pdfOver)
                        {{ number_format($rep->total, 0, ',', '.') }} baris. PDF dibatasi {{ number_format($max['pdf'], 0, ',', '.') }} baris, gunakan Excel atau persempit filter.
                    @else
                        {{ number_format($rep->total, 0, ',', '.') }} baris siap diunduh. Setiap unduhan dicatat di log audit.
                    @endif
                </div>

                <div class="flex flex-col gap-2 sm:flex-row">
                    <x-ui.button type="submit" name="format" value="xlsx" icon="download" :disabled="$noData || $xlsxOver">Unduh Excel</x-ui.button>
                    <x-ui.button type="submit" name="format" value="pdf" variant="outline" icon="download" :disabled="$noData || $pdfOver">Unduh PDF</x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
</div>