<?php

namespace App\Services\Admin\Reports;

use App\Services\Admin\DashboardStats;
use App\Services\Admin\TransactionMonitor as TM;
use Carbon\CarbonImmutable;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Menyusun data laporan. Semua nilai dari pengguna dicocokkan dengan daftar yang diizinkan di normalize(),
 * dan nilai itu hanya masuk ke query lewat binding.
 */
class ReportService
{
    public const TYPES = [
        'transaksi' => ['label' => 'Laporan Transaksi', 'desc' => 'Booking dan pembayaran lintas mitra pada periode tertentu.'],
        'mitra'     => ['label' => 'Laporan Mitra dan Listing', 'desc' => 'Status verifikasi mitra beserta jumlah venue dan lapangan.'],
        'pengguna'  => ['label' => 'Laporan Pengguna', 'desc' => 'Jumlah dan daftar akun menurut peran dan status.'],
    ];

    public const MITRA_STATUS   = ['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'];
    public const ROLES          = ['admin' => 'Admin', 'mitra' => 'Mitra', 'customer' => 'Penyewa'];
    public const ACCOUNT_STATUS = ['aktif' => 'Aktif', 'suspend' => 'Ditangguhkan', 'banned' => 'Diblokir'];

    public function __construct(private TM $monitor)
    {
    }

    // ---- masukan

    /** Menormalkan masukan: nilai yang tidak dikenal dibuang */
    public function normalize(string $type, array $in): array
    {
        abort_unless(isset(self::TYPES[$type]), 404);

        $f = [
            'period'  => array_key_exists($in['period'] ?? '', TM::PERIODS) ? $in['period'] : $this->defaultPeriod($type),
            'from'    => (string) ($in['from'] ?? ''),
            'to'      => (string) ($in['to'] ?? ''),
            'status'  => '', 'method' => '', 'role' => '', 'account' => '',
        ];

        switch ($type) {
            case 'transaksi':
                $f['status'] = array_key_exists($in['status'] ?? '', TM::BOOKING_STATUS) ? $in['status'] : '';
                $f['method'] = in_array($in['method'] ?? '', $this->monitor->methods(), true) ? $in['method'] : '';
                break;
            case 'mitra':
                $f['status'] = array_key_exists($in['status'] ?? '', self::MITRA_STATUS) ? $in['status'] : '';
                break;
            case 'pengguna':
                $f['role']    = array_key_exists($in['role'] ?? '', self::ROLES) ? $in['role'] : '';
                $f['account'] = array_key_exists($in['account'] ?? '', self::ACCOUNT_STATUS) ? $in['account'] : '';
                break;
        }

        return $f;
    }

    public function defaultPeriod(string $type): string
    {
        return $type === 'transaksi' ? 'bulan-ini' : 'semua';
    }

    /** Pilihan status untuk jenis laporan ini (kosong bila tidak berlaku) */
    public function statusOptions(string $type): array
    {
        return match ($type) {
            'transaksi' => ['' => 'Semua status'] + TM::BOOKING_STATUS,
            'mitra'     => ['' => 'Semua status'] + self::MITRA_STATUS,
            default     => [],
        };
    }

    // ---- keluaran

    public function count(string $type, array $f): int
    {
        return match ($type) {
            'transaksi' => $this->monitor->base($this->txFilters($f))->count(),
            'mitra'     => $this->mitraQuery($f)->count(),
            'pengguna'  => $this->userQuery($f)->count(),
            default     => abort(404),
        };
    }

    public function data(string $type, array $f, ?int $limit = null): ReportData
    {
        return match ($type) {
            'transaksi' => $this->transaksi($f, $limit),
            'mitra'     => $this->mitra($f, $limit),
            'pengguna'  => $this->pengguna($f, $limit),
            default     => abort(404),
        };
    }

    /** Teks periode untuk judul dan nama berkas. */
    public function periodLabel(array $f): string
    {
        [$from, $to, $error] = $this->monitor->range(['period' => $f['period'], 'from' => $f['from'], 'to' => $f['to']]);
        if ($error) {
            return 'Periode tidak valid';
        }
        if (! $from && ! $to) {
            return 'Semua waktu';
        }
        $fmt = fn (CarbonImmutable $d) => $d->locale('id')->translatedFormat('j F Y');

        return ($from ? $fmt($from) : 'awal') . ' sampai ' . $fmt($to ?? CarbonImmutable::now());
    }

