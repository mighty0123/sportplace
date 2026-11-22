<?php

namespace App\Livewire\Mitra\Fields;

use App\Http\Requests\Mitra\StoreFieldRequest;
use App\Livewire\Mitra\MitraComponent;
use App\Models\BookingDetail;
use App\Models\Field;
use App\Models\FieldPhoto;
use App\Models\MasterFacility;
use App\Models\MasterSport;
use App\Models\Venue;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\WithFileUploads;

class FieldManager extends MitraComponent
{
    use WithFileUploads;

    public ?int $venueId = null;

    public bool $showAllVenues = false;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'status', history: true)]
    public string $status = 'semua';

    #[Url(as: 'cabor')]
    public string $sportId = 'semua';

    // Modal Form State (Tambah / Edit Lapangan)
    public bool $showFormModal = false;

    public ?int $editingFieldId = null;

    public string $form_venue_id = '';

    public string $nama_lapangan = '';

    public string $sport_id = '';

    public string $harga_perjam = '';

    public string $tipe_bangunan = 'indoor';

    public string $jenis_permukaan = '';

    public string $panjang = '';

    public string $lebar = '';

    public string $kapasitas = '';

    public string $deskripsi = '';

    public string $aturan_lapangan = '';

    public array $facility_ids = [];

    // Manajemen Foto (LW-3.2)
    public $photos = [];

    public array $existingPhotos = [];

    public int $newPrimaryIndex = 0;

    // Modal Konfirmasi Aksi (Ajukan / Toggle Status / Hapus Draft)
    public bool $showConfirmModal = false;

    public ?int $confirmFieldId = null;

    public string $confirmAction = ''; // 'submit', 'toggle_status', 'delete'

    public string $confirmFieldName = '';

    public string $confirmCurrentStatus = '';

    public function mount(?int $venueId = null, bool $showAllVenues = false): void
    {
        $this->authorizeMitraApproved();
        $this->venueId = $venueId;
        $this->showAllVenues = $showAllVenues || is_null($venueId);

        if ($venueId) {
            $venue = Venue::findOrFail($venueId);
            $this->ensureOwnsVenue($venue);
        }
    }

    public function toggleCrossVenue(): void
    {
        $this->showAllVenues = ! $this->showAllVenues;
        if (! $this->showAllVenues && ! $this->venueId) {
            $firstVenue = $this->mitraVenues()->first();
            $this->venueId = $firstVenue?->id;
        }
        unset($this->fields, $this->stats, $this->currentVenue);
    }

    public function setSportFilter(string $sportId): void
    {
        $this->sportId = $sportId;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->status = 'semua';
        $this->sportId = 'semua';
    }

    #[Computed]
    public function currentVenue(): ?Venue
    {
        if ($this->showAllVenues || ! $this->venueId) {
            return null;
        }

        return Venue::with('city')->find($this->venueId);
    }

    #[Computed]
    public function mitraVenues(): Collection
    {
        $profile = $this->authorizeMitraApproved();

        return Venue::where('mitra_id', $profile->id)
            ->orderBy('nama_venue')
            ->get();
    }

    #[Computed]
    public function sports(): Collection
    {
        return MasterSport::where('status_olahraga', 'aktif')
            ->orderBy('nama_olahraga')
            ->get();
    }

    #[Computed]
    public function allFacilities(): Collection
    {
        return MasterFacility::where('status_fasilitas', 'aktif')
            ->orderBy('nama')
            ->get();
    }

    #[Computed]
    public function stats(): array
    {
        $profile = $this->authorizeMitraApproved();
        $venueIds = $this->showAllVenues || ! $this->venueId
            ? Venue::where('mitra_id', $profile->id)->pluck('id')
            : collect([$this->venueId]);

        $all = Field::whereIn('venue_id', $venueIds)->get(['id', 'status_lapangan']);

        return [
            'total' => $all->count(),
            'aktif' => $all->where('status_lapangan', 'aktif')->count(),
            'pending_review' => $all->where('status_lapangan', 'pending_review')->count(),
            'rejected' => $all->where('status_lapangan', 'rejected')->count(),
            'draft' => $all->where('status_lapangan', 'draft')->count(),
            'nonaktif' => $all->where('status_lapangan', 'nonaktif')->count(),
        ];
    }

    #[Computed]
    public function fields(): Collection
    {
        $profile = $this->authorizeMitraApproved();
        $venueIds = $this->showAllVenues || ! $this->venueId
            ? Venue::where('mitra_id', $profile->id)->pluck('id')
            : collect([$this->venueId]);

        $query = Field::with(['sport', 'photos', 'facilities', 'venue.city'])
            ->whereIn('venue_id', $venueIds);

        if ($this->search !== '') {
            $query->where('nama_lapangan', 'like', "%{$this->search}%");
        }

        if ($this->status !== 'semua') {
            $query->where('status_lapangan', $this->status);
        }

        if ($this->sportId !== 'semua') {
            $query->where('sport_id', $this->sportId);
        }

        return $query->orderByDesc('id')->get();
    }

    // ---- Modal Form Tambah / Edit Lapangan ----

    public function openCreateModal(): void
    {
        $this->authorizeMitraApproved();

        $this->editingFieldId = null;
        $this->form_venue_id = (string) ($this->venueId ?? $this->mitraVenues()->first()?->id ?? '');
        $this->nama_lapangan = '';
        $this->sport_id = (string) ($this->sports()->first()?->id ?? '');
        $this->harga_perjam = '';
        $this->tipe_bangunan = 'indoor';
        $this->jenis_permukaan = '';
        $this->panjang = '';
        $this->lebar = '';
        $this->kapasitas = '';
        $this->deskripsi = '';
        $this->aturan_lapangan = '';
        $this->facility_ids = [];
        $this->photos = [];
        $this->existingPhotos = [];
        $this->newPrimaryIndex = 0;

        $this->resetValidation();
        $this->showFormModal = true;
    }

    public function openEditModal(int $fieldId): void
    {
        $field = Field::with(['photos', 'facilities', 'venue'])->findOrFail($fieldId);
        $this->ensureOwnsField($field);

        $this->editingFieldId = $field->id;
        $this->form_venue_id = (string) $field->venue_id;
        $this->nama_lapangan = (string) $field->nama_lapangan;
        $this->sport_id = (string) $field->sport_id;
        $this->harga_perjam = (string) $field->harga_perjam;
        $this->tipe_bangunan = (string) ($field->tipe_bangunan ?? 'indoor');
        $this->jenis_permukaan = (string) ($field->jenis_permukaan ?? '');
        $this->panjang = $field->panjang ? (string) $field->panjang : '';
        $this->lebar = $field->lebar ? (string) $field->lebar : '';
        $this->kapasitas = (string) ($field->kapasitas ?? '');
        $this->deskripsi = (string) ($field->deskripsi ?? '');
        $this->aturan_lapangan = (string) ($field->aturan_lapangan ?? '');
        $this->facility_ids = $field->facilities->pluck('id')->map(fn ($id) => (string) $id)->toArray();

        $this->photos = [];
        $this->newPrimaryIndex = 0;
        $this->loadExistingPhotos($field);

        $this->resetValidation();
        $this->showFormModal = true;
    }

    protected function loadExistingPhotos(Field $field): void
    {
        $field->load('photos');
        $this->existingPhotos = $field->photos->map(function ($p) {
            $url = str_starts_with($p->foto_url, 'http')
                ? $p->foto_url
                : asset('storage/'.$p->foto_url);

            return [
                'id' => $p->id,
                'foto_url' => $url,
                'is_primary' => (bool) $p->is_primary,
            ];
        })->values()->toArray();
    }

    public function closeFormModal(): void
    {
        $this->showFormModal = false;
        $this->photos = [];
        $this->resetValidation();
    }

    // ---- Manajemen Foto Interaktif Seketika (LW-3.2) ----

    public function removeNewPhoto(int $index): void
    {
        if (isset($this->photos[$index])) {
            unset($this->photos[$index]);
            $this->photos = array_values($this->photos);

            if ($this->newPrimaryIndex >= count($this->photos)) {
                $this->newPrimaryIndex = 0;
            }
        }
    }

    public function setPrimaryNewPhoto(int $index): void
    {
        if (isset($this->photos[$index])) {
            $this->newPrimaryIndex = $index;
        }
    }

    public function setPrimaryExistingPhoto(int $photoId): void
    {
        if (! $this->editingFieldId) {
            return;
        }

        $field = Field::findOrFail($this->editingFieldId);
        $this->ensureOwnsField($field);

        $photo = FieldPhoto::where('field_id', $field->id)->where('id', $photoId)->firstOrFail();

        FieldPhoto::where('field_id', $field->id)->update(['is_primary' => false]);
        $photo->update(['is_primary' => true]);

        $this->loadExistingPhotos($field);
        unset($this->fields);
        $this->notify('Foto utama lapangan berhasil diperbarui.', 'success');
    }

    public function deleteExistingPhoto(int $photoId): void
    {
        if (! $this->editingFieldId) {
            return;
        }

        $field = Field::findOrFail($this->editingFieldId);
        $this->ensureOwnsField($field);

        $photo = FieldPhoto::where('field_id', $field->id)->where('id', $photoId)->firstOrFail();
        $wasPrimary = (bool) $photo->is_primary;

        if ($photo->foto_url && ! str_starts_with($photo->foto_url, 'http')) {
            Storage::disk('public')->delete($photo->foto_url);
        }

        $photo->delete();

        if ($wasPrimary) {
            $field->photos()->first()?->update(['is_primary' => true]);
        }

        $this->loadExistingPhotos($field);
        unset($this->fields);
        $this->notify('Foto lapangan berhasil dihapus.', 'success');
    }

    /**
     * Validasi form lapangan.
     *
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        $profile = $this->authorizeMitraApproved();

        return [
            'form_venue_id' => [
                'required',
                'integer',
                Rule::exists('venues', 'id')->where(function ($query) use ($profile) {
                    $query->where('mitra_id', $profile->id);
                }),
            ],
            'nama_lapangan' => ['required', 'string', 'max:100'],
            'sport_id' => [
                'required',
                'integer',
                Rule::exists('master_sports', 'id')->where(function ($query) {
                    $query->where('status_olahraga', 'aktif');
                }),
            ],
            'harga_perjam' => ['required', 'numeric', 'min:1000'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
            'tipe_bangunan' => ['required', 'string', 'in:indoor,semi_outdoor,outdoor'],
            'jenis_permukaan' => ['required', 'string', 'max:100'],
            'panjang' => ['nullable', 'numeric', 'min:1', 'max:200'],
            'lebar' => ['nullable', 'numeric', 'min:1', 'max:200'],
            'kapasitas' => ['nullable', 'string', 'max:50'],
            'aturan_lapangan' => ['nullable', 'string', 'max:2000'],
            'facility_ids' => ['nullable', 'array'],
            'photos.*' => ['file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    /**
     * Pesan validasi dalam Bahasa Indonesia.
     *
     * @return array<string, string>
     */
    protected function messages(): array
    {
        $base = (new StoreFieldRequest())->messages();

        return array_merge($base, [
            'form_venue_id.required' => 'Venue induk wajib dipilih.',
            'form_venue_id.exists' => 'Venue induk tidak valid.',
            'photos.*.mimes' => 'Format foto harus JPG, PNG, atau WEBP.',
            'photos.*.max' => 'Ukuran setiap foto maksimal 5 MB.',
        ]);
    }

    public function updated(string $propertyName): void
    {
        if (str_starts_with($propertyName, 'photos')) {
            $this->validateOnly('photos.*');

            return;
        }

        if ($this->showFormModal && in_array($propertyName, [
            'form_venue_id', 'nama_lapangan', 'sport_id', 'harga_perjam', 'tipe_bangunan', 'jenis_permukaan', 'panjang', 'lebar',
        ], true)) {
            $this->validateOnly($propertyName);
        }
    }

    public function saveField(string $action = 'draft'): void
    {
        $this->authorizeMitraApproved();
        $validated = $this->validate();

        $venue = Venue::findOrFail($validated['form_venue_id']);
        $this->ensureOwnsVenue($venue);

        $existingCount = count($this->existingPhotos);
        $newCount = is_array($this->photos) ? count($this->photos) : 0;
        $totalPhotos = $existingCount + $newCount;

        if ($totalPhotos > 8) {
            $this->addError('photos', 'Total foto lapangan maksimal adalah 8 foto.');

            return;
        }

        // Validasi pengajuan (OQ-010 & T5.3)
        if ($action === 'submit') {
            if ($venue->status_vanue !== 'aktif') {
                $this->notify("Venue '{$venue->nama_venue}' belum Aktif. Lapangan hanya bisa disimpan sebagai Draft (OQ-010).", 'error');

                return;
            }
            if ($totalPhotos < 1) {
                $this->addError('photos', 'Wajib mengunggah minimal 1 foto fasilitas lapangan untuk mengajukan peninjauan.');

                return;
            }
        }

        $data = [
            'venue_id' => $venue->id,
            'sport_id' => $validated['sport_id'],
            'nama_lapangan' => $validated['nama_lapangan'],
            'harga_perjam' => $validated['harga_perjam'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'tipe_bangunan' => $validated['tipe_bangunan'],
            'jenis_permukaan' => $validated['jenis_permukaan'],
            'panjang' => $validated['panjang'] !== '' ? $validated['panjang'] : null,
            'lebar' => $validated['lebar'] !== '' ? $validated['lebar'] : null,
            'kapasitas' => $validated['kapasitas'] ?? null,
            'aturan_lapangan' => $validated['aturan_lapangan'] ?? null,
        ];

        if ($this->editingFieldId) {
            $field = Field::findOrFail($this->editingFieldId);
            $this->ensureOwnsField($field);

            if ($action === 'submit' && in_array($field->status_lapangan, ['draft', 'rejected'], true)) {
                $data['status_lapangan'] = 'pending_review';
                $data['alasan_penolakan'] = null;
                $data['submitted_at'] = now();
                $msg = "Lapangan '{$field->nama_lapangan}' berhasil diperbarui dan diajukan untuk peninjauan.";
            } else {
                $msg = "Data lapangan '{$field->nama_lapangan}' berhasil diperbarui.";
            }

            $field->update($data);
        } else {
            $status = ($action === 'submit') ? 'pending_review' : 'draft';
            $field = Field::create(array_merge($data, [
                'status_lapangan' => $status,
                'submitted_at' => ($status === 'pending_review') ? now() : null,
            ]));

            $msg = $status === 'pending_review'
                ? "Lapangan '{$field->nama_lapangan}' berhasil dibuat dan diajukan untuk ditinjau Admin."
                : "Lapangan '{$field->nama_lapangan}' berhasil disimpan sebagai draft.";
        }

        // Sinkronisasi fasilitas
        $facilityIds = array_map('intval', array_filter($this->facility_ids));
        $field->facilities()->sync($facilityIds);

        // Simpan foto baru
        if (is_array($this->photos) && count($this->photos) > 0) {
            $hasExistingPrimary = $field->photos()->where('is_primary', true)->exists();

            foreach ($this->photos as $idx => $photoFile) {
                $path = $photoFile->store("field_photos/{$field->id}", 'public');
                $isPrimary = ! $hasExistingPrimary && ($idx === $this->newPrimaryIndex);

                FieldPhoto::create([
                    'field_id' => $field->id,
                    'foto_url' => $path,
                    'is_primary' => $isPrimary,
                ]);
            }
        }

        // Pastikan selalu ada minimal 1 foto utama bila foto tersedia
        if ($field->photos()->count() > 0 && ! $field->photos()->where('is_primary', true)->exists()) {
            $field->photos()->first()?->update(['is_primary' => true]);
        }

        $this->closeFormModal();
        unset($this->fields, $this->stats);
        $this->notify($msg, 'success');
    }

    // ---- Modal Konfirmasi Aksi (Submit / Toggle / Delete) ----

    public function confirmActionModal(int $fieldId, string $action): void
    {
        $field = Field::with('venue')->findOrFail($fieldId);
        $this->ensureOwnsField($field);

        $this->confirmFieldId = $field->id;
        $this->confirmAction = $action;
        $this->confirmFieldName = (string) $field->nama_lapangan;
        $this->confirmCurrentStatus = (string) $field->status_lapangan;
        $this->showConfirmModal = true;
    }

    public function closeConfirmModal(): void
    {
        $this->showConfirmModal = false;
        $this->confirmFieldId = null;
        $this->confirmAction = '';
        $this->confirmFieldName = '';
        $this->confirmCurrentStatus = '';
    }

    public function executeConfirmedAction(): void
    {
        if (! $this->confirmFieldId || ! $this->confirmAction) {
            $this->closeConfirmModal();

            return;
        }

        $field = Field::with('venue')->findOrFail($this->confirmFieldId);
        $this->ensureOwnsField($field);

        switch ($this->confirmAction) {
            case 'submit':
                if (! in_array($field->status_lapangan, ['draft', 'rejected'], true)) {
                    $this->notify('Hanya lapangan berstatus draft atau ditolak yang dapat diajukan.', 'error');
                    break;
                }
                if ($field->venue?->status_vanue !== 'aktif') {
                    $this->notify("Venue '{$field->venue?->nama_venue}' harus berstatus Aktif terlebih dahulu (OQ-010).", 'error');
                    break;
                }
                if ($field->photos()->count() === 0) {
                    $this->notify('Harap unggah minimal 1 foto lapangan sebelum mengajukan peninjauan.', 'error');
                    break;
                }

                $field->update([
                    'status_lapangan' => 'pending_review',
                    'alasan_penolakan' => null,
                    'submitted_at' => now(),
                ]);
                $this->notify("Lapangan '{$field->nama_lapangan}' berhasil diajukan untuk peninjauan Admin.");
                break;

            case 'toggle_status':
                if ($field->status_lapangan === 'aktif') {
                    $activeBookings = BookingDetail::where('field_id', $field->id)
                        ->whereDate('tanggal_sewa', '>=', now()->toDateString())
                        ->whereIn('status_item', ['confirmed', 'paid', 'checked_in'])
                        ->count();

                    if ($activeBookings > 0) {
                        $this->notify("Tidak dapat menonaktifkan lapangan karena masih ada {$activeBookings} pesanan aktif mendatang (OQ-012).", 'error');
                        break;
                    }

                    $field->update(['status_lapangan' => 'nonaktif']);
                    $this->notify("Lapangan '{$field->nama_lapangan}' berhasil dinonaktifkan.");
                } elseif ($field->status_lapangan === 'nonaktif') {
                    if ($field->venue?->status_vanue !== 'aktif') {
                        $this->notify("Tidak dapat mengaktifkan lapangan karena venue induk '{$field->venue?->nama_venue}' belum aktif.", 'error');
                        break;
                    }

                    $field->update(['status_lapangan' => 'aktif']);
                    $this->notify("Lapangan '{$field->nama_lapangan}' berhasil diaktifkan kembali.");
                }
                break;

            case 'delete':
                if ($field->status_lapangan !== 'draft') {
                    $this->notify('Hanya lapangan berstatus draft yang dapat dihapus.', 'error');
                    break;
                }

                foreach ($field->photos as $photo) {
                    if ($photo->foto_url && ! str_starts_with($photo->foto_url, 'http')) {
                        Storage::disk('public')->delete($photo->foto_url);
                    }
                }

                $nama = $field->nama_lapangan;
                $field->facilities()->detach();
                $field->photos()->delete();
                $field->delete();
                $this->notify("Draft lapangan '{$nama}' berhasil dihapus.");
                break;
        }

        $this->closeConfirmModal();
        unset($this->fields, $this->stats);
    }

    public function render(): View
    {
        return view('livewire.mitra.fields.field-manager', [
            'venue' => $this->currentVenue(),
            'fieldsList' => $this->fields(),
            'fieldStats' => $this->stats(),
            'sportsList' => $this->sports(),
            'facilitiesList' => $this->allFacilities(),
            'venuesOptions' => $this->mitraVenues(),
        ]);
    }
}
