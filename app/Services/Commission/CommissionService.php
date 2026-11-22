<?php

namespace App\Services\Commission;

use App\Exceptions\Commission\CommissionRuleException;
use App\Exceptions\Commission\NoCommissionRateException;
use App\Models\AuditLog;
use App\Models\User;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Tarif komisi: riwayat yang hanya bertambah, pencarian tarif menurut waktu dan prioritas, perhitungan, dan penilaian
 * per rincian slot. Semua baca-tulis memakai query builder dengan binding.
 */
class CommissionService
{
    private const DT = 'Y-m-d H:i:s';

    // ============================================================ perhitungan

    /**
     * Membagi harga slot menjadi komisi dan hak mitra. Memakai bilangan bulat (persen dalam basis poin) supaya tidak ada
     * galat pecahan. Pembulatan setengah naik pada komisi, dan hak mitra = harga dikurangi komisi sehingga jumlahnya pas.
     *
     * @return array{komisi:int, mitra:int}
     */
    public function split(int|float|string $price, int|float|string $percent): array
    {
        $p = (int) round((float) $price);
        if ($p < 0) {
            throw new InvalidArgumentException('Harga tidak boleh negatif.');
        }

        $bp = (int) round((float) $percent * 100);
        if ($bp < 0 || $bp > 10000) {
            throw new InvalidArgumentException('Persentase harus antara 0 dan 100.');
        }

        $komisi = intdiv($p * $bp + 5000, 10000);

        return ['komisi' => $komisi, 'mitra' => $p - $komisi];
    }

    /** Pratinjau untuk satu atau beberapa slot berharga sama. Biaya layanan dikenakan sekali per booking, tanpa komisi. */
    public function preview(int|float $price, int $slots, ?int $mitraId, ?int $sportId = null, ?CarbonInterface $at = null): array
    {
        $rate = $this->resolve($mitraId, $sportId, $at);
        $per = $this->split($price, $rate->persen);
        $slots = max(1, $slots);
        $fee = (int) config('booking.service_fee', 2500);
        $subtotal = (int) round((float) $price) * $slots;
        $komisi = $per['komisi'] * $slots;

        return [
            'rate'            => $rate,
            'per_slot'        => $per,
            'slots'           => $slots,
            'subtotal'        => $subtotal,
            'komisi'          => $komisi,
            'mitra'           => $per['mitra'] * $slots,
            'biaya_layanan'   => $fee,
            'dibayar_penyewa' => $subtotal + $fee,
            'platform'        => $komisi + $fee,
        ];
    }

    // =============================================================== pencarian

    public static function scopeKey(?int $mitraId, ?int $sportId = null): string
    {
        return match (true) {
            (bool) $mitraId && (bool) $sportId => "m{$mitraId}s{$sportId}",
            (bool) $mitraId                    => "m{$mitraId}",
            (bool) $sportId                    => "s{$sportId}",
            default                            => 'g',
        };
    }

    public function maxPercent(): float
    {
        return (float) config('commission.max_percent', 50);
    }

    /** Teks persen untuk tampilan: 20 menjadi "20%", 12.5 menjadi "12,5%". */
    public static function percentLabel(int|float|string $p): string
    {
        return rtrim(rtrim(number_format((float) $p, 2, ',', ''), '0'), ',') . '%';
    }

