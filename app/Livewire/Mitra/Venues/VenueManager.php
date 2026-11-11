<?php

namespace App\Livewire\Mitra\Venues;

use App\Http\Requests\Mitra\StoreVenueRequest;
use App\Livewire\Mitra\MitraComponent;
use App\Models\BookingDetail;
use App\Models\MasterCity;
use App\Models\Venue;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;

class VenueManager extends MitraComponent
{
    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'kota')]
    public string $cityId = '';

    #[Url(as: 'status', history: true)]
    public string $status = 'semua';

    // Modal Form State (Tambah / Edit Venue)
    public bool $showFormModal = false;

    public ?int $editingVenueId = null;

    public string $nama_venue = '';

    public string $city_id = '';

    public string $alamat_lengkap = '';

    public string $map_url = '';

    public string $jam_buka = '08:00';

    public string $jam_tutup = '22:00';

    public string $deskripsi = '';

    // Modal Konfirmasi Aksi (Ajukan / Nonaktifkan / Aktifkan / Hapus)
    public bool $showConfirmModal = false;

    public ?int $confirmVenueId = null;

    public string $confirmAction = ''; // 'submit', 'deactivate', 'activate', 'delete'

    public string $confirmVenueName = '';

    public function mount(): void
    {
        $this->authorizeMitraApproved();
    }

    public function setStatusFilter(string $status): void
    {
        if (in_array($status, ['semua', 'aktif', 'pending_review', 'rejected', 'draft', 'nonaktif'], true)) {
            $this->status = $status;
        }
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->cityId = '';
        $this->status = 'semua';
    }

    #[Computed]
    public function stats(): array
    {
        $profile = $this->authorizeMitraApproved();
        $all = Venue::where('mitra_id', $profile->id)->get(['id', 'status_vanue']);

        return [
            'total' => $all->count(),
            'aktif' => $all->where('status_vanue', 'aktif')->count(),
            'pending_review' => $all->where('status_vanue', 'pending_review')->count(),
            'rejected' => $all->where('status_vanue', 'rejected')->count(),
            'draft' => $all->where('status_vanue', 'draft')->count(),
            'nonaktif' => $all->where('status_vanue', 'nonaktif')->count(),
        ];
    }

    #[Computed]
    public function venues(): Collection
    {
        $profile = $this->authorizeMitraApproved();

        $query = Venue::with(['city', 'fields.sport'])
            ->where('mitra_id', $profile->id);

        if ($this->search !== '') {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_venue', 'like', "%{$search}%")
                    ->orWhere('alamat_lengkap', 'like', "%{$search}%");
            });
        }

        if ($this->cityId !== '') {
            $query->where('city_id', $this->cityId);
        }

        if ($this->status !== 'semua') {
            $query->where('status_vanue', $this->status);
        }

        return $query->orderByDesc('id')->get();
    }

    #[Computed]
    public function cities(): Collection
    {
        return MasterCity::where('status_kota', 'aktif')->orderBy('nama_kota')->get();
    }

    // ---- Form Tambah / Edit Venue ----

    public function openCreateModal(): void
    {
        $this->authorizeMitraApproved();

        $this->editingVenueId = null;
        $this->nama_venue = '';
        $this->city_id = (string) ($this->cities()->first()?->id ?? '');
        $this->alamat_lengkap = '';
        $this->map_url = '';
        $this->jam_buka = '08:00';
        $this->jam_tutup = '22:00';
        $this->deskripsi = '';

        $this->resetValidation();
        $this->showFormModal = true;
    }

    public function openEditModal(int $venueId): void
    {
        $venue = Venue::findOrFail($venueId);
        $this->ensureOwnsVenue($venue);

        $this->editingVenueId = $venue->id;
        $this->nama_venue = (string) $venue->nama_venue;
        $this->city_id = (string) $venue->city_id;
        $this->alamat_lengkap = (string) $venue->alamat_lengkap;
        $this->map_url = (string) ($venue->map_url ?? '');
        $this->jam_buka = substr((string) $venue->jam_buka, 0, 5);
        $this->jam_tutup = substr((string) $venue->jam_tutup, 0, 5);
        $this->deskripsi = (string) ($venue->deskripsi ?? '');

        $this->resetValidation();
        $this->showFormModal = true;
    }

    public function closeFormModal(): void
    {
        $this->showFormModal = false;
        $this->resetValidation();
    }

    /**
     * Aturan validasi venue sesuai StoreVenueRequest.
     *
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'nama_venue' => ['required', 'string', 'max:200'],
            'city_id' => [
                'required',
                'integer',
                Rule::exists('master_cities', 'id')->where(function ($query) {
                    $query->where('status_kota', 'aktif');
                }),
            ],
            'alamat_lengkap' => ['required', 'string'],
            'map_url' => ['nullable', 'url', 'max:255'],
            'jam_buka' => ['required', 'date_format:H:i'],
            'jam_tutup' => ['required', 'date_format:H:i', 'after:jam_buka'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Pesan error Bahasa Indonesia.
     *
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return (new StoreVenueRequest())->messages();
    }

    public function updated(string $propertyName): void
    {
        if ($this->showFormModal && in_array($propertyName, [
            'nama_venue', 'city_id', 'alamat_lengkap', 'map_url', 'jam_buka', 'jam_tutup', 'deskripsi',
        ], true)) {
            $this->validateOnly($propertyName);
        }
    }

    public function saveVenue(string $action = 'draft'): void
    {
        $profile = $this->authorizeMitraApproved();
        $validated = $this->validate();

        $data = [
            'city_id' => $validated['city_id'],
            'nama_venue' => $validated['nama_venue'],
            'alamat_lengkap' => $validated['alamat_lengkap'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'map_url' => $validated['map_url'] ?? null,
            'jam_buka' => $validated['jam_buka'],
            'jam_tutup' => $validated['jam_tutup'],
        ];

        if ($this->editingVenueId) {
            $venue = Venue::findOrFail($this->editingVenueId);
            $this->ensureOwnsVenue($venue);

            if ($action === 'submit' && in_array($venue->status_vanue, ['draft', 'rejected'], true)) {
                $data['status_vanue'] = 'pending_review';
                $data['alasan_penolakan'] = null;
                $venue->update($data);
                $msg = "Venue '{$venue->nama_venue}' berhasil diperbarui dan diajukan untuk peninjauan.";
            } else {
                $venue->update($data);
                $msg = "Data venue '{$venue->nama_venue}' berhasil diperbarui.";
            }
        } else {
            $status = ($action === 'submit') ? 'pending_review' : 'draft';
            $venue = Venue::create(array_merge($data, [
                'mitra_id' => $profile->id,
                'status_vanue' => $status,
                'alasan_penolakan' => null,
            ]));

            $msg = $status === 'pending_review'
                ? "Venue '{$venue->nama_venue}' berhasil disimpan dan diajukan untuk ditinjau Admin."
                : "Venue '{$venue->nama_venue}' berhasil disimpan sebagai draft.";
        }

        $this->closeFormModal();
        unset($this->venues, $this->stats);
        $this->notify($msg, 'success');
    }

    // ---- Modal Konfirmasi Aksi ----

    public function confirmActionModal(int $venueId, string $action): void
    {
        $venue = Venue::findOrFail($venueId);
        $this->ensureOwnsVenue($venue);

        $this->confirmVenueId = $venue->id;
        $this->confirmAction = $action;
        $this->confirmVenueName = (string) $venue->nama_venue;
        $this->showConfirmModal = true;
    }

    public function closeConfirmModal(): void
    {
        $this->showConfirmModal = false;
        $this->confirmVenueId = null;
        $this->confirmAction = '';
        $this->confirmVenueName = '';
    }

    public function executeConfirmedAction(): void
    {
        if (! $this->confirmVenueId || ! $this->confirmAction) {
            $this->closeConfirmModal();

            return;
        }

        $venue = Venue::findOrFail($this->confirmVenueId);
        $this->ensureOwnsVenue($venue);

        switch ($this->confirmAction) {
            case 'submit':
                if (! in_array($venue->status_vanue, ['draft', 'rejected'], true)) {
                    $this->notify('Hanya venue berstatus draft atau ditolak yang dapat diajukan.', 'error');
                    break;
                }
                $venue->update(['status_vanue' => 'pending_review', 'alasan_penolakan' => null]);
                $this->notify("Venue '{$venue->nama_venue}' berhasil diajukan untuk peninjauan Admin.");
                break;

            case 'deactivate':
                if ($venue->status_vanue !== 'aktif') {
                    $this->notify('Hanya venue berstatus aktif yang dapat dinonaktifkan.', 'error');
                    break;
                }
                $fieldIds = $venue->fields()->pluck('id');
                $activeUpcomingBookings = BookingDetail::whereIn('field_id', $fieldIds)
                    ->whereDate('tanggal_sewa', '>=', now()->toDateString())
                    ->whereIn('status_item', ['confirmed', 'paid', 'checked_in'])
                    ->count();

                if ($activeUpcomingBookings > 0) {
                    $this->notify("Tidak dapat menonaktifkan venue karena masih terdapat {$activeUpcomingBookings} pesanan aktif mendatang.", 'error');
                    break;
                }

                $venue->update(['status_vanue' => 'nonaktif']);
                $this->notify("Venue '{$venue->nama_venue}' berhasil dinonaktifkan.");
                break;

            case 'activate':
                if ($venue->status_vanue !== 'nonaktif') {
                    $this->notify('Hanya venue berstatus nonaktif yang dapat diaktifkan kembali.', 'error');
                    break;
                }
                $venue->update(['status_vanue' => 'aktif']);
                $this->notify("Venue '{$venue->nama_venue}' berhasil diaktifkan kembali.");
                break;

            case 'delete':
                if ($venue->status_vanue !== 'draft') {
                    $this->notify('Hanya venue berstatus draft yang dapat dihapus.', 'error');
                    break;
                }
                $nama = $venue->nama_venue;
                $venue->delete();
                $this->notify("Draft venue '{$nama}' berhasil dihapus.");
                break;
        }

        $this->closeConfirmModal();
        unset($this->venues, $this->stats);
    }

    public function render(): View
    {
        return view('livewire.mitra.venues.venue-manager', [
            'venuesList' => $this->venues(),
            'venueStats' => $this->stats(),
            'citiesList' => $this->cities(),
        ]);
    }
}
