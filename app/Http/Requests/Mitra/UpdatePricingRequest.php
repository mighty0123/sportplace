<?php

namespace App\Http\Requests\Mitra;

use App\Models\Field;
use App\Models\FieldSchedule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;
use Illuminate\Validation\Validator;

class UpdatePricingRequest extends FormRequest
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
        $rules = [
            'tipe_skema' => ['required', 'in:flat,peak_off_peak'],
            'harga_perjam' => ['required', 'numeric', 'min:0'],
        ];

        if ($this->input('tipe_skema') === 'peak_off_peak') {
            $rules['harga_off_peak'] = ['required', 'numeric', 'min:0'];
            $rules['harga_peak'] = ['required', 'numeric', 'min:0'];
            $rules['peak_rules'] = ['required', 'array', 'min:1'];
            $rules['peak_rules.*.hari'] = ['required', 'array', 'min:1'];
            $rules['peak_rules.*.hari.*'] = ['required', 'integer', 'between:1,7'];
            $rules['peak_rules.*.jam_mulai'] = ['required', 'date_format:H:i'];
            $rules['peak_rules.*.jam_selesai'] = ['required', 'date_format:H:i', 'after:peak_rules.*.jam_mulai'];
        }

        return $rules;
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if ($this->input('tipe_skema') !== 'peak_off_peak') {
                return;
            }

            $peakRules = $this->input('peak_rules', []);

            // Pengelompokan aturan peak per hari untuk deteksi tumpang tindih (T7.2)
            $dayIntervals = [];

            foreach ($peakRules as $ruleIndex => $rule) {
                $days = $rule['hari'] ?? [];
                $start = $rule['jam_mulai'] ?? null;
                $end = $rule['jam_selesai'] ?? null;

                if (! $start || ! $end || ! is_array($days)) {
                    continue;
                }

                foreach ($days as $day) {
                    $dayInt = (int) $day;
                    $dayName = FieldSchedule::DAYS[$dayInt] ?? "Hari {$dayInt}";

                    if (! isset($dayIntervals[$dayInt])) {
                        $dayIntervals[$dayInt] = [];
                    }

                    // Cek apakah ada tumpang tindih dengan interval sebelumnya di hari yang sama
                    foreach ($dayIntervals[$dayInt] as $existing) {
                        $existStart = $existing['start'];
                        $existEnd = $existing['end'];

                        // Bertabrakan jika max(start, existStart) < min(end, existEnd)
                        if (max($start, $existStart) < min($end, $existEnd)) {
                            $validator->errors()->add(
                                "peak_rules.{$ruleIndex}.jam_mulai",
                                "Periode jam peak untuk hari {$dayName} bertumpukan ({$start}–{$end} dengan {$existStart}–{$existEnd}). Pastikan jam peak tidak tumpang-tindih."
                            );
                            break 2; // Berhenti jika ditemukan tumpang tindih
                        }
                    }

                    $dayIntervals[$dayInt][] = [
                        'start' => $start,
                        'end' => $end,
                    ];
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'tipe_skema.required' => 'Pilih jenis skema penetapan tarif sewa.',
            'harga_perjam.required' => 'Tarif sewa dasar wajib diisi.',
            'harga_perjam.min' => 'Tarif sewa tidak boleh bernilai negatif.',
            'harga_off_peak.required' => 'Tarif sewa off-peak wajib diisi.',
            'harga_off_peak.min' => 'Tarif sewa off-peak tidak boleh bernilai negatif.',
            'harga_peak.required' => 'Tarif sewa peak wajib diisi.',
            'harga_peak.min' => 'Tarif sewa peak tidak boleh bernilai negatif.',
            'peak_rules.required' => 'Setidaknya tentukan satu aturan jam peak.',
            'peak_rules.min' => 'Setidaknya tentukan satu aturan jam peak.',
            'peak_rules.*.hari.required' => 'Pilih minimal satu hari berlaku untuk aturan peak.',
            'peak_rules.*.jam_mulai.required' => 'Jam mulai peak wajib diisi (JJ:MM).',
            'peak_rules.*.jam_selesai.required' => 'Jam selesai peak wajib diisi (JJ:MM).',
            'peak_rules.*.jam_selesai.after' => 'Jam selesai peak harus lebih malam daripada jam mulai.',
        ];
    }
}
