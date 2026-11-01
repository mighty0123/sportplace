@php
    $items     = app(\App\Services\Admin\MitraVerificationService::class)->checklistItemsFor($mitra);
    $nameLabel = $mitra->nama_usaha ?: 'pendaftaran ini';
    $base  = ['KTP tidak terbaca atau tidak utuh', 'NIK atau nama tidak sesuai dengan KTP', 'Nama rekening tidak sesuai dengan nama pemilik'];
    // Alasan dari pemeriksaan yang bermasalah tampil lebih dulu.
    $quick = collect($checks)->whereIn('status', ['fail', 'warn'])->pluck('reason')->filter()
        ->merge($base)->unique()->take(6)->values()->all();
    $auto = is_array($mitra->review_checklist) ? ($mitra->review_checklist['auto'] ?? null) : null;
@endphp

@if ($mitra->isPending())
    <div x-data="{
            checks: @js(array_fill_keys(array_keys($items), false)),
            reason: @js(old('alasan_penolakan', '')),
            submitting: false,
            get done() { return Object.values(this.checks).filter(Boolean).length },
            get total() { return Object.keys(this.checks).length },
            get complete() { return this.done === this.total },
            blocked: @js($blocked),
            get canApprove() { return this.complete && ! this.blocked },
            quick(text) {
                this.reason = text;
                this.$nextTick(() => document.getElementById('alasan_penolakan').dispatchEvent(new Event('input')));
            },
        }"
        @if ($errors->has('alasan_penolakan')) x-init="$nextTick(() => $dispatch('open-modal', 'reject'))" @endif>

        <fieldset>
            <legend class="text-h6 text-ink">
                Pemeriksaan <span class="font-normal text-ink-muted" x-text="`${done} dari ${total} selesai`"></span>
            </legend>
            @foreach ($items as $key => $label)
                <label class="flex min-h-10 items-start gap-3 py-2 text-body-sm text-ink">
                    <input type="checkbox" x-model="checks.{{ $key }}" class="mt-0.5 size-4.5 shrink-0 accent-primary-500">
                    {{ $label }}
                </label>
            @endforeach
        </fieldset>

        <p class="mt-2 text-caption text-danger-700" x-show="blocked" x-cloak>
            Tidak bisa menyetujui: ada pemeriksaan data yang tidak lolos. Perbaiki datanya atau tolak pendaftaran.
        </p>
        <p class="mt-2 text-caption text-ink-muted" x-show="!blocked && !complete">Centang semua butir pemeriksaan untuk menyetujui.</p>

        <div class="mt-3 flex gap-2">
            <x-ui.button variant="danger-outline" type="button" class="flex-1" x-on:click="$dispatch('open-modal', 'reject')">
                Tolak
            </x-ui.button>
            <x-ui.button type="button" class="flex-[1.4]" ::disabled="!canApprove" x-on:click="$dispatch('open-modal', 'approve')">
                Setujui mitra
            </x-ui.button>
        </div>

        {{-- Dialog setujui --}}
        <x-ui.modal name="approve" :title="'Setujui ' . $nameLabel . '?'"
                    :description="($mitra->user?->name ?? 'Pemilik') . ' bisa langsung membuat venue dan lapangan di SportPlace.'">
            <form id="approve-form" method="POST" action="{{ route('admin.verifikasi.approve', $mitra) }}"
                x-on:submit="submitting = true" class="mt-3">
                @csrf
                @foreach ($items as $key => $label)
                    <input type="hidden" name="checklist[{{ $key }}]" :value="checks.{{ $key }} ? 1 : 0">
                @endforeach

                <dl class="rounded-md border border-primary-100 bg-primary-50 px-3 py-2">
                    <x-admin.info-row label="Pemilik" :value="$mitra->user?->name" />
                    <x-admin.info-row label="Usaha" :value="trim(($mitra->bentuk_badan_usaha ? $mitra->bentuk_badan_usaha . ', ' : '') . $mitra->kota_operasional, ', ')" />
                    <x-admin.info-row label="Pemeriksaan">
                        <span x-text="`${done} dari ${total} butir selesai`"></span>
                    </x-admin.info-row>
                </dl>
                <p class="mt-3 text-caption text-ink-body">
                    Keputusan ini final. Mengubah akses mitra setelahnya dilakukan lewat Manajemen Pengguna.
                </p>
            </form>

            <x-slot:footer>
                <x-ui.button variant="neutral" type="button" x-on:click="$dispatch('close-modal', 'approve')">Batal</x-ui.button>
                <x-ui.button type="submit" form="approve-form" ::disabled="submitting">Ya, setujui mitra</x-ui.button>
            </x-slot:footer>
        </x-ui.modal>

        {{-- Dialog tolak --}}
        <x-ui.modal name="reject" :title="'Tolak ' . $nameLabel . '?'"
                    description="Mitra akan melihat alasan ini dan bisa memperbaiki lalu mengajukan ulang.">
            <form id="reject-form" method="POST" action="{{ route('admin.verifikasi.reject', $mitra) }}"
                x-on:submit="submitting = true" class="mt-3">
                @csrf
                @foreach ($items as $key => $label)
                    <input type="hidden" name="checklist[{{ $key }}]" :value="checks.{{ $key }} ? 1 : 0">
                @endforeach

                <div class="mb-3 flex flex-wrap gap-1.5">
                    @foreach ($quick as $text)
                        <button type="button" x-on:click="quick(@js($text))"
                                class="rounded-full border border-line-strong px-3 py-1.5 text-caption text-ink-body hover:bg-surface-muted">
                            {{ $text }}
                        </button>
                    @endforeach
                </div>

                <x-ui.textarea name="alasan_penolakan" label="Alasan penolakan" :required="true" :maxlength="255" :rows="4"
                            hint="Tulis yang spesifik agar mitra tahu apa yang harus diperbaiki. Minimal 10 karakter."
                            x-model="reason" />
            </form>

            <x-slot:footer>
                <x-ui.button variant="neutral" type="button" x-on:click="$dispatch('close-modal', 'reject')">Batal</x-ui.button>
                <x-ui.button variant="danger" type="submit" form="reject-form"
                            ::disabled="reason.trim().length < 10 || submitting">Tolak pendaftaran</x-ui.button>
            </x-slot:footer>
        </x-ui.modal>
    </div>
