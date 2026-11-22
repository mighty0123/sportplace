<?php

namespace App\Livewire\Mitra\Pricing;

use App\Livewire\Mitra\MitraComponent;
use App\Models\Field;
use App\Models\FieldSchedule;
use App\Models\Venue;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;

class PricingManager extends MitraComponent
{
    public ?int $venueId = null;

    #[Url(as: 'field_id', history: true)]
    public ?int $fieldId = null;

    // Form State Pengaturan Tarif
    public string $tipe_skema = 'flat';

    public $harga_perjam = 100000;

    public $harga_off_peak = 100000;

    public $harga_peak = 130000;

    public array $peak_rules = [];

    public function mount(?int $initialVenueId = null, ?int $initialFieldId = null): void
    {
        $this->authorizeMitraApproved();

        if ($initialFieldId) {
            $this->fieldId = $initialFieldId;
        }
        if ($initialVenueId) {
            $this->venueId = $initialVenueId;
        }

        $allFields = $this->allFields();

        if ($allFields->isNotEmpty()) {
            $selected = null;
            if ($this->fieldId) {
                $selected = $allFields->firstWhere('id', $this->fieldId);
            } elseif ($this->venueId) {
                $selected = $allFields->where('venue_id', $this->venueId)->first();
            }

            if (! $selected) {
                $selected = $allFields->first();
            }

            $this->fieldId = $selected->id;
            $this->venueId = $selected->venue_id;
            $this->loadPricingForField($selected);
        }
    }

    #[Computed]
    public function venues(): Collection
    {
        $profile = $this->authorizeMitraApproved();

        return Venue::with(['fields' => function ($q) {
            $q->with('sport')->orderBy('nama_lapangan');
        }])
            ->where('mitra_id', $profile->id)
            ->orderBy('nama_venue')
            ->get();
    }

    #[Computed]
    public function allFields(): Collection
    {
        return $this->venues()->flatMap->fields;
    }

    #[Computed]
    public function venueFields(): Collection
    {
        if (! $this->venueId) {
            return $this->allFields();
        }

        return $this->allFields()->where('venue_id', $this->venueId)->values();
    }

    #[Computed]
    public function selectedField(): ?Field
    {
        if (! $this->fieldId) {
            return null;
        }

        $field = $this->allFields()->firstWhere('id', $this->fieldId);
        if ($field) {
            $field->loadMissing(['venue', 'sport']);
        }

        return $field;
    }

    // ---- Dual Dropdown Reaktif (Sinkron dengan ScheduleManager) ----

    public function updatedVenueId($value): void
    {
        $venueId = (int) $value;
        $this->venueId = $venueId;

        $firstField = $this->allFields()->where('venue_id', $venueId)->first();
        if ($firstField) {
            $this->fieldId = $firstField->id;
            $this->loadPricingForField($firstField);
        } else {
            $this->fieldId = null;
        }

        $this->resetValidation();
        unset($this->venueFields, $this->selectedField, $this->simulation);
    }

    public function updatedFieldId($value): void
    {
        $fieldId = (int) $value;
        $field = $this->allFields()->firstWhere('id', $fieldId);

        if ($field) {
            $this->fieldId = $field->id;
            $this->venueId = $field->venue_id;
            $this->loadPricingForField($field);
        }

        $this->resetValidation();
        unset($this->selectedField, $this->simulation);
    }

    public function updatedHargaOffPeak($value): void
    {
        if ($this->tipe_skema === 'peak_off_peak' && is_numeric($value)) {
            $this->harga_perjam = (float) $value;
        }
    }

