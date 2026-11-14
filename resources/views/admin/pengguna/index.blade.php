<x-layouts.admin title="Manajemen Pengguna" hero-size="compact">
    <x-slot:hero>
        <x-admin.page-header tone="dark" title="Manajemen Pengguna"
                            description="Lihat akun, dan tangguhkan atau blokir akun yang melanggar ketentuan." />
    </x-slot:hero>

    <livewire:admin.pengguna.user-table />
</x-layouts.admin>