@else
    @php
        $snapshot = is_array($mitra->review_checklist) ? ($mitra->review_checklist['manual'] ?? null) : null;
    @endphp

    <div class="space-y-3">
        <div class="flex items-center justify-between gap-3">
            <x-admin.status-badge :status="$mitra->status" />
            <span class="text-caption text-ink-muted">
                {{ $mitra->reviewed_at?->locale('id')->translatedFormat('j F Y, H.i') ?? 'Waktu tidak tercatat' }}
            </span>
        </div>

        <p class="text-body-sm text-ink">Diputuskan oleh {{ $mitra->reviewer?->name ?? 'admin (tidak tercatat)' }}</p>

        @if ($mitra->isRejected() && $mitra->alasan_penolakan)
            <div class="rounded-md bg-danger-50 px-3 py-2 text-body-sm text-danger-900">{{ $mitra->alasan_penolakan }}</div>
        @endif

        @if ($snapshot)
            <ul class="space-y-1.5">
                @foreach (\App\Services\Admin\MitraVerificationService::CHECKLIST as $key => $label)
                    @continue(! array_key_exists($key, $snapshot))
                    <li class="flex items-start gap-2 text-body-sm {{ $snapshot[$key] ? 'text-ink' : 'text-ink-muted' }}">
                        <x-ui.icon :name="$snapshot[$key] ? 'check-circle' : 'x-circle'"
                                class="{{ $snapshot[$key] ? 'text-success-700' : 'text-danger-700' }}" />
                        <span>{{ $label }}<span class="sr-only">{{ $snapshot[$key] ? ', dicentang' : ', tidak dicentang' }}</span></span>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-caption text-ink-muted">Diputuskan sebelum pencatatan hasil pemeriksaan ada.</p>
        @endif

        @if ($auto)
            @php
                $words = ['pass' => 'lolos', 'warn' => 'perlu perhatian', 'fail' => 'tidak lolos', 'na' => 'tidak berlaku'];
            @endphp
            <p class="text-caption text-ink-muted">
                Pemeriksaan otomatis saat diputuskan:
                {{ collect($auto)->countBy()->map(fn ($n, $s) => "{$n} " . ($words[$s] ?? $s))->implode(', ') }}.
            </p>
        @endif

        <p class="text-caption text-ink-body">
            {{ $mitra->isApproved()
                ? 'Mitra dapat membuat venue dan lapangan.'
                : 'Menunggu mitra memperbaiki berkas dan mengajukan ulang.' }}
            Keputusan sudah final, jadi tombol tidak tersedia.
        </p>
    </div>
@endif