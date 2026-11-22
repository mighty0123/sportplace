<?php

namespace Database\Seeders;

use DateTimeImmutable;
use DateTimeZone;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/** Riwayat tarif komisi global dan tarif khusus mitra. */
class DemoCommissionSeeder extends DemoBase
{
    public function run(): void
    {
        $this->ensureStarted();
        $this->seedCommissionRates();
    }

    private function seedCommissionRates(): void
    {
        // [mitra key|null, persen|null, hari relatif, catatan]
        $plan = [
            [null, '20.00', null, 'Tarif awal sesuai keputusan tim.'],
            ['champion', '12.00', -100, 'Kemitraan strategis: venue pionir di platform.'],
            ['viva', '10.00', -90, 'Tarif khusus masa percobaan kerja sama enam bulan.'],
            [null, '15.00', -80, 'Promo tarif komisi untuk menarik mitra baru kuartal ini.'],
            ['smash', '15.00', -60, 'Tarif khusus untuk gedung olahraga multi-cabang.'],
            [null, '18.00', -30, 'Penyesuaian setelah masa promo berakhir.'],
            ['viva', null, -20, 'Masa tarif khusus berakhir, kembali mengikuti tarif global.'],
            [null, '20.00', 14, 'Tarif normal berlaku setelah evaluasi bulanan tim.'],
        ];
        $rows = [];
        foreach ($plan as $i => [$mk, $persen, $days, $note]) {
            $id = $i + 1;
            $from = $days === null ? new DateTimeImmutable('2020-01-01 00:00:00', $this->now->getTimezone()) : $this->at($days)->setTime(0, 0, 0);
            $mitraId = $mk ? $this->mitra[$mk]['id'] : null;
            $row = [
                'id' => $id, 'scope_key' => $mk ? 'm' . $mitraId : 'g', 'mitra_id' => $mitraId, 'sport_id' => null,
                'persen' => $persen, 'berlaku_mulai' => $this->dt($from),
                'dibuat_oleh' => $days === null ? null : $this->adminId, 'catatan' => $note,
                'created_at' => $this->dt($days === null ? $this->at(-260) : ($days > 0 ? $this->now->modify('-2 days') : $from->modify('-1 day')->setTime(15, 30, 0))),
            ];
            $rows[] = $row;
            $this->rates[] = ['id' => $id, 'scope' => $row['scope_key'], 'persen' => $persen, 'from' => $from, 'note' => $note, 'created' => $row['created_at'], 'mitra' => $mk];
        }
        $this->bulk('commission_rates', $rows);
    }
}
