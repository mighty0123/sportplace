<?php

namespace App\Services\Admin;

use Illuminate\Support\Facades\DB;

/**
 * Pemeriksaan data otomatis atas listing venue dan lapangan. Penyaring awal untuk membantu admin,
 * bukan pengganti penilaian foto dan deskripsi oleh manusia.
 */
class ListingChecks
{
    private const LISTING_STATUS = ['draft' => 'draft', 'pending_review' => 'menunggu', 'aktif' => 'aktif', 'nonaktif' => 'nonaktif', 'rejected' => 'ditolak'];
    private const MITRA_STATUS   = ['pending' => 'menunggu', 'approved' => 'disetujui', 'rejected' => 'ditolak'];

    public function forVenue(object $v): array
    {
        return [
            $this->venueMitra($v), $this->venueData($v), $this->venueHours($v),
            $this->venueMap($v), $this->venueDescription($v), $this->venueDuplicate($v), $this->venueFields($v),
        ];
    }

    public function forField(object $f): array
    {
        return [
            $this->fieldVenue($f), $this->fieldPrice($f), $this->fieldPhotos($f),
            $this->fieldSchedule($f), $this->fieldSport($f), $this->fieldDuplicate($f),
        ];
    }

    public function isBlocked(array $results): bool
    {
        return collect($results)->contains(fn ($r) => $r['status'] === MitraDataChecks::FAIL && $r['blocking']);
    }

    public function blockingMessage(array $results): string
    {
        $problems = collect($results)
            ->filter(fn ($r) => $r['status'] === MitraDataChecks::FAIL && $r['blocking'])
            ->pluck('detail')->implode(' ');

        return "Tidak dapat disetujui. {$problems}";
    }

    /** Ringkasan satu kalimat */
    public function summary(array $results): string
    {
        $words = [MitraDataChecks::PASS => 'lolos', MitraDataChecks::WARN => 'perlu perhatian', MitraDataChecks::FAIL => 'tidak lolos'];

        return collect($results)->countBy('status')
            ->only(array_keys($words))->map(fn ($n, $s) => "{$n} {$words[$s]}")->implode(', ') ?: 'tidak ada yang berlaku';
    }

    // venue
    private function venueMitra(object $v): array
    {
        $label = 'Status mitra pemilik';
        if ($v->mitra_status === 'approved') {
            return $this->item('venue_mitra', $label, MitraDataChecks::PASS, 'Akun mitra berstatus disetujui.');
        }

        return $this->item('venue_mitra', $label, MitraDataChecks::FAIL,
            'Mitra pemilik belum disetujui (status: ' . (self::MITRA_STATUS[$v->mitra_status] ?? $v->mitra_status) . ').', true,
            'Mitra belum disetujui.',
            [['label' => $v->nama_usaha ?: "Mitra #{$v->mitra_id}", 'url' => route('admin.verifikasi.show', $v->mitra_id)]]);
    }

    private function venueData(object $v): array
    {
        $label = 'Kelengkapan data venue';
        $missing = [];
        if (trim((string) $v->nama_venue) === '') $missing[] = 'nama venue';
        if (trim((string) $v->alamat_lengkap) === '') $missing[] = 'alamat';

        return $missing
            ? $this->item('venue_data', $label, MitraDataChecks::FAIL, 'Data wajib belum lengkap: ' . implode(', ', $missing) . '.', true, 'Data venue belum lengkap.')
            : $this->item('venue_data', $label, MitraDataChecks::PASS, 'Nama, alamat, dan kota terisi.');
    }

    private function venueHours(object $v): array
    {
        $label = 'Jam operasional';
        [$open, $close] = [substr((string) $v->jam_buka, 0, 5), substr((string) $v->jam_tutup, 0, 5)];

        if ($open === $close) {
            return $this->item('venue_hours', $label, MitraDataChecks::WARN, "Jam buka dan tutup sama ({$open}). Periksa kembali.", false, 'Jam operasional tidak wajar.');
        }
        // Tutup lebih awal dari buka berarti melewati tengah malam: sah untuk venue malam.
        $note = $close < $open ? ' (tutup melewati tengah malam)' : '';

        return $this->item('venue_hours', $label, MitraDataChecks::PASS, "Buka {$open}, tutup {$close}{$note}.");
    }

