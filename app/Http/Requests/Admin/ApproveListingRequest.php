<?php

namespace App\Http\Requests\Admin;

use App\Services\Admin\ListingModerationService;
use Illuminate\Foundation\Http\FormRequest;

class ApproveListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('moderate-listings') ?? false;
    }

    public function rules(): array
    {
        $rules = ['checklist' => ['required', 'array']];
        foreach (array_keys(ListingModerationService::CHECKLIST[$this->route('jenis')] ?? []) as $key) {
            $rules["checklist.{$key}"] = ['required', 'accepted'];
        }

        return $rules;
    }

    public function messages(): array
    {
        $msg = 'Pemeriksaan belum lengkap. Centang semua butir sebelum menyetujui.';

        return ['checklist.required' => $msg, 'checklist.*.required' => $msg, 'checklist.*.accepted' => $msg];
    }
}