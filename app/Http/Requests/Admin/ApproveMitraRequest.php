<?php

namespace App\Http\Requests\Admin;

use App\Services\Admin\MitraVerificationService;
use Illuminate\Foundation\Http\FormRequest;

class ApproveMitraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('review', $this->route('mitra')) ?? false;
    }

    /** Server tidak percaya tombol di browser: semua butir yang berlaku harus benar-benar dicentang. */
    public function rules(): array
    {
        $items = app(MitraVerificationService::class)->checklistItemsFor($this->route('mitra'));

        $rules = ['checklist' => ['required', 'array']];
        foreach (array_keys($items) as $key) {
            $rules["checklist.{$key}"] = ['required', 'accepted'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'checklist.required'            => 'Pemeriksaan belum lengkap. Centang semua butir sebelum menyetujui.',
            'checklist.*.required'          => 'Pemeriksaan belum lengkap. Centang semua butir sebelum menyetujui.',
            'checklist.*.accepted'          => 'Pemeriksaan belum lengkap. Centang semua butir sebelum menyetujui.',
        ];
    }
}