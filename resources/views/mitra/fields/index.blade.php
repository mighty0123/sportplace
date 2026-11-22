<x-layouts.mitra :title="($venue ? 'Daftar Lapangan — ' . $venue->nama_venue : 'Semua Lapangan') . ' — SportPlace Mitra'">
    <livewire:mitra.fields.field-manager
        :venue-id="$venue?->id"
        :show-all-venues="empty($venue)"
    />
</x-layouts.mitra>
