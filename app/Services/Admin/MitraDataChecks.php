<?php

namespace App\Services\Admin;

use App\Models\MitraProfile;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Pemeriksaan data otomatis atas pendaftaran mitra. Sifatnya penyaring awal untuk membantu admin
 */
class MitraDataChecks
{
    public const PASS = 'pass';
    public const WARN = 'warn';
    public const FAIL = 'fail';
    public const NA   = 'na'; // tidak berlaku

    /** Gelar dan badan usaha diabaikan saat membandingkan nama */
    private const IGNORED_TOKENS = [
        'haji', 'hajjah', 'hj', 'dr', 'drs', 'dra', 'ir', 'prof', 'se', 'sh', 'spd', 'skom', 'kom', 'mm', 'mt',
        'st', 'sp', 'si', 'ssi', 'ak', 'mh', 'mkom', 'msi', 'sos', 'sip', 'ikom',
        'pt', 'cv', 'ud', 'pd', 'fa', 'firma', 'koperasi',
    ];

    /** @return array<int, array{key:string,label:string,status:string,detail:string,blocking:bool,reason:?string,links:array}> */
    public function run(MitraProfile $mitra): array
    {
        $mitra->loadMissing('user');

        return [
            $this->nikFormat($mitra),
            $this->nikUnique($mitra),
            $this->ktpFile($mitra),
            $this->npwpFormat($mitra),
            $this->npwpUnique($mitra),
            $this->phoneUnique($mitra),
            $this->accountName($mitra),
        ];
    }

    /** Cek butir yang memblokir persetujuan */
    public function isBlocked(array $results): bool
    {
        return collect($results)->contains(fn ($r) => $r['status'] === self::FAIL && $r['blocking']);
    }

    public function blockingMessage(array $results): string
    {
        $problems = collect($results)
            ->filter(fn ($r) => $r['status'] === self::FAIL && $r['blocking'])
            ->pluck('detail')->implode(' ');

        return "Tidak dapat disetujui. {$problems}";
    }

    /** Ringkas untuk disimpan sebagai bukti keputusan: kunci => status. */
    public function snapshot(array $results): array
    {
        return collect($results)->pluck('status', 'key')->all();
    }

    // ---- pemeriksaan

    private function nikFormat(MitraProfile $m): array
    {
        $label = 'Format NIK (16 digit)';
        $nik = (string) $m->no_ktp;

        if (! preg_match('/^\d{16}$/', $nik)) {
            return $this->item('nik_format', $label, self::FAIL, 'NIK di formulir bukan 16 digit angka.', true,
                'Format NIK tidak valid, harus 16 digit angka.');
        }
        if (! $this->plausibleBirthDate($nik)) {
            return $this->item('nik_format', $label, self::WARN, 'Tanggal lahir di dalam NIK tampak tidak wajar. Cocokkan dengan KTP.', false,
                'Tanggal lahir pada NIK tidak sesuai, mohon periksa kembali.');
        }

        return $this->item('nik_format', $label, self::PASS, 'NIK terdiri dari 16 digit dan tanggal lahirnya wajar.');
    }

    private function nikUnique(MitraProfile $m): array
    {
        $label = 'NIK belum dipakai pengajuan lain';
        $nik = trim((string) $m->no_ktp);

        if ($nik === '') {
            return $this->item('nik_unique', $label, self::NA, 'Tidak dapat diperiksa karena NIK kosong.');
        }

        $others = MitraProfile::where('id', '!=', $m->id)->where('no_ktp', $nik)->get(['id', 'nama_usaha', 'status']);

        if ($others->isEmpty()) {
            return $this->item('nik_unique', $label, self::PASS, 'Tidak ada pengajuan lain dengan NIK yang sama.');
        }

        return $this->item('nik_unique', $label, self::FAIL, 'NIK yang sama dipakai pengajuan lain.', true,
            'NIK sudah terdaftar pada akun mitra lain.', $this->linksTo($others));
    }

    private function ktpFile(MitraProfile $m): array
    {
        $label = 'Berkas KTP tersimpan';
        $path = $m->documentPath('ktp');

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('local');

        if ($path && $disk->exists($path)) {
            return $this->item('ktp_file', $label, self::PASS, 'Berkas KTP ada di penyimpanan.');
        }

        return $this->item('ktp_file', $label, self::FAIL, 'Berkas KTP tidak ditemukan di penyimpanan.', true,
            'Berkas KTP tidak dapat dibuka, mohon unggah ulang.');
    }

    private function npwpFormat(MitraProfile $m): array
    {
        $label = 'Format NPWP';
        $digits = $this->digits($m->npwp);

        if ($digits === '') {
            return $this->item('npwp_format', $label, self::NA, 'NPWP tidak diisi (opsional).');
        }
        if (! in_array(strlen($digits), [15, 16], true)) {
            return $this->item('npwp_format', $label, self::WARN, 'NPWP berisi ' . strlen($digits) . ' digit, seharusnya 15 atau 16.', false,
                'Format NPWP tidak valid, mohon periksa kembali.');
        }

        return $this->item('npwp_format', $label, self::PASS, 'NPWP terdiri dari ' . strlen($digits) . ' digit.');
    }