    // ---- transaksi

    private function txFilters(array $f): array
    {
        return $this->monitor->filters([
            'period' => $f['period'], 'from' => $f['from'], 'to' => $f['to'], 'status' => $f['status'], 'method' => $f['method'],
        ]);
    }

    private function transaksi(array $f, ?int $limit): ReportData
    {
        $mf = $this->txFilters($f);
        $error = $this->monitor->range($mf)[2];
        $methods = DashboardStats::PAYMENT_LABELS;

        $lines = ['Periode: ' . $this->periodLabel($f)];
        if ($mf['status'] !== '') {
            $lines[] = 'Status booking: ' . TM::BOOKING_STATUS[$mf['status']];
        }
        if ($mf['method'] !== '') {
            $lines[] = 'Metode pembayaran: ' . ($methods[$mf['method']] ?? $mf['method']);
        }

        $rows = collect();
        $summary = [];
        $total = 0;

        if ($error === null) {
            $base  = $this->monitor->base($mf);
            $total = (clone $base)->count();
            $ids   = (clone $base)->select('h.id');

            $q = $this->monitor->reportRows($mf);
            if ($limit) {
                $q->limit($limit);
            }

            $rows = $q->get()->map(fn ($r) => [
                'kode'     => TM::code($r->id, $r->kode_booking),
                'dibuat'   => CarbonImmutable::parse($r->created_at),
                'penyewa'  => $r->customer_name,
                'lapangan' => $r->item_count > 0 ? $r->nama_lapangan : '(tanpa rincian slot)',
                'venue'    => $r->nama_venue,
                'mitra'    => $r->nama_usaha,
                'slot'     => (int) $r->item_count,
                'total'    => (float) $r->total_tagihan,
                'status'   => TM::BOOKING_STATUS[$r->status] ?? $r->status,
                'dibayar'  => (float) $r->paid_total,
                'metode'   => $r->method ? ($methods[$r->method] ?? $r->method) : null,
                'refund'   => (float) $r->refund_done,
            ]);

            $summary = [
                ['Jumlah booking', $total, 'int'],
                ['Total tagihan (Rp)', (float) (clone $base)->sum('h.total_tagihan'), 'money'],
                ['Nilai terbayar (Rp)', (float) DB::table('payments')->whereIn('header_id', $ids)->where('status_pembayaran', 'succes')->sum('nominal_bayar'), 'money'],
                ['Refund selesai (Rp)', (float) DB::table('refunds')->whereIn('header_id', $ids)->where('status_refund', 'completed')->sum('nominal_refund'), 'money'],
            ];
            if ($mf['status'] === '') {
                foreach ($this->monitor->statusCounts($mf) as $status => $n) {
                    $summary[] = ['Booking ' . mb_strtolower(TM::BOOKING_STATUS[$status] ?? $status), (int) $n, 'int'];
                }
            }
        }

        $columns = [
            $this->col('kode', 'Kode booking', 'text', true, true, 16),
            $this->col('dibuat', 'Dibuat', 'datetime', true, true, 18),
            $this->col('penyewa', 'Penyewa', 'text', true, true, 24),
            $this->col('lapangan', 'Lapangan', 'text', true, false, 24),
            $this->col('venue', 'Venue', 'text', false, false, 24),
            $this->col('mitra', 'Mitra', 'text', false, false, 24),
            $this->col('slot', 'Slot', 'int', true, false, 8),
            $this->col('total', 'Total tagihan (Rp)', 'money', true, true, 18),
            $this->col('status', 'Status', 'text', true, true, 16),
            $this->col('dibayar', 'Dibayar (Rp)', 'money', true, false, 16),
            $this->col('metode', 'Metode', 'text', false, false, 16),
            $this->col('refund', 'Refund selesai (Rp)', 'money', true, false, 18),
        ];

        return new ReportData('transaksi', self::TYPES['transaksi']['label'], $this->periodLabel($f), $lines, $columns, $rows, $total, $summary, $error);
    }

    // ---- mitra

