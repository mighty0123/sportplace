@php
    use App\Services\Commission\CommissionService as CS;

    $fmt = fn ($d) => \Carbon\CarbonImmutable::parse($d)->locale('id')->translatedFormat('j M Y, H.i');
    $mitraSelected = (string) old('mitra_id', $prefillMitra ?: '');

    // Lingkup awal: dari isian yang gagal divalidasi, lalu dari ?mitra=, lalu global.
    $initialScope = in_array(old('scope'), ['global', 'mitra'], true)
        ? old('scope')
        : ($prefillMitra ? 'mitra' : 'global');
    $initialSched = filled(old('berlaku_mulai'));
    $maxLabel     = rtrim(rtrim(number_format($maxPercent, 2, ',', ''), '0'), ',');
@endphp

<x-layouts.admin title="Pengaturan Komisi" hero-size="compact">
    <x-slot:hero>
        <x-admin.page-header tone="dark" title="Pengaturan Komisi"
                            description="Atur persentase komisi platform. Setiap perubahan tercatat dan tidak mengubah booking yang sudah dinilai." />
    </x-slot:hero>

    <x-admin.summary-strip :items="[
        ['label' => 'Tarif global', 'value' => CS::percentLabel($global->persen ?? 0), 'note' => $global ? 'Sejak ' . \Carbon\CarbonImmutable::parse($global->berlaku_mulai)->locale('id')->translatedFormat('j M Y') : 'Belum ada tarif'],
        ['label' => 'Tarif khusus mitra', 'value' => number_format($overrides->count(), 0, ',', '.'), 'note' => 'Mitra dengan tarif sendiri'],
        ['label' => 'Terjadwal', 'value' => number_format($scheduledCount, 0, ',', '.'), 'note' => $nextGlobal ? 'Global ' . CS::percentLabel($nextGlobal->persen) . ' mulai ' . \Carbon\CarbonImmutable::parse($nextGlobal->berlaku_mulai)->locale('id')->translatedFormat('j M') : 'Belum ada yang menunggu'],
        ['label' => 'Biaya layanan (Rp)', 'value' => number_format($serviceFee, 0, ',', '.'), 'note' => 'Pendapatan platform, tanpa komisi'],
    ]" />

    <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-[minmax(0,1fr)_26rem] lg:items-start">

        {{-- Satu formulir untuk tarif global dan tarif khusus mitra --}}
        <x-ui.card class="min-w-0" id="form-tarif">
            <div x-data="{ scope: @js($initialScope), sched: @js($initialSched) }">
                <h2 class="text-h6 text-ink">Ubah tarif</h2>

                <form method="POST" action="{{ route('admin.komisi.store') }}" class="mt-3 space-y-4">
                    @csrf

                    {{-- Pilihan lingkup. Radio asli, jadi bisa dipakai dengan keyboard dan terkirim sebagai "scope". --}}
                    <fieldset>
                        <legend class="sr-only">Lingkup tarif</legend>
                        <div class="inline-flex w-full gap-1 rounded-full bg-surface-muted p-1 sm:w-auto">
                            <label class="relative flex-1 sm:flex-none">
                                <input type="radio" name="scope" value="global" x-model="scope" class="peer sr-only">
                                <span class="flex min-h-10 cursor-pointer items-center justify-center rounded-full px-4 text-label-md transition-colors motion-reduce:transition-none peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-brand"
                                        :class="scope === 'global' ? 'bg-brand text-white' : 'text-ink-muted hover:text-ink'">Tarif global</span>
                            </label>
                            <label class="relative flex-1 sm:flex-none">
                                <input type="radio" name="scope" value="mitra" x-model="scope" class="peer sr-only">
                                <span class="flex min-h-10 cursor-pointer items-center justify-center rounded-full px-4 text-label-md transition-colors motion-reduce:transition-none peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-brand"
                                        :class="scope === 'mitra' ? 'bg-brand text-white' : 'text-ink-muted hover:text-ink'">Tarif khusus mitra</span>
                            </label>
                        </div>
                    </fieldset>

                    <p class="text-caption text-ink-muted" x-show="scope === 'global'" @if ($initialScope !== 'global') style="display: none;" @endif>
                        Berlaku untuk semua mitra yang tidak punya tarif khusus. Tarif dinilai saat pembayaran berhasil, jadi booking lama tidak ikut berubah.
                    </p>
                    <p class="text-caption text-ink-muted" x-show="scope === 'mitra'" @if ($initialScope !== 'mitra') style="display: none;" @endif>
                        Berlaku hanya untuk mitra yang dipilih. Kosongkan tarifnya untuk mengembalikan mitra ke tarif global.
                    </p>

                    {{-- Mitra: hanya untuk tarif khusus. Dinonaktifkan saat global agar tidak ikut terkirim. --}}
                    <div x-show="scope === 'mitra'" @if ($initialScope !== 'mitra') style="display: none;" @endif>
                        <label for="mitra_id" class="text-label-md text-ink-strong">Mitra <span class="text-danger-700" aria-hidden="true">*</span></label>
                        <div class="relative mt-1.5">
                            <select id="mitra_id" name="mitra_id" x-bind:required="scope === 'mitra'" x-bind:disabled="scope !== 'mitra'"
                                    @class([
                                        'min-h-11 w-full appearance-none truncate rounded-md border bg-surface pl-3 pr-10 text-body-md text-ink lg:min-h-10',
                                        'focus-visible:outline-2 focus-visible:outline-offset-1',
                                        'border-line-strong focus-visible:outline-brand' => ! $errors->has('mitra_id'),
                                        'border-danger-600 focus-visible:outline-danger-600' => $errors->has('mitra_id'),
                                    ])>
                                <option value="">Pilih mitra</option>
                                @foreach ($mitraOptions as $m)
                                    <option value="{{ $m->id }}" @selected($mitraSelected === (string) $m->id)>{{ $m->nama_usaha ?: 'Mitra #' . $m->id }}</option>
                                @endforeach
                            </select>
                            <x-ui.icon name="chevron-down" class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-ink-muted" />
                        </div>
                        @error('mitra_id') <p class="mt-1 text-caption text-danger-700">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <x-ui.input name="persen" label="Tarif (%)" inputmode="decimal"
                                        x-bind:required="scope === 'global'"
                                        value="{{ old('persen') }}"
                                        hint="0 sampai {{ $maxLabel }}. Contoh: 20 atau 12,5." />
                        </div>

                        {{-- Jadwal: bawaannya berlaku sekarang. Kolom tanggal baru muncul bila diminta. --}}
                        <div>
                            <span class="text-label-md text-ink-strong">Mulai berlaku</span>
                            <label class="mt-1.5 flex min-h-11 cursor-pointer items-center gap-2 text-body-md text-ink lg:min-h-10">
                                <input type="checkbox" x-model="sched"
                                        class="size-4 rounded border-line-strong text-brand focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand">
                                Jadwalkan untuk nanti
                            </label>
                            <p class="text-caption text-ink-muted" x-show="! sched" @if ($initialSched) style="display: none;" @endif>Tanpa jadwal, tarif berlaku begitu disimpan.</p>
                            <div class="mt-2" x-show="sched" @unless ($initialSched) style="display: none;" @endunless>
                                <x-ui.input name="berlaku_mulai" type="datetime-local" label="Tanggal dan jam"
                                            x-bind:disabled="! sched"
                                            value="{{ old('berlaku_mulai') }}" />
                            </div>
                        </div>
                    </div>

                    <x-ui.input name="catatan" label="Alasan perubahan" :required="true" maxlength="255"
                                value="{{ old('catatan') }}"
                                hint="Tercatat di log audit. Minimal 10 karakter." />

                    <x-ui.button type="submit">
                        <span x-text="scope === 'global' ? 'Simpan tarif global' : 'Simpan tarif mitra'">Simpan tarif {{ $initialScope === 'mitra' ? 'mitra' : 'global' }}</span>
                    </x-ui.button>
                </form>

                {{-- Daftar mitra bertarif khusus: hanya tampil bila ada. --}}
                @if ($overrides->isNotEmpty())
                    <div class="mt-5 border-t border-line pt-4">
                        <h3 class="text-label-lg text-ink">Mitra dengan tarif khusus</h3>
                        <ul class="mt-1 divide-y divide-line">
                            @foreach ($overrides as $o)
                                <li class="flex items-center justify-between gap-3 py-3">
                                    <div class="min-w-0">
                                        <p class="truncate text-body-sm font-semibold text-ink">{{ $o->nama_usaha ?: 'Mitra #' . $o->mitra_id }}</p>
                                        <p class="text-caption text-ink-muted">Sejak {{ $fmt($o->berlaku_mulai) }}, oleh {{ $o->admin_name ?? 'Sistem' }}</p>
                                    </div>
                                    <div class="flex shrink-0 items-center gap-3">
                                        <span class="text-label-lg tabular-nums text-ink">{{ CS::percentLabel($o->persen) }}</span>
                                        <a href="{{ route('admin.komisi.index', ['mitra' => $o->mitra_id]) }}#form-tarif"
                                            class="text-label-md text-brand-link underline" aria-label="Ubah tarif {{ $o->nama_usaha }}">Ubah</a>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </x-ui.card>

        <div class="min-w-0 lg:sticky lg:top-4">
            <livewire:admin.komisi.commission-preview />
        </div>
    </div>

    <div class="mt-4">
        <livewire:admin.komisi.commission-history />
    </div>
</x-layouts.admin>