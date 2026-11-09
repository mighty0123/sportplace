@php
    $items     = app(\App\Services\Admin\MitraVerificationService::class)->checklistItemsFor($mitra);
    $nameLabel = $mitra->nama_usaha ?: 'pendaftaran ini';
    $warnCount = collect($checks)->where('status', 'warn')->count();
    $base      = ['KTP tidak terbaca atau tidak utuh', 'NIK atau nama tidak sesuai dengan KTP', 'Nama rekening tidak sesuai dengan nama pemilik'];
    // Alasan dari pemeriksaan yang bermasalah tampil lebih dulu.
    $quick = collect($checks)->whereIn('status', ['fail', 'warn'])->pluck('reason')->filter()
        ->merge($base)->unique()->take(6)->values()->all();
@endphp

<div x-data="{
        checks: @js(array_fill_keys(array_keys($items), false)),
        reason: @js(old('alasan_penolakan', '')),
        submitting: false,
        get complete() { return Object.values(this.checks).every(Boolean) },
        quick(text) {
            this.reason = text;
            this.$nextTick(() => document.getElementById('alasan_penolakan').dispatchEvent(new Event('input')));
        },
    }"
    @if ($errors->has('alasan_penolakan')) x-init="$nextTick(() => $dispatch('open-modal', 'reject'))" @endif>

    {{-- Dialog setujui: butir pemeriksaan manual dikonfirmasi di sini. --}}
    <x-ui.modal name="approve" :title="'Setujui ' . $nameLabel . '?'"
                :description="($mitra->user?->name ?? 'Pemilik') . ' bisa langsung membuat venue dan lapangan di SportPlace.'">
        <form id="approve-form" method="POST" action="{{ route('admin.verifikasi.approve', $mitra) }}"
            x-on:submit="submitting = true" class="mt-3">
            @csrf
            <fieldset>
                <legend class="text-label-md text-ink">Saya sudah memeriksa dokumen dan memastikan</legend>
                @foreach ($items as $key => $label)
                    <label class="flex min-h-10 items-start gap-3 py-2 text-body-sm text-ink">
                        <input type="checkbox" name="checklist[{{ $key }}]" value="1" x-model="checks.{{ $key }}"
                            class="mt-0.5 size-4.5 shrink-0 accent-primary-500">
                        {{ $label }}
                    </label>
                @endforeach
            </fieldset>

            @if ($warnCount)
                <p class="mt-2 rounded-md bg-warning-50 px-3 py-2 text-caption text-warning-900">
                    {{ $warnCount }} pemeriksaan data perlu perhatian. Pastikan sudah kamu periksa sebelum menyetujui.
                </p>
            @endif

            <p class="mt-3 text-caption text-ink-body">
                Keputusan ini final. Mengubah akses mitra setelahnya dilakukan lewat Manajemen Pengguna.
            </p>
        </form>

        <x-slot:footer>
            <x-ui.button variant="neutral" type="button" x-on:click="$dispatch('close-modal', 'approve')">Batal</x-ui.button>
            <x-ui.button type="submit" form="approve-form" ::disabled="!complete || submitting">Ya, setujui mitra</x-ui.button>
        </x-slot:footer>
    </x-ui.modal>

    {{-- Dialog tolak --}}
    <x-ui.modal name="reject" :title="'Tolak ' . $nameLabel . '?'"
                description="Mitra akan melihat alasan ini dan bisa memperbaiki lalu mengajukan ulang.">
        <form id="reject-form" method="POST" action="{{ route('admin.verifikasi.reject', $mitra) }}"
            x-on:submit="submitting = true" class="mt-3">
            @csrf
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