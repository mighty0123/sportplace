<?php

namespace Database\Seeders;

use DateTimeImmutable;
use DateTimeZone;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/** Master kota, cabang olahraga, dan fasilitas. */
class DemoMasterDataSeeder extends DemoBase
{
    public function run(): void
    {
        $this->ensureStarted();
        $this->seedMasterData();
    }

    private function seedMasterData(): void
    {
        $cities = [
            ['Denpasar', 'Bali', 'aktif'], ['Badung', 'Bali', 'aktif'], ['Gianyar', 'Bali', 'aktif'],
            ['Tabanan', 'Bali', 'aktif'], ['Buleleng', 'Bali', 'aktif'], ['Karangasem', 'Bali', 'nonaktif'],
            ['Jakarta Selatan', 'DKI Jakarta', 'aktif'], ['Bandung', 'Jawa Barat', 'aktif'], ['Surabaya', 'Jawa Timur', 'aktif'],
        ];
        foreach ($cities as [$kota, $prov, $status]) {
            DB::table('master_cities')->updateOrInsert(['nama_kota' => $kota, 'provinsi' => $prov], ['status_kota' => $status]);
            $this->cityIds[$kota] = (int) DB::table('master_cities')->where('nama_kota', $kota)->where('provinsi', $prov)->value('id');
        }

        $sports = [
            'futsal' => ['Futsal', '#0B7B69'], 'badminton' => ['Badminton', '#2563EB'], 'mini-soccer' => ['Mini Soccer', '#15803D'],
            'basket' => ['Basket', '#EA580C'], 'tenis' => ['Tenis', '#B45309'], 'voli' => ['Voli', '#7C3AED'],
            'tenis-meja' => ['Tenis Meja', '#DC2626'],
        ];
        foreach ($sports as $slug => [$name, $color]) {
            $icon = '/icons/' . $slug . '.svg';
            $this->putPublic(ltrim($icon, '/'), $this->svgBadge(mb_substr($name, 0, 1), $color));
            DB::table('master_sports')->updateOrInsert(['slug' => $slug], ['nama_olahraga' => $name, 'icon_url' => $icon, 'status_olahraga' => 'aktif']);
            $this->sportIds[$slug] = (int) DB::table('master_sports')->where('slug', $slug)->value('id');
        }

        $facilities = [
            ['Bola / Shuttlecock', 'peralatan'], ['Papan Skor', 'peralatan'], ['Penyewaan Sepatu & Raket', 'peralatan'],
            ['Rompi Tim', 'peralatan'], ['Kamera CCTV', 'keamanan'], ['Kotak P3K', 'keamanan'], ['Petugas Keamanan', 'keamanan'],
            ['Kantin', 'umum'], ['Toilet & Shower', 'umum'], ['Parkir Luas', 'umum'], ['Ruang Ganti & Loker', 'umum'],
            ['Musholla', 'umum'], ['Wi-Fi Gratis', 'umum'], ['Tribun Penonton', 'umum'],
        ];
        foreach ($facilities as [$name, $cat]) {
            $slug = trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($name)), '-');
            $icon = '/icons/fasilitas-' . $slug . '.svg';
            $this->putPublic(ltrim($icon, '/'), $this->svgBadge(mb_substr($name, 0, 1), '#475569'));
            DB::table('master_facilities')->updateOrInsert(['nama' => $name], ['kategori' => $cat, 'icon_url' => $icon, 'status_fasilitas' => 'aktif']);
            $this->facilityIds[$name] = (int) DB::table('master_facilities')->where('nama', $name)->value('id');
        }
    }
}
