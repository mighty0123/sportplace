<?php

namespace App\Services\Admin;

use App\Exceptions\Admin\ListingAlreadyProcessedException;
use App\Exceptions\Admin\ListingBlockedByChecksException;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Moderasi listing venue dan lapangan
 */
class ListingModerationService
{
    /** Satu sumber untuk seluruh modul moderasi. */
    public const TYPES = [
        'venue'    => ['table' => 'venues', 'status' => 'status_vanue',    'entity' => 'Venue', 'label' => 'Venue'],
        'lapangan' => ['table' => 'fields', 'status' => 'status_lapangan', 'entity' => 'Field', 'label' => 'Lapangan'],
    ];

    /** Pemeriksaan manual yang dikonfirmasi admin di dialog Setujui. */
    public const CHECKLIST = [
        'venue' => [
            'info_sesuai'     => 'Nama, alamat, dan lokasi venue sesuai kenyataan',
            'deskripsi_layak' => 'Deskripsi dan tautan peta wajar dan tidak menyesatkan',
        ],
        'lapangan' => [
            'foto_layak'  => 'Foto sesuai dengan lapangan dan layak ditayangkan',
            'harga_wajar' => 'Harga dan jadwal wajar untuk lapangan ini',
        ],
    ];

    private const DAYS = [0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];

    public function __construct(private ListingChecks $checks)
    {
    }

    public static function type(string $jenis): array
    {
        return self::TYPES[$jenis] ?? abort(404);
    }

