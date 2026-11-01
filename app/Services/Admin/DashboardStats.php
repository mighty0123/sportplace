<?php

namespace App\Services\Admin;

use Carbon\CarbonImmutable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

/**
 * Semua angka dashboard admin. Transaksi = pembayaran berhasil; nilai = GMV (bukan komisi).
 */
class DashboardStats
{
    private const PAID = 'succes'; // ejaan mengikuti enum payments.status_pembayaran

    private const ACTIONS = [
        'setujui'     => ['disetujui', 'success', 'check-circle'],
        'tolak'       => ['ditolak', 'danger', 'x-circle'],
        'ajukan'      => ['diajukan', 'info', 'send'],
        'suspend'     => ['disuspend', 'warning', 'ban'],
        'kedaluwarsa' => ['kedaluwarsa', 'neutral', 'clock'],
        'pendaftaran'  => ['didaftarkan', 'info', 'send'],
        'ajukan_ulang' => ['diajukan ulang', 'info', 'send'],
    ];

    private const ENTITY_LABELS = [
        'MitraProfile'  => 'Mitra',
        'Venue'         => 'Venue',
        'Field'         => 'Lapangan',
        'User'          => 'Akun',
        'BookingHeader' => 'Booking',
    ];

    public function kpis(): array
    {
        $now = CarbonImmutable::now();
        $monthStart = $now->startOfMonth();

        $mitra = DB::table('mitra_profiles')->where('status', 'approved');
        $lapangan = DB::table('fields')->where('status_lapangan', 'aktif');

        $newMitra = (clone $mitra)->where('created_at', '>=', $monthStart)->count();
        $newLapangan = (clone $lapangan)->where('created_at', '>=', $monthStart)->count();

        $curr = $this->paidCount($now->subDays(30), $now);
        $prev = $this->paidCount($now->subDays(60), $now->subDays(30));

        if ($prev > 0) {
            $pct = ($curr - $prev) / $prev * 100;
            $trx = [
                (($pct >= 0) ? '+' : '-') . number_format(abs($pct), 1, ',', '.') . '% dibanding 30 hari sebelumnya',
                $pct > 0 ? 'up' : ($pct < 0 ? 'down' : 'flat'),
            ];
        } else {
            $trx = [
                $curr > 0 ? 'Belum ada pembanding periode sebelumnya' : 'Belum ada transaksi dalam 30 hari terakhir',
                'flat',
            ];
        }

        return [
            'mitra' => [
                'value' => $this->n($mitra->count()),
                'delta' => $newMitra ? "+{$newMitra} mitra baru bulan ini" : 'Belum ada mitra baru bulan ini',
                'trend' => $newMitra ? 'up' : 'flat',
            ],
            'lapangan' => [
                'value' => $this->n($lapangan->count()),
                'delta' => $newLapangan ? "+{$newLapangan} lapangan baru bulan ini" : 'Belum ada lapangan baru bulan ini',
                'trend' => $newLapangan ? 'up' : 'flat',
            ],
            'transaksi' => [
                'value' => $this->n($curr),
                'delta' => $trx[0],
                'trend' => $trx[1],
            ],
        ];
    }

    public function queues(): array
    {
        $mitra = DB::table('mitra_profiles')->where('status', 'pending');
        $venues = DB::table('venues')->where('status_vanue', 'pending_review');
        $fields = DB::table('fields')->where('status_lapangan', 'pending_review');

        $oldest = fn ($q) => (clone $q)->selectRaw('MIN(COALESCE(submitted_at, created_at)) as t')->value('t');

        $mitraOldest = (clone $mitra)->min('submitted_at');
        $listingOldest = collect([$oldest($venues), $oldest($fields)])->filter()->min();

        return [
            'mitra' => [
                'count' => $mitra->count(),
                'since' => $this->since($mitraOldest),
            ],
            'moderasi' => [
                'count' => $venues->count() + $fields->count(),
                'since' => $this->since($listingOldest),
            ],
        ];
    }

    public function chart(): array
    {
        $now = CarbonImmutable::now();

        return [
            'asOf' => $now->format('H.i'),
            'ranges' => [
                '7d' => $this->daily($now, 7, 'ddd D'),
                '30d' => $this->daily($now, 30, 'D MMM'),
                '6m' => $this->monthly($now, 6),
            ],
        ];
    }
    
