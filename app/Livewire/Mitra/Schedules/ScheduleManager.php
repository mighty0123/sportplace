<?php

namespace App\Livewire\Mitra\Schedules;

use App\Livewire\Mitra\MitraComponent;
use App\Models\BookingDetail;
use App\Models\Field;
use App\Models\FieldBlock;
use App\Models\FieldSchedule;
use App\Models\Venue;
use App\Services\Mitra\SlotGenerator;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;

class ScheduleManager extends MitraComponent
{
    public ?int $venueId = null;

    #[Url(as: 'field_id', history: true)]
    public ?int $fieldId = null;

    #[Url(as: 'date', history: true)]
    public string $selectedDate = '';

    // Form State: 7 Hari (Index 1..7)
    public array $schedules = [];

    // Modal Tambah Blokir / Maintenance
    public bool $showBlockModal = false;

    public string $block_tanggal_mulai = '';

    public string $block_tanggal_selesai = '';

    public string $block_jam_mulai = '08:00';

    public string $block_jam_selesai = '12:00';

    public string $block_alasan = '';

    // Modal Konfirmasi Hapus Blokir
    public bool $showDeleteBlockModal = false;

    public ?int $confirmDeleteBlockId = null;

    public string $confirmDeleteBlockInfo = '';

    public function mount(?int $initialVenueId = null, ?int $initialFieldId = null, ?string $initialDate = null): void
    {
        $this->authorizeMitraApproved();

        $this->selectedDate = $initialDate ?: ($this->selectedDate ?: now()->toDateString());
        try {
            Carbon::parse($this->selectedDate);
        } catch (\Throwable) {
            $this->selectedDate = now()->toDateString();
        }

        if ($initialFieldId) {
            $this->fieldId = $initialFieldId;
        }
        if ($initialVenueId) {
            $this->venueId = $initialVenueId;
        }

        $allFields = $this->allFields();

        if ($allFields->isNotEmpty()) {
            $selected = null;
            if ($this->fieldId) {
                $selected = $allFields->firstWhere('id', $this->fieldId);
            } elseif ($this->venueId) {
                $selected = $allFields->where('venue_id', $this->venueId)->first();
            }

            if (! $selected) {
                $selected = $allFields->first();
            }

            $this->fieldId = $selected->id;
            $this->venueId = $selected->venue_id;
            $this->loadSchedulesForField($selected);
        }
    }

    #[Computed]
    public function venues(): Collection
    {
        $profile = $this->authorizeMitraApproved();

        return Venue::with(['fields' => function ($q) {
            $q->with('sport')->orderBy('nama_lapangan');
        }])
            ->where('mitra_id', $profile->id)
            ->orderBy('nama_venue')
            ->get();
    }

    #[Computed]
    public function allFields(): Collection
    {
        return $this->venues()->flatMap->fields;
    }

    #[Computed]
    public function venueFields(): Collection
    {
        if (! $this->venueId) {
            return $this->allFields();
        }

        return $this->allFields()->where('venue_id', $this->venueId)->values();
    }

    #[Computed]
    public function selectedField(): ?Field
    {
        if (! $this->fieldId) {
            return null;
        }

        $field = $this->allFields()->firstWhere('id', $this->fieldId);
        if ($field) {
            $field->loadMissing(['venue', 'sport', 'schedules', 'blocks']);
        }

        return $field;
    }

    #[Computed]
    public function blocks(): Collection
    {
        if (! $this->fieldId) {
            return collect();
        }

        return FieldBlock::query()
            ->where('field_id', $this->fieldId)
            ->whereDate('tanggal_selesai', '>=', now()->toDateString())
            ->orderBy('tanggal_mulai')
            ->orderBy('jam_mulai')
            ->get();
    }

    #[Computed]
    public function slotPreview(): ?array
    {
        $field = $this->selectedField();
        if (! $field) {
            return null;
        }

        try {
            $carbonDate = Carbon::parse($this->selectedDate ?: now()->toDateString());
        } catch (\Throwable) {
            $carbonDate = now();
        }

        $field->load(['schedules', 'blocks']);

        return app(SlotGenerator::class)->generateSlots($field, $carbonDate);
    }

    // ---- Dual Dropdown Reaktif (LW-4.1) ----