    private function venueMap(object $v): array
    {
        $label = 'Tautan peta';
        $url = trim((string) $v->map_url);

        if ($url === '') {
            return $this->item('venue_map', $label, MitraDataChecks::WARN, 'Tautan peta belum diisi.', false, 'Tautan peta belum diisi.');
        }
        $scheme = strtolower((string) (parse_url($url, PHP_URL_SCHEME) ?? ''));
        if (! filter_var($url, FILTER_VALIDATE_URL) || ! in_array($scheme, ['http', 'https'], true)) {
            return $this->item('venue_map', $label, MitraDataChecks::WARN, 'Tautan peta bukan alamat web yang valid.', false, 'Tautan peta tidak valid.');
        }

        return $this->item('venue_map', $label, MitraDataChecks::PASS, 'Alamat web: ' . parse_url($url, PHP_URL_HOST) . '.');
    }

    private function venueDescription(object $v): array
    {
        $label = 'Deskripsi venue';
        $len = mb_strlen(trim((string) $v->deskripsi));

        if ($len === 0) {
            return $this->item('venue_desc', $label, MitraDataChecks::WARN, 'Deskripsi kosong.', false, 'Deskripsi venue belum diisi.');
        }
        if ($len < 30) {
            return $this->item('venue_desc', $label, MitraDataChecks::WARN, "Deskripsi hanya {$len} karakter. Mungkin terlalu singkat.", false, 'Deskripsi venue terlalu singkat.');
        }

        return $this->item('venue_desc', $label, MitraDataChecks::PASS, "Deskripsi {$len} karakter.");
    }

    private function venueDuplicate(object $v): array
    {
        $label = 'Nama venue (duplikat)';
        $name = mb_strtolower(trim((string) $v->nama_venue));
        if ($name === '') {
            return $this->item('venue_dup', $label, MitraDataChecks::NA, 'Tidak dapat diperiksa karena nama kosong.');
        }

        $others = DB::table('venues')->where('id', '!=', $v->id)->where('city_id', $v->city_id)
            ->where('status_vanue', '!=', 'draft')->whereRaw('LOWER(TRIM(nama_venue)) = ?', [$name])
            ->get(['id', 'nama_venue', 'status_vanue']);

        if ($others->isEmpty()) {
            return $this->item('venue_dup', $label, MitraDataChecks::PASS, 'Tidak ada venue lain dengan nama yang sama di kota ini.');
        }

        return $this->item('venue_dup', $label, MitraDataChecks::WARN, 'Ada venue lain dengan nama yang sama di kota ini. Pastikan bukan duplikat.', false, null,
            $others->take(3)->map(fn ($o) => [
                'label' => "{$o->nama_venue} (" . (self::LISTING_STATUS[$o->status_vanue] ?? $o->status_vanue) . ')',
                'url'   => route('admin.moderasi.show', ['jenis' => 'venue', 'id' => $o->id]),
            ])->all());
    }

    private function venueFields(object $v): array
    {
        $label = 'Lapangan di venue';
        $n = DB::table('fields')->where('venue_id', $v->id)->count();

        return $n === 0
            ? $this->item('venue_fields', $label, MitraDataChecks::WARN, 'Belum ada lapangan. Venue baru tampil di pencarian setelah ada lapangan yang disetujui.')
            : $this->item('venue_fields', $label, MitraDataChecks::PASS, "{$n} lapangan terdaftar.");
    }

    // lapangan
    private function fieldVenue(object $f): array
    {
        $label = 'Status venue induk';
        if ($f->venue_status === 'aktif') {
            return $this->item('field_venue', $label, MitraDataChecks::PASS, 'Venue induk berstatus aktif.');
        }

        return $this->item('field_venue', $label, MitraDataChecks::FAIL,
            'Venue induk belum aktif. Setujui venue terlebih dahulu.', true,
            'Venue induk belum aktif.',
            [['label' => $f->nama_venue ?: "Venue #{$f->venue_id}", 'url' => route('admin.moderasi.show', ['jenis' => 'venue', 'id' => $f->venue_id])]]);
    }

    private function fieldPrice(object $f): array
    {
        $label = 'Harga per jam';
        $price = (float) $f->harga_perjam;

        if ($price <= 0) {
            return $this->item('field_price', $label, MitraDataChecks::FAIL, 'Harga per jam harus lebih dari 0.', true, 'Harga per jam tidak valid.');
        }

        $raw = trim((string) $f->meta_harga);
        if ($raw !== '') {
            $meta = json_decode($raw, true);
            $bad = ! is_array($meta) || collect($meta)->contains(fn ($p) => ! is_numeric($p) || (float) $p <= 0);
            if ($bad) {
                return $this->item('field_price', $label, MitraDataChecks::WARN, 'Rincian harga siang atau malam tidak terbaca atau berisi nilai tidak valid.', false, 'Rincian harga tidak valid.');
            }
        }

        return $this->item('field_price', $label, MitraDataChecks::PASS, $this->rupiah($price) . ' per jam.');
    }

