<x-layouts.mitra title="Jadwal Operasional & Blokir Slot — SportPlace Mitra" heading="Jadwal & Slot">
    <livewire:mitra.schedules.schedule-manager
        :initial-venue-id="request()->query('venue_id') ? (int) request()->query('venue_id') : ($selectedField?->venue_id ?? null)"
        :initial-field-id="request()->query('field_id') ? (int) request()->query('field_id') : ($selectedField?->id ?? null)"
        :initial-date="request()->query('date', $selectedDate ?? now()->toDateString())"
    />
</x-layouts.mitra>
