<?php

namespace App\Http\Requests\Mitra;

use Illuminate\Foundation\Http\FormRequest;

class ReportFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'mitra' && $this->user()->isMitraApproved();
    }

    public function rules(): array
    {
        return [
            'period_mode' => ['nullable', 'string', 'in:month,range'],
            'month' => ['nullable', 'string', 'regex:/^\d{4}-\d{2}$/'],
            'start_date' => ['nullable', 'date_format:Y-m-d'],
            'end_date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:start_date'],
            'field_id' => ['nullable', 'integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'month.regex' => 'Format bulan harus berupa YYYY-MM (contoh: 2026-11).',
            'start_date.date_format' => 'Format tanggal mulai harus berupa YYYY-MM-DD.',
            'end_date.date_format' => 'Format tanggal akhir harus berupa YYYY-MM-DD.',
            'end_date.after_or_equal' => 'Tanggal akhir tidak boleh lebih awal dari tanggal mulai.',
        ];
    }
}
