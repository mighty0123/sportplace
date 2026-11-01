@props(['mitra', 'field', 'label'])
@php $masked = $mitra->maskedValue($field); @endphp

@if ($masked === '')
    <x-admin.info-row :label="$label" value="—" />
@else
    <div class="grid grid-cols-[6.5rem_minmax(0,1fr)_auto] items-baseline gap-x-3 py-1.5"
        x-data="{
            value: @js($masked), revealed: false, loading: false, failed: false, timer: null,
            async reveal() {
                this.loading = true; this.failed = false;
                try {
                    const res = await fetch(@js(route('admin.verifikasi.reveal', [$mitra, $field])), {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' },
                    });
                    if (!res.ok) throw new Error();
                    this.value = (await res.json()).value; this.revealed = true;
                    this.timer = setTimeout(() => this.hide(), 30000); // tersamar lagi setelah 30 detik
                } catch (e) { this.failed = true } finally { this.loading = false }
            },
            hide() { clearTimeout(this.timer); this.value = @js($masked); this.revealed = false },
        }">
        <dt class="text-caption text-ink-muted">{{ $label }}</dt>
        <dd class="min-w-0 break-all text-body-sm font-medium tabular-nums text-ink" x-text="value">{{ $masked }}</dd>
        <button type="button" x-on:click="revealed ? hide() : reveal()" :disabled="loading"
                :aria-label="(revealed ? 'Sembunyikan ' : 'Tampilkan ') + @js($label)"
                class="text-label-sm text-brand-link underline"
                x-text="loading ? 'Memuat…' : (revealed ? 'Sembunyikan' : 'Tampilkan')">Tampilkan</button>
        <p class="col-span-3 text-caption text-danger-700" x-show="failed" x-cloak>Gagal menampilkan data. Coba lagi.</p>
    </div>
@endif