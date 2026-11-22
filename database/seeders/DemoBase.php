<?php

namespace Database\Seeders;

use DateTimeImmutable;
use DateTimeZone;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Seeder;

/**
 * Dasar untuk seeder data simulasi SportPlace.
 */
abstract class DemoBase extends Seeder
{
    protected const SEED = 20261118;          // null = benar-benar acak tiap dijalankan
    protected const PASSWORD = 'password123';
    protected const SERVICE_FEE = 2500;
    protected const PAYMENT_WINDOW_MIN = 15;
    protected const HISTORY_DAYS = 170;       // riwayat ke belakang (mengisi grafik 6 bulan)
    protected const FUTURE_DAYS = 14;         // jadwal ke depan yang sudah dibayar
    protected const MIDTRANS_SERVER_KEY = 'SB-Mid-server-DEMO';
    protected const DOC_DISK = 'local';
    protected const IMAGE_DIR = 'images/demo';          // foto asli di public/images/demo/...
    protected const DOC_SOURCE = 'seeders/assets/dokumen';

    /** Keadaan bersama antar-tahap (diakses lewat $this->nama). */
    protected static array $S = [];

    public function &__get(string $name)
    {
        if (! array_key_exists($name, self::$S)) {
            self::$S[$name] = null;
        }

        return self::$S[$name];
    }

    public function __set(string $name, $value): void
    {
        self::$S[$name] = $value;
    }

    public function __isset(string $name): bool
    {
        return isset(self::$S[$name]);
    }

    protected function begin(): void
    {
        self::$S = [];
        if (self::SEED !== null) {
            mt_srand(self::SEED);
        }
        $tz = new DateTimeZone((string) config('app.timezone', 'Asia/Makassar'));
        $this->now = new DateTimeImmutable('now', $tz);
        $this->today = $this->now->setTime(0, 0, 0);
        $this->hash = Hash::make(self::PASSWORD);
        $this->adminId = 0;
        foreach (['sportIds', 'cityIds', 'facilityIds', 'mitra', 'customers', 'fields', 'bookable', 'rates', 'occ', 'usedCodes', 'seq',
            'userCreated', 'missingImages', 'headers', 'details', 'payments', 'events', 'reservations', 'refunds', 'earnings', 'audit', 'report'] as $key) {
            self::$S[$key] = [];
        }
        self::$S['started'] = true;
    }

    protected function ensureStarted(): void
    {
        if (empty(self::$S['started'])) {
            throw new \RuntimeException('Seeder demo harus dijalankan berurutan lewat DemoSeeder atau DatabaseSeeder (mulai dari DemoResetSeeder).');
        }
    }

    protected function at(int $days): DateTimeImmutable
    {
        return $this->today->modify(($days >= 0 ? '+' : '') . $days . ' days');
    }

    protected function dt(DateTimeImmutable $d): string
    {
        return $d->format('Y-m-d H:i:s');
    }

    protected function hm(int $minutes): string
    {
        return sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
    }

    protected function minutes(string $hm): int
    {
        [$h, $m] = array_map('intval', explode(':', $hm));

        return $h * 60 + $m;
    }

    protected function next(string $name): int
    {
        return $this->seq[$name] = ($this->seq[$name] ?? 0) + 1;
    }

    protected function bulk(string $table, array $rows, int $size = 250): void
    {
        foreach (array_chunk($rows, $size) as $chunk) {
            DB::table($table)->insert($chunk);
        }
    }

    protected function chance(float $p): bool
    {
        return mt_rand() / mt_getrandmax() < $p;
    }

    protected function pick(array $a): mixed
    {
        return $a[mt_rand(0, count($a) - 1)];
    }

    protected function sample(array $a, int $n): array
    {
        $a = array_values($a);
        $out = [];
        while ($a && count($out) < $n) {
            $i = mt_rand(0, count($a) - 1);
            $out[] = $a[$i];
            array_splice($a, $i, 1);
        }

        return $out;
    }

    protected function shuffled(array $a): array
    {
        for ($i = count($a) - 1; $i > 0; $i--) {
            $j = mt_rand(0, $i);
            [$a[$i], $a[$j]] = [$a[$j], $a[$i]];
        }

        return $a;
    }

    protected function weighted(array $weights): int|string
    {
        $sum = array_sum($weights);
        $r = mt_rand() / mt_getrandmax() * $sum;
        foreach ($weights as $k => $w) {
            $r -= $w;
            if ($r <= 0) {
                return $k;
            }
        }

        return array_key_last($weights);
    }

