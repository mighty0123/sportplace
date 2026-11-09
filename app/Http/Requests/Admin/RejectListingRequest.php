<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RejectListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('moderate-listings') ?? false;
    }

    public function rules(): array
    {
        return ['alasan_penolakan' => ['required', 'string', 'min:10', 'max:255']];
    }

    public function messages(): array
    {
        return [
            'alasan_penolakan.required' => 'Alasan penolakan wajib diisi supaya mitra tahu apa yang perlu diperbaiki.',
            'alasan_penolakan.min'      => 'Alasan penolakan minimal 10 karakter.',
            'alasan_penolakan.max'      => 'Alasan penolakan maksimal 255 karakter.',
        ];
    }
}