    protected function loadPricingForField(Field $field): void
    {
        $basePrice = (float) ($field->harga_perjam ?? 100000);
        $meta = is_array($field->meta_harga) ? $field->meta_harga : [];

        $this->tipe_skema = ($meta['tipe_skema'] ?? 'flat') === 'peak_off_peak' ? 'peak_off_peak' : 'flat';
        $this->harga_perjam = $basePrice;
        $this->harga_off_peak = isset($meta['harga_off_peak']) ? (float) $meta['harga_off_peak'] : $basePrice;
        $this->harga_peak = isset($meta['harga_peak'])
            ? (float) $meta['harga_peak']
            : ($basePrice > 0 ? round($basePrice * 1.3, -3) : 150000);

        $rules = $meta['peak_rules'] ?? [
            [
                'hari' => [6, 7],
                'jam_mulai' => '08:00',
                'jam_selesai' => '23:00',
            ],
        ];

        $this->peak_rules = collect($rules)->map(function ($r) {
            return [
                'hari' => array_map('intval', $r['hari'] ?? []),
                'jam_mulai' => substr((string) ($r['jam_mulai'] ?? '08:00'), 0, 5),
                'jam_selesai' => substr((string) ($r['jam_selesai'] ?? '23:00'), 0, 5),
            ];
        })->values()->toArray();

        if (empty($this->peak_rules)) {
            $this->peak_rules = [
                [
                    'hari' => [6, 7],
                    'jam_mulai' => '08:00',
                    'jam_selesai' => '23:00',
                ],
            ];
        }
    }

    // ---- Dynamic Peak Rules Repeater (LW-5.1) ----

    public function addPeakRule(): void
    {
        $this->peak_rules[] = [
            'hari' => [1, 2, 3, 4, 5],
            'jam_mulai' => '18:00',
            'jam_selesai' => '23:00',
        ];
    }

    public function removePeakRule(int $index): void
    {
        if (count($this->peak_rules) <= 1) {
            $this->notify('Minimal harus terdapat 1 aturan jam peak untuk skema dinamis.', 'warning');

            return;
        }

        if (isset($this->peak_rules[$index])) {
            unset($this->peak_rules[$index]);
            $this->peak_rules = array_values($this->peak_rules);
        }
    }

    public function toggleRuleDay(int $ruleIndex, int $dayNum): void
    {
        if (! isset($this->peak_rules[$ruleIndex])) {
            return;
        }

        $days = array_map('intval', $this->peak_rules[$ruleIndex]['hari'] ?? []);
        if (in_array($dayNum, $days, true)) {
            $days = array_values(array_diff($days, [$dayNum]));
        } else {
            $days[] = $dayNum;
            sort($days);
        }

        $this->peak_rules[$ruleIndex]['hari'] = $days;
    }

    public function applyPreset(string $preset): void
    {
        $this->tipe_skema = 'peak_off_peak';

        if ($preset === 'weekend') {
            $this->peak_rules = [
                [
                    'hari' => [6, 7],
                    'jam_mulai' => '08:00',
                    'jam_selesai' => '23:00',
                ],
            ];
            $this->notify('Preset Weekend Penuh (Sabtu & Minggu) diterapkan.', 'info');
        } elseif ($preset === 'night') {
            $this->peak_rules = [
                [
                    'hari' => [1, 2, 3, 4, 5],
                    'jam_mulai' => '18:00',
                    'jam_selesai' => '23:00',
                ],
            ];
            $this->notify('Preset Malam Hari (Senin–Jumat 18:00–23:00) diterapkan.', 'info');
        } elseif ($preset === 'combo') {
            $this->peak_rules = [
                [
                    'hari' => [1, 2, 3, 4, 5],
                    'jam_mulai' => '18:00',
                    'jam_selesai' => '23:00',
                ],
                [
                    'hari' => [6, 7],
                    'jam_mulai' => '08:00',
                    'jam_selesai' => '23:00',
                ],
            ];
            $this->notify('Preset Kombinasi (Malam Hari + Weekend Penuh) diterapkan.', 'info');
        }

        unset($this->simulation);
    }

    // ---- Live Pricing Simulator (Computed PHP Property - LW-5.1) ----

