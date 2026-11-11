<?php

namespace App\Services\Admin;

use Carbon\CarbonImmutable;
use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TransactionMonitor
{
    // Label usulan: arti status 'payment' belum diputuskan
    public const BOOKING_STATUS = ['pending' => 'Menunggu bayar', 'payment' => 'Dibayar', 'canceled' => 'Dibatalkan', 'expired' => 'Kedaluwarsa'];
    public const PAYMENT_STATE  = ['lunas' => 'Lunas', 'menunggu' => 'Menunggu bayar', 'gagal' => 'Gagal', 'belum' => 'Belum ada pembayaran'];
    public const PERIODS = ['hari-ini' => 'Hari ini', '7' => '7 hari terakhir', '30' => '30 hari terakhir', '90' => '90 hari terakhir', 'semua' => 'Semua waktu', 'kustom' => 'Pilih tanggal'];
    public const SORTS   = ['terbaru' => 'Terbaru dulu', 'terlama' => 'Terlama dulu', 'nominal' => 'Nominal terbesar'];

    private const PAID   = "EXISTS (SELECT 1 FROM payments p WHERE p.header_id = h.id AND p.status_pembayaran = 'succes')";
    private const PEND   = "EXISTS (SELECT 1 FROM payments p WHERE p.header_id = h.id AND p.status_pembayaran = 'pending')";
    private const FAIL   = "EXISTS (SELECT 1 FROM payments p WHERE p.header_id = h.id AND p.status_pembayaran = 'failed')";
    private const ANYPAY = 'EXISTS (SELECT 1 FROM payments p WHERE p.header_id = h.id)';
    private const PAID_SUM  = "(SELECT COALESCE(SUM(p.nominal_bayar), 0) FROM payments p WHERE p.header_id = h.id AND p.status_pembayaran = 'succes')";
    private const ITEMS     = 'EXISTS (SELECT 1 FROM booking_details d WHERE d.header_id = h.id)';
    private const ITEMS_SUM = '(SELECT COALESCE(SUM(d.harga_slot), 0) FROM booking_details d WHERE d.header_id = h.id)';
    private const REFUND_ACTIVE = "(SELECT COALESCE(SUM(r.nominal_refund), 0) FROM refunds r WHERE r.header_id = h.id AND r.status_refund IN ('pending', 'processing', 'completed'))";

    /** Penanda data yang saling bertentangan: [ekspresi SQL, pesan]. Dipakai untuk kolom, filter, dan teks. */
    private const FLAGS = [
        'no_paid'         => ["(h.status = 'payment' AND NOT " . self::PAID . ')', 'Berstatus dibayar, tetapi tidak ada pembayaran berhasil.'],
        'paid_not_booked' => ["(h.status IN ('pending', 'expired', 'canceled') AND " . self::PAID . ')', 'Ada pembayaran berhasil, tetapi status booking %s. Uang sudah masuk, periksa apakah perlu refund.'],
        'amount_mismatch' => ['(' . self::PAID . ' AND ABS(' . self::PAID_SUM . ' - h.total_tagihan) > 0.5)', 'Nominal yang berhasil dibayar berbeda dari total tagihan.'],
        'no_items'        => ['(NOT ' . self::ITEMS . ')', 'Booking tidak memiliki rincian slot.'],
        'items_mismatch'  => ['(' . self::ITEMS . ' AND ABS(' . self::ITEMS_SUM . ' - h.total_tagihan) > 0.5)', 'Total tagihan berbeda dari jumlah harga slot.'],
        'refund_over'     => ['(' . self::REFUND_ACTIVE . ' > ' . self::PAID_SUM . ' + 0.5)', 'Total refund melebihi nominal yang berhasil dibayar.'],
        'overdue_pending' => ["(h.status = 'pending' AND h.expires_at IS NOT NULL AND h.expires_at < NOW() AND NOT " . self::PAID . ')','Melewati batas bayar, tetapi belum berstatus kedaluwarsa. Penjadwal mungkin tidak berjalan.'],
    ];

    // ---- filter

    /** Menormalkan masukan, semua nilai dicocokkan dengan daftar yang diizinkan */
    public function filters(array $in): array
    {
        $methods = $this->methods();
        $method  = (string) ($in['method'] ?? '');

        return [
            'period'  => array_key_exists($in['period'] ?? '', self::PERIODS) ? $in['period'] : '30',
            'from'    => (string) ($in['from'] ?? ''),
            'to'      => (string) ($in['to'] ?? ''),
            'status'  => array_key_exists($in['status'] ?? '', self::BOOKING_STATUS) ? $in['status'] : '',
            'payment' => array_key_exists($in['payment'] ?? '', self::PAYMENT_STATE) ? $in['payment'] : '',
            'method'  => in_array($method, $methods, true) ? $method : '',
            'flagged' => (bool) ($in['flagged'] ?? false),
            'search'  => mb_substr(trim((string) ($in['search'] ?? '')), 0, 100),
            'sort'    => array_key_exists($in['sort'] ?? '', self::SORTS) ? $in['sort'] : 'terbaru',
        ];
    }

    /** @return array{0: ?CarbonImmutable, 1: ?CarbonImmutable, 2: ?string} awal, akhir, pesan galat */
    public function range(array $f): array
    {
        $now = CarbonImmutable::now();

        switch ($f['period']) {
            case 'hari-ini': return [$now->startOfDay(), null, null];
            case '7':        return [$now->subDays(6)->startOfDay(), null, null];
            case '30':       return [$now->subDays(29)->startOfDay(), null, null];
            case '90':       return [$now->subDays(89)->startOfDay(), null, null];
            case 'semua':    return [null, null, null];
        }

        $from = $this->parseDate($f['from']);
        $to   = $this->parseDate($f['to']);
        if (($f['from'] !== '' && ! $from) || ($f['to'] !== '' && ! $to)) {
            return [null, null, 'Tanggal tidak valid. Gunakan pilihan tanggal.'];
        }
        if ($from && $to && $from > $to) {
            return [null, null, 'Tanggal mulai tidak boleh setelah tanggal akhir.'];
        }

        return [$from?->startOfDay(), $to?->endOfDay(), null];
    }

    public function methods(): array
    {
        return DB::table('payments')->distinct()->orderBy('metode_pembayaran')->pluck('metode_pembayaran')->all();
    }

    // ---- daftar

    public function base(array $f, bool $withStatus = true): Builder
    {
        [$from, $to] = $this->range($f);
        $q = DB::table('booking_headers as h')->join('users as u', 'u.id', '=', 'h.penyewa_id');

        if ($from) {
            $q->where('h.created_at', '>=', $from);
        }
        if ($to) {
            $q->where('h.created_at', '<=', $to);
        }
        if ($withStatus && $f['status'] !== '') {
            $q->where('h.status', $f['status']);
        }

        match ($f['payment']) {
            'lunas'    => $q->whereRaw(self::PAID),
            'menunggu' => $q->whereRaw('NOT ' . self::PAID)->whereRaw(self::PEND),
            'gagal'    => $q->whereRaw('NOT ' . self::PAID)->whereRaw('NOT ' . self::PEND)->whereRaw(self::FAIL),
            'belum'    => $q->whereRaw('NOT ' . self::ANYPAY),
            default    => null,
        };

        if ($f['method'] !== '') {
            $q->whereExists(fn ($e) => $e->selectRaw('1')->from('payments as p')
                ->whereColumn('p.header_id', 'h.id')->where('p.metode_pembayaran', $f['method']));
        }
        if ($f['flagged']) {
            $q->whereRaw($this->anyFlag());
        }

        if ($f['search'] !== '') {
            $term = $f['search'];
            $like = '%' . addcslashes($term, '%_\\') . '%';
            $q->where(function ($w) use ($term, $like) {
                // "BK-000123", "bk123", atau "123" dicari sebagai ID booking.
                if (preg_match('/^(?:bk-?)?0*(\d{1,18})$/i', $term, $m)) {
                    $w->orWhere('h.id', (int) $m[1]);
                }
                $w->orWhere('h.kode_booking', 'like', $like);
                $w->orWhere('u.name', 'like', $like)->orWhere('u.email', 'like', $like)
                    ->orWhereExists(fn ($e) => $e->selectRaw('1')->from('payments as p')
                        ->whereColumn('p.header_id', 'h.id')->where('p.pg_transaction_id', 'like', $like));
            });
        }

        return $q;
    }

    public function rows(array $f, int $perPage): LengthAwarePaginator
    {
        $q = $this->base($f)
            ->leftJoin('booking_details as d1', function ($j) {
                // Hanya rincian pertama per booking, supaya satu booking tetap satu baris.
                $j->on('d1.header_id', '=', 'h.id')
                    ->whereRaw('d1.id = (SELECT MIN(d0.id) FROM booking_details d0 WHERE d0.header_id = h.id)');
            })
            ->leftJoin('fields as fx', 'fx.id', '=', 'd1.field_id')
            ->leftJoin('venues as vx', 'vx.id', '=', 'fx.venue_id')
            ->leftJoin('mitra_profiles as mx', 'mx.id', '=', 'vx.mitra_id')
            ->selectRaw($this->core() . ', vx.nama_venue, fx.nama_lapangan, mx.nama_usaha');

        match ($f['sort']) {
            'terlama' => $q->orderBy('h.created_at')->orderBy('h.id'),
            'nominal' => $q->orderByDesc('h.total_tagihan')->orderByDesc('h.id'),
            default   => $q->orderByDesc('h.created_at')->orderByDesc('h.id'),
        };

        return $q->paginate($perPage);
    }

    /** Jumlah booking per status untuk tab */
    public function statusCounts(array $f): array
    {
        return $this->base($f, false)->selectRaw('h.status as s, COUNT(*) as n')->groupBy('h.status')->pluck('n', 's')->all();
    }

    /** Angka ringkasan, mengikuti semua filter */
    public function summary(array $f): array
    {
        $base = $this->base($f);
        $ids  = (clone $base)->select('h.id');

        return [
            'count'   => (clone $base)->count(),
            'paid'    => (float) DB::table('payments')->whereIn('header_id', $ids)->where('status_pembayaran', 'succes')->sum('nominal_bayar'),
            'refund'  => (float) DB::table('refunds')->whereIn('header_id', $ids)->where('status_refund', 'completed')->sum('nominal_refund'),
            'flagged' => (clone $base)->whereRaw($this->anyFlag())->count(),
        ];
    }

    // ---- detail

    public function find(int $id): ?object
    {
        return DB::table('booking_headers as h')->join('users as u', 'u.id', '=', 'h.penyewa_id')
            ->where('h.id', $id)
            ->selectRaw($this->core() . ', u.no_hp as customer_phone')
            ->first();
    }

    public function items(int $id): Collection
    {
        return DB::table('booking_details as d')
            ->join('fields as f', 'f.id', '=', 'd.field_id')
            ->join('venues as v', 'v.id', '=', 'f.venue_id')
            ->join('mitra_profiles as m', 'm.id', '=', 'v.mitra_id')
            ->join('master_sports as s', 's.id', '=', 'f.sport_id')
            ->where('d.header_id', $id)->orderBy('d.tanggal_sewa')->orderBy('d.jam_mulai')->orderBy('d.id')
            ->get(['d.id', 'd.tanggal_sewa', 'd.jam_mulai', 'd.jam_selesai', 'd.harga_slot', 'd.status_item',
                'f.id as field_id', 'f.nama_lapangan', 'v.nama_venue', 'm.id as mitra_id', 'm.nama_usaha', 's.nama_olahraga']);
    }

    public function payments(int $id): Collection
    {
        return DB::table('payments')->where('header_id', $id)->orderBy('id')->get();
    }

    public function refunds(int $id): Collection
    {
        return DB::table('refunds as r')
            ->leftJoin('users as a', 'a.id', '=', 'r.diproses_oleh')
            ->leftJoin('payments as p', 'p.id', '=', 'r.payment_id')
            ->leftJoin('booking_details as d', 'd.id', '=', 'r.detail_id')
            ->leftJoin('fields as f', 'f.id', '=', 'd.field_id')
            ->where('r.header_id', $id)->orderBy('r.id')
            ->get(['r.*', 'a.name as processor', 'p.pg_transaction_id', 'f.nama_lapangan', 'd.tanggal_sewa', 'd.jam_mulai']);
    }

    /** Rekonsiliasi uang untuk satu booking */
    public function ledger(object $trx, Collection $payments, Collection $refunds): array
    {
        $paid = (float) $payments->where('status_pembayaran', 'succes')->sum('nominal_bayar');
        $done = (float) $refunds->where('status_refund', 'completed')->sum('nominal_refund');

        return [
            'tagihan'         => (float) $trx->total_tagihan,
            'dibayar'         => $paid,
            'refund_selesai'  => $done,
            'refund_berjalan' => (float) $refunds->whereIn('status_refund', ['pending', 'processing'])->sum('nominal_refund'),
            'bersih'          => $paid - $done,
        ];
    }

    // ----- turunan

    public static function code(int $id, ?string $kode = null): string
    {
        return $kode ?: 'BK-' . str_pad((string) $id, 6, '0', STR_PAD_LEFT);
    }
    /** Status pembayaran diturunkan dari tabel payments */
    public function paymentState(object $row): string
    {
        return match (true) {
            (int) $row->pay_succes > 0  => 'lunas',
            (int) $row->pay_pending > 0 => 'menunggu',
            (int) $row->pay_failed > 0  => 'gagal',
            default                     => 'belum',
        };
    }

    /** @return string[] pesan untuk setiap penanda yang menyala pada baris ini */
    public function flagsFor(object $row): array
    {
        $out = [];
        foreach (self::FLAGS as $key => [, $message]) {
            if (! empty($row->{"flag_{$key}"})) {
                $out[] = $key === 'paid_not_booked'
                    ? sprintf($message, mb_strtolower(self::BOOKING_STATUS[$row->status] ?? $row->status))
                    : $message;
            }
        }

        return $out;
    }

    /** Rekening tujuan refund tersamar */
    public static function maskAccount(?string $value): string
    {
        return (string) preg_replace_callback('/\d{5,}/', fn ($m) => str_repeat('•', strlen($m[0]) - 4) . substr($m[0], -4), (string) $value);
    }

    // ---- helpers

    private function core(): string
    {
        $count = fn (string $status) => "(SELECT COUNT(*) FROM payments p WHERE p.header_id = h.id AND p.status_pembayaran = '{$status}')";
        $flags = implode(', ', array_map(fn ($k, $v) => "{$v[0]} as flag_{$k}", array_keys(self::FLAGS), self::FLAGS));

        return "h.id, h.kode_booking, h.total_tagihan, h.status, h.expires_at, h.created_at, u.id as customer_id, u.name as customer_name, u.email as customer_email,
            (SELECT COUNT(*) FROM booking_details d WHERE d.header_id = h.id) as item_count,
            " . self::PAID_SUM . " as paid_total,
            {$count('succes')} as pay_succes, {$count('pending')} as pay_pending, {$count('failed')} as pay_failed,
            (SELECT p.metode_pembayaran FROM payments p WHERE p.header_id = h.id ORDER BY (p.status_pembayaran = 'succes') DESC, p.id DESC LIMIT 1) as method,
            (SELECT COALESCE(SUM(r.nominal_refund), 0) FROM refunds r WHERE r.header_id = h.id AND r.status_refund = 'completed') as refund_done,
            (SELECT COUNT(*) FROM refunds r WHERE r.header_id = h.id AND r.status_refund IN ('pending', 'processing')) as refund_open,
            {$flags}";
    }

    private function anyFlag(): string
    {
        return '(' . implode(' OR ', array_column(self::FLAGS, 0)) . ')';
    }

    private function parseDate(string $s): ?CarbonImmutable
    {
        if (! preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $s, $m) || ! checkdate((int) $m[2], (int) $m[3], (int) $m[1])) {
            return null;
        }

        return CarbonImmutable::createFromDate((int) $m[1], (int) $m[2], (int) $m[3]);
    }
}