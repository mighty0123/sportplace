<x-layouts.admin title="Monitoring Transaksi" hero-size="compact">
    <x-slot:hero>
        <x-admin.page-header tone="dark" title="Monitoring Transaksi"
                            description="Pantau booking dan pembayaran lintas mitra, dan temukan data yang tidak sejalan." />
    </x-slot:hero>

    <livewire:admin.transaksi.transaction-table />
</x-layouts.admin>