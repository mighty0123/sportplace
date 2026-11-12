<?php

namespace App\Http\Requests\Mitra;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateFieldRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * BR-023: Hanya pemilik venue yang dapat memperbarui unit lapangan miliknya.
     */
    public function authorize(): bool
    {
        $field = $this->route('field');

        return $field && Gate::allows('update', $field);
    }

    /**
     * Get the validation rules that apply to the request.
     * FR-FIELD-003
     */
    public function rules(): array
    {
        return [
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
            'primary_photo_id' => ['nullable', 'integer'],
            'primary_new_photo_index' => ['nullable', 'integer', 'min:0', 'max:7'],
            'deleted_photos' => ['nullable', 'array'],
            'deleted_photos.*' => ['integer', 'exists:field_photo,id'],
            'action' => ['nullable', 'string', 'in:draft,submit'],
        ];
    }

    public function messages(): array
    {
        return [
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
