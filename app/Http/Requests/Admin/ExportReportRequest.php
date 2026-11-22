<?php

namespace App\Http\Requests\Admin;

use App\Services\Admin\Reports\ReportService;
use App\Services\Admin\TransactionMonitor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExportReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('view-reports') ?? false;
    }

    public function rules(): array
    {
        return [
            'type'    => ['required', Rule::in(array_keys(ReportService::TYPES))],
            'format'  => ['required', Rule::in(['xlsx', 'pdf'])],
            'period'  => ['nullable', Rule::in(array_keys(TransactionMonitor::PERIODS))],
            'from'    => ['nullable', 'date_format:Y-m-d'],
            'to'      => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from'],
            'status'  => ['nullable', 'string', 'max:20'],
            'method'  => ['nullable', 'string', 'max:30'],
            'role'    => ['nullable', 'string', 'max:20'],
            'account' => ['nullable', 'string', 'max:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'from.date_format' => 'Tanggal mulai tidak valid.',
            'to.date_format'   => 'Tanggal akhir tidak valid.',
            'to.after_or_equal' => 'Tanggal akhir tidak boleh sebelum tanggal mulai.',
        ];
    }
}