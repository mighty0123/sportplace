<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mitra\StoreBlockRequest;
use App\Http\Requests\Mitra\UpdateScheduleRequest;
use App\Models\Field;
use App\Models\FieldBlock;
use App\Models\FieldSchedule;
use App\Models\Venue;
use App\Services\Mitra\SlotGenerator;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    /**
     * MTR-06 / T6.1: Menampilkan halaman utama manajemen jadwal dan blokir lapangan.
     */
    public function index(Request $request, SlotGenerator $slotGenerator): View
    {
        $mitra = Auth::user()->mitraProfile;

        // Ambil semua venue dan lapangan milik mitra yang sudah disetujui
        $venues = Venue::with(['fields' => function ($q) {
            $q->orderBy('nama_lapangan');
        }])
            ->where('mitra_id', $mitra->id)
            ->get();

        $allFields = $venues->flatMap->fields;

        // Jika mitra belum punya lapangan
        if ($allFields->isEmpty()) {
            return view('mitra.schedules.index', [
                'venues' => $venues,
                'allFields' => collect(),
                'selectedField' => null,
                'schedules' => collect(),
                'blocks' => collect(),
                'slotPreview' => null,
                'selectedDate' => now()->toDateString(),
            ]);
        }

        // Tentukan lapangan aktif
        $fieldId = $request->query('field_id');
        $selectedField = $allFields->firstWhere('id', $fieldId) ?? $allFields->first();

        // Pastikan relasi schedules, blocks, dan venue ter-load
        $selectedField->loadMissing(['venue', 'sport', 'schedules', 'blocks']);

        // Inisialisasi 7 hari (Senin s.d. Minggu) jika ada hari yang belum tersimpan di DB
        $schedules = collect();
        for ($day = 1; $day <= 7; $day++) {
            $existing = $selectedField->schedules->firstWhere('hari', $day);
            if ($existing) {
                $schedules->push($existing);
            } else {
                // Default jam operasional mengacu ke jam buka venue induk
                $newSchedule = new FieldSchedule([
                    'field_id' => $selectedField->id,
                    'hari' => $day,
                    'status_hari' => 'buka',
                    'jam_buka' => $selectedField->venue?->jam_buka ?? '08:00:00',
                    'jam_tutup' => $selectedField->venue?->jam_tutup ?? '22:00:00',
                    'durasi' => '01:00:00',
                ]);
                $schedules->push($newSchedule);
            }
        }

        // Ambil data blokir mendatang dan hari ini
        $blocks = FieldBlock::query()
            ->where('field_id', $selectedField->id)
            ->whereDate('tanggal_selesai', '>=', now()->toDateString())
            ->orderBy('tanggal_mulai')
            ->orderBy('jam_mulai')
            ->get();

        // Tanggal yang sedang dilihat untuk pratinjau slot real-time
        $selectedDate = $request->query('date', now()->toDateString());
        try {
            $carbonDate = Carbon::parse($selectedDate);
        } catch (\Exception $e) {
            $carbonDate = now();
            $selectedDate = $carbonDate->toDateString();
        }

        // Generate slot waktu untuk tanggal tersebut
        $slotPreview = $slotGenerator->generateSlots($selectedField, $carbonDate);

        return view('mitra.schedules.index', [
            'venues' => $venues,
            'allFields' => $allFields,
            'selectedField' => $selectedField,
            'schedules' => $schedules,
            'blocks' => $blocks,
            'slotPreview' => $slotPreview,
            'selectedDate' => $selectedDate,
        ]);
    }

    /**
     * MTR-06 / T6.1: Memperbarui jadwal operasional mingguan (Senin s.d. Minggu) per lapangan.
     */
    public function update(UpdateScheduleRequest $request, Field $field): RedirectResponse
    {
        $schedulesData = $request->validated('schedules');

        foreach ($schedulesData as $item) {
            $status = $item['status_hari'] ?? 'buka';
            $durasi = $item['durasi'] ?? '01:00';
            if (strlen($durasi) === 5) {
                $durasi .= ':00';
            }

            FieldSchedule::updateOrCreate(
                [
                    'field_id' => $field->id,
                    'hari' => (int) $item['hari'],
                ],
                [
                    'status_hari' => $status,
                    'jam_buka' => $status === 'buka' && ! empty($item['jam_buka']) ? (strlen($item['jam_buka']) === 5 ? $item['jam_buka'].':00' : $item['jam_buka']) : null,
                    'jam_tutup' => $status === 'buka' && ! empty($item['jam_tutup']) ? (strlen($item['jam_tutup']) === 5 ? $item['jam_tutup'].':00' : $item['jam_tutup']) : null,
                    'durasi' => $status === 'buka' ? $durasi : '01:00:00',
                ]
            );
        }

        return redirect()
            ->route('mitra.schedules.index', ['field_id' => $field->id])
            ->with('success', "Jadwal operasional mingguan untuk '{$field->nama_lapangan}' berhasil disimpan.");
    }

    /**
     * MTR-06 / T6.3 & T6.4: Menambah blokir tanggal/jam dengan proteksi tabrakan booking confirmed.
     */
    public function storeBlock(StoreBlockRequest $request, Field $field): RedirectResponse
    {
        $validated = $request->validated();

        $jamMulai = $validated['jam_mulai'];
        if (strlen($jamMulai) === 5) {
            $jamMulai .= ':00';
        }

        $jamSelesai = $validated['jam_selesai'];
        if (strlen($jamSelesai) === 5) {
            $jamSelesai .= ':00';
        }

        FieldBlock::create([
            'field_id' => $field->id,
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'jam_mulai' => $jamMulai,
            'jam_selesai' => $jamSelesai,
            'alasan_pemblokiran' => $validated['alasan_pemblokiran'] ?? null,
        ]);

        return redirect()
            ->route('mitra.schedules.index', [
                'field_id' => $field->id,
                'date' => $validated['tanggal_mulai'],
            ])
            ->with('success', 'Blokir jadwal pemeliharaan berhasil ditambahkan.');
    }

    /**
     * MTR-06 / T6.3: Menghapus blokir jadwal lapangan.
     */
    public function destroyBlock(Request $request, Field $field, FieldBlock $block): RedirectResponse
    {
        $mitraId = Auth::user()->mitraProfile?->id;

        // Proteksi otorisasi
        if (! $mitraId || ! $field->venue || $field->venue->mitra_id !== $mitraId || $block->field_id !== $field->id) {
            abort(403, 'Anda tidak berhak menghapus blokir pada lapangan ini.');
        }

        $block->delete();

        return redirect()
            ->route('mitra.schedules.index', ['field_id' => $field->id])
            ->with('success', 'Blokir jadwal berhasil dihapus. Slot kembali aktif sesuai jadwal reguler.');
    }

    /**
     * Endpoint JSON untuk pergantian tanggal kalender interaktif.
     */
    public function slotsJson(Request $request, Field $field, SlotGenerator $slotGenerator): JsonResponse
    {
        $mitraId = Auth::user()->mitraProfile?->id;

        if (! $mitraId || ! $field->venue || $field->venue->mitra_id !== $mitraId) {
            return response()->json(['error' => 'Akses ditolak.'], 403);
        }

        $dateString = $request->query('date', now()->toDateString());
        $carbonDate = Carbon::parse($dateString);

        $slotsData = $slotGenerator->generateSlots($field, $carbonDate);

        return response()->json($slotsData);
    }
}
