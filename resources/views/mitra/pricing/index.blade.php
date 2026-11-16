<x-layouts.mitra title="Pengaturan Tarif Sewa & Peak Hours — SportPlace Mitra" heading="Pengaturan Harga">
    <livewire:mitra.pricing.pricing-manager
        :initial-venue-id="request()->query('venue_id') ? (int) request()->query('venue_id') : ($selectedField?->venue_id ?? null)"
        :initial-field-id="request()->query('field_id') ? (int) request()->query('field_id') : ($selectedField?->id ?? null)"
    />
</x-layouts.mitra>