    private function npwpUnique(MitraProfile $m): array
    {
        $label = 'NPWP belum dipakai pengajuan lain';
        $digits = $this->digits($m->npwp);

        if ($digits === '') {
            return $this->item('npwp_unique', $label, self::NA, 'NPWP tidak diisi (opsional).');
        }

        // Titik, strip, dan spasi diabaikan supaya "09.254.294.3-407.000" sama dengan "092542943407000".
        $others = MitraProfile::where('id', '!=', $m->id)
            ->whereRaw("REPLACE(REPLACE(REPLACE(npwp, '.', ''), '-', ''), ' ', '') = ?", [$digits])
            ->get(['id', 'nama_usaha', 'status']);

        if ($others->isEmpty()) {
            return $this->item('npwp_unique', $label, self::PASS, 'Tidak ada pengajuan lain dengan NPWP yang sama.');
        }

        // Peringatan, bukan pemblokir: satu NPWP usaha bisa dipakai beberapa pemilik atau cabang.
        return $this->item('npwp_unique', $label, self::WARN, 'NPWP yang sama dipakai pengajuan lain. Pastikan hubungannya wajar.', false,
            null, $this->linksTo($others));
    }

    private function phoneUnique(MitraProfile $m): array
    {
        $label = 'Nomor HP belum dipakai akun lain';
        $phone = $this->digits($m->user?->no_hp);

        if ($phone === '') {
            return $this->item('phone_unique', $label, self::NA, 'Nomor HP pemilik tidak tersedia.');
        }

        // 0812... dan 62812... dianggap nomor yang sama.
        $local = Str::startsWith($phone, '62') ? '0' . substr($phone, 2) : $phone;
        $intl  = '62' . ltrim($local, '0');

        $others = DB::table('users')
            ->where('id', '!=', $m->user_id)
            ->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(no_hp, ' ', ''), '-', ''), '+', ''), '.', '') IN (?, ?)", [$local, $intl])
            ->get(['name', 'role']);

        if ($others->isEmpty()) {
            return $this->item('phone_unique', $label, self::PASS, 'Tidak ada akun lain dengan nomor yang sama.');
        }

        $names = $others->take(3)->map(fn ($u) => "{$u->name} ({$u->role})")->implode(', ');

        return $this->item('phone_unique', $label, self::WARN, "Nomor yang sama dipakai akun: {$names}.");
    }

    private function accountName(MitraProfile $m): array
    {
        $label = 'Nama rekening sesuai pemilik atau usaha';
        $account = trim((string) $m->nama_rekening);

        $candidates = array_filter(['pemilik' => $m->user?->name, 'usaha' => $m->nama_usaha]);

        if ($account === '' || $candidates === []) {
            return $this->item('rekening_nama', $label, self::NA, 'Nama rekening atau nama pembanding belum diisi.');
        }

        $rank = ['different' => 0, 'similar' => 1, 'exact' => 2];
        $best = ['different', null];
        foreach ($candidates as $who => $name) {
            $result = $this->nameMatch($account, $name);
            if ($rank[$result] > $rank[$best[0]]) {
                $best = [$result, $who];
            }
        }
        [$result, $who] = $best;

        return match ($result) {
            'exact'   => $this->item('rekening_nama', $label, self::PASS, "Sama dengan nama {$who}."),
            'similar' => $this->item('rekening_nama', $label, self::WARN, "Mirip dengan nama {$who}. Periksa singkatan atau gelar."),
            default   => $this->item('rekening_nama', $label, self::WARN,
                'Berbeda dari nama pemilik dan nama usaha. Pastikan rekening atas nama pemilik atau usahanya.', false,
                'Nama pemilik rekening tidak sesuai dengan nama pemilik atau usaha.'),
        };
    }

    //---- helpers

    /** 'exact' | 'similar' | 'different'. Public supaya mudah diuji. */
    public function nameMatch(string $a, string $b): string
    {
        $ta = $this->tokens($a);
        $tb = $this->tokens($b);

        if ($ta === [] || $tb === []) {
            return 'different';
        }

        sort($ta);
        sort($tb);
        if ($ta === $tb) {
            return 'exact';
        }

        $shared = count(array_intersect($ta, $tb));
        $union  = count(array_unique(array_merge($ta, $tb)));

        // Satu nama termuat di nama lain ("Budi Santoso" dan "Budi Santoso Putra"), atau cukup banyak kata sama.
        if ($shared === min(count($ta), count($tb)) || $shared / $union >= 0.6) {
            return 'similar';
        }

        return 'different';
    }

    private function tokens(string $name): array
    {
        $clean = preg_replace('/[^a-z\s]/', ' ', Str::lower(Str::ascii($name)));

        return array_values(array_unique(array_filter(
            preg_split('/\s+/', trim($clean)),
            // Singkatan satu huruf ("M.", "H.") dan gelar diabaikan.
            fn ($t) => strlen($t) > 1 && ! in_array($t, self::IGNORED_TOKENS, true)
        )));
    }

    /** Hanya memeriksa tanggal kalender di dalam NIK valid; bukan memeriksa umur. */
    private function plausibleBirthDate(string $nik): bool
    {
        $day   = (int) substr($nik, 6, 2);
        $day   = $day > 40 ? $day - 40 : $day;
        $month = (int) substr($nik, 8, 2);
        $yy    = (int) substr($nik, 10, 2);
        $year  = 2000 + $yy <= (int) now()->format('Y') ? 2000 + $yy : 1900 + $yy;

        return checkdate($month, $day, $year);
    }

    private function digits(?string $value): string
    {
        return preg_replace('/\D/', '', (string) $value);
    }

    private function linksTo($profiles): array
    {
        $status = ['pending' => 'menunggu', 'approved' => 'disetujui', 'rejected' => 'ditolak'];

        return $profiles->take(3)->map(fn ($p) => [
            'label' => ($p->nama_usaha ?: "Pengajuan #{$p->id}") . ' (' . ($status[$p->status] ?? $p->status) . ')',
            'url'   => route('admin.verifikasi.show', $p->id),
        ])->all();
    }

    private function item(string $key, string $label, string $status, string $detail, bool $blocking = false, ?string $reason = null, array $links = []): array
    {
        return compact('key', 'label', 'status', 'detail', 'blocking', 'reason', 'links');
    }
}