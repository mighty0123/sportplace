@php
    use App\Services\Admin\UserAccountService as Accounts;

    $rows  = $this->rows;
    $s     = $this->summary;
    $rc    = $this->roleCounts;
    $ac    = $this->accountCounts;
    $hasTerm = trim($search) !== '';
    $filtered = $role !== '' || $account !== '' || $hasTerm || $sort !== 'terbaru';
    $num   = fn ($n) => number_format((int) $n, 0, ',', '.');
    $initialsOf = fn ($name) => \Illuminate\Support\Str::of($name ?: '?')->explode(' ')->filter()->take(2)
        ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
    $roleTabs = ['' => 'Semua'] + Accounts::ROLES;
    $accountChips = ['' => 'Semua status'] + Accounts::STATUS;
    $roleTotal = array_sum($rc);
    $accountTotal = array_sum($ac);
@endphp

<div class="space-y-4">
    <x-admin.summary-strip :items="[
        ['label' => 'Pengguna', 'value' => $num($s['total'])],
        ['label' => 'Mitra', 'value' => $num($s['mitra'])],
        ['label' => 'Penyewa', 'value' => $num($s['customer'])],
        ['label' => 'Akun bermasalah', 'value' => $num($s['flagged']), 'alert' => $s['flagged'] > 0, 'note' => 'Ditangguhkan atau diblokir'],
    ]" />

    <div class="flex flex-col gap-3 xl:flex-row xl:items-end xl:justify-between">
        <nav aria-label="Filter peran"
            class="flex gap-5 overflow-x-auto overflow-y-hidden border-b border-line-strong pb-px xl:flex-1 xl:overflow-visible">
            @foreach ($roleTabs as $key => $label)
                <button type="button" wire:click="setRole('{{ $key }}')" wire:key="role-{{ $key ?: 'semua' }}"
                        @if ($role === (string) $key) aria-current="true" @endif
                        @class([
                            '-mb-px min-h-11 shrink-0 whitespace-nowrap border-b-2 text-label-lg transition-colors motion-reduce:transition-none',
                            'border-brand font-semibold text-brand-link' => $role === (string) $key,
                            'border-transparent text-ink-body hover:text-ink' => $role !== (string) $key,
                        ])>
                    {{ $label }}<span class="ml-1 tabular-nums">{{ $key === '' ? $roleTotal : ($rc[$key] ?? 0) }}</span>
                </button>
            @endforeach
        </nav>

        <div class="relative xl:w-80 xl:flex-none">
            <label for="cari-user" class="sr-only">Cari pengguna</label>
            <x-ui.icon name="search" class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-ink-muted" />
            <input id="cari-user" type="search" maxlength="100" autocomplete="off" wire:model.live.debounce.300ms="search"
                placeholder="Nama, email, nomor HP, atau usaha"
                class="min-h-11 w-full rounded-md border border-line-strong bg-surface pl-10 pr-3 text-body-sm text-ink placeholder:text-ink-muted focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-brand lg:min-h-10">
        </div>
    </div>

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div role="group" aria-label="Status akun" class="flex flex-wrap gap-2">
            @foreach ($accountChips as $key => $label)
                <button type="button" wire:click="setAccount('{{ $key }}')" wire:key="acc-{{ $key ?: 'semua' }}"
                        aria-pressed="{{ $account === (string) $key ? 'true' : 'false' }}"
                        @class([
                            'min-h-9 rounded-full border px-3.5 text-label-md transition-colors motion-reduce:transition-none',
                            'border-brand bg-brand-soft font-semibold text-primary-700' => $account === (string) $key,
                            'border-line-strong text-ink-body hover:bg-surface-muted' => $account !== (string) $key,
                        ])>
                    {{ $label }}<span class="ml-1 tabular-nums">{{ $key === '' ? $accountTotal : ($ac[$key] ?? 0) }}</span>
                </button>
            @endforeach
        </div>

        <div class="flex items-center gap-2">
            <label for="urut-user" class="sr-only">Urutan</label>
            <select id="urut-user" wire:model.live="sort"
                    class="min-h-10 rounded-md border border-line-strong bg-surface px-2 text-body-sm text-ink focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-brand">
                @foreach (\App\Livewire\Admin\Pengguna\UserTable::SORTS as $k => $l)
                    <option value="{{ $k }}">{{ $l }}</option>
                @endforeach
            </select>
            @if ($filtered)
                <x-ui.button variant="ghost" size="sm" type="button" wire:click="clearFilters">Reset filter</x-ui.button>
            @endif
        </div>
    </div>

    <div wire:loading.class="opacity-60"
        wire:target="search,sort,setRole,setAccount,clearFilters,nextPage,previousPage,gotoPage,perPage"
        class="space-y-4 transition-opacity motion-reduce:transition-none">
        <p class="sr-only" role="status">{{ $rows->total() }} pengguna ditemukan</p>

        @if ($rows->isEmpty())
            <x-ui.card>
                @if ($hasTerm)
                    <x-ui.empty-state icon="search" title="Tidak ada hasil untuk “{{ trim($search) }}”"
                                    description="Periksa ejaan, atau cari dengan nama, email, nomor HP, atau nama usaha lain.">
                        <x-ui.button variant="outline" type="button" wire:click="$set('search', '')">Hapus pencarian</x-ui.button>
                    </x-ui.empty-state>
                @else
                    <x-ui.empty-state icon="search" title="Tidak ada pengguna yang cocok"
                                    description="Longgarkan filter peran atau status.">
                        <x-ui.button variant="outline" type="button" wire:click="clearFilters">Reset filter</x-ui.button>
                    </x-ui.empty-state>
                @endif
            </x-ui.card>
        @else
            {{-- Tabel (md ke atas) --}}
            <div class="hidden overflow-x-auto rounded-md border border-line bg-surface md:block">
                <table class="w-full text-left text-body-sm">
                    <caption class="sr-only">Daftar pengguna</caption>
                    <thead class="bg-surface-muted text-label-sm text-ink-body">
                        <tr>
                            <th scope="col" class="px-4 py-3 font-medium">Pengguna</th>
                            <th scope="col" class="px-4 py-3 font-medium">Peran</th>
                            <th scope="col" class="px-4 py-3 font-medium">Status</th>
                            <th scope="col" class="px-4 py-3 font-medium">Terdaftar</th>
                            <th scope="col" class="px-4 py-3 text-right font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @foreach ($rows as $r)
                            @php $url = route('admin.pengguna.show', $r->id); @endphp
                            <tr wire:key="row-{{ $r->id }}" class="align-middle">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <span class="grid size-10 shrink-0 place-items-center rounded-full bg-primary-100 text-label-md text-primary-700" aria-hidden="true">{{ $initialsOf($r->name) }}</span>
                                        <div class="min-w-0">
                                            <a href="{{ $url }}" class="block max-w-56 truncate font-semibold text-ink hover:underline" title="{{ $r->name }}">{{ $r->name }}</a>
                                            <p class="max-w-56 truncate text-caption text-ink-muted" title="{{ $r->email }}">{{ $r->email }}</p>
                                            @if ($r->nama_usaha)
                                                <p class="max-w-56 truncate text-caption text-ink-muted">{{ $r->nama_usaha }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3"><x-ui.badge>{{ Accounts::ROLES[$r->role] ?? $r->role }}</x-ui.badge></td>
                                <td class="px-4 py-3"><x-admin.status-badge :status="$r->status_akun" /></td>
                                <td class="px-4 py-3 text-ink-body">
                                    {{ \Carbon\CarbonImmutable::parse($r->created_at)->locale('id')->translatedFormat('j M Y') }}
                                    @unless ($r->email_verified_at)
                                        <p class="text-caption text-ink-muted">Email belum diverifikasi</p>
                                    @endunless
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <x-ui.button variant="outline" size="sm" :href="$url" aria-label="Lihat {{ $r->name }}">Lihat</x-ui.button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Daftar kartu (di bawah md) --}}
            <ul class="space-y-3 md:hidden">
                @foreach ($rows as $r)
                    @php $url = route('admin.pengguna.show', $r->id); @endphp
                    <li wire:key="card-{{ $r->id }}"
                        @class([
                            'relative rounded-md border border-l-4 border-line bg-surface p-4 transition-colors active:bg-surface-muted motion-reduce:transition-none',
                            'border-l-warning-500' => $r->status_akun === 'suspend',
                            'border-l-danger-500' => $r->status_akun === 'banned',
                            'border-l-line-strong' => $r->status_akun === 'aktif',
                        ])>
                        <div class="flex items-center gap-3">
                            <span class="grid size-11 shrink-0 place-items-center rounded-full bg-primary-100 text-label-lg text-primary-700" aria-hidden="true">{{ $initialsOf($r->name) }}</span>
                            <div class="min-w-0 flex-1">
                                <a href="{{ $url }}" class="block truncate text-label-lg font-semibold text-ink after:absolute after:inset-0 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand">
                                    <span class="sr-only">Lihat </span>{{ $r->name }}
                                </a>
                                <p class="truncate text-caption text-ink-muted">{{ $r->email }}</p>
                            </div>
                            <x-ui.icon name="chevron-right" class="text-ink-muted" />
                        </div>
                        <div class="mt-3 flex flex-wrap items-center gap-2">
                            <x-ui.badge>{{ Accounts::ROLES[$r->role] ?? $r->role }}</x-ui.badge>
                            <x-admin.status-badge :status="$r->status_akun" />
                        </div>
                    </li>
                @endforeach
            </ul>

            <x-ui.pagination :rows="$rows" :pages="$this->pageWindow()" :per-page-options="$perPageOptions" noun="pengguna" />
        @endif
    </div>
</div>