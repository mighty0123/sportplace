<?php

namespace App\Http\Requests\Mitra;

use Illuminate\Foundation\Http\FormRequest;

class BulkCompleteOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'mitra' && $this->user()->isMitraApproved();
    }

    public function rules(): array
    {
        return [
            'detail_ids' => ['required', 'array', 'min:1'],
            'detail_ids.*' => ['required', 'integer', 'exists:booking_details,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'detail_ids.required' => 'Pilih minimal satu pesanan untuk diselesaikan.',
            'detail_ids.min' => 'Pilih minimal satu pesanan untuk diselesaikan.',
            'detail_ids.*.exists' => 'Data pesanan yang dipilih tidak valid.',
        ];
    }
}

