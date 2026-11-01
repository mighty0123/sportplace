@php
    $name        = $mitra->nama_usaha ?: 'Pendaftaran tanpa nama usaha';
    $owner       = $mitra->user;
    $submittedAt = $mitra->submitted_at ?? $mitra->updated_at ?? $mitra->created_at;
    $days        = $submittedAt ? (int) floor($submittedAt->diffInDays(now())) : null;
    $isLate      = $mitra->isPending() && $days !== null && $days >= config('admin.verification_sla_days', 2);
    $backUrl     = \Illuminate\Support\Facades\Route::has('admin.verifikasi.index')
        ? route('admin.verifikasi.index') : route('admin.dashboard');

    $description = $submittedAt
        ? 'Diajukan ' . $submittedAt->locale('id')->translatedFormat('j F Y')
            . ($mitra->isPending() ? ($days === 0 ? ', menunggu sejak hari ini' : ", menunggu {$days} hari") : '')
        : 'Tanggal pengajuan tidak tercatat';
@endphp

<x-layouts.admin :title="$name">
    <a href="{{ $backUrl }}" class="mb-3 inline-flex min-h-9 items-center text-label-md text-brand-link underline">
        Kembali ke daftar
    </a>

    <x-admin.page-header :title="$name" :description="$description">
        <x-slot:actions>
            <div class="flex items-center gap-2">
                @if ($isLate)
                    <x-ui.badge tone="danger" icon="alert-circle">Perlu segera</x-ui.badge>
                @endif
                <x-admin.status-badge :status="$mitra->status" />
            </div>
        </x-slot:actions>
    </x-admin.page-header>

    @if ($errors->any() && ! $errors->has('alasan_penolakan'))
        <x-ui.alert type="error" class="mt-4">{{ $errors->first() }}</x-ui.alert>
    @endif

    <div class="mt-5 grid grid-cols-1 gap-4 lg:grid-cols-[minmax(0,1fr)_24rem] lg:items-start">
        <x-ui.card class="min-w-0">
            <x-admin.document-viewer :documents="$documents" />
        </x-ui.card>

        {{-- Kolom kanan menempel di layar besar dan bisa digulir sendiri bila lebih tinggi dari layar. --}}
        <div class="grid min-w-0 grid-cols-1 content-start gap-4 lg:sticky lg:top-4 lg:max-h-[calc(100vh-2rem)] lg:overflow-y-auto">
            <x-ui.card class="min-w-0">
                <h2 class="text-h6 text-ink">Pemilik</h2>
                <dl class="mt-1">
                    <x-admin.info-row label="Nama" :value="$owner?->name" />

                    <div class="grid grid-cols-[6.5rem_minmax(0,1fr)] items-baseline gap-x-3 py-1.5"
                        x-data="{
                            nik: @js($mitra->maskedNik()), revealed: false, loading: false, failed: false, timer: null,
                            async reveal() {
                                this.loading = true; this.failed = false;
                                try {
                                    const res = await fetch(@js(route('admin.verifikasi.nik', $mitra)), {
                                        method: 'POST',
                                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' },
                                    });
                                    if (!res.ok) throw new Error();
                                    this.nik = (await res.json()).nik; this.revealed = true;
                                    this.timer = setTimeout(() => this.hide(), 30000); // tersamar lagi setelah 30 detik
                                } catch (e) { this.failed = true } finally { this.loading = false }
                            },
                            hide() { clearTimeout(this.timer); this.nik = @js($mitra->maskedNik()); this.revealed = false },
                        }">
                        <dt class="text-caption text-ink-muted">NIK</dt>
                        <dd class="min-w-0 text-body-sm text-ink">
                            <span class="font-medium tabular-nums" x-text="nik"></span>
                            <button type="button" class="ml-2 text-label-sm text-brand-link underline"
                                    x-on:click="revealed ? hide() : reveal()" :disabled="loading"
                                    x-text="loading ? 'Memuat…' : (revealed ? 'Sembunyikan' : 'Tampilkan')"></button>
                            <span class="block text-caption text-danger-700" x-show="failed" x-cloak>Gagal menampilkan NIK. Coba lagi.</span>
                        </dd>
                    </div>

                    <x-admin.info-row label="Email" :value="$owner?->email" />
                    <x-admin.info-row label="Telepon" :value="$owner?->no_hp" />
                </dl>

                <h2 class="mt-4 text-h6 text-ink">Usaha</h2>
                <dl class="mt-1">
                    <x-admin.info-row label="Jenis" :value="$mitra->bentuk_badan_usaha" />
                    <x-admin.info-row label="Kota" :value="$mitra->kota_operasional" />
                    <x-admin.info-row label="NPWP" :value="$mitra->npwp" />
                    <x-admin.info-row label="Deskripsi" :value="$mitra->deskripsi_usaha" />
                </dl>

                <h2 class="mt-4 text-h6 text-ink">Rekening pencairan</h2>
                <dl class="mt-1">
                    <x-admin.info-row label="Bank" :value="$mitra->bank_nama" />
                    <x-admin.info-row label="Nomor" :value="$mitra->maskedAccountNumber()" />
                    <x-admin.info-row label="Atas nama" :value="$mitra->nama_rekening" />
                </dl>
            </x-ui.card>

            <x-ui.card class="min-w-0">
                @include('admin.verifikasi.partials.decision')
            </x-ui.card>

            <x-ui.card class="min-w-0">
                @include('admin.verifikasi.partials.history')
            </x-ui.card>
        </div>
    </div>
</x-layouts.admin>