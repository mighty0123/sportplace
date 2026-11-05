@php
    $label     = $type['label'];
    $nameLabel = $isVenue ? ($item->nama_venue ?: 'venue ini') : $item->nama_lapangan;
    $warnCount = collect($checks)->where('status', 'warn')->count();
    $base = $isVenue
        ? ['Informasi venue tidak sesuai atau menyesatkan', 'Deskripsi atau tautan peta tidak layak', 'Nama venue sama dengan venue lain']
        : ['Foto tidak sesuai atau tidak layak', 'Harga atau jadwal tidak wajar', 'Foto lapangan belum lengkap'];
    // Alasan dari pemeriksaan yang bermasalah tampil lebih dulu.
    $quick = collect($checks)->whereIn('status', ['fail', 'warn'])->pluck('reason')->filter()->merge($base)->unique()->take(6)->values()->all();
@endphp

<div x-data="{
        checks: @js(array_fill_keys(array_keys($checklistItems), false)),
        reason: @js(old('alasan_penolakan', '')),
        submitting: false,
        get complete() { return Object.values(this.checks).every(Boolean) },
        quick(text) {
            this.reason = text;
            this.$nextTick(() => document.getElementById('alasan_penolakan').dispatchEvent(new Event('input')));
        },
    }"
    @if ($errors->has('alasan_penolakan')) x-init="$nextTick(() => $dispatch('open-modal', 'reject'))" @endif>

    <x-ui.modal name="approve" :title="'Setujui ' . $nameLabel . '?'"
                :description="$isVenue ? 'Venue menjadi aktif, dan mitra bisa mengajukan lapangannya.' : 'Lapangan menjadi aktif dan tampil di pencarian penyewa.'">
        <form id="approve-form" method="POST" action="{{ route('admin.moderasi.approve', ['jenis' => $jenis, 'id' => $item->id]) }}"
            x-on:submit="submitting = true" class="mt-3">
            @csrf
            <fieldset>
                <legend class="text-label-md text-ink">Saya sudah memeriksa dan memastikan</legend>
                @foreach ($checklistItems as $key => $text)
                    <label class="flex min-h-10 items-start gap-3 py-2 text-body-sm text-ink">
                        <input type="checkbox" name="checklist[{{ $key }}]" value="1" x-model="checks.{{ $key }}"
                            class="mt-0.5 size-4.5 shrink-0 accent-primary-500">
                        {{ $text }}
                    </label>
                @endforeach
            </fieldset>

            @if ($warnCount)
                <p class="mt-2 rounded-md bg-warning-50 px-3 py-2 text-caption text-warning-900">
                    {{ $warnCount }} pemeriksaan data perlu perhatian. Pastikan sudah kamu periksa sebelum menyetujui.
                </p>
            @endif
            <p class="mt-3 text-caption text-ink-body">Keputusan ini final. Mitra dapat menonaktifkan listing miliknya, atau admin menonaktifkannya lewat pengelolaan data.</p>
        </form>

        <x-slot:footer>
            <x-ui.button variant="neutral" type="button" x-on:click="$dispatch('close-modal', 'approve')">Batal</x-ui.button>
            <x-ui.button type="submit" form="approve-form" ::disabled="!complete || submitting">Ya, setujui {{ strtolower($label) }}</x-ui.button>
        </x-slot:footer>
    </x-ui.modal>

    <x-ui.modal name="reject" :title="'Tolak ' . $nameLabel . '?'"
                description="Mitra akan melihat alasan ini dan bisa memperbaiki lalu mengajukan ulang.">
        <form id="reject-form" method="POST" action="{{ route('admin.moderasi.reject', ['jenis' => $jenis, 'id' => $item->id]) }}"
            x-on:submit="submitting = true" class="mt-3">
            @csrf
            <div class="mb-3 flex flex-wrap gap-1.5">
                @foreach ($quick as $text)
                    <button type="button" x-on:click="quick(@js($text))"
                            class="rounded-full border border-line-strong px-3 py-1.5 text-caption text-ink-body hover:bg-surface-muted">{{ $text }}</button>
                @endforeach
            </div>

            <x-ui.textarea name="alasan_penolakan" label="Alasan penolakan" :required="true" :maxlength="255" :rows="4"
                        hint="Tulis yang spesifik agar mitra tahu apa yang harus diperbaiki. Minimal 10 karakter."
                        x-model="reason" />
        </form>

        <x-slot:footer>
            <x-ui.button variant="neutral" type="button" x-on:click="$dispatch('close-modal', 'reject')">Batal</x-ui.button>
            <x-ui.button variant="danger" type="submit" form="reject-form"
                        ::disabled="reason.trim().length < 10 || submitting">Tolak pengajuan</x-ui.button>
        </x-slot:footer>
    </x-ui.modal>
</div>