    public function venue(int $id): ?object
    {
        return DB::table('venues as v')
            ->join('mitra_profiles as m', 'm.id', '=', 'v.mitra_id')
            ->join('users as u', 'u.id', '=', 'm.user_id')
            ->join('master_cities as c', 'c.id', '=', 'v.city_id')
            ->leftJoin('users as r', 'r.id', '=', 'v.reviewed_by')
            ->where('v.id', $id)
            ->selectRaw('v.*, COALESCE(v.submitted_at, v.updated_at, v.created_at) as waiting_since,
                m.nama_usaha, m.status as mitra_status, u.name as owner_name, u.email as owner_email, u.no_hp as owner_phone,
                c.nama_kota, c.provinsi, r.name as reviewer_name')
            ->first();
    }

    public function field(int $id): ?object
    {
        return DB::table('fields as f')
            ->join('venues as v', 'v.id', '=', 'f.venue_id')
            ->join('mitra_profiles as m', 'm.id', '=', 'v.mitra_id')
            ->join('users as u', 'u.id', '=', 'm.user_id')
            ->join('master_sports as s', 's.id', '=', 'f.sport_id')
            ->leftJoin('users as r', 'r.id', '=', 'f.reviewed_by')
            ->where('f.id', $id)
            ->selectRaw('f.*, COALESCE(f.submitted_at, f.updated_at, f.created_at) as waiting_since,
                v.nama_venue, v.status_vanue as venue_status, v.jam_buka as venue_open, v.jam_tutup as venue_close, v.mitra_id,
                m.nama_usaha, m.status as mitra_status, u.name as owner_name, u.email as owner_email, u.no_hp as owner_phone,
                s.nama_olahraga, s.status_olahraga as sport_status, r.name as reviewer_name')
            ->first();
    }

    public function venueFields(int $venueId): Collection
    {
        return DB::table('fields as f')->join('master_sports as s', 's.id', '=', 'f.sport_id')
            ->where('f.venue_id', $venueId)->orderBy('f.id')
            ->get(['f.id', 'f.nama_lapangan', 'f.harga_perjam', 'f.status_lapangan', 's.nama_olahraga']);
    }

    public function fieldPhotos(int $fieldId): Collection
    {
        $seenPrimary = false;

        return DB::table('field_photo')->where('field_id', $fieldId)
            ->orderByDesc('is_primary')->orderBy('id')->get()
            ->map(function ($p) use (&$seenPrimary) {
                $isPrimary   = (bool) $p->is_primary && ! $seenPrimary;
                $seenPrimary = $seenPrimary || (bool) $p->is_primary;

                return (object) ['id' => $p->id, 'primary' => $isPrimary, 'url' => $this->photoUrl((string) $p->foto_url)];
            });
    }

    public function fieldFacilities(int $fieldId): Collection
    {
        return DB::table('field_facilities as ff')->join('master_facilities as mf', 'mf.id', '=', 'ff.facilities_id')
            ->where('ff.field_id', $fieldId)->orderBy('mf.kategori')->orderBy('mf.nama')
            ->get(['mf.nama', 'mf.kategori']);
    }

    /** @return array{rows: Collection, note: ?string} */
    public function fieldSchedule(int $fieldId): array
    {
        $days = DB::table('field_schedule')->where('field_id', $fieldId)->get()
            ->map(function ($r) {
                $iso  = (int) $r->hari === 0 ? 7 : (int) $r->hari; // 0 dan 7 sama-sama Minggu
                $open = $r->status_hari === 'buka' && $r->jam_buka && $r->jam_tutup;

                return (object) [
                    'iso'   => $iso,
                    'day'   => self::DAYS[$iso] ?? "Hari {$r->hari}",
                    'open'  => $open,
                    'hours' => $open ? substr($r->jam_buka, 0, 5) . ' sampai ' . substr($r->jam_tutup, 0, 5) : null,
                    'slot'  => $open ? $this->humanDuration($r->durasi) : null,
                ];
            })->sortBy('iso')->values();

        // Gabung hari berurutan yang jam dan durasinya sama: "Senin sampai Jumat"
        $groups = [];
        foreach ($days as $d) {
            $sig  = $d->open ? "{$d->hours}|{$d->slot}" : 'tutup';
            $last = array_key_last($groups);
            if ($last !== null && $groups[$last]['sig'] === $sig && $groups[$last]['to']->iso === $d->iso - 1) {
                $groups[$last]['to'] = $d;
            } else {
                $groups[] = ['sig' => $sig, 'from' => $d, 'to' => $d];
            }
        }

        $rows = collect($groups)->map(function ($g) use ($days) {
            [$from, $to] = [$g['from'], $g['to']];
            $label = match (true) {
                $days->count() === 7 && $from->iso === 1 && $to->iso === 7 => 'Setiap hari',
                $from->iso === $to->iso                                    => $from->day,
                default                                                    => "{$from->day} sampai {$to->day}",
            };

            return (object) ['label' => $label, 'open' => $from->open, 'hours' => $from->hours, 'slot' => $from->slot];
        });

        // Durasi seragam cukup disebut sekali di atas, bukan diulang di tiap baris.
        $slots = $rows->where('open', true)->pluck('slot')->filter()->unique();
        $note  = $slots->count() === 1 ? "Satu sesi sewa berdurasi {$slots->first()}." : null;
        if ($note) {
            foreach ($rows as $row) {
                $row->slot = null;
            }
        }

        return ['rows' => $rows, 'note' => $note];
    }

    private function humanDuration(?string $time): ?string
    {
        if (! $time) {
            return null;
        }
        [$h, $m] = array_map('intval', array_pad(explode(':', $time), 2, 0));
        $minutes = $h * 60 + $m;
        if ($minutes <= 0) {
            return null;
        }
        $hours = intdiv($minutes, 60);
        $rest  = $minutes % 60;

        return trim(($hours ? "{$hours} jam" : '') . ($rest ? " {$rest} menit" : ''));
    }

    public function history(string $jenis, int $id): Collection
    {
        $entity = self::type($jenis)['entity'];

        return AuditLog::query()->with('user:id,name')
            ->whereIn('entitas_type', [$entity, "App\\Models\\{$entity}"])
            ->where('entitas_id', $id)
            ->whereNotIn('aksi', ['lihat_dokumen', 'lihat_nik', 'lihat_data', 'ekspor'])
            ->latest('created_at')->latest('id')->get();
    }

    /** Hanya alamat web (http/https) atau path relatif di disk foto */
    public function photoUrl(string $path): ?string
    {
        $path = trim($path);
        if ($path === '') {
            return null;
        }
        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }
        if (preg_match('#^[a-z][a-z0-9+.\-]*:#i', $path)) {
            return null;
        }

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk(config('admin.listing_photo_disk', 'public'));

        return $disk->url(ltrim($path, '/'));
    }

    /** @return string nama listing, untuk pesan sukses. */
    public function approve(string $jenis, int $id, User $admin, array $checklist): string
    {
        $subject = $jenis === 'venue' ? $this->venue($id) : $this->field($id);
        abort_if(! $subject, 404);

        $results = $jenis === 'venue' ? $this->checks->forVenue($subject) : $this->checks->forField($subject);
        if ($this->checks->isBlocked($results)) {
            throw new ListingBlockedByChecksException($this->checks->blockingMessage($results));
        }

        $items = self::CHECKLIST[$jenis];
        $done = collect($items)->keys()->filter(fn ($k) => filter_var($checklist[$k] ?? false, FILTER_VALIDATE_BOOLEAN))->count();
        $note = 'Pemeriksaan otomatis: ' . $this->checks->summary($results) . ". Pemeriksaan manual: {$done} dari " . count($items) . ' butir dicentang.';

        $this->decide($jenis, $id, $admin, 'aktif', 'setujui', null, $note, requireParent: true);

        return $this->nameOf($jenis, $subject);
    }

    public function reject(string $jenis, int $id, User $admin, string $reason): string
    {
        $subject = $jenis === 'venue' ? $this->venue($id) : $this->field($id);
        abort_if(! $subject, 404);

        $this->decide($jenis, $id, $admin, 'rejected', 'tolak', $reason, $reason, requireParent: false);

        return $this->nameOf($jenis, $subject);
    }

    private function decide(string $jenis, int $id, User $admin, string $newStatus, string $aksi, ?string $reason, string $note, bool $requireParent): void
    {
        $t = self::type($jenis);

        DB::transaction(function () use ($t, $jenis, $id, $admin, $newStatus, $aksi, $reason, $note, $requireParent) {
            // hanya berhasil bila status masih 'pending_review' saat query berjalan.
            $query = DB::table($t['table'])->where('id', $id)->where($t['status'], 'pending_review');

            // Syarat induk dijaga di database, bukan hanya di pemeriksaan sebelumnya, supaya aman dari perubahan bersamaan.
            if ($requireParent) {
                $jenis === 'venue'
                    ? $query->whereExists(fn ($q) => $q->selectRaw('1')->from('mitra_profiles')
                        ->whereColumn('mitra_profiles.id', 'venues.mitra_id')->where('mitra_profiles.status', 'approved'))
                    : $query->whereExists(fn ($q) => $q->selectRaw('1')->from('venues')
                        ->whereColumn('venues.id', 'fields.venue_id')->where('venues.status_vanue', 'aktif'));
            }

            $affected = $query->update([
                $t['status']       => $newStatus,
                'alasan_penolakan' => $reason,
                'reviewed_by'      => $admin->id,
                'reviewed_at'      => now(),
                'updated_at'       => now(),
            ]);

            if ($affected === 0) {
                throw new ListingAlreadyProcessedException();
            }

            // Satu transaksi dengan perubahan status: kalau pencatatan gagal, status ikut dibatalkan.
            AuditLog::record($admin, $t['entity'], $id, $aksi, 'pending_review', $newStatus, $note);
        });
    }

    private function nameOf(string $jenis, object $subject): string
    {
        $name = $jenis === 'venue' ? $subject->nama_venue : $subject->nama_lapangan;

        return trim((string) $name) !== '' ? $name : self::TYPES[$jenis]['label'] . " #{$subject->id}";
    }
}