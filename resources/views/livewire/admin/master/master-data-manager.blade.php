@php
    $svc      = app(\App\Services\Admin\MasterDataService::class);
    $rows     = $this->rows;
    $counts   = $this->counts;
    $totals   = $this->totals;
    $isFac    = $tipe === 'fasilitas';
    $isCity   = $tipe === 'kota';
    $isSport  = $tipe === 'olahraga';
    $hasIcon  = $config['icon'];
    $nameCol  = $config['name'];
    $hasTerm  = trim($search) !== '';
    $item     = $this->toggleItem;
    $statuses = ['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif'];
@endphp

{{-- Dialog dibuka dan ditutup lewat peristiwa dari server, supaya data sudah terisi sebelum dialog tampil --}}
<div class="space-y-4"
    x-data
    x-on:open-master-form.window="$dispatch('open-modal', 'master-form')"
    x-on:close-master-form.window="$dispatch('close-modal', 'master-form')"
    x-on:open-master-toggle.window="$dispatch('open-modal', 'master-toggle')"
    x-on:close-master-toggle.window="$dispatch('close-modal', 'master-toggle')">

    <x-ui.flash :messages="$notice ? ['success' => $notice] : []" />

    {{-- Jenis data --}}
    <div role="group" aria-label="Jenis data"
        class="grid w-full grid-cols-3 gap-1 rounded-lg bg-surface-sunken p-1 md:inline-grid md:w-auto">
        @foreach ($types as $key => $t)
            <button type="button" wire:click="setTipe('{{ $key }}')" wire:key="tipe-{{ $key }}"
                    aria-pressed="{{ $tipe === $key ? 'true' : 'false' }}"
                    @class([
                        'flex min-h-11 items-center justify-center gap-2 rounded-md px-3 text-label-lg transition-colors motion-reduce:transition-none md:px-5',
                        'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand',
                        'bg-primary-500 font-semibold text-white' => $tipe === $key,
                        'text-ink-body hover:bg-surface' => $tipe !== $key,
                    ])>
                {{ $key === 'olahraga' ? 'Olahraga' : $t['label'] }}
                <span @class([
                    'hidden rounded-full px-2 py-0.5 text-caption tabular-nums sm:inline',
                    'bg-primary-700 text-white' => $tipe === $key,
                    'bg-surface text-ink-body' => $tipe !== $key,
                ])>{{ $totals[$key] }}<span class="sr-only"> aktif</span></span>
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
                    {{ $label }}<span class="ml-1 tabular-nums">{{ $counts[$key] ?? 0 }}</span>
                </button>
            @endforeach
        </nav>

        <div class="flex gap-2">
            <div class="relative flex-1 md:w-64 md:flex-none">
                <label for="cari-master" class="sr-only">Cari {{ $config['noun'] }}</label>
                <x-ui.icon name="search" class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-ink-muted" />
                <input id="cari-master" type="search" maxlength="100" autocomplete="off"
                    wire:model.live.debounce.300ms="search" placeholder="Cari {{ $config['noun'] }}"
                    class="min-h-11 w-full rounded-md border border-line-strong bg-surface pl-10 pr-3 text-body-sm text-ink placeholder:text-ink-muted focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-brand lg:min-h-10">
            </div>
            <x-ui.button type="button" icon="plus" wire:click="startCreate" class="shrink-0">Tambah</x-ui.button>
        </div>
    </div>

    @if ($isFac)
        <div role="group" aria-label="Kategori fasilitas" class="flex flex-wrap gap-2">
            @foreach (['' => 'Semua kategori'] + $categories as $key => $label)
                <button type="button" wire:click="setKategori('{{ $key }}')" wire:key="kat-{{ $key ?: 'semua' }}"
                        aria-pressed="{{ $kategori === (string) $key ? 'true' : 'false' }}"
                        @class([
                            'min-h-9 rounded-full border px-3.5 text-label-md transition-colors motion-reduce:transition-none',
                            'border-brand bg-brand-soft font-semibold text-primary-700' => $kategori === (string) $key,
                            'border-line-strong text-ink-body hover:bg-surface-muted' => $kategori !== (string) $key,
                        ])>{{ $label }}</button>
            @endforeach
        </div>
    @endif

    <div wire:loading.class="opacity-60"
        wire:target="search,setTipe,setStatus,setKategori,clearSearch,nextPage,previousPage,gotoPage,perPage,activate,deactivate"
        class="space-y-4 transition-opacity motion-reduce:transition-none">
        <p class="sr-only" role="status">{{ $rows->total() }} {{ $config['noun'] }} ditemukan</p>

        @if ($rows->isEmpty())
            <x-ui.card>
                @if ($hasTerm)
                    <x-ui.empty-state icon="search" title="Tidak ada hasil untuk “{{ trim($search) }}”"
                                    description="Periksa ejaan, atau cari dengan kata lain.">
                        <x-ui.button variant="outline" type="button" wire:click="clearSearch">Hapus pencarian</x-ui.button>
                    </x-ui.empty-state>
                @elseif ($status === 'nonaktif')
                    <x-ui.empty-state variant="done" title="Tidak ada {{ $config['noun'] }} yang nonaktif"
                                    description="Semua {{ $config['noun'] }} sedang aktif dan bisa dipilih mitra." />
                @else
                    <x-ui.empty-state title="Belum ada {{ $config['noun'] }}"
                                    description="Tambahkan {{ $config['noun'] }} pertama supaya bisa dipilih mitra.">
                        <x-ui.button type="button" icon="plus" wire:click="startCreate">Tambah {{ $config['noun'] }}</x-ui.button>
                    </x-ui.empty-state>
                @endif
            </x-ui.card>
        @else
            {{-- Tabel (md ke atas) --}}
            <div class="hidden overflow-hidden rounded-md border border-line bg-surface md:block">
                <table class="w-full text-left text-body-sm">
                    <caption class="sr-only">Daftar {{ $config['noun'] }} berstatus {{ $status }}</caption>
                    <thead class="bg-surface-muted text-label-sm text-ink-body">
                        <tr>
                            <th scope="col" class="px-4 py-3 font-medium">{{ $config['label'] }}</th>
                            @if ($isSport)<th scope="col" class="px-4 py-3 font-medium">Slug</th>@endif
                            @if ($isCity)<th scope="col" class="px-4 py-3 font-medium">Provinsi</th>@endif
                            @if ($isFac)<th scope="col" class="px-4 py-3 font-medium">Kategori</th>@endif
                            <th scope="col" class="px-4 py-3 font-medium">Dipakai oleh</th>
                            <th scope="col" class="px-4 py-3"><span class="sr-only">Aksi</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @foreach ($rows as $r)
                            <tr wire:key="row-{{ $tipe }}-{{ $r->id }}" class="align-middle">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        @if ($hasIcon)
                                            <x-admin.master-icon :src="$svc->iconUrl($r->icon_url)" :name="$r->{$nameCol}" />
                                        @endif
                                        <span class="font-semibold text-ink">{{ $r->{$nameCol} }}</span>
                                    </div>
                                </td>
                                @if ($isSport)<td class="px-4 py-3 text-ink-body"><code class="text-caption">{{ $r->slug }}</code></td>@endif
                                @if ($isCity)<td class="px-4 py-3 text-ink-body">{{ $r->provinsi }}</td>@endif
                                @if ($isFac)<td class="px-4 py-3"><x-ui.badge>{{ $categories[$r->kategori] ?? $r->kategori }}</x-ui.badge></td>@endif
                                <td class="px-4 py-3 tabular-nums {{ $r->usage_count ? 'text-ink-body' : 'text-ink-muted' }}">
                                    {{ $r->usage_count ? $r->usage_count . ' ' . $config['usage_noun'] : 'Belum dipakai' }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-2">
                                        <x-ui.button variant="outline" size="sm" type="button" wire:click="startEdit({{ $r->id }})"
                                                    aria-label="Ubah {{ $r->{$nameCol} }}">Ubah</x-ui.button>
                                        @if ($status === 'aktif')
                                            <x-ui.button variant="danger-outline" size="sm" type="button" wire:click="confirmDeactivate({{ $r->id }})"
                                                        aria-label="Nonaktifkan {{ $r->{$nameCol} }}">Nonaktifkan</x-ui.button>
                                        @else
                                            <x-ui.button size="sm" type="button" wire:click="activate({{ $r->id }})"
                                                        aria-label="Aktifkan {{ $r->{$nameCol} }}">Aktifkan</x-ui.button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Daftar kartu (di bawah md) --}}
            <ul class="space-y-3 md:hidden">
                @foreach ($rows as $r)
                    <li wire:key="card-{{ $tipe }}-{{ $r->id }}" class="rounded-md border border-line bg-surface p-4">
                        <div class="flex items-center gap-3">
                            @if ($hasIcon)
                                <x-admin.master-icon :src="$svc->iconUrl($r->icon_url)" :name="$r->{$nameCol}" size="size-11" />
                            @endif
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-label-lg font-semibold text-ink">{{ $r->{$nameCol} }}</p>
                                <p class="truncate text-caption text-ink-muted">
                                    @if ($isCity){{ $r->provinsi }}@endif
                                    @if ($isSport){{ $r->slug }}@endif
                                    @if ($isFac){{ $categories[$r->kategori] ?? $r->kategori }}@endif
                                </p>
                            </div>
                        </div>
                        <p class="mt-3 text-caption tabular-nums {{ $r->usage_count ? 'text-ink-body' : 'text-ink-muted' }}">
                            {{ $r->usage_count ? 'Dipakai oleh ' . $r->usage_count . ' ' . $config['usage_noun'] : 'Belum dipakai' }}
                        </p>
                        <div class="mt-3 grid grid-cols-2 gap-2">
                            <x-ui.button variant="outline" type="button" wire:click="startEdit({{ $r->id }})">Ubah</x-ui.button>
                            @if ($status === 'aktif')
                                <x-ui.button variant="danger-outline" type="button" wire:click="confirmDeactivate({{ $r->id }})">Nonaktifkan</x-ui.button>
                            @else
                                <x-ui.button type="button" wire:click="activate({{ $r->id }})">Aktifkan</x-ui.button>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>

            <x-ui.pagination :rows="$rows" :pages="$this->pageWindow()" :per-page-options="$perPageOptions" :noun="$config['noun']" />
        @endif
    </div>

    {{-- Dialog tambah / ubah --}}
    <x-ui.modal name="master-form" wire:ignore.self
                :title="($editingId ? 'Ubah ' : 'Tambah ') . strtolower($config['label'])"
                :description="$editingId ? null : ($isSport ? 'Slug dibuat otomatis dari nama dan tidak berubah saat nama diedit.' : null)">
        <form id="master-form-el" wire:submit="save" class="mt-3 space-y-4" novalidate>
            <x-ui.input name="nama" wire:model="nama" :label="'Nama ' . $config['noun']" :required="true" :maxlength="$config['name_max']" />

            @if ($isCity)
                <x-ui.input name="provinsi" wire:model="provinsi" label="Provinsi" :required="true" maxlength="50" />
            @endif

            @if ($isFac)
                <x-ui.select name="formKategori" wire:model="formKategori" label="Kategori" :options="$categories" :required="true"
                            hint="Kategori bersifat tetap, jadi tidak bisa menambah kategori baru dari sini." />
            @endif

            @if ($hasIcon)
                <div>
                    <label for="icon" class="text-label-md text-ink-strong">
                        Ikon
                        @unless ($editingId)
                            <span class="text-danger-700" aria-hidden="true"> *</span>
                        @endunless
                    </label>
                    <p id="icon-hint" class="mt-0.5 text-caption text-ink-muted">PNG, JPG, atau WebP, maksimal 512 KB, 32 sampai 1024 piksel. Gambar persegi paling rapi.</p>

                    <div class="mt-2 flex items-center gap-3">
                        @if ($icon && method_exists($icon, 'temporaryUrl'))
                            <img src="{{ $icon->temporaryUrl() }}" alt="Pratinjau ikon baru" class="size-14 rounded-md border border-line bg-surface object-contain p-1">
                        @elseif ($currentIcon)
                            <x-admin.master-icon :src="$svc->iconUrl($currentIcon)" :name="$nama" size="size-14" />
                        @endif
                        <input id="icon" type="file" wire:model="icon" accept="image/png,image/jpeg,image/webp" aria-describedby="icon-hint"
                            class="min-w-0 flex-1 text-caption text-ink-body file:mr-3 file:min-h-10 file:rounded-md file:border file:border-line-strong file:bg-surface file:px-3 file:text-label-md file:text-ink-body hover:file:bg-surface-muted">
                    </div>
                    <p wire:loading wire:target="icon" class="mt-1 text-caption text-ink-muted">Mengunggah…</p>
                    @error('icon') <p class="mt-1 text-caption text-danger-700">{{ $message }}</p> @enderror
                </div>
            @endif
        </form>

        <x-slot:footer>
            <x-ui.button variant="neutral" type="button" x-on:click="$dispatch('close-modal', 'master-form')">Batal</x-ui.button>
            <x-ui.button type="submit" form="master-form-el" wire:loading.attr="disabled" wire:target="save,icon">Simpan</x-ui.button>
        </x-slot:footer>
    </x-ui.modal>

    {{-- Dialog nonaktifkan --}}
    <x-ui.modal name="master-toggle" wire:ignore.self
                :title="'Nonaktifkan ' . ($item ? $item->{$nameCol} : '') . '?'">
        <div class="mt-2 space-y-2 text-body-sm text-ink-body">
            @if ($item && $item->usage_count > 0)
                <p>Sedang dipakai oleh <strong class="text-ink">{{ $item->usage_count }} {{ $config['usage_noun'] }}</strong>.
                Data itu tetap utuh dan tidak berubah. {{ $config['label'] }} ini hanya tidak bisa dipilih untuk data baru.</p>
            @else
                <p>Belum dipakai oleh data apa pun, jadi tidak ada yang terpengaruh.</p>
            @endif
            <p>Kamu bisa mengaktifkannya kembali kapan saja dari tab Nonaktif.</p>
        </div>

        <x-slot:footer>
            <x-ui.button variant="neutral" type="button" x-on:click="$dispatch('close-modal', 'master-toggle')">Batal</x-ui.button>
            <x-ui.button variant="danger" type="button" wire:click="deactivate" wire:loading.attr="disabled" wire:target="deactivate">Nonaktifkan</x-ui.button>
        </x-slot:footer>
    </x-ui.modal>
</div>