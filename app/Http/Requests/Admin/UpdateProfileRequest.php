<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name'  => preg_replace('/\s+/u', ' ', trim((string) $this->input('name'))),
            'no_hp' => preg_replace('/[\s().-]/', '', (string) $this->input('no_hp')),
        ]);
    }

    /** Email sengaja tidak ada di sini: identitas login tidak diubah dari halaman ini */
    public function rules(): array
    {
        return [
            'name'  => ['required', 'string', 'min:3', 'max:100'],
            'no_hp' => ['required', 'regex:/^(\+62|62|0)8[1-9][0-9]{6,11}$/', Rule::unique('users', 'no_hp')->ignore($this->user()->id)],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'name.min'      => 'Nama minimal 3 karakter.',
            'name.max'      => 'Nama maksimal 100 karakter.',
            'no_hp.required' => 'Nomor HP wajib diisi.',
            'no_hp.regex'   => 'Nomor HP tidak valid. Contoh: 081234567890.',
            'no_hp.unique'  => 'Nomor HP ini sudah dipakai akun lain.',
        ];
    }
}