@props(['rows', 'pages', 'perPageOptions' => [15, 30, 50], 'noun' => 'data'])

<div class="flex flex-col items-center gap-3 text-center text-caption text-ink-muted lg:flex-row lg:justify-between lg:text-left">
    <div class="flex flex-col items-center gap-2 sm:flex-row sm:gap-4">
        <p>Menampilkan {{ $rows->firstItem() }} sampai {{ $rows->lastItem() }} dari {{ $rows->total() }} {{ $noun }}</p>
        <label class="flex items-center gap-2">
            Baris per halaman
            <select wire:model.live="perPage"
                    class="min-h-9 rounded-md border border-line-strong bg-surface px-2 text-ink focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-brand">
                @foreach ($perPageOptions as $n)
                    <option value="{{ $n }}">{{ $n }}</option>
                @endforeach
            </select>
        </label>
    </div>

    @if ($rows->hasPages())
        <nav aria-label="Halaman" class="flex items-center justify-center gap-2">
            <x-ui.button variant="neutral" type="button" wire:click="previousPage" :disabled="$rows->onFirstPage()">Sebelumnya</x-ui.button>
            <span class="px-1 text-label-md tabular-nums text-ink md:hidden">Halaman {{ $rows->currentPage() }} dari {{ $rows->lastPage() }}</span>
            <div class="hidden items-center gap-1 md:flex">
                @foreach ($pages as $p)
                    @if ($p === null)
                        <span class="px-1" aria-hidden="true">…</span>
                    @else
                        <button type="button" wire:click="gotoPage({{ $p }})" wire:key="page-{{ $p }}" aria-label="Halaman {{ $p }}"
                                @if ($p === $rows->currentPage()) aria-current="page" @endif
                                @class([
                                    'grid min-h-9 min-w-9 place-items-center rounded-md border px-2 text-label-md tabular-nums',
                                    'border-brand bg-brand-soft font-semibold text-primary-700' => $p === $rows->currentPage(),
                                    'border-line-strong text-ink-body hover:bg-surface-muted' => $p !== $rows->currentPage(),
                                ])>{{ $p }}</button>
                    @endif
                @endforeach
            </div>
            <x-ui.button variant="neutral" type="button" wire:click="nextPage" :disabled="! $rows->hasMorePages()">Berikutnya</x-ui.button>
        </nav>
    @endif
</div>