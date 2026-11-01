@php
    $counts = $this->counts;
    $rows   = $this->rows;
    $hasTerm = trim($search) !== '';
@endphp

<div class="space-y-4">
    {{-- Tab status dan pencarian --}}
    <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
        <nav aria-label="Filter status" class="flex gap-5 overflow-x-auto border-b border-line-strong md:flex-1">
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
    <div wire:loading.class="opacity-60" wire:target="search,setStatus,toggleSort,clearSearch,nextPage,previousPage"
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
                    <li wire:key="card-{{ $m->id }}" class="rounded-md border border-line bg-surface p-4">
                        <a href="{{ route('admin.verifikasi.show', $m) }}" class="text-label-lg font-semibold text-ink">
                            {{ $m->nama_usaha ?: 'Tanpa nama usaha' }}
                        </a>
                        <p class="text-caption text-ink-muted">{{ $m->user?->name ?? '—' }}</p>
                        <p class="mt-1 text-caption text-ink-muted">
                            {{ $m->kota_operasional ?: '—' }}, {{ $m->bentuk_badan_usaha ?: '—' }}
                        </p>
                        <div class="mt-2"><x-admin.verification-timing :mitra="$m" :sla="$sla" /></div>
                        <div class="mt-3 flex items-center justify-between gap-3">
                            <span class="text-caption tabular-nums text-ink-muted">{{ $m->uploadedDocumentsCount() }} dari 4 berkas</span>
                            <x-ui.button variant="outline" size="sm" :href="route('admin.verifikasi.show', $m)"
                                        aria-label="Tinjau {{ $m->nama_usaha ?: 'pendaftaran #' . $m->id }}">Tinjau</x-ui.button>
                        </div>
                    </li>
                @endforeach
            </ul>

            {{-- Navigasi halaman --}}
            <div class="flex flex-col gap-3 text-caption text-ink-muted sm:flex-row sm:items-center sm:justify-between">
                <p>Menampilkan {{ $rows->firstItem() }} sampai {{ $rows->lastItem() }} dari {{ $rows->total() }} pendaftaran</p>
                @if ($rows->hasPages())
                    <div class="flex gap-2">
                        <x-ui.button variant="neutral" size="sm" type="button" wire:click="previousPage" :disabled="$rows->onFirstPage()">Sebelumnya</x-ui.button>
                        <x-ui.button variant="neutral" size="sm" type="button" wire:click="nextPage" :disabled="! $rows->hasMorePages()">Berikutnya</x-ui.button>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>