    /**
     * Tarif yang berlaku untuk mitra dan cabang olahraga pada waktu tertentu. Urutan: mitra+olahraga, mitra, olahraga, global.
     * Baris berpersen kosong ("ikuti") dilewati ke tingkat berikutnya. Bila tidak ada sama sekali: NoCommissionRateException.
     *
     * @return object{id:int, persen:string, level:string, mitra_id:?int, sport_id:?int, berlaku_mulai:string}
     */
    public function resolve(?int $mitraId, ?int $sportId = null, ?CarbonInterface $at = null): object
    {
        $when = ($at ?? now())->format(self::DT);

        $rows = DB::table('commission_rates')->where('berlaku_mulai', '<=', $when)
            ->where(function ($w) use ($mitraId, $sportId) {
                $w->where(fn ($g) => $g->whereNull('mitra_id')->whereNull('sport_id'));
                if ($mitraId) {
                    $w->orWhere(fn ($m) => $m->where('mitra_id', $mitraId)->whereNull('sport_id'));
                }
                if ($sportId) {
                    $w->orWhere(fn ($s) => $s->whereNull('mitra_id')->where('sport_id', $sportId));
                }
                if ($mitraId && $sportId) {
                    $w->orWhere(fn ($ms) => $ms->where('mitra_id', $mitraId)->where('sport_id', $sportId));
                }
            })
            ->orderByDesc('berlaku_mulai')->orderByDesc('id')->get();

        $levels = [
            'mitra_olahraga' => fn ($r) => $r->mitra_id !== null && $r->sport_id !== null,
            'mitra'          => fn ($r) => $r->mitra_id !== null && $r->sport_id === null,
            'olahraga'       => fn ($r) => $r->mitra_id === null && $r->sport_id !== null,
            'global'         => fn ($r) => $r->mitra_id === null && $r->sport_id === null,
        ];

        foreach ($levels as $name => $test) {
            $row = $rows->first($test); // terbaru pada tingkat ini (urutan sudah menurun)
            if ($row && $row->persen !== null) {
                $row->level = $name;

                return $row;
            }
        }

        throw new NoCommissionRateException();
    }

    // ================================================================ penilaian

    /**
     * Menilai komisi semua rincian slot booking yang belum dinilai, memakai tarif pada waktu penilaian. Idempoten:
     * rincian yang sudah dinilai dilewati, dan tarif yang berubah sesudahnya tidak mengubah hasil lama. Dipanggil di
     * dalam transaksi pelunasan, dan gagal seluruhnya bila ada rincian yang tarifnya tidak ditemukan.
     *
     * @return array{details:int, komisi:int, mitra:int}
     */
    public function assess(int $headerId, ?CarbonInterface $at = null): array
    {
        $at ??= now();

        return DB::transaction(function () use ($headerId, $at) {
            $details = DB::table('booking_details')->where('header_id', $headerId)->whereNull('komisi_nominal')
                ->orderBy('id')->lockForUpdate()->get(['id', 'field_id', 'harga_slot']);

            $owners = []; // [field_id => [mitra_id, sport_id]]
            $sum = ['details' => 0, 'komisi' => 0, 'mitra' => 0];

            foreach ($details as $d) {
                $owners[$d->field_id] ??= $this->ownerOf((int) $d->field_id);
                [$mitraId, $sportId] = $owners[$d->field_id];

                $rate = $this->resolve($mitraId, $sportId, $at);
                $s = $this->split($d->harga_slot, $rate->persen);

                DB::table('booking_details')->where('id', $d->id)->update([
                    'komisi_persen' => $rate->persen, 'komisi_nominal' => $s['komisi'],
                    'pendapatan_mitra' => $s['mitra'], 'komisi_rate_id' => $rate->id,
                ]);

                $sum['details']++;
                $sum['komisi'] += $s['komisi'];
                $sum['mitra'] += $s['mitra'];
            }

            return $sum;
        });
    }

    /** @return array{0:int,1:int} [mitra_id, sport_id] pemilik lapangan */
    private function ownerOf(int $fieldId): array
    {
        $row = DB::table('fields as f')->join('venues as v', 'v.id', '=', 'f.venue_id')
            ->where('f.id', $fieldId)->first(['v.mitra_id', 'f.sport_id']);

        if (! $row) {
            throw new InvalidArgumentException("Lapangan #{$fieldId} tidak ditemukan.");
        }

        return [(int) $row->mitra_id, (int) $row->sport_id];
    }

    // ============================================================== penjadwalan