    private function fieldPhotos(object $f): array
    {
        $label = 'Foto lapangan';
        $photos = DB::table('field_photo')->where('field_id', $f->id);
        $total = (clone $photos)->count();
        $primary = (clone $photos)->where('is_primary', 1)->count();

        if ($total === 0) {
            return $this->item('field_photos', $label, MitraDataChecks::WARN, 'Belum ada foto lapangan.', false, 'Foto lapangan belum ada.');
        }
        if ($primary === 0) {
            return $this->item('field_photos', $label, MitraDataChecks::WARN, "{$total} foto, tetapi belum ada yang ditandai sebagai foto utama.", false, 'Belum ada foto utama.');
        }
        if ($primary > 1) {
            return $this->item('field_photos', $label, MitraDataChecks::WARN, "{$total} foto, tetapi {$primary} ditandai sebagai foto utama. Seharusnya satu.", false, 'Foto utama lebih dari satu.');
        }

        return $this->item('field_photos', $label, MitraDataChecks::PASS, "{$total} foto, termasuk satu foto utama.");
    }

    private function fieldSchedule(object $f): array
    {
        $label = 'Jadwal operasional';
        $rows = DB::table('field_schedule')->where('field_id', $f->id)->get(['hari', 'jam_buka', 'jam_tutup', 'status_hari']);
        $open = $rows->filter(fn ($r) => $r->status_hari === 'buka' && $r->jam_buka && $r->jam_tutup);

        if ($open->isEmpty()) {
            return $this->item('field_schedule', $label, MitraDataChecks::WARN, 'Belum ada hari buka dengan jam yang jelas. Lapangan belum bisa dipesan.', false, 'Jadwal operasional belum diatur.');
        }

        // Jam di luar jam venue
        $venueOpen = substr((string) $f->venue_open, 0, 5);
        $venueClose = substr((string) $f->venue_close, 0, 5);
        if ($venueClose > $venueOpen) {
            $outside = $open->filter(fn ($r) => substr($r->jam_buka, 0, 5) < $venueOpen || substr($r->jam_tutup, 0, 5) > $venueClose)->count();
            if ($outside > 0) {
                return $this->item('field_schedule', $label, MitraDataChecks::WARN, "{$outside} hari memiliki jam di luar jam operasional venue ({$venueOpen} sampai {$venueClose}).", false, 'Jadwal di luar jam operasional venue.');
            }
        }

        return $this->item('field_schedule', $label, MitraDataChecks::PASS, $open->count() . ' hari buka dalam seminggu.');
    }

    private function fieldSport(object $f): array
    {
        $label = 'Cabang olahraga';

        return $f->sport_status === 'aktif'
            ? $this->item('field_sport', $label, MitraDataChecks::PASS, "Cabang olahraga: {$f->nama_olahraga}.")
            : $this->item('field_sport', $label, MitraDataChecks::WARN, "Cabang olahraga {$f->nama_olahraga} berstatus nonaktif.", false, 'Cabang olahraga nonaktif.');
    }

    private function fieldDuplicate(object $f): array
    {
        $label = 'Nama lapangan (duplikat)';
        $others = DB::table('fields')->where('id', '!=', $f->id)->where('venue_id', $f->venue_id)
            ->where('status_lapangan', '!=', 'draft')->whereRaw('LOWER(TRIM(nama_lapangan)) = ?', [mb_strtolower(trim((string) $f->nama_lapangan))])
            ->get(['id', 'nama_lapangan', 'status_lapangan']);

        if ($others->isEmpty()) {
            return $this->item('field_dup', $label, MitraDataChecks::PASS, 'Tidak ada lapangan lain dengan nama yang sama di venue ini.');
        }

        return $this->item('field_dup', $label, MitraDataChecks::WARN, 'Ada lapangan lain dengan nama yang sama di venue ini.', false, null,
            $others->take(3)->map(fn ($o) => [
                'label' => "{$o->nama_lapangan} (" . (self::LISTING_STATUS[$o->status_lapangan] ?? $o->status_lapangan) . ')',
                'url'   => route('admin.moderasi.show', ['jenis' => 'lapangan', 'id' => $o->id]),
            ])->all());
    }

    // ---- helpers

    private function rupiah(float $n): string
    {
        return 'Rp ' . number_format($n, 0, ',', '.');
    }

    private function item(string $key, string $label, string $status, string $detail, bool $blocking = false, ?string $reason = null, array $links = []): array
    {
        return compact('key', 'label', 'status', 'detail', 'blocking', 'reason', 'links');
    }
}