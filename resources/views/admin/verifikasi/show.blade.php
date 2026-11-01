@php
    $name     = $mitra->nama_usaha ?: 'Pendaftaran tanpa nama usaha';
    $owner    = $mitra->user;
    $initials = \Illuminate\Support\Str::of($owner?->name ?? '?')->explode(' ')->filter()->take(2)
        ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');

    $submittedAt = $mitra->submitted_at ?? $mitra->updated_at ?? $mitra->created_at;
    $days   = $submittedAt ? (int) floor($submittedAt->diffInDays(now())) : null;
    $isLate = $mitra->isPending() && $days !== null && $days >= config('admin.verification_sla_days', 2);
    $backUrl = \Illuminate\Support\Facades\Route::has('admin.verifikasi.index') ? route('admin.verifikasi.index') : route('admin.dashboard');

    $description = $submittedAt
        ? 'Diajukan ' . $submittedAt->locale('id')->translatedFormat('j F Y, H.i')
            . ($mitra->isPending() ? ($days === 0 ? ', menunggu sejak hari ini' : ", menunggu {$days} hari") : '')
        : 'Tanggal pengajuan tidak tercatat';

    $blockingProblems = collect($checks)->filter(fn ($c) => $c['status'] === 'fail' && $c['blocking'])->pluck('detail')->implode(' ');
    $warnCount = collect($checks)->where('status', 'warn')->count();
    $caption = $blocked
        ? "Tidak bisa disetujui. {$blockingProblems}"
        : ($warnCount
            ? "{$warnCount} pemeriksaan data perlu perhatian. Saat menyetujui, kamu akan diminta mengonfirmasi pemeriksaan dokumen."
            : 'Semua pemeriksaan data lolos. Saat menyetujui, kamu akan diminta mengonfirmasi pemeriksaan dokumen.');
@endphp

<x-layouts.admin :title="$name" hero-size="compact">
    <x-slot:hero>
        <nav aria-label="Breadcrumb" class="mb-3 text-caption text-primary-200">
            <a href="{{ $backUrl }}" class="underline hover:text-white">Verifikasi Mitra</a>
            <span aria-hidden="true" class="mx-1">/</span>
            <span aria-current="page">{{ $name }}</span>
        </nav>

        <x-admin.page-header tone="dark" :title="$name" :description="$description">
            <x-slot:actions>
                <div class="flex flex-wrap items-center gap-2">
                    @if ($isLate)<x-ui.badge tone="danger" icon="alert-circle">Perlu segera</x-ui.badge>@endif
                    <x-admin.status-badge :status="$mitra->status" />
                </div>
            </x-slot:actions>
        </x-admin.page-header>
    </x-slot:hero>

    @if ($errors->any() && ! $errors->has('alasan_penolakan'))
        <x-ui.alert type="error" class="mb-4">{{ $errors->first() }}</x-ui.alert>
    @endif

    {{-- Pemohon dan keputusan: aksi utama selalu terlihat tanpa menggulir. --}}
    <x-ui.card class="mt-4">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex min-w-0 items-center gap-4">
                <div class="grid size-14 shrink-0 place-items-center rounded-full bg-primary-100 text-h5 text-primary-700" aria-hidden="true">{{ $initials }}</div>
                <div class="min-w-0">
                    <p class="truncate text-h5 text-ink">{{ $owner?->name ?? '—' }}</p>
                    <div class="flex flex-wrap gap-x-4 text-body-sm text-ink-muted">
                        <span class="break-all">{{ $owner?->email ?? '—' }}</span>
                        <span>{{ $owner?->no_hp ?? '—' }}</span>
                    </div>
                </div>
            </div>

            @if ($mitra->isPending())
                <div class="flex flex-col gap-2 lg:items-end">
                    <div class="flex gap-2">
                        <x-ui.button variant="danger-outline" type="button" class="flex-1 lg:flex-none"
                                    x-on:click="$dispatch('open-modal', 'reject')">Tolak</x-ui.button>
                        <x-ui.button type="button" class="flex-1 lg:flex-none" :disabled="$blocked"
                                    x-on:click="$dispatch('open-modal', 'approve')">Setujui mitra</x-ui.button>
                    </div>
                    <p class="max-w-md text-caption lg:text-right {{ $blocked ? 'text-danger-700' : 'text-ink-muted' }}">{{ $caption }}</p>
                </div>
            @else
                <p class="text-body-sm text-ink-muted lg:text-right">
                    Diputuskan oleh {{ $mitra->reviewer?->name ?? 'admin (tidak tercatat)' }}<br>
                    {{ $mitra->reviewed_at?->locale('id')->translatedFormat('j F Y, H.i') ?? 'waktu tidak tercatat' }}
                </p>
            @endif
        </div>
    </x-ui.card>

    <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-[minmax(0,1fr)_22rem] lg:items-start">
        <div class="grid min-w-0 grid-cols-1 gap-4">
            <x-ui.card class="min-w-0">
                <div class="grid gap-6 md:grid-cols-2">
                    <section>
                        <h2 class="text-h6 text-ink">Pemilik</h2>
                        <dl class="mt-1">
                            <x-admin.info-row label="Nama lengkap" :value="$owner?->name" />
                            <x-admin.info-row label="Email" :value="$owner?->email" />
                            <x-admin.info-row label="No. HP" :value="$owner?->no_hp" />
                        </dl>
                    </section>
                    <section>
                        <h2 class="text-h6 text-ink">Usaha</h2>
                        <dl class="mt-1">
                            <x-admin.info-row label="Nama usaha" :value="$mitra->nama_usaha" />
                            <x-admin.info-row label="Bentuk usaha" :value="$mitra->bentuk_badan_usaha" />
                            <x-admin.info-row label="Kota" :value="$mitra->kota_operasional" />
                            <x-admin.info-row label="Deskripsi" :value="$mitra->deskripsi_usaha" />
                        </dl>
                    </section>
                </div>
            </x-ui.card>

            <x-ui.card class="min-w-0">
                <h2 class="text-h6 text-ink">Identitas dan rekening</h2>
                <dl class="mt-1">
                    <x-admin.sensitive-value :mitra="$mitra" field="nik" label="No. KTP (NIK)" />
                    <x-admin.sensitive-value :mitra="$mitra" field="npwp" label="NPWP" />
                    <x-admin.info-row label="Bank" :value="$mitra->bank_nama" />
                    <x-admin.sensitive-value :mitra="$mitra" field="rekening" label="No. rekening" />
                    <x-admin.info-row label="Nama rekening" :value="$mitra->nama_rekening" />
                </dl>
                <p class="mt-2 text-caption text-ink-muted">Data lengkap disamarkan dan baru tampil saat diminta. Setiap permintaan dicatat.</p>
            </x-ui.card>

            <x-ui.card class="min-w-0">
                <h2 class="text-h6 text-ink">Dokumen unggahan</h2>
                <div class="mt-1"><x-admin.document-list :documents="$documents" :mitra="$mitra" /></div>
            </x-ui.card>
        </div>

        <div class="grid min-w-0 grid-cols-1 content-start gap-4">
            <x-ui.card class="min-w-0">
                @if ($mitra->isPending())
                    <x-admin.data-checks :checks="$checks" />
                @else
                    @include('admin.verifikasi.partials.result')
                @endif
            </x-ui.card>

            <x-ui.card class="min-w-0">
                @include('admin.verifikasi.partials.history')
            </x-ui.card>
        </div>
    </div>

    @if ($mitra->isPending())
        @include('admin.verifikasi.partials.dialogs')
    @endif
</x-layouts.admin>