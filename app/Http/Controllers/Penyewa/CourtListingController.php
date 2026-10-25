<?php

namespace App\Http\Controllers\Penyewa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourtListingController extends Controller
{
    /**
     * Tampilkan halaman eksplorasi & pencarian daftar lapangan olahraga.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search', '');
        $sport = strtolower($request->query('sport', 'all'));
        $city = strtolower($request->query('city', 'all'));
        $surface = strtolower($request->query('surface', 'all'));
        $sort = strtolower($request->query('sort', 'popular'));

        $allCourts = collect([
            [
                'id' => 1,
                'venue_name' => 'Viva Arena Futsal',
                'field_name' => 'Lapangan Vinyl A',
                'sport' => 'FUTSAL',
                'sport_slug' => 'futsal',
                'court_type' => 'Indoor Vinyl Mat',
                'surface' => 'vinyl',
                'location' => 'Renon, Denpasar',
                'city' => 'denpasar',
                'rating' => 4.8,
                'reviews_count' => 128,
                'price_per_hour' => 150000,
                'facilities' => ['Parkir Luas', 'Kantin', 'Musholla', 'Shower'],
                'open_hours' => '08:00 - 23:00',
                'image_src' => asset('images/courts/futsal.jpg'),
            ],
            [
                'id' => 2,
                'venue_name' => 'Smash Center Arena',
                'field_name' => 'Lapangan 1 & 2',
                'sport' => 'BADMINTON',
                'sport_slug' => 'badminton',
                'court_type' => 'Karpet Standar BWF',
                'surface' => 'karpet',
                'location' => 'Goa Gong, Jimbaran',
                'city' => 'badung',
                'rating' => 4.9,
                'reviews_count' => 96,
                'price_per_hour' => 120000,
                'facilities' => ['AC / Sejuk', 'Loker', 'Kantin', 'Parkir Gratis'],
                'open_hours' => '07:00 - 23:00',
                'image_src' => asset('images/courts/badminton.jpg'),
            ],
            [
                'id' => 3,
                'venue_name' => 'Champion Tennis Club',
                'field_name' => 'Court 2 Outdoor',
                'sport' => 'TENIS',
                'sport_slug' => 'tenis',
                'court_type' => 'Outdoor Hard Court',
                'surface' => 'hard_court',
                'location' => 'Kediri, Tabanan',
                'city' => 'tabanan',
                'rating' => 4.7,
                'reviews_count' => 64,
                'price_per_hour' => 175000,
                'facilities' => ['Lampu Malam', 'Sewa Raket', 'Kantin', 'Tribun'],
                'open_hours' => '06:00 - 22:00',
                'image_src' => asset('images/courts/tennis.jpg'),
            ],
            [
                'id' => 4,
                'venue_name' => 'Hoops Hall Arena',
                'field_name' => 'Full Court Timber',
                'sport' => 'BASKET',
                'sport_slug' => 'basket',
                'court_type' => 'Indoor Parquet Wood',
                'surface' => 'parquet',
                'location' => 'Kedonganan, Kuta Selatan',
                'city' => 'badung',
                'rating' => 4.9,
                'reviews_count' => 142,
                'price_per_hour' => 300000,
                'facilities' => ['Full AC', 'Tribun', 'Papan Skor Digital', 'Loker'],
                'open_hours' => '08:00 - 24:00',
                'image_src' => asset('images/courts/basketball.jpg'),
            ],
            [
                'id' => 5,
                'venue_name' => 'Bali Futsal Center',
                'field_name' => 'Court B Interlock',
                'sport' => 'FUTSAL',
                'sport_slug' => 'futsal',
                'court_type' => 'Semi-Indoor Interlock',
                'surface' => 'interlock',
                'location' => 'Teuku Umar Barat',
                'city' => 'denpasar',
                'rating' => 4.6,
                'reviews_count' => 75,
                'price_per_hour' => 160000,
                'facilities' => ['Parkir Luas', 'Kantin', 'Musholla'],
                'open_hours' => '08:00 - 23:00',
                'image_src' => asset('images/courts/futsal.jpg'),
            ],
            [
                'id' => 6,
                'venue_name' => 'Sanur Grand Tennis',
                'field_name' => 'Outdoor Court A',
                'sport' => 'TENIS',
                'sport_slug' => 'tenis',
                'court_type' => 'Outdoor Hard Court',
                'surface' => 'hard_court',
                'location' => 'Sanur, Denpasar',
                'city' => 'denpasar',
                'rating' => 4.7,
                'reviews_count' => 52,
                'price_per_hour' => 125000,
                'facilities' => ['Lampu Malam', 'Parkir', 'Kantin'],
                'open_hours' => '06:00 - 21:00',
                'image_src' => asset('images/courts/tennis.jpg'),
            ],
            [
                'id' => 7,
                'venue_name' => 'Gatot Subroto Futsal Arena',
                'field_name' => 'Lapangan Rumput Sintetis',
                'sport' => 'FUTSAL',
                'sport_slug' => 'futsal',
                'court_type' => 'Rumput Sintetis Monofilament',
                'surface' => 'sintetis',
                'location' => 'Gatot Subroto Barat, Denpasar',
                'city' => 'denpasar',
                'rating' => 4.5,
                'reviews_count' => 83,
                'price_per_hour' => 140000,
                'facilities' => ['Kantin', 'Parkir', 'Rompi Gratis'],
                'open_hours' => '09:00 - 24:00',
                'image_src' => asset('images/courts/futsal.jpg'),
            ],
            [
                'id' => 8,
                'venue_name' => 'Paradise Badminton Hall',
                'field_name' => 'Court VIP 3',
                'sport' => 'BADMINTON',
                'sport_slug' => 'badminton',
                'court_type' => 'Karpet Standar BWF',
                'surface' => 'karpet',
                'location' => 'Batubulan, Gianyar',
                'city' => 'gianyar',
                'rating' => 4.8,
                'reviews_count' => 110,
                'price_per_hour' => 110000,
                'facilities' => ['Kantin', 'Musholla', 'Parkir Luas', 'Loker'],
                'open_hours' => '08:00 - 23:00',
                'image_src' => asset('images/courts/badminton.jpg'),
            ],
        ]);

        // Filter: Search Keyword
        $filtered = $allCourts;
        if (!empty($search)) {
            $filtered = $filtered->filter(function ($court) use ($search) {
                return str_contains(strtolower($court['venue_name']), strtolower($search))
                    || str_contains(strtolower($court['field_name']), strtolower($search))
                    || str_contains(strtolower($court['location']), strtolower($search))
                    || str_contains(strtolower($court['court_type']), strtolower($search));
            });
        }

        // Filter: Sport
        if ($sport !== 'all') {
            $filtered = $filtered->where('sport_slug', $sport);
        }

        // Filter: City
        if ($city !== 'all') {
            $filtered = $filtered->where('city', $city);
        }

        // Filter: Surface
        if ($surface !== 'all') {
            $filtered = $filtered->where('surface', $surface);
        }

        // Sort
        if ($sort === 'price_low') {
            $filtered = $filtered->sortBy('price_per_hour');
        } elseif ($sort === 'price_high') {
            $filtered = $filtered->sortByDesc('price_per_hour');
        } elseif ($sort === 'rating') {
            $filtered = $filtered->sortByDesc('rating');
        } else {
            // popular
            $filtered = $filtered->sortByDesc('reviews_count');
        }

        // Sport Category Counts
        $sportCounts = [
            'all'       => $allCourts->count(),
            'futsal'    => $allCourts->where('sport_slug', 'futsal')->count(),
            'badminton' => $allCourts->where('sport_slug', 'badminton')->count(),
            'tenis'     => $allCourts->where('sport_slug', 'tenis')->count(),
            'basket'    => $allCourts->where('sport_slug', 'basket')->count(),
        ];

        return view('penyewa.lapangan', [
            'courts'      => $filtered->values(),
            'sportCounts' => $sportCounts,
            'currentSport' => $sport,
            'currentCity'  => $city,
            'currentSurface' => $surface,
            'currentSort'  => $sort,
            'search'       => $search,
            'totalCount'   => $filtered->count(),
        ]);
    }
}