    public function activity(int $limit = 4): array
    {
        $rows = DB::table('audit_logs')
            ->leftJoin('users', 'users.id', '=', 'audit_logs.user_id')
            ->whereNotIn('audit_logs.aksi', ['lihat_dokumen', 'lihat_nik'])
            ->orderByDesc('audit_logs.created_at')
            ->limit($limit)
            ->get(['users.name as actor', 'audit_logs.aksi', 'audit_logs.entitas_type',
                'audit_logs.entitas_id', 'audit_logs.catatan', 'audit_logs.created_at']);

        $names = $this->entityNames($rows);

        return $rows->map(function ($r) use ($names) {
            $type = class_basename($r->entitas_type);
            [$verb, $tone, $icon] = self::ACTIONS[$r->aksi] ?? [Str::lower(Str::headline($r->aksi)), 'neutral', 'activity'];
            $label = self::ENTITY_LABELS[$type] ?? Str::headline($type);
            $name = $names[$type][$r->entitas_id] ?? '#' . $r->entitas_id; // nama kalau ketemu, kalau tidak nomor ID

            return [
                'subject' => Str::startsWith(Str::lower($name), Str::lower($label)) ? $name : "{$label} {$name}",
                'verb' => $verb,
                'tone' => $tone,
                'icon' => $icon,
                'actor' => $r->actor ?? 'Sistem',
                'note' => $r->catatan ? Str::limit($r->catatan, 70) : null,
                'time' => CarbonImmutable::parse($r->created_at)->locale('id')->diffForHumans(),
            ];
        })->all();
    }

    // Helper
    private function daily(CarbonImmutable $now, int $days, string $format): array
    {
        $start = $now->subDays($days - 1)->startOfDay();

        $rows = DB::table('payments')
            ->selectRaw('DATE(paid_at) as d, SUM(nominal_bayar) as total, COUNT(*) as n')
            ->where('status_pembayaran', self::PAID)
            ->whereBetween('paid_at', [$start, $now])
            ->groupBy('d')->get()->keyBy('d');

        $labels = $nilai = $jumlah = [];
        for ($i = 0; $i < $days; $i++) {
            $day = $start->addDays($i);
            $row = $rows->get($day->toDateString());
            $labels[] = $day->locale('id')->isoFormat($format);
            $nilai[] = (float) ($row->total ?? 0);
            $jumlah[] = (int) ($row->n ?? 0);
        }

        return compact('labels', 'nilai', 'jumlah');
    }

    private function monthly(CarbonImmutable $now, int $months): array
    {
        $start = $now->startOfMonth()->subMonths($months - 1);

        $rows = DB::table('payments')
            ->selectRaw("DATE_FORMAT(paid_at, '%Y-%m') as m, SUM(nominal_bayar) as total, COUNT(*) as n")
            ->where('status_pembayaran', self::PAID)
            ->whereBetween('paid_at', [$start, $now])
            ->groupBy('m')->get()->keyBy('m');

        $labels = $nilai = $jumlah = [];
        for ($i = 0; $i < $months; $i++) {
            $month = $start->addMonths($i);
            $row = $rows->get($month->format('Y-m'));
            $labels[] = $month->locale('id')->isoFormat('MMM');
            $nilai[] = (float) ($row->total ?? 0);
            $jumlah[] = (int) ($row->n ?? 0);
        }

        return compact('labels', 'nilai', 'jumlah');
    }

    private function paidCount(CarbonImmutable $from, CarbonImmutable $to): int
    {
        return DB::table('payments')
            ->where('status_pembayaran', self::PAID)
            ->whereBetween('paid_at', [$from, $to])
            ->count();
    }

    private function since(?string $timestamp): ?string
    {
        if (! $timestamp) {
            return null;
        }
        $date = CarbonImmutable::parse($timestamp);
        $days = (int) floor($date->diffInDays(CarbonImmutable::now()));
        $when = $days === 0 ? 'hari ini' : "{$days} hari";

        return 'Terlama menunggu sejak ' . $date->locale('id')->isoFormat('D MMM') . " ({$when})";
    }

    private function n(int|float $n): string
    {
        return number_format($n, 0, ',', '.');
    }
    
    private function entityNames($rows): array
    {
        $sources = [
            'MitraProfile' => ['mitra_profiles', 'nama_usaha'],
            'Venue'        => ['venues', 'nama_venue'],
            'Field'        => ['fields', 'nama_lapangan'],
            'User'         => ['users', 'name'],
        ];

        $names = [];
        foreach ($sources as $type => [$table, $column]) {
            $ids = $rows->filter(fn ($r) => class_basename($r->entitas_type) === $type)
                ->pluck('entitas_id')->unique();

            if ($ids->isNotEmpty()) {
                $names[$type] = DB::table($table)->whereIn('id', $ids)->pluck($column, 'id');
            }
        }

        return $names;
    }
}