<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class VerifyOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kode' => ['required', 'digits:'.config('otp.length', 6)],
        ];
    }

    public function messages(): array
    {
        return [
            'kode.required' => 'Kode OTP wajib diisi.',
            'kode.digits' => 'Kode OTP harus berupa :digits digit angka.',
        ];
    }
}