    /**
     * Menambah satu baris riwayat tarif. Tidak pernah mengubah atau menghapus baris lama. Persen kosong pada tarif mitra
     * berarti "ikuti tarif global". Mulai berlaku kosong berarti sekarang.
     *
     * @throws CommissionRuleException
     */
    public function schedule(User $admin, ?int $mitraId, ?string $persen, ?CarbonInterface $from, string $note, ?int $sportId = null): int
    {
        $now = CarbonImmutable::now();
        $from = $from ? CarbonImmutable::instance($from) : $now;
        $isGlobal = ! $mitraId && ! $sportId;
        $note = trim($note);

        if ($isGlobal && $persen === null) {
            throw new CommissionRuleException('persen', 'Tarif global wajib berisi persentase.');
        }
        if ($persen !== null) {
            if (! is_numeric($persen) || (float) $persen < 0) {
                throw new CommissionRuleException('persen', 'Persentase harus berupa angka nol atau lebih.');
            }
            if ((float) $persen > $this->maxPercent()) {
                throw new CommissionRuleException('persen', 'Persentase maksimal ' . self::percentLabel($this->maxPercent()) . '.');
            }
            $persen = number_format((float) $persen, 2, '.', '');
        }
        if (mb_strlen($note) < 10) {
            throw new CommissionRuleException('catatan', 'Alasan minimal 10 karakter.');
        }
        if ($from->lt($now->subMinute())) {
            throw new CommissionRuleException('berlaku_mulai', 'Waktu mulai berlaku tidak boleh di masa lalu.');
        }
        if ($from->gt($now->addDays((int) config('commission.max_schedule_days', 365)))) {
            throw new CommissionRuleException('berlaku_mulai', 'Tarif hanya bisa dijadwalkan sampai ' . (int) config('commission.max_schedule_days', 365) . ' hari ke depan.');
        }

        $mitraName = null;
        if ($mitraId) {
            $mitra = DB::table('mitra_profiles')->where('id', $mitraId)->first(['status', 'nama_usaha']);
            if (! $mitra || $mitra->status !== 'approved') {
                throw new CommissionRuleException('mitra_id', 'Tarif khusus hanya untuk mitra yang sudah disetujui.');
            }
            $mitraName = $mitra->nama_usaha ?: "Mitra #{$mitraId}";
        }
        if ($sportId && ! DB::table('master_sports')->where('id', $sportId)->exists()) {
            throw new CommissionRuleException('sport_id', 'Cabang olahraga tidak ditemukan.');
        }

        $key = self::scopeKey($mitraId, $sportId);
        $when = $from->format(self::DT);

        try {
            return DB::transaction(function () use ($admin, $mitraId, $sportId, $persen, $note, $key, $when, $from, $isGlobal, $mitraName) {
                // Kunci baris lingkup ini: dua admin yang bertindak bersamaan tidak bisa menyisipkan tarif yang saling bertabrakan.
                DB::table('commission_rates')->where('scope_key', $key)->lockForUpdate()->get(['id']);

                if (DB::table('commission_rates')->where('scope_key', $key)->where('berlaku_mulai', $when)->exists()) {
                    throw new CommissionRuleException('berlaku_mulai', 'Sudah ada tarif pada waktu yang sama untuk lingkup ini. Pilih waktu lain.');
                }

                // Tidak ada gunanya menambah baris yang tidak mengubah apa pun pada tingkat yang sama.
                $before = $this->scopePercentAt($key, $when);
                if ($before === $persen) {
                    throw new CommissionRuleException('persen', $persen === null
                        ? 'Mitra ini sudah mengikuti tarif global.'
                        : 'Tarif ini sama dengan yang sudah berlaku pada waktu tersebut.');
                }

                $id = DB::table('commission_rates')->insertGetId([
                    'scope_key' => $key, 'mitra_id' => $mitraId, 'sport_id' => $sportId, 'persen' => $persen,
                    'berlaku_mulai' => $when, 'dibuat_oleh' => $admin->id, 'catatan' => $note, 'created_at' => now(),
                ]);

                $scopeLabel = $isGlobal ? 'global' : ($mitraName ?? "olahraga #{$sportId}");
                $beforeText = $before === null ? ($isGlobal ? '-' : 'ikut global') : self::percentLabel($before);
                $afterText = $persen === null ? 'ikut global' : self::percentLabel($persen);

                AuditLog::record($admin, 'CommissionRate', $id, 'tambah_tarif', mb_substr($beforeText, 0, 50), mb_substr($afterText, 0, 50),
                    mb_substr("Tarif {$scopeLabel}: {$beforeText} menjadi {$afterText}, berlaku {$from->locale('id')->translatedFormat('j M Y, H.i')}. Alasan: {$note}", 0, 500));

                return $id;
            });
        } catch (UniqueConstraintViolationException) {
            throw new CommissionRuleException('berlaku_mulai', 'Tarif pada waktu yang sama baru saja ditambahkan admin lain. Muat ulang halaman.');
        }
    }