    protected function code(int $len): string
    {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $s = '';
        for ($i = 0; $i < $len; $i++) {
            $s .= $alphabet[mt_rand(0, strlen($alphabet) - 1)];
        }

        return $s;
    }

    protected function bookingCode(): string
    {
        do {
            $c = 'SP-' . $this->code(6);
        } while (isset($this->usedCodes[$c]));
        $this->usedCodes[$c] = true;

        return $c;
    }

    protected function uuid(): string
    {
        $h = '';
        for ($i = 0; $i < 32; $i++) {
            $h .= dechex(mt_rand(0, 15));
        }

        return substr($h, 0, 8) . '-' . substr($h, 8, 4) . '-4' . substr($h, 13, 3) . '-a' . substr($h, 17, 3) . '-' . substr($h, 20, 12);
    }

    protected function phone(): string
    {
        return '08' . $this->pick(['12', '13', '21', '52', '53', '57', '78', '81', '82', '31']) . mt_rand(10000000, 99999999);
    }

    protected function putPublic(string $relative, string $content): void
    {
        $path = public_path($relative);
        $dir = dirname($path);
        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        file_put_contents($path, $content);
    }

    protected function esc(string $s): string
    {
        return htmlspecialchars($s, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }

    protected function svgBadge(string $letter, string $color): string
    {
        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 96 96"><rect width="96" height="96" rx="20" fill="' . $color . '"/>'
            . '<text x="48" y="62" font-family="Arial,Helvetica,sans-serif" font-size="44" font-weight="700" text-anchor="middle" fill="#fff">' . $this->esc($letter) . '</text></svg>';
    }

    protected function upsertUser(string $email, string $name, string $phone, string $role, int $createdDays): int
    {
        $created = $this->at($createdDays)->setTime(mt_rand(8, 21), mt_rand(0, 59), mt_rand(0, 59));
        $verified = $created->modify('+' . mt_rand(1, 20) . ' minutes');
        $photo = $this->avatarPath($name, $email, $role);

        DB::table('users')->updateOrInsert(['email' => $email], [
            'name' => $name,
            'email_verified_at' => $this->dt($verified),
            'password' => $this->hash,
            'no_hp' => $phone,
            'phone_verified_at' => $this->dt($verified->modify('+' . mt_rand(1, 30) . ' minutes')),
            'role' => $role,
            'status_akun' => 'aktif',
            'foto_profil' => $photo,
            'created_at' => $this->dt($created),
            'updated_at' => $this->dt($created->modify('+' . mt_rand(0, 40) . ' days')),
        ]);

        $id = (int) DB::table('users')->where('email', $email)->value('id');
        $this->userCreated[$id] = $created;

        return $id;
    }

    /** Tarif yang berlaku untuk mitra pada waktu tertentu: tarif khusus mitra lebih dulu, lalu global. */
    protected function rateFor(string $mitraKey, DateTimeImmutable $at): array
    {
        $scopes = ['m' . $this->mitra[$mitraKey]['id'], 'g'];
        foreach ($scopes as $scope) {
            $latest = null;
            foreach ($this->rates as $r) {
                if ($r['scope'] === $scope && $r['from'] <= $at && ($latest === null || $r['from'] > $latest['from'])) {
                    $latest = $r;
                }
            }
            if ($latest !== null && $latest['persen'] !== null) {
                return $latest;
            }
        }

        return ['id' => 1, 'persen' => '20.00'];
    }

    // ---- foto asli (disediakan sendiri di public/images/demo) -----------------------------

    /**
     * Berkas gambar milik seeder dengan nama berawalan "<prefix>-", terurut. Dicari di dua tempat:
     * storage/app/public/images/demo/<folder> (diakses lewat /storage/...) dan public/images/demo/<folder>.
     * Yang disimpan di database tetap path relatif yang sama: images/demo/<folder>/<berkas>.
     */
    protected function imagePool(string $folder, string $prefix): array
    {
        $dirs = [
            storage_path('app/public/' . self::IMAGE_DIR . '/' . $folder),
            public_path(self::IMAGE_DIR . '/' . $folder),
        ];
        $names = [];
        foreach ($dirs as $dir) {
            if (! is_dir($dir)) {
                continue;
            }
            foreach (scandir($dir) ?: [] as $f) {
                if (preg_match('/^' . preg_quote($prefix, '/') . '-.+\.(jpe?g|png|webp)$/i', $f)) {
                    $names[$f] = true;
                }
            }
        }
        $names = array_keys($names);
        sort($names);

        return array_map(fn ($f) => self::IMAGE_DIR . '/' . $folder . '/' . $f, $names);
    }

    protected function gender(string $name): string
    {
        if (preg_match('/^Ni /', $name)) {
            return 'wanita';
        }
        if (preg_match('/^(I|Gusti) /', $name)) {
            return 'pria';
        }
        $first = strtolower((string) strtok($name, ' '));

        return in_array($first, ['ayu', 'rina', 'siti', 'nadia', 'citra', 'fitri', 'maya', 'putri', 'dian', 'vina'], true) ? 'wanita' : 'pria';
    }

    /** Foto profil: dipilih dari kumpulan pria/wanita menurut nama; tidak memakai angka acak, jadi data lain tidak bergeser. */
    protected function avatarPath(string $name, string $email, string $role): string
    {
        $group = $role === 'admin' ? 'admin' : $this->gender($name);
        $pool = $this->imagePool('avatar', $group);
        if (! $pool && $group === 'admin') {
            $pool = $this->imagePool('avatar', 'pria');
        }
        if (! $pool) {
            $this->noteMissing('avatar/' . $group . '-01.jpg');

            return self::IMAGE_DIR . '/avatar/' . $group . '-01.jpg';
        }

        return $pool[crc32($email) % count($pool)];
    }

    /** Hingga 3 foto per lapangan dari kumpulan jenis lapangannya; yang pertama menjadi foto utama. */
    protected function fieldPhotoPaths(string $sport, string $name, int $fieldId): array
    {
        $kind = match (true) {
            $sport === 'tenis' && stripos($name, 'Tanah Liat') !== false => 'tenis-clay',
            $sport === 'tenis' => 'tenis-hard',
            $sport === 'voli' && stripos($name, 'Pasir') !== false => 'voli-pasir',
            $sport === 'voli' => 'voli-indoor',
            default => $sport,
        };
        $pool = $this->imagePool('lapangan', $kind);
        if (! $pool) {
            $this->noteMissing('lapangan/' . $kind . '-01.jpg');

            return array_map(fn ($i) => self::IMAGE_DIR . '/lapangan/' . $kind . '-0' . $i . '.jpg', [1, 2, 3]);
        }
        $n = min(3, count($pool));
        $start = ($fieldId * 2) % count($pool);
        $out = [];
        for ($i = 0; $i < $n; $i++) {
            $out[] = $pool[($start + $i) % count($pool)];
        }

        return $out;
    }

    protected function noteMissing(string $what): void
    {
        $this->missingImages[$what] = true;
    }

    /** Menyalin contoh dokumen ke disk dokumen. Bila contoh belum ada, dibuat PNG polos sebagai pengganti. */
    protected function documentFile(string $mitraKey, string $slug, string $label, string $owner): string
    {
        $dir = database_path(self::DOC_SOURCE);
        $src = null;
        foreach (is_dir($dir) ? (scandir($dir) ?: []) : [] as $f) {
            if (preg_match('/^' . preg_quote($slug, '/') . '\.(jpe?g|png|pdf|webp)$/i', $f)) {
                $src = $dir . '/' . $f;
                break;
            }
        }
        if ($src) {
            $ext = strtolower(pathinfo($src, PATHINFO_EXTENSION));
            $bin = (string) file_get_contents($src);
        } else {
            $this->noteMissing('dokumen/' . $slug . '.jpg (database/seeders/assets/dokumen)');
            $ext = 'png';
            $bin = $this->placeholderPng($label, $owner);
        }
        $path = 'mitra-documents/' . $mitraKey . '/' . $slug . '.' . $ext;
        Storage::disk(self::DOC_DISK)->put($path, $bin);

        return $path;
    }

    protected function placeholderPng(string $label, string $owner): string
    {
        if (function_exists('imagecreatetruecolor')) {
            $im = imagecreatetruecolor(640, 400);
            imagefilledrectangle($im, 0, 0, 640, 400, imagecolorallocate($im, 248, 250, 252));
            imagerectangle($im, 20, 20, 619, 379, imagecolorallocate($im, 203, 213, 225));
            imagestring($im, 5, 40, 150, $label, imagecolorallocate($im, 15, 23, 42));
            imagestring($im, 4, 40, 190, $owner, imagecolorallocate($im, 71, 85, 105));
            imagestring($im, 3, 40, 340, 'Dokumen contoh (pengganti sementara)', imagecolorallocate($im, 148, 163, 184));
            ob_start();
            imagepng($im);

            return (string) ob_get_clean();
        }

        return (string) base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');
    }
}