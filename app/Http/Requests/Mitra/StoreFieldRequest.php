<?php

namespace App\Http\Requests\Mitra;

use App\Models\Venue;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFieldRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * BR-002: Hanya mitra approved yang dapat membuat unit lapangan.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        if (! $user || $user->role !== 'mitra' || ! $user->isMitraApproved()) {
            return false;
        }

        $venueId = $this->input('venue_id') ?? $this->route('venue')?->id;
        if (! $venueId) {
            return false;
        }

        $venue = Venue::where('id', $venueId)
            ->where('mitra_id', $user->mitraProfile?->id)
            ->first();

        return $venue !== null;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if (! $this->has('venue_id') && $this->route('venue')) {
            $venue = $this->route('venue');
            $this->merge([
                'venue_id' => is_object($venue) ? $venue->id : $venue,
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     * FR-FIELD-001 & BR-031
     */
    public function rules(): array
    {
        return [
            'venue_id' => [
                'required',
                'integer',
                Rule::exists('venues', 'id')->where(function ($query) {
                    $query->where('mitra_id', $this->user()->mitraProfile?->id);
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
            'facility_ids.*' => ['integer', 'exists:master_facilities,id'],
            'photos' => ['nullable', 'array', 'max:8'],
            'photos.*' => ['file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'primary_photo_index' => ['nullable', 'integer', 'min:0', 'max:7'],
            'action' => ['nullable', 'string', 'in:draft,submit'],
        ];
    }

    public function messages(): array
    {
        return [
            'venue_id.required' => 'Pilihan venue induk wajib disertakan.',
            'venue_id.exists' => 'Venue tidak valid atau bukan milik akun Anda.',
            'nama_lapangan.required' => 'Nama lapangan wajib diisi.',
            'nama_lapangan.max' => 'Nama lapangan maksimal 100 karakter.',
            'sport_id.required' => 'Cabang olahraga wajib dipilih.',
            'sport_id.exists' => 'Cabang olahraga yang dipilih tidak aktif atau tidak ditemukan.',
            'harga_perjam.required' => 'Tarif sewa per jam wajib diisi.',
            'harga_perjam.numeric' => 'Tarif sewa per jam harus berupa nominal angka.',
            'harga_perjam.min' => 'Tarif sewa per jam minimal Rp 1.000.',
            'tipe_bangunan.required' => 'Tipe bangunan / sirkulasi wajib dipilih.',
            'jenis_permukaan.required' => 'Jenis permukaan lapangan wajib diisi atau dipilih.',
            'photos.max' => 'Maksimal 8 foto fasilitas lapangan yang dapat diunggah.',
            'photos.*.mimes' => 'Format foto lapangan harus berupa JPG, PNG, atau WebP.',
            'photos.*.max' => 'Ukuran setiap foto lapangan maksimal 5 MB.',
        ];
    }
}
