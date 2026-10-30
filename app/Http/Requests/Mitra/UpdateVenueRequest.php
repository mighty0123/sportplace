<?php

namespace App\Http\Requests\Mitra;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVenueRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * BR-023: Hanya pemilik venue yang dapat memperbarui data venue miliknya.
     */
    public function authorize(): bool
    {
        $venue = $this->route('venue');
        return $venue && $this->user()?->can('update', $venue);
    }

    /**
     * Get the validation rules that apply to the request.
     * FR-VENUE-003
     */
    public function rules(): array
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
            'action' => ['nullable', 'string', 'in:draft,submit'],
        ];
    }

    /**
     * Custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'nama_venue.required' => 'Nama venue wajib diisi.',
            'nama_venue.max' => 'Nama venue maksimal 200 karakter.',
            'city_id.required' => 'Kota/wilayah venue wajib dipilih.',
            'city_id.exists' => 'Kota yang dipilih tidak aktif atau tidak ditemukan.',
            'alamat_lengkap.required' => 'Alamat lengkap lokasi venue wajib diisi.',
            'map_url.url' => 'Format tautan Google Maps tidak valid.',
            'map_url.max' => 'Tautan Google Maps maksimal 255 karakter.',
            'jam_buka.required' => 'Jam buka operasional wajib diisi.',
            'jam_buka.date_format' => 'Format jam buka harus JJ:MM (contoh 08:00).',
            'jam_tutup.required' => 'Jam tutup operasional wajib diisi.',
            'jam_tutup.date_format' => 'Format jam tutup harus JJ:MM (contoh 23:00).',
            'jam_tutup.after' => 'Jam tutup operasional harus lebih lambat dari jam buka.',
            'deskripsi.max' => 'Deskripsi venue maksimal 1000 karakter.',
        ];
    }
}