    #[Computed]
    public function simulation(): array
    {
        $isPeakScheme = $this->tipe_skema === 'peak_off_peak';
        $basePrice = is_numeric($this->harga_perjam) ? max(0, (float) $this->harga_perjam) : 0.0;
        $offPeakPrice = is_numeric($this->harga_off_peak) ? max(0, (float) $this->harga_off_peak) : $basePrice;
        $peakPrice = is_numeric($this->harga_peak) ? max(0, (float) $this->harga_peak) : $basePrice;

        $scenarios = [
            'weekday_day' => ['day' => 1, 'time' => '10:00', 'title' => 'Senin, 10:00 – 11:00 WIB', 'subtitle' => 'Hari Kerja (Siang Hari)'],
            'weekday_night' => ['day' => 1, 'time' => '19:00', 'title' => 'Senin, 19:00 – 20:00 WIB', 'subtitle' => 'Hari Kerja (Malam Hari)'],
            'weekend_day' => ['day' => 6, 'time' => '10:00', 'title' => 'Sabtu, 10:00 – 11:00 WIB', 'subtitle' => 'Weekend (Siang Hari)'],
            'weekend_night' => ['day' => 6, 'time' => '20:00', 'title' => 'Sabtu, 20:00 – 21:00 WIB', 'subtitle' => 'Weekend (Malam Hari)'],
        ];

        $results = [];

        foreach ($scenarios as $key => $sc) {
            $matchedPeak = false;

            if ($isPeakScheme) {
                foreach ($this->peak_rules as $rule) {
                    $days = array_map('intval', $rule['hari'] ?? []);
                    $start = $rule['jam_mulai'] ?? '00:00';
                    $end = $rule['jam_selesai'] ?? '24:00';

                    if (in_array($sc['day'], $days, true)) {
                        if ($sc['time'] >= $start && $sc['time'] < $end) {
                            $matchedPeak = true;
                            break;
                        }
                    }
                }
            }

            $amount = $isPeakScheme ? ($matchedPeak ? $peakPrice : $offPeakPrice) : $basePrice;
            $badge = $isPeakScheme ? ($matchedPeak ? 'Peak Hour' : 'Off-Peak') : 'Reguler';

            $results[$key] = [
                'title' => $sc['title'],
                'subtitle' => $sc['subtitle'],
                'price' => $amount,
                'formatted_price' => 'Rp '.number_format($amount, 0, ',', '.'),
                'badge' => $badge,
                'is_peak' => $isPeakScheme && $matchedPeak,
            ];
        }

        return $results;
    }

    // ---- Simpan Pengaturan Tarif ----

