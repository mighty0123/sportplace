<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mitra\StoreFieldRequest;
use App\Http\Requests\Mitra\UpdateFieldRequest;
use App\Models\BookingDetail;
use App\Models\Field;
use App\Models\FieldPhoto;
use App\Models\MasterFacility;
use App\Models\MasterSport;
use App\Models\Venue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class FieldController extends Controller
{
    /**
     * MTR-04 / T5.1: Menampilkan daftar lapangan dalam satu venue milik mitra.
     */
    public function index(Request $request, Venue $venue): View
    {
        // Proteksi kepemilikan venue (BR-023)
        Gate::authorize('view', $venue);

        $query = Field::with(['sport', 'photos', 'facilities'])
            ->where('venue_id', $venue->id);

        // Statistik ringkasan untuk 4 kartu metrik
        $allFields = (clone $query)->get();
        $totalFields = $allFields->count();
        $activeFields = $allFields->where('status_lapangan', 'aktif')->count();
        $pendingFields = $allFields->where('status_lapangan', 'pending_review')->count();
        $rejectedFields = $allFields->where('status_lapangan', 'rejected')->count();
        $draftFields = $allFields->where('status_lapangan', 'draft')->count();
        $nonaktifFields = $allFields->where('status_lapangan', 'nonaktif')->count();

        // Filter pencarian nama lapangan
        if ($search = $request->input('search')) {
            $query->where('nama_lapangan', 'like', "%{$search}%");
        }

        // Filter status lapangan
        if ($status = $request->input('status')) {
            if ($status !== 'semua') {
                $query->where('status_lapangan', $status);
            }
        }

        // Filter cabang olahraga
        if ($sportId = $request->input('sport_id')) {
            if ($sportId !== 'semua') {
                $query->where('sport_id', $sportId);
            }
        }

        $fields = $query->orderByDesc('id')->get();

        // Ambil daftar olahraga yang ada di venue ini untuk filter tabs
        $availableSportIds = $allFields->pluck('sport_id')->unique()->filter();
        $sports = MasterSport::whereIn('id', $availableSportIds)->orderBy('nama_olahraga')->get();

        return view('mitra.fields.index', compact(
            'venue',
            'fields',
            'sports',
            'totalFields',
            'activeFields',
            'pendingFields',
            'rejectedFields',
            'draftFields',
            'nonaktifFields'
        ));
    }

    /**
     * Menampilkan semua lapangan milik mitra lintas venue (toggle pada desain Figma).
     */
    public function allFields(Request $request): View
    {
        $user = Auth::user();
        $mitraProfile = $user->mitraProfile;

        // Ambil semua venue ID milik mitra
        $venueIds = Venue::where('mitra_id', $mitraProfile->id)->pluck('id');

        $query = Field::with(['sport', 'photos', 'facilities', 'venue.city'])
            ->whereIn('venue_id', $venueIds);

        $allFields = (clone $query)->get();
        $totalFields = $allFields->count();
        $activeFields = $allFields->where('status_lapangan', 'aktif')->count();
        $pendingFields = $allFields->where('status_lapangan', 'pending_review')->count();
        $rejectedFields = $allFields->where('status_lapangan', 'rejected')->count();
        $draftFields = $allFields->where('status_lapangan', 'draft')->count();
        $nonaktifFields = $allFields->where('status_lapangan', 'nonaktif')->count();

        if ($search = $request->input('search')) {
            $query->where('nama_lapangan', 'like', "%{$search}%");
        }

        if ($status = $request->input('status')) {
            if ($status !== 'semua') {
                $query->where('status_lapangan', $status);
            }
        }

        if ($sportId = $request->input('sport_id')) {
            if ($sportId !== 'semua') {
                $query->where('sport_id', $sportId);
            }
        }

        $fields = $query->orderByDesc('id')->get();
        $sports = MasterSport::where('status_olahraga', 'aktif')->orderBy('nama_olahraga')->get();

        return view('mitra.fields.index', [
            'venue' => null,
            'isCrossVenue' => true,
            'fields' => $fields,
            'sports' => $sports,
            'totalFields' => $totalFields,
            'activeFields' => $activeFields,
            'pendingFields' => $pendingFields,
            'rejectedFields' => $rejectedFields,
            'draftFields' => $draftFields,
            'nonaktifFields' => $nonaktifFields,
        ]);
    }

    /**
     * MTR-05 / T5.2: Menampilkan form tambah lapangan baru untuk venue tertentu.
     */
    public function create(Venue $venue): View
    {
        Gate::authorize('update', $venue);

        $sports = MasterSport::where('status_olahraga', 'aktif')->orderBy('nama_olahraga')->get();
        $facilities = MasterFacility::where('status_fasilitas', 'aktif')->orderBy('nama')->get();

        return view('mitra.fields.create', compact('venue', 'sports', 'facilities'));
    }

    /**
     * MTR-05 / T5.2, T5.3, T5.4: Menyimpan unit lapangan baru (draft atau ajukan verifikasi).
     */
    public function store(StoreFieldRequest $request, Venue $venue): RedirectResponse
    {
        Gate::authorize('update', $venue);

        $validated = $request->validated();
        $action = $request->input('action', 'draft');

        // T5.4 / OQ-010: Bila langsung diajukan, pastikan venue berstatus aktif
        if ($action === 'submit') {
            if ($venue->status_vanue !== 'aktif') {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', "Venue '{$venue->nama_venue}' belum berstatus Aktif. Lapangan hanya dapat disimpan sebagai Draft hingga venue disetujui (OQ-010).");
            }

            if (! $request->hasFile('photos')) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Wajib mengunggah minimal 1 foto fasilitas lapangan untuk mengajukan verifikasi.');
            }

            $status = 'pending_review';
            $submittedAt = now();
        } else {
            $status = 'draft';
            $submittedAt = null;
        }

        $field = Field::create([
            'venue_id' => $venue->id,
            'sport_id' => $validated['sport_id'],
            'nama_lapangan' => $validated['nama_lapangan'],
            'harga_perjam' => $validated['harga_perjam'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'tipe_bangunan' => $validated['tipe_bangunan'],
            'jenis_permukaan' => $validated['jenis_permukaan'],
            'panjang' => $validated['panjang'] ?? null,
            'lebar' => $validated['lebar'] ?? null,
            'kapasitas' => $validated['kapasitas'] ?? null,
            'aturan_lapangan' => $validated['aturan_lapangan'] ?? null,
            'status_lapangan' => $status,
            'submitted_at' => $submittedAt,
        ]);

        // Hubungkan fasilitas pendukung
        if (! empty($validated['facility_ids'])) {
            $field->facilities()->sync($validated['facility_ids']);
        }

        // T5.3: Simpan foto-foto yang diunggah
        if ($request->hasFile('photos')) {
            $primaryIndex = (int) $request->input('primary_photo_index', 0);

            foreach ($request->file('photos') as $idx => $photoFile) {
                $path = $photoFile->store("field_photos/{$field->id}", 'public');

                FieldPhoto::create([
                    'field_id' => $field->id,
                    'foto_url' => $path,
                    'is_primary' => ($idx === $primaryIndex),
                ]);
            }
        }

        if ($status === 'pending_review') {
            return redirect()
                ->route('mitra.venues.fields.index', $venue)
                ->with('success', "Lapangan '{$field->nama_lapangan}' berhasil dibuat dan diajukan untuk ditinjau oleh Admin.");
        }

        return redirect()
            ->route('mitra.venues.fields.index', $venue)
            ->with('success', "Lapangan '{$field->nama_lapangan}' berhasil disimpan sebagai draft.");
    }

    /**
     * MTR-05 / T5.5: Menampilkan form edit lapangan.
     */
    public function edit(Field $field): View
    {
        Gate::authorize('update', $field);

        $field->load(['photos', 'facilities', 'venue.city', 'sport']);
        $sports = MasterSport::where('status_olahraga', 'aktif')->orderBy('nama_olahraga')->get();
        $facilities = MasterFacility::where('status_fasilitas', 'aktif')->orderBy('nama')->get();

        return view('mitra.fields.edit', compact('field', 'sports', 'facilities'));
    }

    /**
     * MTR-05 / T5.5: Memperbarui data lapangan, sinkronisasi fasilitas & foto.
     */
    public function update(UpdateFieldRequest $request, Field $field): RedirectResponse
    {
        Gate::authorize('update', $field);

        $validated = $request->validated();
        $action = $request->input('action', 'save');

        // 1. Tangani penghapusan foto yang ditandai hapus
        if (! empty($validated['deleted_photos'])) {
            $photosToDelete = FieldPhoto::where('field_id', $field->id)
                ->whereIn('id', $validated['deleted_photos'])
                ->get();

            foreach ($photosToDelete as $photo) {
                if ($photo->foto_url && ! str_starts_with($photo->foto_url, 'http')) {
                    Storage::disk('public')->delete($photo->foto_url);
                }
                $photo->delete();
            }
        }

        // 2. Unggah foto baru jika ada
        if ($request->hasFile('photos')) {
            $currentPhotoCount = $field->photos()->count();
            $maxAllowed = 8;
            $newPhotos = $request->file('photos');

            $newPrimaryIndex = $request->input('primary_new_photo_index');

            foreach ($newPhotos as $idx => $photoFile) {
                if ($currentPhotoCount >= $maxAllowed) {
                    break;
                }

                $path = $photoFile->store("field_photos/{$field->id}", 'public');
                $isPrimary = ($newPrimaryIndex !== null && (int) $newPrimaryIndex === $idx);

                FieldPhoto::create([
                    'field_id' => $field->id,
                    'foto_url' => $path,
                    'is_primary' => $isPrimary,
                ]);

                $currentPhotoCount++;
            }
        }

        // 3. Atur foto utama jika ditentukan
        if ($request->filled('primary_photo_id')) {
            $primaryId = (int) $request->input('primary_photo_id');

            FieldPhoto::where('field_id', $field->id)->update(['is_primary' => false]);
            FieldPhoto::where('field_id', $field->id)
                ->where('id', $primaryId)
                ->update(['is_primary' => true]);
        }

        // Pastikan ada setidaknya satu foto utama jika foto tersedia
        if ($field->photos()->count() > 0 && ! $field->photos()->where('is_primary', true)->exists()) {
            $field->photos()->first()?->update(['is_primary' => true]);
        }

        // 4. Sinkronisasi fasilitas lapangan
        $field->facilities()->sync($validated['facility_ids'] ?? []);

        // 5. Perbarui data spesifikasi lapangan
        $dataToUpdate = [
            'nama_lapangan' => $validated['nama_lapangan'],
            'sport_id' => $validated['sport_id'],
            'harga_perjam' => $validated['harga_perjam'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'tipe_bangunan' => $validated['tipe_bangunan'],
            'jenis_permukaan' => $validated['jenis_permukaan'],
            'panjang' => $validated['panjang'] ?? null,
            'lebar' => $validated['lebar'] ?? null,
            'kapasitas' => $validated['kapasitas'] ?? null,
            'aturan_lapangan' => $validated['aturan_lapangan'] ?? null,
        ];

        // 6. Tangani aksi ajukan kembali jika status draft / rejected
        if ($action === 'submit' && in_array($field->status_lapangan, ['draft', 'rejected'], true)) {
            if ($field->venue->status_vanue !== 'aktif') {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', "Venue '{$field->venue->nama_venue}' belum berstatus Aktif. Lapangan hanya dapat diajukan setelah venue disetujui (OQ-010).");
            }

            if ($field->photos()->count() === 0) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Wajib mengunggah minimal 1 foto fasilitas lapangan sebelum mengajukan peninjauan.');
            }

            $dataToUpdate['status_lapangan'] = 'pending_review';
            $dataToUpdate['alasan_penolakan'] = null;
            $dataToUpdate['submitted_at'] = now();

            $field->update($dataToUpdate);

            return redirect()
                ->route('mitra.venues.fields.index', $field->venue_id)
                ->with('success', "Lapangan '{$field->nama_lapangan}' berhasil diperbarui dan diajukan untuk peninjauan.");
        }

        $field->update($dataToUpdate);

        return redirect()
            ->route('mitra.venues.fields.index', $field->venue_id)
            ->with('success', "Data lapangan '{$field->nama_lapangan}' berhasil diperbarui.");
    }

    /**
     * T5.4 / FR-FIELD-002: Mengajukan lapangan berstatus draft atau rejected untuk moderasi admin.
     */
    public function submitForReview(Request $request, Field $field): RedirectResponse
    {
        Gate::authorize('update', $field);

        if (! in_array($field->status_lapangan, ['draft', 'rejected'], true)) {
            return redirect()
                ->back()
                ->with('error', 'Hanya lapangan berstatus draft atau ditolak yang dapat diajukan untuk peninjauan.');
        }

        // Syarat OQ-010: Venue induk wajib berstatus aktif
        if ($field->venue?->status_vanue !== 'aktif') {
            return redirect()
                ->back()
                ->with('error', "Venue '{$field->venue?->nama_venue}' harus berstatus Aktif terlebih dahulu sebelum unit lapangan dapat diajukan (OQ-010).");
        }

        // Syarat minimal 1 foto (T5.3)
        if ($field->photos()->count() === 0) {
            return redirect()
                ->back()
                ->with('error', 'Harap unggah minimal 1 foto fasilitas lapangan sebelum mengajukan peninjauan.');
        }

        // Validasi kelengkapan data
        $missingFields = [];
        if (empty($field->nama_lapangan)) {
            $missingFields[] = 'Nama Lapangan';
        }
        if (empty($field->sport_id)) {
            $missingFields[] = 'Cabang Olahraga';
        }
        if (empty($field->harga_perjam) || $field->harga_perjam <= 0) {
            $missingFields[] = 'Tarif Sewa per Jam';
        }
        if (empty($field->tipe_bangunan)) {
            $missingFields[] = 'Tipe Bangunan';
        }
        if (empty($field->jenis_permukaan)) {
            $missingFields[] = 'Jenis Permukaan';
        }

        if (! empty($missingFields)) {
            return redirect()
                ->back()
                ->with('error', 'Data belum lengkap untuk diajukan: '.implode(', ', $missingFields));
        }

        $field->update([
            'status_lapangan' => 'pending_review',
            'alasan_penolakan' => null,
            'submitted_at' => now(),
        ]);

        return redirect()
            ->route('mitra.venues.fields.index', $field->venue_id)
            ->with('success', "Lapangan '{$field->nama_lapangan}' berhasil diajukan untuk ditinjau oleh Admin.");
    }

    /**
     * T5.6 / FR-FIELD-004: Mengubah status operasional lapangan (Aktif <-> Nonaktif) dengan safeguard booking aktif (OQ-012).
     */
    public function toggleStatus(Request $request, Field $field): RedirectResponse
    {
        Gate::authorize('update', $field);

        if ($field->status_lapangan === 'aktif') {
            // Safeguard OQ-012: Tolak penonaktifan jika ada booking aktif mendatang
            $today = now()->toDateString();
            $activeUpcomingBookingsCount = BookingDetail::where('field_id', $field->id)
                ->whereDate('tanggal_sewa', '>=', $today)
                ->whereIn('status_item', ['confirmed', 'paid', 'checked_in'])
                ->count();

            if ($activeUpcomingBookingsCount > 0) {
                return redirect()
                    ->back()
                    ->with('error', "Tidak dapat menonaktifkan lapangan '{$field->nama_lapangan}' karena masih memiliki {$activeUpcomingBookingsCount} pesanan aktif mendatang (OQ-012).");
            }

            $field->update([
                'status_lapangan' => 'nonaktif',
            ]);

            return redirect()
                ->back()
                ->with('success', "Operasional lapangan '{$field->nama_lapangan}' berhasil dinonaktifkan.");
        }

        if ($field->status_lapangan === 'nonaktif') {
            if ($field->venue?->status_vanue !== 'aktif') {
                return redirect()
                    ->back()
                    ->with('error', "Tidak dapat mengaktifkan lapangan karena venue '{$field->venue?->nama_venue}' sedang tidak aktif.");
            }

            $field->update([
                'status_lapangan' => 'aktif',
            ]);

            return redirect()
                ->back()
                ->with('success', "Operasional lapangan '{$field->nama_lapangan}' berhasil diaktifkan kembali.");
        }

        return redirect()
            ->back()
            ->with('error', 'Hanya lapangan yang sudah disetujui (aktif/nonaktif) yang dapat diubah status operasionalnya secara langsung.');
    }

    /**
     * T5.3: Menghapus satu foto lapangan secara spesifik.
     */
    public function deletePhoto(Field $field, FieldPhoto $photo): JsonResponse|RedirectResponse
    {
        Gate::authorize('update', $field);

        if ($photo->field_id !== $field->id) {
            abort(404);
        }

        $wasPrimary = $photo->is_primary;

        if ($photo->foto_url && ! str_starts_with($photo->foto_url, 'http')) {
            Storage::disk('public')->delete($photo->foto_url);
        }

        $photo->delete();

        // Jika foto yang dihapus adalah foto utama, jadikan foto pertama yang tersisa sebagai utama
        if ($wasPrimary) {
            $field->photos()->first()?->update(['is_primary' => true]);
        }

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Foto lapangan berhasil dihapus.',
            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Foto lapangan berhasil dihapus.');
    }

    /**
     * T5.3: Menandai satu foto sebagai foto cover utama.
     */
    public function setPrimaryPhoto(Field $field, FieldPhoto $photo): JsonResponse|RedirectResponse
    {
        Gate::authorize('update', $field);

        if ($photo->field_id !== $field->id) {
            abort(404);
        }

        FieldPhoto::where('field_id', $field->id)->update(['is_primary' => false]);
        $photo->update(['is_primary' => true]);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Foto utama berhasil diperbarui.',
            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Foto utama berhasil diperbarui.');
    }

    /**
     * Menghapus unit lapangan (hanya untuk status draft).
     */
    public function destroy(Field $field): RedirectResponse
    {
        Gate::authorize('delete', $field);

        if ($field->status_lapangan !== 'draft') {
            return redirect()
                ->back()
                ->with('error', 'Hanya lapangan berstatus draft yang dapat dihapus secara permanen.');
        }

        // Hapus berkas foto
        foreach ($field->photos as $photo) {
            if ($photo->foto_url && ! str_starts_with($photo->foto_url, 'http')) {
                Storage::disk('public')->delete($photo->foto_url);
            }
        }

        $venueId = $field->venue_id;
        $namaLapangan = $field->nama_lapangan;

        $field->facilities()->detach();
        $field->photos()->delete();
        $field->delete();

        return redirect()
            ->route('mitra.venues.fields.index', $venueId)
            ->with('success', "Draft lapangan '{$namaLapangan}' berhasil dihapus.");
    }
}
