<?php

namespace App\Http\Requests\Mitra;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class MitraOnboardingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === 'mitra';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $hasExistingProfile = (bool) $this->user()?->mitraProfile?->file_ktp;

        return [
            'nama_usaha' => ['required', 'string', 'max:150'],
            'bentuk_badan_usaha' => ['required', 'string', 'max:50'],
            'kota_operasional' => ['required', 'string', 'max:100'],
            'deskripsi_usaha' => ['nullable', 'string', 'max:1000'],
            'no_ktp' => ['required', 'string', 'min:16', 'max:20'],
            'npwp' => ['nullable', 'string', 'max:24'],
            'bank_nama' => ['required', 'string', 'max:50'],
            'nomor_rekening' => ['required', 'string', 'max:50'],
            'nama_rekening' => ['required', 'string', 'max:100'],
            'file_ktp' => [$hasExistingProfile ? 'nullable' : 'required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'file_npwp' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'file_nib' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'file_sewa_lahan' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'setuju_syarat' => ['accepted'],
        ];
    }

    /**
     * Custom validation messages in Indonesian.
     */
    public function messages(): array
    {
        return [
            'nama_usaha.required' => 'Nama usaha lapangan wajib diisi.',
            'bentuk_badan_usaha.required' => 'Bentuk badan usaha wajib dipilih.',
            'kota_operasional.required' => 'Kota operasional wajib diisi.',
            'no_ktp.required' => 'Nomor KTP (NIK) pemilik usaha wajib diisi.',
            'no_ktp.min' => 'Nomor KTP (NIK) minimal 16 karakter.',
            'bank_nama.required' => 'Nama bank pencairan wajib diisi.',
            'nomor_rekening.required' => 'Nomor rekening pencairan wajib diisi.',
            'nama_rekening.required' => 'Nama pemilik rekening wajib diisi.',
            'file_ktp.required' => 'Dokumen KTP asli pemilik usaha wajib diunggah.',
            'file_ktp.mimes' => 'Format berkas KTP harus berupa JPG, PNG, atau PDF.',
            'file_ktp.max' => 'Ukuran berkas KTP maksimal 5 MB.',
            'file_npwp.mimes' => 'Format berkas NPWP harus berupa JPG, PNG, atau PDF.',
            'file_npwp.max' => 'Ukuran berkas NPWP maksimal 5 MB.',
            'file_nib.mimes' => 'Format berkas NIB harus berupa JPG, PNG, atau PDF.',
            'file_nib.max' => 'Ukuran berkas NIB maksimal 5 MB.',
            'file_sewa_lahan.mimes' => 'Format berkas surat sewa harus berupa JPG, PNG, atau PDF.',
            'file_sewa_lahan.max' => 'Ukuran berkas surat sewa maksimal 5 MB.',
            'setuju_syarat.accepted' => 'Anda wajib menyetujui Syarat & Ketentuan Kemitraan serta Kebijakan Privasi platform.',
        ];
    }
}