    /** Persen pada lingkup ini saja (tanpa turun ke tingkat atas). NULL = belum ada baris, atau barisnya "ikuti". */
    private function scopePercentAt(string $key, string $at): ?string
    {
        $v = DB::table('commission_rates')->where('scope_key', $key)->where('berlaku_mulai', '<=', $at)
            ->orderByDesc('berlaku_mulai')->orderByDesc('id')->limit(1)->value('persen');

        return $v === null ? null : number_format((float) $v, 2, '.', '');
    }

    // ============================================================== untuk tampilan

    /** Baris yang sedang berlaku (terbaru yang sudah mulai) untuk setiap lingkup. */
    public function effectiveRows(): Builder
    {
        $now = now()->format(self::DT);

        return DB::table('commission_rates as r')->where('r.berlaku_mulai', '<=', $now)
            ->whereNotExists(fn ($q) => $q->selectRaw('1')->from('commission_rates as r2')
                ->whereColumn('r2.scope_key', 'r.scope_key')->where('r2.berlaku_mulai', '<=', $now)
                ->whereColumn('r2.berlaku_mulai', '>', 'r.berlaku_mulai'));
    }

    public function currentGlobal(): ?object
    {
        return $this->effectiveRows()->whereNull('r.mitra_id')->whereNull('r.sport_id')
            ->leftJoin('users as a', 'a.id', '=', 'r.dibuat_oleh')
            ->first(['r.id', 'r.persen', 'r.berlaku_mulai', 'r.catatan', 'a.name as admin_name']);
    }

    /** Tarif global terdekat yang akan berlaku (terjadwal), bila ada. */
    public function nextGlobal(): ?object
    {
        return DB::table('commission_rates')->whereNull('mitra_id')->whereNull('sport_id')
            ->where('berlaku_mulai', '>', now()->format(self::DT))->orderBy('berlaku_mulai')->first(['persen', 'berlaku_mulai']);
    }

    /** Mitra yang saat ini punya tarif khusus (bukan "ikuti global"). */
    public function activeOverrides(): Collection
    {
        return $this->effectiveRows()->whereNotNull('r.mitra_id')->whereNull('r.sport_id')->whereNotNull('r.persen')
            ->join('mitra_profiles as m', 'm.id', '=', 'r.mitra_id')
            ->leftJoin('users as a', 'a.id', '=', 'r.dibuat_oleh')
            ->orderBy('m.nama_usaha')
            ->get(['r.id', 'r.mitra_id', 'r.persen', 'r.berlaku_mulai', 'r.catatan', 'm.nama_usaha', 'a.name as admin_name']);
    }

    public function scheduledCount(): int
    {
        return DB::table('commission_rates')->where('berlaku_mulai', '>', now()->format(self::DT))->count();
    }

    /** Mitra yang disetujui, untuk pilihan di formulir. */
    public function mitraOptions(int $limit = 300): Collection
    {
        return DB::table('mitra_profiles as m')->where('m.status', 'approved')->orderBy('m.nama_usaha')->limit($limit)->get(['m.id', 'm.nama_usaha']);
    }
}