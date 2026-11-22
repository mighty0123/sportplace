<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class LogoutOtherSessionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return ['logout_password' => ['required', 'current_password']];
    }

    public function messages(): array
    {
        return [
            'logout_password.required'         => 'Masukkan kata sandimu untuk melanjutkan.',
            'logout_password.current_password' => 'Kata sandi tidak cocok.',
        ];
    }
}