    private function mitraQuery(array $f): Builder
    {
        [$from, $to] = $this->monitor->range(['period' => $f['period'], 'from' => $f['from'], 'to' => $f['to']]);
        $since = 'COALESCE(m.submitted_at, m.updated_at, m.created_at)';

        return DB::table('mitra_profiles as m')
            ->join('users as u', 'u.id', '=', 'm.user_id')
            ->leftJoin('users as r', 'r.id', '=', 'm.reviewed_by')
            ->when($f['status'] !== '', fn ($q) => $q->where('m.status', $f['status']))
            ->when($from, fn ($q) => $q->whereRaw("{$since} >= ?", [$from]))
            ->when($to, fn ($q) => $q->whereRaw("{$since} <= ?", [$to]));
    }

    private function mitra(array $f, ?int $limit): ReportData
    {
        $error = $this->monitor->range(['period' => $f['period'], 'from' => $f['from'], 'to' => $f['to']])[2];
        $lines = ['Periode diajukan: ' . $this->periodLabel($f)];
        if ($f['status'] !== '') {
            $lines[] = 'Status: ' . self::MITRA_STATUS[$f['status']];
        }

        $rows = collect();
        $summary = [];
        $total = 0;

        if ($error === null) {
            $base  = $this->mitraQuery($f);
            $total = (clone $base)->count();
            $ids   = (clone $base)->select('m.id');

            $q = (clone $base)->selectRaw("m.nama_usaha, m.bentuk_badan_usaha, m.kota_operasional, u.name as owner_name, m.status,
                    COALESCE(m.submitted_at, m.updated_at, m.created_at) as submitted, m.reviewed_at, r.name as reviewer_name,
                    (SELECT COUNT(*) FROM venues v WHERE v.mitra_id = m.id) as venues,
                    (SELECT COUNT(*) FROM venues v WHERE v.mitra_id = m.id AND v.status_vanue = 'aktif') as venues_aktif,
                    (SELECT COUNT(*) FROM fields fl JOIN venues v ON v.id = fl.venue_id WHERE v.mitra_id = m.id) as fields_total,
                    (SELECT COUNT(*) FROM fields fl JOIN venues v ON v.id = fl.venue_id WHERE v.mitra_id = m.id AND fl.status_lapangan = 'aktif') as fields_aktif")
                ->orderBy('submitted')->orderBy('m.id');
            if ($limit) {
                $q->limit($limit);
            }

            $rows = $q->get()->map(fn ($r) => [
                'usaha'    => $r->nama_usaha ?: '(tanpa nama usaha)',
                'pemilik'  => $r->owner_name,
                'kota'     => $r->kota_operasional,
                'bentuk'   => $r->bentuk_badan_usaha,
                'status'   => self::MITRA_STATUS[$r->status] ?? $r->status,
                'diajukan' => $r->submitted ? CarbonImmutable::parse($r->submitted) : null,
                'putusan'  => $r->reviewed_at ? CarbonImmutable::parse($r->reviewed_at) : null,
                'oleh'     => $r->reviewer_name,
                'venue'    => (int) $r->venues,
                'venue_aktif' => (int) $r->venues_aktif,
                'lapangan' => (int) $r->fields_total,
                'lapangan_aktif' => (int) $r->fields_aktif,
            ]);

            $summary = [['Jumlah mitra', $total, 'int']];
            if ($f['status'] === '') {
                foreach ((clone $base)->selectRaw('m.status as k, COUNT(*) as n')->groupBy('m.status')->pluck('n', 'k') as $status => $n) {
                    $summary[] = ['Mitra ' . mb_strtolower(self::MITRA_STATUS[$status] ?? $status), (int) $n, 'int'];
                }
            }
            $summary[] = ['Venue aktif', (int) DB::table('venues')->whereIn('mitra_id', $ids)->where('status_vanue', 'aktif')->count(), 'int'];
            $summary[] = ['Lapangan aktif', (int) DB::table('fields as fl')->join('venues as v', 'v.id', '=', 'fl.venue_id')
                ->whereIn('v.mitra_id', $ids)->where('fl.status_lapangan', 'aktif')->count(), 'int'];
        }

        $columns = [
            $this->col('usaha', 'Nama usaha', 'text', true, true, 28),
            $this->col('pemilik', 'Pemilik', 'text', true, true, 24),
            $this->col('kota', 'Kota', 'text', true, true, 18),
            $this->col('bentuk', 'Bentuk usaha', 'text', false, false, 16),
            $this->col('status', 'Status', 'text', true, true, 14),
            $this->col('diajukan', 'Diajukan', 'date', true, true, 14),
            $this->col('putusan', 'Diputuskan', 'date', false, false, 14),
            $this->col('oleh', 'Diputuskan oleh', 'text', false, false, 22),
            $this->col('venue', 'Venue', 'int', false, false, 9),
            $this->col('venue_aktif', 'Venue aktif', 'int', true, false, 12),
            $this->col('lapangan', 'Lapangan', 'int', false, false, 11),
            $this->col('lapangan_aktif', 'Lapangan aktif', 'int', true, false, 14),
        ];

        return new ReportData('mitra', self::TYPES['mitra']['label'], $this->periodLabel($f), $lines, $columns, $rows, $total, $summary, $error);
    }

    // ---- pengguna

    private function userQuery(array $f): Builder
    {
        [$from, $to] = $this->monitor->range(['period' => $f['period'], 'from' => $f['from'], 'to' => $f['to']]);

        return DB::table('users as u')
            ->when($f['role'] !== '', fn ($q) => $q->where('u.role', $f['role']))
            ->when($f['account'] !== '', fn ($q) => $q->where('u.status_akun', $f['account']))
            ->when($from, fn ($q) => $q->where('u.created_at', '>=', $from))
            ->when($to, fn ($q) => $q->where('u.created_at', '<=', $to));
    }

    private function pengguna(array $f, ?int $limit): ReportData
    {
        $error = $this->monitor->range(['period' => $f['period'], 'from' => $f['from'], 'to' => $f['to']])[2];
        $lines = ['Periode daftar: ' . $this->periodLabel($f)];
        if ($f['role'] !== '') {
            $lines[] = 'Peran: ' . self::ROLES[$f['role']];
        }
        if ($f['account'] !== '') {
            $lines[] = 'Status akun: ' . self::ACCOUNT_STATUS[$f['account']];
        }

        $rows = collect();
        $summary = [];
        $total = 0;

        if ($error === null) {
            $base  = $this->userQuery($f);
            $total = (clone $base)->count();

            // Hanya nama dan email. Nomor HP dan data lain sengaja tidak ikut dalam laporan.-
            $q = (clone $base)->select('u.name', 'u.email', 'u.role', 'u.status_akun', 'u.email_verified_at', 'u.created_at')
                ->orderBy('u.created_at')->orderBy('u.id');
            if ($limit) {
                $q->limit($limit);
            }

            $rows = $q->get()->map(fn ($r) => [
                'nama'   => $r->name,
                'email'  => $r->email,
                'peran'  => self::ROLES[$r->role] ?? $r->role,
                'status' => self::ACCOUNT_STATUS[$r->status_akun] ?? $r->status_akun,
                'verif'  => $r->email_verified_at !== null,
                'daftar' => $r->created_at ? CarbonImmutable::parse($r->created_at) : null,
            ]);

            $summary = [['Jumlah pengguna', $total, 'int']];
            if ($f['role'] === '') {
                foreach ((clone $base)->selectRaw('u.role as k, COUNT(*) as n')->groupBy('u.role')->pluck('n', 'k') as $k => $n) {
                    $summary[] = [self::ROLES[$k] ?? $k, (int) $n, 'int'];
                }
            }
            if ($f['account'] === '') {
                foreach ((clone $base)->selectRaw('u.status_akun as k, COUNT(*) as n')->groupBy('u.status_akun')->pluck('n', 'k') as $k => $n) {
                    $summary[] = ['Akun ' . mb_strtolower(self::ACCOUNT_STATUS[$k] ?? $k), (int) $n, 'int'];
                }
            }
        }

        $columns = [
            $this->col('nama', 'Nama', 'text', true, true, 26),
            $this->col('email', 'Email', 'text', true, true, 32),
            $this->col('peran', 'Peran', 'text', true, true, 12),
            $this->col('status', 'Status akun', 'text', true, true, 16),
            $this->col('verif', 'Email terverifikasi', 'bool', true, false, 18),
            $this->col('daftar', 'Terdaftar', 'datetime', true, true, 18),
        ];

        return new ReportData('pengguna', self::TYPES['pengguna']['label'], $this->periodLabel($f), $lines, $columns, $rows, $total, $summary, $error);
    }

    private function col(string $key, string $label, string $kind, bool $pdf, bool $preview, int $width): array
    {
        return compact('key', 'label', 'kind', 'pdf', 'preview', 'width');
    }
}