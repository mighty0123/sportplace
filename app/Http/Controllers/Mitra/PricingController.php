<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mitra\UpdatePricingRequest;
use App\Models\Field;
use App\Models\Venue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PricingController extends Controller
{
    /**
     * MTR-07 / T7.1 & T7.2: Menampilkan halaman formulir pengaturan harga peak / off-peak per lapangan.
     */
    public function index(Request $request): View
    {
        $mitra = Auth::user()->mitraProfile;

        // Ambil semua venue dan lapangan milik mitra
        $venues = Venue::with(['fields' => function ($q) {
            $q->orderBy('nama_lapangan');
        }])
            ->where('mitra_id', $mitra->id)
            ->get();

        $allFields = $venues->flatMap->fields;

        if ($allFields->isEmpty()) {
            return view('mitra.pricing.index', [
                'venues' => $venues,
                'allFields' => collect(),
                'selectedField' => null,
                'metaHarga' => null,
            ]);
        }

        $fieldId = $request->query('field_id');
        $selectedField = $allFields->firstWhere('id', $fieldId) ?? $allFields->first();
        $selectedField->loadMissing(['venue', 'sport']);

        // Default konfigurasi meta_harga jika belum pernah diatur
        $metaHarga = $selectedField->meta_harga ?? [
            'tipe_skema' => 'flat',
            'harga_off_peak' => (float) $selectedField->harga_perjam,
            'harga_peak' => (float) ($selectedField->harga_perjam > 0 ? round($selectedField->harga_perjam * 1.3, -3) : 150000),
            'peak_rules' => [
                [
                    'hari' => [6, 7], // Akhir pekan (Sabtu & Minggu)
                    'jam_mulai' => '08:00',
                    'jam_selesai' => '23:00',
                ],
            ],
        ];

        return view('mitra.pricing.index', [
            'venues' => $venues,
            'allFields' => $allFields,
            'selectedField' => $selectedField,
            'metaHarga' => $metaHarga,
        ]);
    }

    /**
     * MTR-07 / T7.2: Menyimpan skema tarif dasar, off-peak, dan peak hours.
     */
    public function update(UpdatePricingRequest $request, Field $field): RedirectResponse
    {
        $tipeSkema = $request->validated('tipe_skema');
        $hargaDasar = (float) $request->validated('harga_perjam');

        if ($tipeSkema === 'flat') {
            $field->update([
                'harga_perjam' => $hargaDasar,
                'meta_harga' => [
                    'tipe_skema' => 'flat',
                ],
            ]);

            return redirect()
                ->route('mitra.pricing.index', ['field_id' => $field->id])
                ->with('success', 'Skema tarif flat sebesar Rp '.number_format($hargaDasar, 0, ',', '.')."/jam berhasil disimpan untuk {$field->nama_lapangan}.");
        }

        // Skema Peak / Off-Peak (T7.1)
        $meta = [
            'tipe_skema' => 'peak_off_peak',
            'harga_off_peak' => (float) $request->validated('harga_off_peak'),
            'harga_peak' => (float) $request->validated('harga_peak'),
            'peak_rules' => array_values($request->validated('peak_rules', [])),
        ];

        $field->update([
            'harga_perjam' => (float) $request->validated('harga_off_peak'), // Sinkronisasi harga dasar dengan off-peak
            'meta_harga' => $meta,
        ]);

        return redirect()
            ->route('mitra.pricing.index', ['field_id' => $field->id])
            ->with('success', "Pengaturan tarif dinamis (Peak & Off-Peak) berhasil disimpan untuk {$field->nama_lapangan}.");
    }
}