    public function savePricing(): void
    {
        $field = $this->selectedField();
        if (! $field) {
            return;
        }
        $this->ensureOwnsField($field);

        $this->resetValidation();

        $rules = [
            'tipe_skema' => ['required', 'in:flat,peak_off_peak'],
            'harga_perjam' => ['required', 'numeric', 'min:0'],
        ];

        if ($this->tipe_skema === 'peak_off_peak') {
            $rules['harga_off_peak'] = ['required', 'numeric', 'min:0'];
            $rules['harga_peak'] = ['required', 'numeric', 'min:0'];
            $rules['peak_rules'] = ['required', 'array', 'min:1'];
            $rules['peak_rules.*.hari'] = ['required', 'array', 'min:1'];
            $rules['peak_rules.*.hari.*'] = ['required', 'integer', 'between:1,7'];
            $rules['peak_rules.*.jam_mulai'] = ['required', 'date_format:H:i'];
            $rules['peak_rules.*.jam_selesai'] = ['required', 'date_format:H:i'];
        }

        $messages = [
            'tipe_skema.required' => 'Pilih jenis skema penetapan tarif sewa.',
            'harga_perjam.required' => 'Tarif sewa dasar wajib diisi.',
            'harga_perjam.min' => 'Tarif sewa tidak boleh bernilai negatif.',
            'harga_off_peak.required' => 'Tarif sewa off-peak wajib diisi.',
            'harga_off_peak.min' => 'Tarif sewa off-peak tidak boleh bernilai negatif.',
            'harga_peak.required' => 'Tarif sewa peak wajib diisi.',
            'harga_peak.min' => 'Tarif sewa peak tidak boleh bernilai negatif.',
            'peak_rules.required' => 'Setidaknya tentukan satu aturan jam peak.',
            'peak_rules.*.hari.required' => 'Pilih minimal satu hari berlaku untuk aturan peak.',
            'peak_rules.*.hari.min' => 'Pilih minimal satu hari berlaku untuk aturan peak.',
            'peak_rules.*.jam_mulai.required' => 'Jam mulai peak wajib diisi (JJ:MM).',
            'peak_rules.*.jam_selesai.required' => 'Jam selesai peak wajib diisi (JJ:MM).',
        ];

        $this->validate($rules, $messages);

        if ($this->tipe_skema === 'peak_off_peak') {
            // Validasi jam selesai > jam mulai & anti tumpang tindih per hari (T7.2)
            $dayIntervals = [];
            $hasRuleError = false;

            foreach ($this->peak_rules as $ruleIndex => $rule) {
                $days = array_map('intval', $rule['hari'] ?? []);
                $start = $rule['jam_mulai'] ?? '';
                $end = $rule['jam_selesai'] ?? '';

                if ($end <= $start) {
                    $this->addError(
                        "peak_rules.{$ruleIndex}.jam_selesai",
                        'Jam selesai peak harus lebih malam daripada jam mulai.'
                    );
                    $hasRuleError = true;
                    continue;
                }

                foreach ($days as $dayInt) {
                    $dayName = FieldSchedule::DAYS[$dayInt] ?? "Hari {$dayInt}";
                    $dayIntervals[$dayInt] ??= [];

                    foreach ($dayIntervals[$dayInt] as $existing) {
                        if (max($start, $existing['start']) < min($end, $existing['end'])) {
                            $this->addError(
                                "peak_rules.{$ruleIndex}.jam_mulai",
                                "Periode jam peak untuk hari {$dayName} bertumpukan ({$start}–{$end} dengan {$existing['start']}–{$existing['end']}). Pastikan jam peak tidak tumpang-tindih."
                            );
                            $hasRuleError = true;
                            break 2;
                        }
                    }

                    $dayIntervals[$dayInt][] = [
                        'start' => $start,
                        'end' => $end,
                    ];
                }
            }

            if ($hasRuleError) {
                $this->notify('Terdapat konflik atau kesalahan pada aturan jam peak.', 'error');

                return;
            }
        }

        if ($this->tipe_skema === 'flat') {
            $hargaDasar = (float) $this->harga_perjam;

            $field->update([
                'harga_perjam' => $hargaDasar,
                'meta_harga' => [
                    'tipe_skema' => 'flat',
                ],
            ]);

            unset($this->allFields, $this->venueFields, $this->selectedField);
            $this->notify('Skema tarif flat sebesar Rp '.number_format($hargaDasar, 0, ',', '.')."/jam berhasil disimpan untuk {$field->nama_lapangan}.");

            return;
        }

        $offPeak = (float) $this->harga_off_peak;
        $peak = (float) $this->harga_peak;

        $meta = [
            'tipe_skema' => 'peak_off_peak',
            'harga_off_peak' => $offPeak,
            'harga_peak' => $peak,
            'peak_rules' => array_values($this->peak_rules),
        ];

        $field->update([
            'harga_perjam' => $offPeak,
            'meta_harga' => $meta,
        ]);

        unset($this->allFields, $this->venueFields, $this->selectedField);
        $this->notify("Pengaturan tarif dinamis (Peak & Off-Peak) berhasil disimpan untuk {$field->nama_lapangan}.");
    }

    public function render(): View
    {
        return view('livewire.mitra.pricing.pricing-manager', [
            'venuesList' => $this->venues(),
            'allFieldsList' => $this->allFields(),
            'venueFieldsList' => $this->venueFields(),
            'currentField' => $this->selectedField(),
            'simResults' => $this->simulation(),
        ]);
    }
}
