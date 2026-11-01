<?php

namespace App\Http\Controllers\Penyewa;

use App\Http\Controllers\Controller;
use App\Models\BookingDetail;
use App\Models\MasterSport;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman Profil Penyewa dengan data riil dari database.
     */
    public function index(Request $request): View
    {
        Carbon::setLocale('id');

        // Ambil user yang sedang login, atau fallback ke user customer pertama di database
        $user = auth()->user() ?? User::where('role', 'customer')->first();

        if (!$user) {
            abort(404, 'Akun penyewa tidak ditemukan.');
        }

        // Cari riwayat booking terkini beserta relasinya untuk mendapatkan lokasi dan aktivitas
        $recentBookings = BookingDetail::whereHas('header', fn($q) => $q->where('penyewa_id', $user->id))
            ->with(['header.payment', 'field.venue.city', 'field.sport'])
            ->orderByDesc('tanggal_sewa')
            ->orderByDesc('jam_mulai')
            ->take(5)
            ->get();

        // 1. Tentukan Kota & Domisili Utama berdasarkan venue yang paling sering/terakhir dibooking
        $latestBooking = $recentBookings->first();
        $cityName = $latestBooking?->field?->venue?->city?->nama_kota ?? 'Jakarta Selatan';
        $provinsi = $latestBooking?->field?->venue?->city?->provinsi ?? 'DKI Jakarta';
        $fullCity = "{$cityName}, {$provinsi}";

        // Tentukan avatar profil: hanya gunakan foto profil yang diunggah pengguna di database
        $avatar = null;
        if (!empty($user->foto_profil)) {
            $avatar = str_starts_with($user->foto_profil, 'http')
                ? $user->foto_profil
                : asset('storage/' . ltrim($user->foto_profil, '/'));
        }

        // Inisial nama
        $initials = collect(explode(' ', trim($user->name)))
            ->take(2)
            ->map(fn ($w) => mb_substr($w, 0, 1))
            ->implode('');

        // 2. Susun Data Profil Utama
        $profile = [
            'id'           => $user->id,
            'name'         => $user->name,
            'nickname'     => explode(' ', trim($user->name))[0] ?? $user->name,
            'email'        => $user->email,
            'phone'        => $user->no_hp ?? '-',
            'gender'       => 'Laki-laki',
            'birthdate'    => $user->created_at ? $user->created_at->translatedFormat('d F Y') : '14 Agustus 1996',
            'occupation'   => 'Member Komunitas SportPlace',
            'city'         => $fullCity,
            'member_since' => $user->created_at ? $user->created_at->translatedFormat('F Y') : 'Oktober 2026',
            'status'       => $user->status_akun === 'aktif' ? 'Penyewa Aktif' : ($user->status_akun ? ucfirst($user->status_akun) : 'Penyewa Aktif'),
            'avatar'       => $avatar,
            'initials'     => $initials,
        ];

        // 3. Preferensi Olahraga Favorit (Dihitung dari riwayat booking database)
        $sportStats = BookingDetail::whereHas('header', fn($q) => $q->where('penyewa_id', $user->id))
            ->join('fields', 'booking_details.field_id', '=', 'fields.id')
            ->join('master_sports', 'fields.sport_id', '=', 'master_sports.id')
            ->select('master_sports.id', 'master_sports.nama_olahraga', 'master_sports.slug', DB::raw('count(booking_details.id) as total_played'))
            ->groupBy('master_sports.id', 'master_sports.nama_olahraga', 'master_sports.slug')
            ->orderByDesc('total_played')
            ->get();

        $icons = [
            'futsal'      => '⚽',
            'badminton'   => '🏸',
            'basket'      => '🏀',
            'tenis'       => '🎾',
            'mini-soccer' => '⚽',
        ];

        $sportsPreferences = $sportStats->map(function ($s) use ($icons) {
            $slug = strtolower($s->slug);
            return [
                'name' => $s->nama_olahraga,
                'spec' => "{$s->total_played}x Sesi Main",
                'icon' => $icons[$slug] ?? '🏅',
            ];
        })->toArray();

        // Fallback jika belum pernah booking: tampilkan cabang olahraga aktif dari master_sports
        if (empty($sportsPreferences)) {
            $sportsPreferences = MasterSport::where('status_olahraga', 'aktif')
                ->take(4)
                ->get()
                ->map(function ($s) use ($icons) {
                    $slug = strtolower($s->slug);
                    return [
                        'name' => $s->nama_olahraga,
                        'spec' => 'Tersedia',
                        'icon' => $icons[$slug] ?? '🏅',
                    ];
                })
                ->toArray();
        }

        // 4. Alamat Utama / Home Base Olahraga
        $latestVenue = $latestBooking?->field?->venue;
        $primaryAddress = [
            'title'      => $latestVenue ? 'Home Base Olahraga (' . $latestVenue->nama_venue . ')' : 'Area Olahraga Utama',
            'is_primary' => true,
            'address'    => ($latestVenue?->alamat_lengkap ? $latestVenue->alamat_lengkap . ', ' : '') . $fullCity,
        ];

        // 5. Aktivitas Booking Terkini
        $recentActivities = $recentBookings->map(function ($b) {
            $sportSlug = strtolower($b->field?->sport?->slug ?? 'futsal');
            $venueName = ($b->field?->venue?->nama_venue ?? 'Sport Arena') . ' - ' . ($b->field?->nama_lapangan ?? 'Lapangan');

            $dateObj = Carbon::parse($b->tanggal_sewa);
            $start = substr($b->jam_mulai, 0, 5);
            $end = substr($b->jam_selesai, 0, 5);
            $schedule = $dateObj->translatedFormat('l, d M Y') . ' • ' . $start . ' - ' . $end . ' WIB';

            $statusLabel = match($b->status_item) {
                'completed' => 'Selesai Main',
                'ongoing'   => 'Jadwal Aktif',
                'no_show'   => 'Tidak Hadir',
                default     => ucfirst($b->status_item ?? 'Selesai Main'),
            };

            return [
                'sport'          => $sportSlug,
                'venue_name'     => $venueName,
                'schedule'       => $schedule,
                'price'          => (float) $b->harga_slot,
                'payment_method' => strtoupper($b->header?->payment?->metode_pembayaran ?? 'QRIS'),
                'status_label'   => $statusLabel,
            ];
        })->toArray();

        return view('penyewa.profil', [
            'profile'           => $profile,
            'sportsPreferences' => $sportsPreferences,
            'primaryAddress'    => $primaryAddress,
            'recentActivities'  => $recentActivities,
        ]);
    }

    /**
     * Perbarui data profil penyewa di database.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'no_hp'       => ['required', 'string', 'max:20'],
            'foto_profil' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ], [
            'name.required'        => 'Nama lengkap wajib diisi.',
            'no_hp.required'       => 'Nomor telepon wajib diisi.',
            'foto_profil.image'    => 'File yang diupload harus berupa gambar.',
            'foto_profil.max'      => 'Ukuran foto profil maksimal 2MB.',
        ]);

        $user = auth()->user() ?? User::where('role', 'customer')->first();

        if ($user) {
            $dataToUpdate = [
                'name'  => $validated['name'],
                'no_hp' => $validated['no_hp'],
            ];

            if ($request->hasFile('foto_profil')) {
                // Hapus foto lama jika tersimpan di disk public
                if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
                    Storage::disk('public')->delete($user->foto_profil);
                }

                $path = $request->file('foto_profil')->store('avatars', 'public');
                $dataToUpdate['foto_profil'] = $path;
            }

            $user->update($dataToUpdate);
        }

        return redirect()->route('penyewa.profil')->with('success', 'Data profil Anda berhasil diperbarui!');
    }
}
