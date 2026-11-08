@php
    $isVenue  = $jenis === 'venue';
    $name     = $isVenue ? ($item->nama_venue ?: 'Venue tanpa nama') : $item->nama_lapangan;
    $initials = \Illuminate\Support\Str::of($name)->explode(' ')->filter()->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
    $pending  = $status === 'pending_review';
    $since    = $item->waiting_since ? \Carbon\CarbonImmutable::parse($item->waiting_since) : null;
    $days     = $since ? (int) floor($since->diffInDays(now())) : null;
    $isLate   = $pending && $days !== null && $days >= config('admin.verification_sla_days', 2);
    $backUrl  = route('admin.moderasi.index', ['jenis' => $jenis]);
    $mitraUrl = route('admin.verifikasi.show', $item->mitra_id);
    $rupiah   = fn ($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');

    $description = $since
        ? 'Diajukan ' . $since->locale('id')->translatedFormat('j F Y, H.i') . ($pending ? ($days === 0 ? ', menunggu sejak hari ini' : ", menunggu {$days} hari") : '')
        : 'Tanggal pengajuan tidak tercatat';

    $problems = collect($checks)->filter(fn ($c) => $c['status'] === 'fail' && $c['blocking'])->pluck('detail')->implode(' ');
    $warnCount = collect($checks)->where('status', 'warn')->count();
    $caption = $blocked
        ? "Tidak bisa disetujui. {$problems}"
        : ($warnCount
            ? "{$warnCount} pemeriksaan data perlu perhatian. Saat menyetujui, kamu akan diminta mengonfirmasi pemeriksaan manual."
            : 'Semua pemeriksaan data lolos. Saat menyetujui, kamu akan diminta mengonfirmasi pemeriksaan manual.');

    // Tautan peta hanya ditampilkan bila benar-benar alamat web (http/https).
    $mapUrl = $isVenue ? trim((string) $item->map_url) : '';
    $mapOk  = $mapUrl !== '' && filter_var($mapUrl, FILTER_VALIDATE_URL) && in_array(strtolower((string) parse_url($mapUrl, PHP_URL_SCHEME)), ['http', 'https'], true);

    $facilityLabels = ['peralatan' => 'Peralatan', 'keamanan' => 'Keamanan', 'umum' => 'Umum'];
    $meta = (! $isVenue && trim((string) $item->meta_harga) !== '') ? json_decode($item->meta_harga, true) : null;
@endphp

<x-layouts.admin :title="$name" hero-size="compact">
    <x-slot:hero>
        <nav aria-label="Breadcrumb" class="mb-3 text-caption text-primary-200">
            <a href="{{ $backUrl }}" class="underline hover:text-white">Moderasi Listing</a>
            <span aria-hidden="true" class="mx-1">/</span>
            <span>{{ $type['label'] }}</span>
            <span aria-hidden="true" class="mx-1">/</span>
            <span aria-current="page">{{ $name }}</span>
        </nav>

        <x-admin.page-header tone="dark" :title="$name" :description="$description">
            <x-slot:actions>
                <div class="flex flex-wrap items-center gap-2">
                    @if ($isLate)<x-ui.badge tone="danger" icon="alert-circle">Perlu segera</x-ui.badge>@endif
                    <x-admin.status-badge :status="$status" />
                </div>
            </x-slot:actions>
        </x-admin.page-header>
    </x-slot:hero>

    @if ($errors->any() && ! $errors->has('alasan_penolakan'))
        <x-ui.alert type="error" class="mb-4">{{ $errors->first() }}</x-ui.alert>
    @endif

    {{-- Ringkasan dan keputusan --}}
    <x-ui.card>
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex min-w-0 items-center gap-4">
                <div class="grid size-14 shrink-0 place-items-center rounded-full bg-primary-100 text-h5 text-primary-700" aria-hidden="true">{{ $initials }}</div>
                <div class="min-w-0">
                    <p class="truncate text-h5 text-ink">{{ $type['label'] }} milik {{ $item->nama_usaha ?: 'mitra tanpa nama usaha' }}</p>
                    <p class="text-body-sm text-ink-muted">
                        {{ $item->owner_name }}, <span class="break-all">{{ $item->owner_email }}</span>
                    </p>
                </div>
            </div>

            @if ($pending)
                <div class="flex flex-col gap-2 lg:items-end">
                    <div class="flex gap-2">
                        <x-ui.button variant="danger-outline" type="button" class="flex-1 lg:flex-none"
                                    x-on:click="$dispatch('open-modal', 'reject')">Tolak</x-ui.button>
                        <x-ui.button type="button" class="flex-1 lg:flex-none" :disabled="$blocked"
                                    x-on:click="$dispatch('open-modal', 'approve')">Setujui {{ strtolower($type['label']) }}</x-ui.button>
                    </div>
                    <p class="max-w-md text-caption lg:text-right {{ $blocked ? 'text-danger-700' : 'text-ink-muted' }}">{{ $caption }}</p>
                </div>
            @elseif (in_array($status, ['aktif', 'rejected'], true))
                <p class="text-body-sm text-ink-muted lg:text-right">
                    Diputuskan oleh {{ $item->reviewer_name ?? 'admin (tidak tercatat)' }}<br>
                    {{ $item->reviewed_at ? \Carbon\CarbonImmutable::parse($item->reviewed_at)->locale('id')->translatedFormat('j F Y, H.i') : 'waktu tidak tercatat' }}
                </p>
            @else
                <p class="text-body-sm text-ink-muted lg:text-right">Listing berstatus {{ $status === 'draft' ? 'draft' : 'nonaktif' }} tidak dimoderasi di sini.</p>
            @endif
        </div>
    </x-ui.card>

    <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-[minmax(0,1fr)_22rem] lg:items-start">
        <div class="grid min-w-0 grid-cols-1 gap-4">
            @if ($isVenue)
                <x-ui.card class="min-w-0">
                    <h2 class="text-h6 text-ink">Data venue</h2>
                    <dl class="mt-1">
                        <x-admin.info-row label="Nama venue" :value="$item->nama_venue" />
                        <x-admin.info-row label="Alamat" :value="$item->alamat_lengkap" />
                        <x-admin.info-row label="Kota" :value="$item->nama_kota . ', ' . $item->provinsi" />
                        <x-admin.info-row label="Jam operasional" :value="substr($item->jam_buka, 0, 5) . ' sampai ' . substr($item->jam_tutup, 0, 5)" />
                        <x-admin.info-row label="Peta">
                            @if ($mapOk)
                                <a href="{{ $mapUrl }}" target="_blank" rel="noopener noreferrer" class="break-all text-brand-link underline">{{ parse_url($mapUrl, PHP_URL_HOST) }}, buka di tab baru</a>
                            @else
                                {{ $mapUrl === '' ? '—' : 'Tautan tidak valid' }}
                            @endif
                        </x-admin.info-row>
                        <x-admin.info-row label="Deskripsi" :value="$item->deskripsi" />
                    </dl>
                </x-ui.card>

                <x-ui.card class="min-w-0">
                    <h2 class="text-h6 text-ink">Lapangan di venue ini</h2>
                    @if ($children->isEmpty())
                        <p class="mt-2 text-body-sm text-ink-muted">Belum ada lapangan. Mitra menambahkannya setelah venue disetujui.</p>
                    @else
                        <ul class="mt-1 divide-y divide-line">
                            @foreach ($children as $c)
                                <li class="flex items-center justify-between gap-3 py-3">
                                    <div class="min-w-0">
                                        <a href="{{ route('admin.moderasi.show', ['jenis' => 'lapangan', 'id' => $c->id]) }}" class="text-body-sm font-semibold text-ink hover:underline">{{ $c->nama_lapangan }}</a>
                                        <p class="text-caption tabular-nums text-ink-muted">{{ $c->nama_olahraga }}, {{ $rupiah($c->harga_perjam) }} per jam</p>
                                    </div>
                                    <x-admin.status-badge :status="$c->status_lapangan" />
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </x-ui.card>
            @else
                <x-ui.card class="min-w-0">
                    <h2 class="text-h6 text-ink">Data lapangan</h2>
                    <dl class="mt-1">
                        <x-admin.info-row label="Nama lapangan" :value="$item->nama_lapangan" />
                        <x-admin.info-row label="Olahraga" :value="$item->nama_olahraga" />
                        <x-admin.info-row label="Venue">
                            <a href="{{ route('admin.moderasi.show', ['jenis' => 'venue', 'id' => $item->venue_id]) }}" class="text-brand-link underline">{{ $item->nama_venue ?: 'Venue #' . $item->venue_id }}</a>
                            <x-admin.status-badge :status="$item->venue_status" class="ml-1" />
                        </x-admin.info-row>
                        <x-admin.info-row label="Harga per jam" :value="$rupiah($item->harga_perjam)" />
                        @if (is_array($meta))
                            @foreach ($meta as $key => $value)
                                <x-admin.info-row :label="'Harga ' . \Illuminate\Support\Str::lower((string) $key)" :value="is_numeric($value) ? $rupiah($value) : '—'" />
                            @endforeach
                        @endif
                    </dl>
                </x-ui.card>

                <x-ui.card class="min-w-0">
                    <h2 class="text-h6 text-ink">Foto lapangan</h2>
                    @if ($photos->isEmpty())
                        <p class="mt-2 text-body-sm text-ink-muted">Belum ada foto.</p>
                    @else
                        <ul class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-3">
                            @foreach ($photos as $p)
                                <li class="relative overflow-hidden rounded-md border border-line bg-surface-sunken">
                                    @if ($p->url)
                                        <a href="{{ $p->url }}" target="_blank" rel="noopener noreferrer" class="block focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand">
                                            <img src="{{ $p->url }}" alt="Foto lapangan {{ $item->nama_lapangan }}{{ $p->primary ? ', foto utama' : '' }}" loading="lazy" referrerpolicy="no-referrer"
                                                class="aspect-4/3 w-full object-cover"
                                                onerror="this.closest('a').replaceWith(Object.assign(document.createElement('p'),{className:'grid aspect-[4/3] place-items-center p-2 text-center text-caption text-ink-muted',textContent:'Foto tidak dapat dimuat'}))">
                                        </a>
                                    @else
                                        <p class="grid aspect-4/3 place-items-center p-2 text-center text-caption text-ink-muted">Alamat foto tidak valid</p>
                                    @endif
                                    @if ($p->primary)
                                        <x-ui.badge tone="success" class="absolute left-2 top-2">Utama</x-ui.badge>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </x-ui.card>

                <x-ui.card class="min-w-0">
                    <div class="grid gap-6 md:grid-cols-2">
                        <section>
                            <h2 class="text-h6 text-ink">Jadwal mingguan</h2>
                            @if ($schedule['rows']->isEmpty())
                                <p class="mt-2 text-body-sm text-ink-muted">Belum diatur.</p>
                            @else
                                @if ($schedule['note'])
                                    <p class="mt-1 text-caption text-ink-muted">{{ $schedule['note'] }}</p>
                                @endif
                                <ul class="mt-1 divide-y divide-line">
                                    @foreach ($schedule['rows'] as $g)
                                        <li class="flex items-baseline justify-between gap-3 py-2 text-body-sm">
                                            <span class="text-ink">{{ $g->label }}</span>
                                            <span class="text-right tabular-nums {{ $g->open ? 'text-ink-body' : 'text-ink-muted' }}">
                                                @if ($g->open)
                                                    {{ $g->hours }}
                                                    @if ($g->slot)<span class="block text-caption text-ink-muted">sesi {{ $g->slot }}</span>@endif
                                                @else
                                                    Tutup
                                                @endif
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </section>
                        <section>
                            <h2 class="text-h6 text-ink">Fasilitas</h2>
                            @if ($facilities->isEmpty())
                                <p class="mt-2 text-body-sm text-ink-muted">Belum ada fasilitas.</p>
                            @else
                                @foreach ($facilities->groupBy('kategori') as $cat => $list)
                                    <p class="mt-2 text-caption text-ink-muted">{{ $facilityLabels[$cat] ?? ucfirst($cat) }}</p>
                                    <ul class="mt-1 flex flex-wrap gap-1.5">
                                        @foreach ($list as $f)
                                            <li class="rounded-full bg-surface-sunken px-2.5 py-1 text-caption text-ink-body">{{ $f->nama }}</li>
                                        @endforeach
                                    </ul>
                                @endforeach
                            @endif
                        </section>
                    </div>
                </x-ui.card>
            @endif

            <x-ui.card class="min-w-0">
                <h2 class="text-h6 text-ink">Mitra pemilik</h2>
                <dl class="mt-1">
                    <x-admin.info-row label="Usaha">
                        <a href="{{ $mitraUrl }}" class="text-brand-link underline">{{ $item->nama_usaha ?: 'Mitra #' . $item->mitra_id }}</a>
                        <x-admin.status-badge :status="$item->mitra_status" class="ml-1" />
                    </x-admin.info-row>
                    <x-admin.info-row label="Pemilik" :value="$item->owner_name" />
                    <x-admin.info-row label="Email" :value="$item->owner_email" />
                    <x-admin.info-row label="No. HP" :value="$item->owner_phone" />
                </dl>
            </x-ui.card>
        </div>

        <div class="grid min-w-0 grid-cols-1 content-start gap-4">
            <x-ui.card class="min-w-0">
                @if ($pending)
                    <x-admin.data-checks :checks="$checks" />
                @else
                    @include('admin.moderasi.partials.result')
                @endif
            </x-ui.card>

            <x-ui.card class="min-w-0">
                @include('admin.verifikasi.partials.history')
            </x-ui.card>
        </div>
    </div>

    @if ($pending)
        @include('admin.moderasi.partials.dialogs')
    @endif
</x-layouts.admin>