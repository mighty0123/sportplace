<?php

namespace App\Http\Controllers\Penyewa;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman Profil Penyewa.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();

        // Data profil yang terintegrasi dengan user login
        $profile = [
            'name'        => $user?->name ?? 'Dimas Pratama',
            'nickname'    => 'Dimas',
            'email'       => $user?->email ?? 'dimas.pratama@email.com',
            'phone'       => $user?->no_hp ?? '+62 812-3456-7890',
            'gender'      => 'Laki-laki',
            'birthdate'   => '14 Agustus 1996',
            'occupation'  => 'Lead Product Designer',
            'city'        => 'Denpasar, Bali',
            'member_since'=> 'April 2023',
            'status'      => 'Penyewa Aktif',
            'avatar'      => asset('images/avatars/dimas-pratama.jpg'),
        ];

        // Preferensi Olahraga Favorit
        $sportsPreferences = [
            ['name' => 'Futsal', 'spec' => 'Vinyl & Rumput', 'icon' => '⚽'],
            ['name' => 'Badminton', 'spec' => 'Karpet Enlio', 'icon' => '🏸'],
            ['name' => 'Basket', 'spec' => '', 'icon' => '🏀'],
            ['name' => 'Tenis Lapangan', 'spec' => '', 'icon' => '🎾'],
        ];

        // Alamat Utama
        $primaryAddress = [
            'title'       => 'Rumah Senopati Residence',
            'is_primary'  => true,
            'address'     => 'Jl. Senopati Dalam II No. 18B, RT 04 / RW 02, Kebayoran Baru, Jakarta Selatan, DKI Jakarta 12190',
        ];

        // Aktivitas Booking Terkini
        $recentActivities = [
            [
                'sport'          => 'futsal',
                'venue_name'     => 'Arena Futsal Cilandak - Lapangan Vinyl A',
                'schedule'       => 'Jumat, 24 Mei 2024 • 19:00 - 21:00 WIB',
                'price'          => 350000,
                'payment_method' => 'QRIS',
                'status_label'   => 'Selesai Main',
            ],
            [
                'sport'          => 'badminton',
                'venue_name'     => 'Grand Smash Badminton Court 3 (Enlio)',
                'schedule'       => 'Minggu, 19 Mei 2024 • 08:00 - 10:00 WIB',
                'price'          => 180000,
                'payment_method' => 'Gopay',
                'status_label'   => 'Selesai Main',
            ],
        ];

        return view('penyewa.profil', [
            'profile'           => $profile,
            'sportsPreferences' => $sportsPreferences,
            'primaryAddress'    => $primaryAddress,
            'recentActivities'  => $recentActivities,
        ]);
    }

    /**
     * Perbarui data profil penyewa.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:100'],
            'no_hp' => ['required', 'string', 'max:20'],
        ]);

        $user = auth()->user();
        if ($user) {
            $user->update($validated);
        }

        return redirect()->route('penyewa.profil')->with('success', 'Data profil berhasil diperbarui!');
    }
}
