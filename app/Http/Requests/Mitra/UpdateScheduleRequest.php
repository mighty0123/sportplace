<?php

namespace App\Http\Requests\Mitra;

use App\Models\Field;
use App\Models\FieldSchedule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;
use Illuminate\Validation\Validator;

class UpdateScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        $route = $this->route();
        $field = $route instanceof Route ? $route->parameter('field') : $route;

        if (! $field instanceof Field && is_numeric($field)) {
            $field = Field::find($field);
        }

        if (! $field) {
            return false;
        }

        $mitraId = $this->user()?->mitraProfile?->id;

        return $mitraId && $field->venue && $field->venue->mitra_id === $mitraId;
    }

    public function rules(): array
    {
        return [
            'schedules' => ['required', 'array'],
            'schedules.*.hari' => ['required', 'integer', 'between:1,7'],
            'schedules.*.status_hari' => ['required', 'in:buka,tutup'],
            'schedules.*.jam_buka' => ['nullable', 'date_format:H:i'],
            'schedules.*.jam_tutup' => ['nullable', 'date_format:H:i'],
            'schedules.*.durasi' => ['nullable', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $route = $this->route();
            $field = $route instanceof Route ? $route->parameter('field') : $route;

            if (! $field instanceof Field && is_numeric($field)) {
                $field = Field::with('venue')->find($field);
            } elseif ($field instanceof Field) {
                $field->loadMissing('venue');
            }

            if (! $field || ! $field->venue) {
                return;
            }

            $venueOpen = substr($field->venue->jam_buka ?? '06:00:00', 0, 5);
            $venueClose = substr($field->venue->jam_tutup ?? '23:59:00', 0, 5);
            $schedules = $this->input('schedules', []);

            foreach ($schedules as $key => $sched) {
                $hari = (int) ($sched['hari'] ?? 1);
                $namaHari = FieldSchedule::DAYS[$hari] ?? "Hari {$hari}";
                $status = $sched['status_hari'] ?? 'buka';

                if ($status === 'buka') {
                    $open = $sched['jam_buka'] ?? null;
                    $close = $sched['jam_tutup'] ?? null;

                    if (! $open || ! $close) {
                        $validator->errors()->add("schedules.{$key}.jam_buka", "Jam buka dan jam tutup wajib diisi untuk hari {$namaHari}.");

                        continue;
                    }

                    if ($close <= $open) {
                        $validator->errors()->add("schedules.{$key}.jam_tutup", "Jam tutup harus lebih malam daripada jam buka untuk hari {$namaHari}.");
                    }

                    // Validasi jam operasional lapangan harus berada dalam jam operasional venue (KS-14)
                    if ($open < $venueOpen || $close > $venueClose) {
                        $validator->errors()->add(
                            "schedules.{$key}.jam_buka",
                            "Jam operasional {$namaHari} ({$open}–{$close}) berada di luar jam buka venue ({$venueOpen}–{$venueClose})."
                        );
                    }
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'schedules.required' => 'Jadwal operasional mingguan wajib diisi.',
            'schedules.*.jam_buka.date_format' => 'Format jam buka harus berupa JJ:MM (contoh: 08:00).',
            'schedules.*.jam_tutup.date_format' => 'Format jam tutup harus berupa JJ:MM (contoh: 22:00).',
        ];
    }
}
