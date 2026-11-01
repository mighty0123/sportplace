<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mitra\StoreVenueRequest;
use App\Http\Requests\Mitra\UpdateVenueRequest;
use App\Models\BookingDetail;
use App\Models\MasterCity;
use App\Models\Venue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class VenueController extends Controller
{
    /**
     * MTR-04: Menampilkan daftar venue milik mitra yang sedang login.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $mitraProfile = $user->mitraProfile;

        // Query dasar venue milik mitra yang bersangkutan (BR-023)
        $venuesQuery = Venue::with(['city', 'fields.sport'])
            ->where('mitra_id', $mitraProfile->id);

        // Hitung statistik untuk metric cards di atas
        $allVenues = (clone $venuesQuery)->get();
        $totalVenues = $allVenues->count();
        $activeVenues = $allVenues->where('status_vanue', 'aktif')->count();
        $pendingVenues = $allVenues->where('status_vanue', 'pending_review')->count();
        $rejectedVenues = $allVenues->where('status_vanue', 'rejected')->count();
        $draftVenues = $allVenues->where('status_vanue', 'draft')->count();
        $nonaktifVenues = $allVenues->where('status_vanue', 'nonaktif')->count();

        // Filter pencarian teks
        if ($search = $request->input('search')) {
            $venuesQuery->where(function ($q) use ($search) {
                $q->where('nama_venue', 'like', "%{$search}%")
                    ->orWhere('alamat_lengkap', 'like', "%{$search}%");
            });
        }

        // Filter kota
        if ($cityId = $request->input('city_id')) {
            $venuesQuery->where('city_id', $cityId);
        }

        // Filter status
        if ($status = $request->input('status')) {
            if ($status !== 'semua') {
                $venuesQuery->where('status_vanue', $status);
            }
        }

        $venues = $venuesQuery->orderByDesc('id')->get();
        $cities = MasterCity::where('status_kota', 'aktif')->orderBy('nama_kota')->get();

        return view('mitra.venues.index', compact(
            'venues',
            'cities',
            'totalVenues',
            'activeVenues',
            'pendingVenues',
            'rejectedVenues',
            'draftVenues',
            'nonaktifVenues'
        ));
    }

    /**
     * MTR-05b / FR-VENUE-001: Form pembuatan venue baru.
     */
    public function create(): View
    {
        Gate::authorize('create', Venue::class);

        $cities = MasterCity::where('status_kota', 'aktif')->orderBy('nama_kota')->get();

        return view('mitra.venues.create', compact('cities'));
    }

    /**
     * MTR-05b / FR-VENUE-001 & FR-VENUE-002: Menyimpan venue baru (draft atau langsung ajukan).
     */
    public function store(StoreVenueRequest $request): RedirectResponse
    {
        Gate::authorize('create', Venue::class);

        $validated = $request->validated();
        $action = $request->input('action', 'draft');

        $mitraProfile = Auth::user()->mitraProfile;

        // Tentukan status awal berdasarkan tombol submit yang ditekan
        $status = ($action === 'submit') ? 'pending_review' : 'draft';

        $venue = Venue::create([
            'mitra_id' => $mitraProfile->id,
            'city_id' => $validated['city_id'],
            'nama_venue' => $validated['nama_venue'],
            'alamat_lengkap' => $validated['alamat_lengkap'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'map_url' => $validated['map_url'] ?? null,
            'jam_buka' => $validated['jam_buka'],
            'jam_tutup' => $validated['jam_tutup'],
            'status_vanue' => $status,
            'alasan_penolakan' => null,
        ]);

        if ($status === 'pending_review') {
            return redirect()
                ->route('mitra.venues.index')
                ->with('success', "Venue '{$venue->nama_venue}' berhasil disimpan dan diajukan untuk ditinjau oleh Admin.");
        }

        return redirect()
            ->route('mitra.venues.index')
            ->with('success', "Venue '{$venue->nama_venue}' berhasil disimpan sebagai draft.");
    }

    /**
     * MTR-05b / FR-VENUE-003: Form edit venue.
     */
    public function edit(Venue $venue): View
    {
        Gate::authorize('update', $venue);

        $cities = MasterCity::where('status_kota', 'aktif')->orderBy('nama_kota')->get();

        return view('mitra.venues.edit', compact('venue', 'cities'));
    }

    /**
     * MTR-05b / FR-VENUE-003: Memperbarui data venue milik mitra.
     */
    public function update(UpdateVenueRequest $request, Venue $venue): RedirectResponse
    {
        Gate::authorize('update', $venue);

        $validated = $request->validated();
        $action = $request->input('action');

        $dataToUpdate = [
            'city_id' => $validated['city_id'],
            'nama_venue' => $validated['nama_venue'],
            'alamat_lengkap' => $validated['alamat_lengkap'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'map_url' => $validated['map_url'] ?? null,
            'jam_buka' => $validated['jam_buka'],
            'jam_tutup' => $validated['jam_tutup'],
        ];

        // Jika tombol aksi adalah "submit" (ajukan peninjauan kembali dari status draft/rejected)
        if ($action === 'submit' && in_array($venue->status_vanue, ['draft', 'rejected'], true)) {
            $dataToUpdate['status_vanue'] = 'pending_review';
            $dataToUpdate['alasan_penolakan'] = null;

            $venue->update($dataToUpdate);

            return redirect()
                ->route('mitra.venues.index')
                ->with('success', "Venue '{$venue->nama_venue}' berhasil diperbarui dan diajukan untuk peninjauan.");
        }

        // Default OQ-011: Edit pada venue aktif tidak memicu moderasi ulang
        $venue->update($dataToUpdate);

        return redirect()
            ->route('mitra.venues.index')
            ->with('success', "Data venue '{$venue->nama_venue}' berhasil diperbarui.");
    }

    /**
     * FR-VENUE-002: Mengajukan venue berstatus draft atau rejected untuk ditinjau admin.
     */
    public function submitForReview(Request $request, Venue $venue): RedirectResponse
    {
        Gate::authorize('update', $venue);

        if (! in_array($venue->status_vanue, ['draft', 'rejected'], true)) {
            return redirect()
                ->back()
                ->with('error', 'Hanya venue berstatus draft atau ditolak yang dapat diajukan untuk peninjauan.');
        }

        // Validasi kelengkapan data wajib sebelum diajukan (FR-VENUE-002)
        $missingFields = [];
        if (empty($venue->nama_venue)) {
            $missingFields[] = 'Nama Venue';
        }
        if (empty($venue->city_id)) {
            $missingFields[] = 'Kota/Wilayah';
        }
        if (empty($venue->alamat_lengkap)) {
            $missingFields[] = 'Alamat Lengkap';
        }
        if (empty($venue->jam_buka) || empty($venue->jam_tutup)) {
            $missingFields[] = 'Jam Operasional Buka & Tutup';
        }

        if (! empty($missingFields)) {
            return redirect()
                ->back()
                ->with('error', 'Data belum lengkap untuk diajukan: '.implode(', ', $missingFields));
        }

        $venue->update([
            'status_vanue' => 'pending_review',
            'alasan_penolakan' => null,
        ]);

        return redirect()
            ->route('mitra.venues.index')
            ->with('success', "Venue '{$venue->nama_venue}' berhasil diajukan untuk ditinjau oleh Admin.");
    }

    /**
     * FR-VENUE-004: Menonaktifkan venue aktif (OQ-012).
     */
    public function deactivate(Request $request, Venue $venue): RedirectResponse
    {
        Gate::authorize('delete', $venue);

        if ($venue->status_vanue !== 'aktif') {
            return redirect()
                ->back()
                ->with('error', 'Hanya venue berstatus aktif yang dapat dinonaktifkan.');
        }

        // Safeguard OQ-012: Tolak penonaktifan jika ada booking mendatang berstatus aktif
        $fieldIds = $venue->fields()->pluck('id');
        $today = now()->toDateString();

        $activeUpcomingBookingsCount = BookingDetail::whereIn('field_id', $fieldIds)
            ->whereDate('tanggal_sewa', '>=', $today)
            ->whereIn('status_item', ['confirmed', 'paid', 'checked_in'])
            ->count();

        if ($activeUpcomingBookingsCount > 0) {
            return redirect()
                ->back()
                ->with('error', "Tidak dapat menonaktifkan venue ini karena masih memiliki {$activeUpcomingBookingsCount} pesanan aktif mendatang.");
        }

        $venue->update([
            'status_vanue' => 'nonaktif',
        ]);

        return redirect()
            ->route('mitra.venues.index')
            ->with('success', "Venue '{$venue->nama_venue}' berhasil dinonaktifkan.");
    }

    /**
     * Mengaktifkan kembali venue yang sedang berstatus nonaktif.
     */
    public function activate(Request $request, Venue $venue): RedirectResponse
    {
        Gate::authorize('update', $venue);

        if ($venue->status_vanue !== 'nonaktif') {
            return redirect()
                ->back()
                ->with('error', 'Hanya venue berstatus nonaktif yang dapat diaktifkan kembali.');
        }

        $venue->update([
            'status_vanue' => 'aktif',
        ]);

        return redirect()
            ->route('mitra.venues.index')
            ->with('success', "Venue '{$venue->nama_venue}' berhasil diaktifkan kembali.");
    }

    /**
     * Menghapus venue (hanya diperbolehkan untuk status draft).
     */
    public function destroy(Venue $venue): RedirectResponse
    {
        Gate::authorize('delete', $venue);

        if ($venue->status_vanue !== 'draft') {
            return redirect()
                ->back()
                ->with('error', 'Hanya venue berstatus draft yang dapat dihapus.');
        }

        $namaVenue = $venue->nama_venue ?? 'Draft Venue';
        $venue->delete();

        return redirect()
            ->route('mitra.venues.index')
            ->with('success', "Draft venue '{$namaVenue}' berhasil dihapus.");
    }
}
