@php
    $counts  = $this->counts;
    $rows    = $this->rows;
    $s       = $this->summary;
    $hasTerm = trim($search) !== '';
@endphp

<div class="space-y-4">
    <dl class="grid grid-cols-2 gap-px overflow-hidden rounded-md border border-line bg-line md:grid-cols-4">
        <div class="bg-surface p-4">
            <dt class="text-caption text-ink-muted">Menunggu verifikasi</dt>
            <dd class="mt-1 text-metric tabular-nums text-ink">{{ $s['pending'] }}</dd>
        </div>
        <div class="bg-surface p-4">
            <dt class="text-caption text-ink-muted">Melewati batas {{ $sla }} hari</dt>
            <dd class="mt-1 flex items-center gap-2 text-metric tabular-nums {{ $s['late'] ? 'text-danger-700' : 'text-ink' }}">
                {{ $s['late'] }}
                @if ($s['late'])<x-ui.icon name="alert-circle" /><span class="sr-only">perlu segera ditinjau</span>@endif
            </dd>
        </div>
        <div class="bg-surface p-4">
            <dt class="text-caption text-ink-muted">Terlama menunggu</dt>
            <dd class="mt-1 text-metric tabular-nums text-ink">{{ $s['oldest'] === null ? '—' : $s['oldest'] . ' hari' }}</dd>
        </div>
        <div class="bg-surface p-4">
            <dt class="text-caption text-ink-muted">Diputuskan 7 hari terakhir</dt>
            <dd class="mt-1 text-metric tabular-nums text-ink">{{ $s['decided'] }}</dd>
        </div>
    </dl>

    {{-- Tab status dan pencarian --}}
    <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
        <nav aria-label="Filter status" class="flex justify-center gap-5 border-b border-line-strong md:flex-1 md:justify-start">
            @foreach ($statuses as $key => $label)
                <button type="button" wire:click="setStatus('{{ $key }}')" wire:key="tab-{{ $key }}"
                        @if ($status === $key) aria-current="true" @endif
                        @class([
                            '-mb-px min-h-11 whitespace-nowrap border-b-2 text-label-lg transition-colors motion-reduce:transition-none',
                            'border-brand font-semibold text-brand-link' => $status === $key,
                            'border-transparent text-ink-body hover:text-ink' => $status !== $key,
                        ])>
                    {{ $label }}
                    <span class="ml-1 tabular-nums">{{ $counts[$key] ?? 0 }}</span>
                </button>
            @endforeach
        </nav>

        <div class="flex gap-2">
            <div class="relative flex-1 md:w-72 md:flex-none">
                <label for="cari-mitra" class="sr-only">Cari nama usaha, pemilik, atau email</label>
                <x-ui.icon name="search" class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-ink-muted" />
                <input id="cari-mitra" type="search" maxlength="100" autocomplete="off"
                    wire:model.live.debounce.300ms="search" placeholder="Cari usaha, pemilik, atau email"
                    class="min-h-11 w-full rounded-md border border-line-strong bg-surface pl-10 pr-3 text-body-sm text-ink placeholder:text-ink-muted focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-brand lg:min-h-10">
            </div>
            <x-ui.button variant="neutral" type="button" wire:click="toggleSort" class="shrink-0">
                {{ $direction === 'asc' ? 'Terlama dulu' : 'Terbaru dulu' }}
            </x-ui.button>
        </div>
    </div>

    {{-- Hasil: meredup selagi memuat, dan diumumkan ke pembaca layar --}}
    <div wire:loading.class="opacity-60" wire:target="search,setStatus,toggleSort,clearSearch,nextPage,previousPage,gotoPage,perPage"
        class="space-y-4 transition-opacity motion-reduce:transition-none">
        <p class="sr-only" role="status">{{ $rows->total() }} pendaftaran ditemukan</p>

        @if ($rows->isEmpty())
            <x-ui.card>
                @if ($hasTerm)
                    <x-ui.empty-state icon="search" title="Tidak ada hasil untuk “{{ trim($search) }}”"
                                    description="Periksa ejaan, atau cari dengan nama usaha, nama pemilik, atau email lain.">
                        <x-ui.button variant="outline" type="button" wire:click="clearSearch">Hapus pencarian</x-ui.button>
                    </x-ui.empty-state>
                @elseif ($status === 'pending')
                    <x-ui.empty-state title="Tidak ada pendaftaran yang menunggu"
                                    description="Pendaftaran baru dari pemilik lapangan akan muncul di sini." />
                @else
                    <x-ui.empty-state title="Belum ada pendaftaran yang {{ strtolower($statuses[$status]) }}"
                                    description="Keputusan yang sudah dibuat akan tercatat di sini." />
                @endif
            </x-ui.card>
        @else
            {{-- Tabel (md ke atas) --}}
            <div class="hidden overflow-hidden rounded-md border border-line bg-surface md:block">
                <table class="w-full text-left text-body-sm">
                    <caption class="sr-only">Daftar pendaftaran mitra berstatus {{ strtolower($statuses[$status]) }}</caption>
                    <thead class="bg-surface-muted text-label-sm text-ink-body">
                        <tr>
                            <th scope="col" class="px-4 py-3 font-medium">Usaha dan pemilik</th>
                            <th scope="col" class="px-4 py-3 font-medium">Kontak</th>
                            <th scope="col" class="px-4 py-3 font-medium">Kota dan jenis</th>
                            <th scope="col" class="px-4 py-3 font-medium">{{ $status === 'pending' ? 'Diajukan' : 'Keputusan' }}</th>
                            <th scope="col" class="px-4 py-3 font-medium">Berkas</th>
                            <th scope="col" class="px-4 py-3"><span class="sr-only">Aksi</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @foreach ($rows as $m)
                            <tr wire:key="row-{{ $m->id }}" class="align-top">
                                <td class="px-4 py-3.5">
                                    <a href="{{ route('admin.verifikasi.show', $m) }}" class="font-semibold text-ink hover:underline">
                                        {{ $m->nama_usaha ?: 'Tanpa nama usaha' }}
                                    </a>
                                    <p class="text-caption text-ink-muted">{{ $m->user?->name ?? '—' }}</p>
                                </td>
                                <td class="px-4 py-3.5 text-ink-body"><span class="break-all">{{ $m->user?->email ?? '—' }}</span></td>
                                <td class="px-4 py-3.5 text-ink-body">
                                    {{ $m->kota_operasional ?: '—' }}
                                    <p class="text-caption text-ink-muted">{{ $m->bentuk_badan_usaha ?: '—' }}</p>
                                </td>
                                <td class="px-4 py-3.5"><x-admin.verification-timing :mitra="$m" :sla="$sla" /></td>
                                <td class="px-4 py-3.5 tabular-nums text-ink-body">{{ $m->uploadedDocumentsCount() }} dari 4</td>
                                <td class="px-4 py-3.5 text-right">
                                    <x-ui.button variant="outline" size="sm" :href="route('admin.verifikasi.show', $m)"
                                                aria-label="Tinjau {{ $m->nama_usaha ?: 'pendaftaran #' . $m->id }}">Tinjau</x-ui.button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Daftar kartu (di bawah md) --}}
            <ul class="space-y-3 md:hidden">
                @foreach ($rows as $m)
                    @php
                        $initials = \Illuminate\Support\Str::of($m->nama_usaha ?: '?')->explode(' ')->filter()->take(2)
                            ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
                        $days = $m->daysWaiting();
                        $late = $m->isPending() && $days !== null && $days >= $sla;
                        $docs = $m->uploadedDocumentsCount();
                    @endphp
            
                    <li wire:key="card-{{ $m->id }}"
                        @class([
                            'relative rounded-md border border-l-4 border-line bg-surface p-4 transition-colors active:bg-surface-muted motion-reduce:transition-none',
                            'border-l-danger-500' => $late,
                            'border-l-line-strong' => ! $late,
                        ])>
                        <div class="flex items-center gap-3">
                            <span class="grid size-11 shrink-0 place-items-center rounded-full bg-primary-100 text-label-lg text-primary-700" aria-hidden="true">{{ $initials }}</span>
                            <div class="min-w-0 flex-1">
                                {{-- Tautan membentang ke seluruh kartu --}}
                                <a href="{{ route('admin.verifikasi.show', $m) }}"
                                class="block truncate text-label-lg font-semibold text-ink after:absolute after:inset-0 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand">
                                    <span class="sr-only">Tinjau </span>{{ $m->nama_usaha ?: 'Tanpa nama usaha' }}
                                </a>
                                <p class="truncate text-caption text-ink-muted">{{ $m->user?->name ?? '—' }}</p>
                            </div>
                            <x-ui.icon name="chevron-right" class="text-ink-muted" />
                        </div>
            
                        <p class="mt-3 inline-flex items-center gap-1.5 text-caption text-ink-body [&>svg]:size-4">
                            <x-ui.icon name="map-pin" />
                            {{ $m->kota_operasional ?: '—' }}, {{ $m->bentuk_badan_usaha ?: '—' }}
                        </p>
            
                        <div class="mt-3 flex items-end justify-between gap-3">
                            <x-admin.verification-timing :mitra="$m" :sla="$sla" />
            
                            <div class="shrink-0 text-right">
                                <div class="flex justify-end gap-0.5" aria-hidden="true">
                                    @for ($i = 1; $i <= 4; $i++)
                                        <span @class(['h-1.5 w-4 rounded-full', 'bg-primary-500' => $i <= $docs, 'bg-line-strong' => $i > $docs])></span>
                                    @endfor
                                </div>
                                <p class="mt-1 text-caption tabular-nums text-ink-muted">{{ $docs }} dari 4 berkas</p>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>

            {{-- Navigasi halaman --}}
            <div class="flex flex-col items-center gap-3 text-center text-caption text-ink-muted lg:flex-row lg:justify-between lg:text-left">
                <div class="flex flex-col items-center gap-2 sm:flex-row sm:gap-4">
                    <p>Menampilkan {{ $rows->firstItem() }} sampai {{ $rows->lastItem() }} dari {{ $rows->total() }} pendaftaran</p>
                    <label class="flex items-center gap-2">
                        Baris per halaman
                        <select wire:model.live="perPage"
                                class="min-h-9 rounded-md border border-line-strong bg-surface px-2 text-ink focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-brand">
                            @foreach ($perPageOptions as $n)
                                <option value="{{ $n }}">{{ $n }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>
            
                @if ($rows->hasPages())
                    <nav aria-label="Halaman" class="flex items-center justify-center gap-2">
                        <x-ui.button variant="neutral" type="button" wire:click="previousPage" :disabled="$rows->onFirstPage()">Sebelumnya</x-ui.button>
            
                        {{-- Ponsel: ringkas --}}
                        <span class="px-1 text-label-md tabular-nums text-ink md:hidden">Halaman {{ $rows->currentPage() }} dari {{ $rows->lastPage() }}</span>
            
                        {{-- Tablet ke atas: nomor halaman --}}
                        <div class="hidden items-center gap-1 md:flex">
                            @foreach ($this->pageWindow() as $p)
                                @if ($p === null)
                                    <span class="px-1" aria-hidden="true">…</span>
                                @else
                                    <button type="button" wire:click="gotoPage({{ $p }})" wire:key="page-{{ $p }}"
                                            aria-label="Halaman {{ $p }}"
                                            @if ($p === $rows->currentPage()) aria-current="page" @endif
                                            @class([
                                                'grid min-h-9 min-w-9 place-items-center rounded-md border px-2 text-label-md tabular-nums',
                                                'border-brand bg-brand-soft font-semibold text-primary-700' => $p === $rows->currentPage(),
                                                'border-line-strong text-ink-body hover:bg-surface-muted' => $p !== $rows->currentPage(),
                                            ])>{{ $p }}</button>
                                @endif
                            @endforeach
                        </div>
            
                        <x-ui.button variant="neutral" type="button" wire:click="nextPage" :disabled="! $rows->hasMorePages()">Berikutnya</x-ui.button>
                    </nav>
                @endif
            </div>
        @endif
    </div>
</div>