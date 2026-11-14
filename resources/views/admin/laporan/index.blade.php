<x-layouts.admin title="Laporan" hero-size="compact">
    <x-slot:hero>
        <x-admin.page-header tone="dark" title="Laporan"
                            description="Susun laporan, lihat pratinjaunya, lalu unduh dalam Excel atau PDF." />
    </x-slot:hero>

    <livewire:admin.laporan.report-builder />
</x-layouts.admin>