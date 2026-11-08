@php
    $counts  = $this->counts;
    $rows    = $this->rows;
    $s       = $this->summary;
    $hasTerm = trim($search) !== '';
    $isVenue = $jenis === 'venue';
    $noun    = $isVenue ? 'venue' : 'lapangan';
    $initialsOf = fn ($name) => \Illuminate\Support\Str::of($name ?: '?')->explode(' ')->filter()->take(2)
        ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
@endphp

<div class="space-y-4">
    <dl class="grid grid-cols-2 gap-px overflow-hidden rounded-md border border-line bg-line md:grid-cols-4">
        <div class="bg-surface p-4">
            <dt class="text-caption text-ink-muted">Menunggu moderasi</dt>
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

    {{-- Jenis listing --}}
    <div role="group" aria-label="Jenis listing"
        class="grid w-full grid-cols-2 gap-1 rounded-lg bg-surface-sunken p-1 md:inline-grid md:w-auto">
        @foreach ($types as $key => $t)
            <button type="button" wire:click="setJenis('{{ $key }}')" wire:key="jenis-{{ $key }}"
                    aria-pressed="{{ $jenis === $key ? 'true' : 'false' }}"
                    @class([
                        'flex min-h-11 items-center justify-center gap-2 rounded-md px-5 text-label-lg transition-colors motion-reduce:transition-none',
                        'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand',
                        'bg-primary-500 font-semibold text-white' => $jenis === $key,
                        'text-ink-body hover:bg-surface' => $jenis !== $key,
                    ])>
                {{ $t['label'] }}
                <span @class([
                    'rounded-full px-2 py-0.5 text-caption tabular-nums',
                    'bg-primary-700 text-white' => $jenis === $key,
                    'bg-surface text-ink-body' => $jenis !== $key,
                ])>{{ $counts[$key]['pending_review'] ?? 0 }}<span class="sr-only"> menunggu</span></span>
            </button>
        @endforeach
    </div>

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
                    <span class="ml-1 tabular-nums">{{ $counts[$jenis][$key] ?? 0 }}</span>
                </button>
            @endforeach
        </nav>

        <div class="flex gap-2">
            <div class="relative flex-1 md:w-72 md:flex-none">
                <label for="cari-listing" class="sr-only">Cari {{ $noun }}</label>
                <x-ui.icon name="search" class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-ink-muted" />
                <input id="cari-listing" type="search" maxlength="100" autocomplete="off"
                    wire:model.live.debounce.300ms="search"
                    placeholder="{{ $isVenue ? 'Cari venue, mitra, atau pemilik' : 'Cari lapangan, venue, atau mitra' }}"
                    class="min-h-11 w-full rounded-md border border-line-strong bg-surface pl-10 pr-3 text-body-sm text-ink placeholder:text-ink-muted focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-brand lg:min-h-10">
            </div>
            <x-ui.button variant="neutral" type="button" wire:click="toggleSort" class="shrink-0">
                {{ $direction === 'asc' ? 'Terlama dulu' : 'Terbaru dulu' }}
            </x-ui.button>
        </div>
    </div>

    <div wire:loading.class="opacity-60"
        wire:target="search,setJenis,setStatus,toggleSort,clearSearch,nextPage,previousPage,gotoPage,perPage"
        class="space-y-4 transition-opacity motion-reduce:transition-none">
        <p class="sr-only" role="status">{{ $rows->total() }} {{ $noun }} ditemukan</p>

        @if ($rows->isEmpty())
            <x-ui.card>
                @if ($hasTerm)
                    <x-ui.empty-state icon="search" title="Tidak ada hasil untuk “{{ trim($search) }}”"
                                    description="Periksa ejaan, atau cari dengan nama lain.">
                        <x-ui.button variant="outline" type="button" wire:click="clearSearch">Hapus pencarian</x-ui.button>
                    </x-ui.empty-state>
                @elseif ($status === 'pending_review')
                    <x-ui.empty-state variant="done" title="Tidak ada {{ $noun }} yang menunggu"
                                    description="Pengajuan baru dari mitra akan muncul di sini." />
                @else
                    <x-ui.empty-state title="Belum ada {{ $noun }} yang {{ strtolower($statuses[$status]) }}"
                                    description="Keputusan yang sudah dibuat akan tercatat di sini." />
                @endif
            </x-ui.card>
        @else
            {{-- Tabel (md ke atas) --}}
            <div class="hidden overflow-hidden rounded-md border border-line bg-surface md:block">
                <table class="w-full text-left text-body-sm">
                    <caption class="sr-only">Daftar {{ $noun }} berstatus {{ strtolower($statuses[$status]) }}</caption>
                    <thead class="bg-surface-muted text-label-sm text-ink-body">
                        <tr>
                            <th scope="col" class="px-4 py-3 font-medium">{{ $isVenue ? 'Venue dan mitra' : 'Lapangan dan venue' }}</th>
                            <th scope="col" class="px-4 py-3 font-medium">{{ $isVenue ? 'Kota' : 'Olahraga dan harga' }}</th>
                            <th scope="col" class="px-4 py-3 font-medium">{{ $isVenue ? 'Lapangan' : 'Foto' }}</th>
                            <th scope="col" class="px-4 py-3 font-medium">{{ $status === 'pending_review' ? 'Diajukan' : 'Keputusan' }}</th>
                            <th scope="col" class="px-4 py-3"><span class="sr-only">Aksi</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @foreach ($rows as $r)
                            @php
                                $title = $isVenue ? ($r->nama_venue ?: 'Venue tanpa nama') : $r->nama_lapangan;
                                $url   = route('admin.moderasi.show', ['jenis' => $jenis, 'id' => $r->id]);
                                $blockedByVenue = ! $isVenue && $status === 'pending_review' && $r->venue_status !== 'aktif';
                            @endphp
                            <tr wire:key="row-{{ $jenis }}-{{ $r->id }}" class="align-top">
                                <td class="px-4 py-3.5">
                                    <a href="{{ $url }}" class="font-semibold text-ink hover:underline">{{ $title }}</a>
                                    <p class="text-caption text-ink-muted">
                                        {{ $isVenue ? ($r->nama_usaha ?: 'Mitra tanpa nama usaha') : ($r->nama_venue ?: 'Venue tanpa nama') }}
                                    </p>
                                    @if ($blockedByVenue)
                                        <x-ui.badge tone="warning" icon="alert-circle" class="mt-1">Venue belum aktif</x-ui.badge>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-ink-body">
                                    @if ($isVenue)
                                        {{ $r->nama_kota }}
                                    @else
                                        {{ $r->nama_olahraga }}
                                        <p class="text-caption tabular-nums text-ink-muted">Rp {{ number_format((float) $r->harga_perjam, 0, ',', '.') }} per jam</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 tabular-nums text-ink-body">
                                    {{ $isVenue ? $r->field_count . ' lapangan' : $r->photo_count . ' foto' }}
                                </td>
                                <td class="px-4 py-3.5">
                                    <x-admin.review-timing :status="$status" :waiting="$r->waiting_since" :reviewed-at="$r->reviewed_at"
                                                        :reviewer="$r->reviewer_name" :reason="$r->alasan_penolakan" :sla="$sla" />
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    <x-ui.button variant="outline" size="sm" :href="$url" aria-label="Tinjau {{ $title }}">Tinjau</x-ui.button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Daftar kartu (di bawah md) --}}
            <ul class="space-y-3 md:hidden">
                @foreach ($rows as $r)
                    @php
                        $title = $isVenue ? ($r->nama_venue ?: 'Venue tanpa nama') : $r->nama_lapangan;
                        $url   = route('admin.moderasi.show', ['jenis' => $jenis, 'id' => $r->id]);
                        $days  = $r->waiting_since ? (int) floor(\Carbon\CarbonImmutable::parse($r->waiting_since)->diffInDays(now())) : null;
                        $late  = $status === 'pending_review' && $days !== null && $days >= $sla;
                        $blockedByVenue = ! $isVenue && $status === 'pending_review' && $r->venue_status !== 'aktif';
                    @endphp
                    <li wire:key="card-{{ $jenis }}-{{ $r->id }}"
                        @class([
                            'relative rounded-md border border-l-4 border-line bg-surface p-4 transition-colors active:bg-surface-muted motion-reduce:transition-none',
                            'border-l-danger-500' => $late,
                            'border-l-line-strong' => ! $late,
                        ])>
                        <div class="flex items-center gap-3">
                            <span class="grid size-11 shrink-0 place-items-center rounded-full bg-primary-100 text-label-lg text-primary-700" aria-hidden="true">{{ $initialsOf($title) }}</span>
                            <div class="min-w-0 flex-1">
                                <a href="{{ $url }}" class="block truncate text-label-lg font-semibold text-ink after:absolute after:inset-0 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand">
                                    <span class="sr-only">Tinjau </span>{{ $title }}
                                </a>
                                <p class="truncate text-caption text-ink-muted">
                                    {{ $isVenue ? ($r->nama_usaha ?: 'Mitra tanpa nama usaha') : ($r->nama_venue ?: 'Venue tanpa nama') }}
                                </p>
                            </div>
                            <x-ui.icon name="chevron-right" class="text-ink-muted" />
                        </div>

                        <p class="mt-3 text-caption text-ink-body">
                            {{ $isVenue ? $r->nama_kota . ', ' . $r->field_count . ' lapangan' : $r->nama_olahraga . ', Rp ' . number_format((float) $r->harga_perjam, 0, ',', '.') . ' per jam, ' . $r->photo_count . ' foto' }}
                        </p>
                        @if ($blockedByVenue)
                            <x-ui.badge tone="warning" icon="alert-circle" class="mt-2">Venue belum aktif</x-ui.badge>
                        @endif
                        <div class="mt-3">
                            <x-admin.review-timing :status="$status" :waiting="$r->waiting_since" :reviewed-at="$r->reviewed_at"
                                                :reviewer="$r->reviewer_name" :reason="$r->alasan_penolakan" :sla="$sla" />
                        </div>
                    </li>
                @endforeach
            </ul>

            <x-ui.pagination :rows="$rows" :pages="$this->pageWindow()" :per-page-options="$perPageOptions" :noun="$noun" />
        @endif
    </div>
</div>