    public function updatedVenueId($value): void
    {
        $venueId = (int) $value;
        $this->venueId = $venueId;

        $firstField = $this->allFields()->where('venue_id', $venueId)->first();
        if ($firstField) {
            $this->fieldId = $firstField->id;
            $this->loadSchedulesForField($firstField);
        } else {
            $this->fieldId = null;
            $this->schedules = [];
        }

        $this->resetValidation();
        unset($this->venueFields, $this->selectedField, $this->blocks, $this->slotPreview);
    }

    public function updatedFieldId($value): void
    {
        $fieldId = (int) $value;
        $field = $this->allFields()->firstWhere('id', $fieldId);

        if ($field) {
            $this->fieldId = $field->id;
            $this->venueId = $field->venue_id;
            $this->loadSchedulesForField($field);
        }

        $this->resetValidation();
        unset($this->selectedField, $this->blocks, $this->slotPreview);
    }

    public function updatedSelectedDate($value): void
    {
        try {
            $parsed = Carbon::parse($value);
            $this->selectedDate = $parsed->toDateString();
        } catch (\Throwable) {
            $this->selectedDate = now()->toDateString();
        }
        unset($this->slotPreview);
    }

    // ---- Navigasi Tanggal Cepat ----

    public function previousDate(): void
    {
        $this->selectedDate = Carbon::parse($this->selectedDate ?: now()->toDateString())->subDay()->toDateString();
        unset($this->slotPreview);
    }

    public function nextDate(): void
    {
        $this->selectedDate = Carbon::parse($this->selectedDate ?: now()->toDateString())->addDay()->toDateString();
        unset($this->slotPreview);
    }

    public function todayDate(): void
    {
        $this->selectedDate = now()->toDateString();
        unset($this->slotPreview);
    }

    // ---- Form Jadwal Mingguan (LW-4.1) ----

    protected function loadSchedulesForField(Field $field): void
    {
        $field->loadMissing(['schedules', 'venue']);
        $venueOpen = substr($field->venue?->jam_buka ?? '08:00:00', 0, 5);
        $venueClose = substr($field->venue?->jam_tutup ?? '22:00:00', 0, 5);

        $rows = [];
        for ($day = 1; $day <= 7; $day++) {
            $existing = $field->schedules->firstWhere('hari', $day);
            $isOpen = $existing ? ($existing->status_hari === 'buka') : true;
            $buka = $existing && $existing->jam_buka ? substr($existing->jam_buka, 0, 5) : $venueOpen;
            $tutup = $existing && $existing->jam_tutup ? substr($existing->jam_tutup, 0, 5) : $venueClose;
            $durasi = $existing && $existing->durasi ? substr($existing->durasi, 0, 5) : '01:00';

            $rows[$day] = [
                'hari' => $day,
                'nama_hari' => FieldSchedule::DAYS[$day] ?? "Hari {$day}",
                'is_open' => $isOpen,
                'jam_buka' => $buka,
                'jam_tutup' => $tutup,
                'durasi' => $durasi,
            ];
        }

        $this->schedules = $rows;
    }

    public function copyMondayToAll(): void
    {
        if (! isset($this->schedules[1])) {
            return;
        }

        $monday = $this->schedules[1];
        for ($day = 2; $day <= 7; $day++) {
            $this->schedules[$day]['is_open'] = (bool) $monday['is_open'];
            $this->schedules[$day]['jam_buka'] = $monday['jam_buka'];
            $this->schedules[$day]['jam_tutup'] = $monday['jam_tutup'];
            $this->schedules[$day]['durasi'] = $monday['durasi'];
        }

        $this->notify('Pengaturan hari Senin berhasil disalin ke Selasa s.d. Minggu. Klik Simpan untuk menyimpan perubahan.', 'info');
    }

