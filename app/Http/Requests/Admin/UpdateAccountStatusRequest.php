<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAccountStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-users') ?? false;
    }

    public function rules(): array
    {
        $sanction = in_array($this->input('status'), ['suspend', 'banned'], true);

        return [
            'status' => ['required', Rule::in(['aktif', 'suspend', 'banned'])],
            // Alasan wajib untuk sanksi, opsional saat mengaktifkan kembali
            'alasan' => $sanction ? ['required', 'string', 'min:10', 'max:255'] : ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.in'       => 'Status tidak dikenal.',
            'alasan.required' => 'Alasan wajib diisi untuk penangguhan dan pemblokiran.',
            'alasan.min'      => 'Alasan minimal 10 karakter supaya jejak auditnya jelas.',
            'alasan.max'      => 'Alasan maksimal 255 karakter.',
        ];
    }
}