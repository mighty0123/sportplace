<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCommissionRateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-commission') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $persen = trim((string) $this->input('persen'));
        $from = trim((string) $this->input('berlaku_mulai'));

        $this->merge([
            'persen'        => $persen === '' ? null : str_replace(',', '.', $persen), // "12,5" juga diterima
            'berlaku_mulai' => $from === '' ? null : $from,
            'catatan'       => trim((string) $this->input('catatan')),
        ]);
    }

    public function rules(): array
    {
        return [
            'scope'         => ['required', Rule::in(['global', 'mitra'])],
            'mitra_id'      => ['required_if:scope,mitra', 'nullable', 'integer'],
            'persen'        => [Rule::requiredIf(fn () => $this->input('scope') === 'global'), 'nullable', 'regex:/^\d{1,3}(\.\d{1,2})?$/'],
            'berlaku_mulai' => ['nullable', 'date_format:Y-m-d\TH:i'],
            'catatan'       => ['required', 'string', 'min:10', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'scope.in'                 => 'Lingkup tarif tidak dikenal.',
            'mitra_id.required_if'     => 'Pilih mitra.',
            'persen.required'          => 'Tarif global wajib berisi persentase.',
            'persen.regex'             => 'Persentase harus berupa angka, maksimal dua desimal. Contoh: 20 atau 12,5.',
            'berlaku_mulai.date_format' => 'Waktu mulai berlaku tidak valid.',
            'catatan.required'         => 'Alasan perubahan wajib diisi.',
            'catatan.min'              => 'Alasan minimal 10 karakter supaya jejak auditnya jelas.',
            'catatan.max'              => 'Alasan maksimal 255 karakter.',
        ];
    }
}