    public function saveSchedules(): void
    {
        $field = $this->selectedField();
        if (! $field) {
            return;
        }

        $this->ensureOwnsField($field);
        $this->resetValidation();

        $venueOpen = substr($field->venue?->jam_buka ?? '06:00:00', 0, 5);
        $venueClose = substr($field->venue?->jam_tutup ?? '23:59:00', 0, 5);

        $hasError = false;

        foreach ($this->schedules as $day => $sched) {
            $hari = (int) ($sched['hari'] ?? $day);
            $namaHari = FieldSchedule::DAYS[$hari] ?? "Hari {$hari}";
            $isOpen = (bool) ($sched['is_open'] ?? false);

            if ($isOpen) {
                $open = $sched['jam_buka'] ?? '';
                $close = $sched['jam_tutup'] ?? '';

                if (! preg_match('/^\d{2}:\d{2}$/', $open) || ! preg_match('/^\d{2}:\d{2}$/', $close)) {
                    $this->addError("schedules.{$day}.jam_buka", "Jam buka dan jam tutup wajib diisi format JJ:MM untuk hari {$namaHari}.");
                    $hasError = true;
                    continue;
                }

                if ($close <= $open) {
                    $this->addError("schedules.{$day}.jam_tutup", "Jam tutup harus lebih malam daripada jam buka untuk hari {$namaHari}.");
                    $hasError = true;
                }

                // KS-14: Validasi jam buka lapangan berada dalam jam buka venue induk
                if ($open < $venueOpen || $close > $venueClose) {
                    $this->addError(
                        "schedules.{$day}.jam_buka",
                        "Jam operasional {$namaHari} ({$open}–{$close}) berada di luar jam buka venue ({$venueOpen}–{$venueClose})."
                    );
                    $hasError = true;
                }
            }
        }

        if ($hasError) {
            $this->notify('Periksa kembali jadwal yang masih bermasalah.', 'error');

            return;
        }

        foreach ($this->schedules as $day => $sched) {
            $isOpen = (bool) ($sched['is_open'] ?? false);
            $status = $isOpen ? 'buka' : 'tutup';
            $durasi = $sched['durasi'] ?? '01:00';
            if (strlen($durasi) === 5) {
                $durasi .= ':00';
            }

            $open = $sched['jam_buka'] ?? '08:00';
            $close = $sched['jam_tutup'] ?? '22:00';

            FieldSchedule::updateOrCreate(
                [
                    'field_id' => $field->id,
                    'hari' => (int) ($sched['hari'] ?? $day),
                ],
                [
                    'status_hari' => $status,
                    'jam_buka' => $isOpen ? (strlen($open) === 5 ? $open.':00' : $open) : null,
                    'jam_tutup' => $isOpen ? (strlen($close) === 5 ? $close.':00' : $close) : null,
                    'durasi' => $isOpen ? $durasi : '01:00:00',
                ]
            );
        }

        unset($this->selectedField, $this->slotPreview);
        $this->notify("Jadwal operasional mingguan untuk '{$field->nama_lapangan}' berhasil disimpan.", 'success');
    }

    // ---- Modal Tambah & Hapus Blokir / Maintenance (LW-4.2) ----

    public function openBlockModal(): void
    {
        $field = $this->selectedField();
        if (! $field) {
            return;
        }
        $this->ensureOwnsField($field);

        $defaultDate = $this->selectedDate >= now()->toDateString()
            ? $this->selectedDate
            : now()->toDateString();

        $this->block_tanggal_mulai = $defaultDate;
        $this->block_tanggal_selesai = $defaultDate;
        $this->block_jam_mulai = '08:00';
        $this->block_jam_selesai = '12:00';
        $this->block_alasan = '';

        $this->resetValidation();
        $this->showBlockModal = true;
    }

    public function closeBlockModal(): void
    {
        $this->showBlockModal = false;
        $this->resetValidation();
    }

