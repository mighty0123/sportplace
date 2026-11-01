<?php

namespace App\Http\Controllers\Penyewa;

use App\Http\Controllers\Controller;
use App\Models\Field;
use App\Models\MasterCity;
use App\Models\MasterSport;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourtListingController extends Controller
{
    /**
     * Tampilkan halaman eksplorasi & pencarian daftar lapangan olahraga dari database.
     */
    public function index(Request $request): View
    {
        $search  = trim($request->query('search', ''));
        $sport   = strtolower(trim($request->query('sport', 'all')));
        $city    = strtolower(trim($request->query('city', 'all')));
        $surface = strtolower(trim($request->query('surface', 'all')));
        $sort    = strtolower(trim($request->query('sort', 'popular')));

        // Query dasar: Hanya ambil lapangan yang aktif beserta relasinya
        $query = Field::with(['venue.city', 'sport', 'facilities', 'photos'])
            ->where('status_lapangan', 'aktif')
            ->whereHas('venue', function ($q) {
                // Pastikan venue juga aktif jika ada status venue
                $q->where('status_vanue', '!=', 'nonaktif');
            });

        // Filter: Search Keyword
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lapangan', 'LIKE', "%{$search}%")
                  ->orWhereHas('venue', function ($vq) use ($search) {
                      $vq->where('nama_venue', 'LIKE', "%{$search}%")
                         ->orWhere('alamat_lengkap', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('sport', function ($sq) use ($search) {
                      $sq->where('nama_olahraga', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Filter: Sport / Cabang Olahraga
        if ($sport !== 'all') {
            $query->whereHas('sport', function ($sq) use ($sport) {
                $sq->where('slug', $sport);
            });
        }

        // Filter: City / Lokasi Kota
        if ($city !== 'all') {
            $query->whereHas('venue.city', function ($cq) use ($city) {
                $cq->where('nama_kota', 'LIKE', "%{$city}%")
                   ->orWhere('id', $city);
            });
        }

        // Filter: Surface / Jenis Permukaan (Berdasarkan keyword di nama lapangan)
        if ($surface !== 'all') {
            $query->where('nama_lapangan', 'LIKE', "%{$surface}%");
        }

        // Sorting
        if ($sort === 'price_low') {
            $query->orderBy('harga_perjam', 'asc');
        } elseif ($sort === 'price_high') {
            $query->orderBy('harga_perjam', 'desc');
        } else {
            // Default: ID atau populer
            $query->orderBy('id', 'asc');
        }

        $fields = $query->get();

        // Format data lapangan agar ramah dipakai di Blade component
        $courts = $fields->map(function ($field) {
            $sportName = strtoupper($field->sport?->nama_olahraga ?? 'OLAHRAGA');
            $sportSlug = strtolower($field->sport?->slug ?? 'futsal');

            // Default image fallback jika belum ada foto di database
            $defaultImages = [
                'futsal'      => asset('images/courts/futsal.jpg'),
                'badminton'   => asset('images/courts/badminton.jpg'),
                'tenis'       => asset('images/courts/tennis.jpg'),
                'basket'      => asset('images/courts/basketball.jpg'),
                'mini-soccer' => asset('images/courts/futsal.jpg'),
            ];

            $primaryPhoto = $field->photos->firstWhere('is_primary', 1) ?? $field->photos->first();
            $photoUrl = $primaryPhoto ? asset($primaryPhoto->foto_url) : ($defaultImages[$sportSlug] ?? asset('images/courts/futsal.jpg'));

            // Format jam operasional venue
            $jamBuka = $field->venue?->jam_buka ? substr($field->venue->jam_buka, 0, 5) : '08:00';
            $jamTutup = $field->venue?->jam_tutup ? substr($field->venue->jam_tutup, 0, 5) : '23:00';
            $openHours = "{$jamBuka} - {$jamTutup}";

            // Fasilitas dari relasi
            $facilityNames = $field->facilities->pluck('nama')->toArray();
            if (empty($facilityNames)) {
                $facilityNames = ['Parkir Luas', 'Loker', 'Kantin'];
            }

            return [
                'id'             => $field->id,
                'venue_name'     => $field->venue?->nama_venue ?? 'Sport Arena',
                'field_name'     => $field->nama_lapangan,
                'sport'          => $sportName,
                'sport_slug'     => $sportSlug,
                'court_type'     => $field->nama_lapangan,
                'surface'        => $sportSlug,
                'location'       => $field->venue?->alamat_lengkap ?? 'Indonesia',
                'city'           => $field->venue?->city?->nama_kota ?? 'Jakarta',
                'rating'         => 4.8,
                'reviews_count'  => 120 + ($field->id * 7),
                'price_per_hour' => (int) $field->harga_perjam,
                'facilities'     => $facilityNames,
                'open_hours'     => $openHours,
                'image_src'      => $photoUrl,
            ];
        });

        // Hitung jumlah lapangan per cabor secara dinamis dari database
        $sportsInDb = MasterSport::where('status_olahraga', 'aktif')->get();
        $totalActiveFields = Field::where('status_lapangan', 'aktif')->count();

        $sportCounts = ['all' => $totalActiveFields];
        foreach ($sportsInDb as $s) {
            $count = Field::where('status_lapangan', 'aktif')->where('sport_id', $s->id)->count();
            $sportCounts[$s->slug] = $count;
        }

        // Ambil daftar kota aktif untuk dropdown filter
        $cities = MasterCity::where('status_kota', 'aktif')->get();

        return view('penyewa.lapangan', [
            'courts'         => $courts,
            'sportCounts'    => $sportCounts,
            'sportsInDb'     => $sportsInDb,
            'cities'         => $cities,
            'currentSport'   => $sport,
            'currentCity'    => $city,
            'currentSurface' => $surface,
            'currentSort'    => $sort,
            'search'         => $search,
            'totalCount'     => $courts->count(),
        ]);
    }

    /**
     * Tampilkan detail lapangan tertentu sebelum masuk ke kalender jadwal booking.
     */
    public function show(int $id): View
    {
        $field = Field::with(['venue.city', 'venue.mitra', 'sport', 'facilities', 'photos', 'schedules'])
            ->find($id);

        // Jika ID bukan ID Lapangan (misal ID Booking Header), cari lapangan terkait
        if (!$field) {
            $bookingDetail = \App\Models\BookingDetail::where('header_id', $id)->first();
            if ($bookingDetail && $bookingDetail->field_id) {
                $field = Field::with(['venue.city', 'venue.mitra', 'sport', 'facilities', 'photos', 'schedules'])->find($bookingDetail->field_id);
            }
        }

        // Fallback ke lapangan aktif pertama jika belum ketemu
        if (!$field) {
            $field = Field::with(['venue.city', 'venue.mitra', 'sport', 'facilities', 'photos', 'schedules'])
                ->where('status_lapangan', 'aktif')
                ->firstOrFail();
        }

        // Simpan ke session agar flow booking konsisten
        session(['selected_field_id' => $field->id, 'last_field_id' => $field->id]);

        $sportSlug = strtolower($field->sport?->slug ?? 'futsal');
        $defaultImages = [
            'futsal'      => asset('images/courts/futsal.jpg'),
            'badminton'   => asset('images/courts/badminton.jpg'),
            'tenis'       => asset('images/courts/tennis.jpg'),
            'basket'      => asset('images/courts/basketball.jpg'),
            'mini-soccer' => asset('images/courts/futsal.jpg'),
        ];

        $primaryPhoto = $field->photos->firstWhere('is_primary', 1) ?? $field->photos->first();
        $photoUrl = $primaryPhoto ? asset($primaryPhoto->foto_url) : ($defaultImages[$sportSlug] ?? asset('images/courts/futsal.jpg'));

        // Lapangan lain di venue yang sama sebagai rekomendasi
        $otherCourts = Field::with(['sport', 'photos'])
            ->where('venue_id', $field->venue_id)
            ->where('id', '!=', $field->id)
            ->where('status_lapangan', 'aktif')
            ->take(3)
            ->get();

        return view('penyewa.lapangan-detail', [
            'field'       => $field,
            'photoUrl'    => $photoUrl,
            'sportSlug'   => $sportSlug,
            'otherCourts' => $otherCourts,
        ]);
    }
}
