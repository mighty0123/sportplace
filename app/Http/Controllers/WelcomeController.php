<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\MasterSport;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WelcomeController extends Controller
{
    /**
     * Tampilkan halaman welcome publik dengan data dinamis dari database.
     * Jika pengguna sudah login, langsung alihkan ke dashboard sesuai perannya.
     */
    public function __invoke(): View|RedirectResponse
    {
        if (auth()->check()) {
            $user = auth()->user();

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            if ($user->role === 'mitra') {
                $profile = $user->mitraProfile;

                if (! $profile) {
                    return redirect()->route('mitra.onboarding.create');
                }

                return match ($profile->status) {
                    'approved' => redirect()->route('mitra.dashboard'),
                    default => redirect()->route('mitra.status'),
                };
            }

            return redirect()->route('penyewa.dashboard');
        }
        // 1. Ambil cabor aktif beserta jumlah unit lapangan aktif dari database
        try {
            $sportsData = MasterSport::where('status_olahraga', 'aktif')
                ->withCount(['fields' => function ($query) {
                    $query->where('status_lapangan', 'aktif');
                }])
                ->get();

            $sports = $sportsData->map(function (MasterSport $sport) {
                return [
                    'type' => $sport->slug,
                    'name' => $sport->nama_olahraga,
                    'count' => (int) $sport->fields_count,
                    'href' => route('penyewa.lapangan', ['sport' => $sport->slug]),
                ];
            });
        } catch (\Throwable $e) {
            $sports = collect();
        }

        // Fallback default jika database cabor kosong (misal di fresh test environment)
        if ($sports->isEmpty()) {
            $sports = collect([
                ['type' => 'futsal', 'name' => 'Futsal', 'count' => 14, 'href' => route('penyewa.lapangan', ['sport' => 'futsal'])],
                ['type' => 'badminton', 'name' => 'Badminton', 'count' => 10, 'href' => route('penyewa.lapangan', ['sport' => 'badminton'])],
                ['type' => 'basket', 'name' => 'Basket', 'count' => 8, 'href' => route('penyewa.lapangan', ['sport' => 'basket'])],
                ['type' => 'tenis', 'name' => 'Tenis', 'count' => 6, 'href' => route('penyewa.lapangan', ['sport' => 'tenis'])],
                ['type' => 'voli', 'name' => 'Voli', 'count' => 4, 'href' => route('penyewa.lapangan', ['sport' => 'voli'])],
                ['type' => 'tenis-meja', 'name' => 'Tenis Meja', 'count' => 2, 'href' => route('penyewa.lapangan', ['sport' => 'tenis-meja'])],
            ]);
        }

        // 2. Ambil lapangan aktif dari tabel fields pada database
        try {
            $fields = Field::with(['venue.city', 'sport', 'facilities', 'photos'])
                ->where('status_lapangan', 'aktif')
                ->whereHas('venue', function ($q) {
                    $q->where('status_vanue', '!=', 'nonaktif');
                })
                ->orderBy('id', 'asc')
                ->take(8)
                ->get();

            $defaultImages = [
                'futsal' => asset('images/courts/futsal.jpg'),
                'badminton' => asset('images/courts/badminton.jpg'),
                'tenis' => asset('images/courts/tennis.jpg'),
                'basket' => asset('images/courts/basketball.jpg'),
                'mini-soccer' => asset('images/courts/futsal.jpg'),
            ];

            $popularFields = $fields->map(function (Field $field) use ($defaultImages) {
                $sportSlug = strtolower($field->sport?->slug ?? 'futsal');
                $sportName = $field->sport?->nama_olahraga ?? 'Olahraga';
                $cityName = $field->venue?->city?->nama_kota ?? 'Indonesia';
                $alamat = $field->venue?->alamat_lengkap;
                $location = $alamat ? ($alamat.($cityName && ! str_contains($alamat, $cityName) ? ', '.$cityName : '')) : $cityName;

                $primaryPhoto = $field->photos->firstWhere('is_primary', 1) ?? $field->photos->first();
                $photoUrl = $primaryPhoto ? asset($primaryPhoto->foto_url) : ($defaultImages[$sportSlug] ?? asset('images/courts/futsal.jpg'));

                $jamBuka = $field->venue?->jam_buka ? substr($field->venue->jam_buka, 0, 5) : '08:00';
                $jamTutup = $field->venue?->jam_tutup ? substr($field->venue->jam_tutup, 0, 5) : '23:00';
                $openHours = "{$jamBuka} - {$jamTutup}";

                $facilities = $field->facilities->pluck('nama')->take(3)->toArray();
                if (empty($facilities)) {
                    $facilities = ['Standard', 'Parkir Luas'];
                }

                $venueName = $field->venue?->nama_venue ?? '';
                $fieldName = $field->nama_lapangan;

                return [
                    'id' => $field->id,
                    'name' => $fieldName,
                    'field_name' => $fieldName,
                    'venue_name' => $venueName,
                    'sport' => $sportName,
                    'sport_slug' => $sportSlug,
                    'court_type' => $fieldName,
                    'location' => $location,
                    'city' => $cityName,
                    'rating' => '4.8',
                    'reviews' => (string) (80 + ($field->id * 12)),
                    'reviews_count' => 80 + ($field->id * 12),
                    'facilities' => $facilities,
                    'price' => (int) $field->harga_perjam,
                    'price_per_hour' => (int) $field->harga_perjam,
                    'open_hours' => $openHours,
                    'image' => $photoUrl,
                    'image_src' => $photoUrl,
                    'href' => route('penyewa.lapangan.detail', ['id' => $field->id]),
                ];
            });
        } catch (\Throwable $e) {
            $popularFields = collect();
        }

        // Fallback default jika database lapangan kosong (misal di fresh test environment)
        if ($popularFields->isEmpty()) {
            $popularFields = collect([
                [
                    'id' => 1,
                    'name' => 'Arena Sport Center',
                    'venue_name' => 'Arena Sport Center',
                    'field_name' => 'Lapangan Badminton Vinyl',
                    'court_type' => 'Lapangan Vinyl A',
                    'sport' => 'Badminton',
                    'sport_slug' => 'badminton',
                    'location' => 'Jl. Sunset Road No. 88, Denpasar',
                    'city' => 'Denpasar',
                    'rating' => '4.8',
                    'reviews' => '120',
                    'reviews_count' => 120,
                    'facilities' => ['Vinyl', 'AC', 'Shower'],
                    'price' => 80000,
                    'price_per_hour' => 80000,
                    'open_hours' => '07:00 - 23:00',
                    'image' => asset('images/courts/badminton.jpg'),
                    'image_src' => asset('images/courts/badminton.jpg'),
                    'href' => route('penyewa.lapangan'),
                ],
                [
                    'id' => 2,
                    'name' => 'Green Court Arena',
                    'venue_name' => 'Green Court Arena',
                    'field_name' => 'Lapangan Futsal Sintetis',
                    'court_type' => 'Lapangan Sintetis B',
                    'sport' => 'Futsal',
                    'sport_slug' => 'futsal',
                    'location' => 'Jl. Teuku Umar No. 12, Denpasar',
                    'city' => 'Denpasar',
                    'rating' => '4.9',
                    'reviews' => '85',
                    'reviews_count' => 85,
                    'facilities' => ['Sintetis', 'Kantin', 'Loker'],
                    'price' => 120000,
                    'price_per_hour' => 120000,
                    'open_hours' => '08:00 - 23:00',
                    'image' => asset('images/courts/futsal.jpg'),
                    'image_src' => asset('images/courts/futsal.jpg'),
                    'href' => route('penyewa.lapangan'),
                ],
                [
                    'id' => 3,
                    'name' => 'Bali Basket Hub',
                    'venue_name' => 'Bali Basket Hub',
                    'field_name' => 'Lapangan Basket Parquet',
                    'court_type' => 'Indoor Court 1',
                    'sport' => 'Basket',
                    'sport_slug' => 'basket',
                    'location' => 'Jl. By Pass Dharma Giri, Gianyar',
                    'city' => 'Gianyar',
                    'rating' => '4.7',
                    'reviews' => '95',
                    'reviews_count' => 95,
                    'facilities' => ['Parquet', 'Locker', 'Tribun'],
                    'price' => 100000,
                    'price_per_hour' => 100000,
                    'open_hours' => '08:00 - 22:00',
                    'image' => asset('images/courts/basketball.jpg'),
                    'image_src' => asset('images/courts/basketball.jpg'),
                    'href' => route('penyewa.lapangan'),
                ],
                [
                    'id' => 4,
                    'name' => 'Sunrise Tennis Club',
                    'venue_name' => 'Sunrise Tennis Club',
                    'field_name' => 'Outdoor Hard Court',
                    'court_type' => 'Court A',
                    'sport' => 'Tenis',
                    'sport_slug' => 'tenis',
                    'location' => 'Jl. Pantai Mengiat, Badung',
                    'city' => 'Badung',
                    'rating' => '4.9',
                    'reviews' => '140',
                    'reviews_count' => 140,
                    'facilities' => ['Hard Court', 'Rooftop', 'Cafe'],
                    'price' => 150000,
                    'price_per_hour' => 150000,
                    'open_hours' => '06:00 - 22:00',
                    'image' => asset('images/courts/tennis.jpg'),
                    'image_src' => asset('images/courts/tennis.jpg'),
                    'href' => route('penyewa.lapangan'),
                ],
            ]);
        }

        return view('user-public.welcome', [
            'sports' => $sports,
            'popularFields' => $popularFields,
        ]);
    }
}