    public function storeBlock(): void
    {
        $field = $this->selectedField();
        if (! $field) {
            return;
        }
        $this->ensureOwnsField($field);

        $validated = $this->validate([
            'block_tanggal_mulai' => ['required', 'date', 'after_or_equal:today'],
            'block_tanggal_selesai' => ['required', 'date', 'after_or_equal:block_tanggal_mulai'],
            'block_jam_mulai' => ['required', 'date_format:H:i'],
            'block_jam_selesai' => ['required', 'date_format:H:i', 'after:block_jam_mulai'],
            'block_alasan' => ['nullable', 'string', 'max:255'],
        ], [
            'block_tanggal_mulai.required' => 'Tanggal mulai blokir wajib diisi.',
            'block_tanggal_mulai.after_or_equal' => 'Tanggal mulai blokir tidak boleh tanggal lampau.',
            'block_tanggal_selesai.required' => 'Tanggal selesai blokir wajib diisi.',
            'block_tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            'block_jam_mulai.required' => 'Jam mulai blokir wajib diisi.',
            'block_jam_selesai.required' => 'Jam selesai blokir wajib diisi.',
            'block_jam_selesai.after' => 'Jam selesai harus lebih akhir daripada jam mulai.',
            'block_alasan.max' => 'Alasan pemblokiran maksimal 255 karakter.',
        ]);

        // T6.4 Proteksi Konflik Blokir vs Booking aktif / confirmed (OQ-013)
        $conflictingBookings = BookingDetail::with('header.user')
            ->where('field_id', $field->id)
            ->whereBetween(DB::raw('DATE(tanggal_sewa)'), [$validated['block_tanggal_mulai'], $validated['block_tanggal_selesai']])
            ->where(function ($q) use ($validated) {
                $q->where('jam_mulai', '<', $validated['block_jam_selesai'])
                    ->where('jam_selesai', '>', $validated['block_jam_mulai']);
            })
            ->whereHas('header', function ($q) {
                $q->whereNotIn('status', ['canceled', 'expired']);
            })
            ->get();

        if ($conflictingBookings->isNotEmpty()) {
            $count = $conflictingBookings->count();
            $first = $conflictingBookings->first();
            $tgl = Carbon::parse($first->tanggal_sewa)->translatedFormat('d M Y');
            $jam = substr($first->jam_mulai, 0, 5).'–'.substr($first->jam_selesai, 0, 5);
            $nama = $first->header?->user?->name ?? 'Penyewa';

            $this->addError(
                'block_jam_mulai',
                "Tidak dapat memblokir jadwal ini karena terdapat {$count} reservasi aktif yang bentrok (Contoh: {$nama} pada {$tgl} jam {$jam}). Silakan selesaikan atau jadwalkan ulang reservasi terlebih dahulu."
            );

            return;
        }

        FieldBlock::create([
            'field_id' => $field->id,
            'tanggal_mulai' => $validated['block_tanggal_mulai'],
            'tanggal_selesai' => $validated['block_tanggal_selesai'],
            'jam_mulai' => $validated['block_jam_mulai'].':00',
            'jam_selesai' => $validated['block_jam_selesai'].':00',
            'alasan_pemblokiran' => $validated['block_alasan'] ?: null,
        ]);

        $this->selectedDate = $validated['block_tanggal_mulai'];
        $this->closeBlockModal();
        unset($this->blocks, $this->slotPreview);
        $this->notify('Blokir jadwal pemeliharaan berhasil ditambahkan.', 'success');
    }

    public function confirmDeleteBlock(int $blockId): void
    {
        $field = $this->selectedField();
        if (! $field) {
            return;
        }
        $this->ensureOwnsField($field);

        $block = FieldBlock::where('field_id', $field->id)->findOrFail($blockId);

        $tglMulai = Carbon::parse($block->tanggal_mulai)->translatedFormat('d M Y');
        $tglSelesai = Carbon::parse($block->tanggal_selesai)->translatedFormat('d M Y');
        $dateText = $block->tanggal_mulai === $block->tanggal_selesai ? $tglMulai : "{$tglMulai} – {$tglSelesai}";
        $timeText = substr($block->jam_mulai, 0, 5).'–'.substr($block->jam_selesai, 0, 5).' WIB';

        $this->confirmDeleteBlockId = $block->id;
        $this->confirmDeleteBlockInfo = "{$dateText} ({$timeText})";
        $this->showDeleteBlockModal = true;
    }

    public function closeDeleteBlockModal(): void
    {
        $this->showDeleteBlockModal = false;
        $this->confirmDeleteBlockId = null;
        $this->confirmDeleteBlockInfo = '';
    }

    public function executeDeleteBlock(): void
    {
        $field = $this->selectedField();
        if (! $field || ! $this->confirmDeleteBlockId) {
            $this->closeDeleteBlockModal();

            return;
        }

        $this->ensureOwnsField($field);

        $block = FieldBlock::where('field_id', $field->id)->findOrFail($this->confirmDeleteBlockId);
        $block->delete();

        $this->closeDeleteBlockModal();
        unset($this->blocks, $this->slotPreview);
        $this->notify('Blokir jadwal berhasil dihapus. Slot kembali aktif sesuai jadwal reguler.', 'success');
    }

    public function render(): View
    {
        return view('livewire.mitra.schedules.schedule-manager', [
            'venuesList' => $this->venues(),
            'allFieldsList' => $this->allFields(),
            'venueFieldsList' => $this->venueFields(),
            'currentField' => $this->selectedField(),
            'activeBlocks' => $this->blocks(),
            'previewData' => $this->slotPreview(),
        ]);
    }
}
