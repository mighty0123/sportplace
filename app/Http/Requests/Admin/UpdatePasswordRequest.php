<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdatePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', 'max:72', 'different:current_password',
                                    Password::min(10)->letters()->mixedCase()->numbers()],
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required'         => 'Kata sandi saat ini wajib diisi.',
            'current_password.current_password' => 'Kata sandi saat ini tidak cocok.',
            'password.required'  => 'Kata sandi baru wajib diisi.',
            'password.confirmed' => 'Ulangi kata sandi baru dengan benar.',
            'password.different' => 'Kata sandi baru harus berbeda dari yang lama.',
            'password.max'       => 'Kata sandi maksimal 72 karakter.',
            'password.min'       => 'Kata sandi minimal 10 karakter.',
            'password.letters'   => 'Kata sandi harus memuat huruf.',
            'password.mixed'     => 'Kata sandi harus memuat huruf besar dan huruf kecil.',
            'password.numbers'   => 'Kata sandi harus memuat angka.',
        ];
    }
}