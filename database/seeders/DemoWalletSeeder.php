<?php

namespace Database\Seeders;

use DateTimeImmutable;
use DateTimeZone;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/** Dompet mitra, buku besar, dan penarikan dana. */
class DemoWalletSeeder extends DemoBase
{
    public function run(): void
    {
        $this->ensureStarted();
        $this->seedMoney();
    }

    private function seedMoney(): void
    {
        $byMitra = [];
        foreach ($this->earnings as $e) {
            $byMitra[$e['mitra']][] = $e;
        }

        $ledger = [];
        $withdrawals = [];
        $walletRows = [];
        $wdSeq = 0;

        foreach ($this->mitra as $key => $m) {
            $rows = $byMitra[$key] ?? [];
            $released = 0;
            $held = 0;
            $releaseEvents = [];
            foreach ($rows as $e) {
                // Pendapatan dicatat saat pembayaran: kredit pendapatan, debit komisi platform.
                $ledger[] = $this->txn($key, 'kredit', 'booking_revenue', $e['harga'], 'booking_detail', $e['detail'], 'Pendapatan booking ' . $e['code'] . ' (' . $e['field'] . ', ' . $e['day']->format('d/m/Y') . ' ' . $this->hm($e['a']) . '-' . $this->hm($e['b']) . ')', $e['paid']);
                $fee = $e['refund'] === 'completed' ? (int) $this->feeFor($e) : (int) $e['komisi'];
                $ledger[] = $this->txn($key, 'debit', 'platform_fee_deduction', $fee, 'booking_detail', $e['detail'], 'Komisi platform ' . rtrim(rtrim($e['persen'], '0'), '.') . '% booking ' . $e['code'], $e['paid']);

                if ($e['refund'] === 'completed') {
                    $at = $e['refund_at']->modify('+' . mt_rand(30, 70) . ' hours');
                    $at = $at > $this->now ? $this->now->modify('-2 hours') : $at;
                    $ledger[] = $this->txn($key, 'debit', 'refund_deduction', $e['harga'], 'refund', (int) $e['refund_id'], 'Refund booking ' . $e['code'], $at);
                    $ledger[] = $this->txn($key, 'kredit', 'platform_fee_deduction', $fee, 'refund', (int) $e['refund_id'], 'Pembatalan komisi booking ' . $e['code'], $at);
                    continue;
                }
                if ($e['release'] <= $this->now && $e['refund'] === null && $e['item'] !== 'ongoing') {
                    $released += $e['mitra_amt'];
                    $releaseEvents[] = [$e['release'], $e['mitra_amt']];
                } else {
                    $held += $e['mitra_amt'];
                }
            }

            // Penarikan: dihitung dari dana yang sudah tersedia pada saat itu.
            usort($releaseEvents, fn ($a, $b) => $a[0] <=> $b[0]);
            $withdrawn = 0;
            if ($released > 300000 && $m['status'] === 'approved') {
                $count = $released > 6000000 ? 5 : ($released > 2500000 ? 3 : 2);
                $cum = 0;
                $targets = [];
                for ($i = 0; $i < $count; $i++) {
                    $room = $released - $withdrawn - array_sum($targets);
                    if ($room < 150000) {
                        break;
                    }
                    $amount = max(100000, (int) (round($room * (mt_rand(22, 42) / 100) / 50000) * 50000));
                    if ($amount > $room - 50000) {
                        break;
                    }
                    $targets[] = $amount;
                }
                $cum = 0;
                $need = 0;
                foreach ($targets as $i => $amount) {
                    $need += $amount;
                    $when = null;
                    $acc = 0;
                    foreach ($releaseEvents as [$t, $v]) {
                        $acc += $v;
                        if ($acc >= $need) {
                            $when = $t;
                            break;
                        }
                    }
                    if ($when === null) {
                        break;
                    }
                    $when = $when->modify('+' . mt_rand(2, 6 * 24) . ' hours');
                    if ($when > $this->now->modify('-1 hour')) {
                        $when = $this->now->modify('-' . mt_rand(2, 40) . ' hours');
                    }
                    $last = $i === count($targets) - 1;
                    $status = $last ? $this->pick(['pending', 'processing']) : 'completed';
                    if ($i === 1 && count($targets) > 3) {
                        $status = 'failed';
                    }
                    $wdSeq++;
                    $fee = $m['bank'] === 'BCA' ? 2500 : 6500;
                    $done = $status === 'completed' ? $when->modify('+' . mt_rand(2, 30) . ' hours') : null;
                    if ($done && $done > $this->now) {
                        $done = $this->now->modify('-1 hour');
                    }
                    $withdrawals[] = [
                        'id' => $wdSeq, 'user_id' => $m['user_id'], 'bank_kode' => strtolower($m['bank']), 'nomor_rekening' => $m['rek'],
                        'nama_pemilik_rek' => $m['an'], 'nominal_tarik' => $amount, 'biaya_admin_bank' => $fee, 'status' => $status,
                        'pg_disbursement_id' => 'DSB-' . $when->format('ymd') . '-' . $this->code(8),
                        'alasan_gagal' => $status === 'failed' ? 'Nomor rekening tujuan tidak valid. Dana dikembalikan ke saldo.' : null,
                        'created_at' => $this->dt($when), 'complated_at' => $done ? $this->dt($done) : ($status === 'failed' ? $this->dt($when->modify('+3 hours')) : null),
                    ];
                    if ($status !== 'failed') {
                        $withdrawn += $amount;
                        $ledger[] = $this->txn($key, 'debit', 'withdrawal', $amount, 'withdrawal', $wdSeq, 'Penarikan dana ke ' . $m['bank'] . ' ' . $m['rek'], $when);
                    }
                }
            }

            $walletRows[$m['user_id']] = [
                'saldo_aktif' => max(0, $released - $withdrawn), 'saldo_tertahan' => $held,
            ];
        }

        // Dompet semua akun. Hanya mitra yang punya saldo.
        foreach (DB::table('users')->pluck('id') as $uid) {
            $w = $walletRows[$uid] ?? ['saldo_aktif' => 0, 'saldo_tertahan' => 0];
            DB::table('wallets')->updateOrInsert(['user_id' => $uid], $w + ['updated_at' => $this->dt($this->now)]);
        }
        $walletIds = DB::table('wallets')->pluck('id', 'user_id');
        usort($ledger, fn ($a, $b) => strcmp($a['created_at'], $b['created_at']));
        foreach ($ledger as $i => $row) {
            $row['wallet_id'] = $walletIds[$this->mitra[$row['_mitra']]['user_id']];
            unset($row['_mitra']);
            $ledger[$i] = ['wallet_id' => $row['wallet_id']] + $row;
        }
        $this->bulk('wallet_transactions', $ledger, 200);
        $this->bulk('withdrawals', $withdrawals);
        $this->report['withdrawals'] = count($withdrawals);
    }

    private function feeFor(array $e): int
    {
        $p = (int) round(((float) $e['persen']) * 100);

        return intdiv($e['harga'] * $p + 5000, 10000);
    }

    private function txn(string $mitra, string $jenis, string $kategori, int|float $nominal, string $refType, int $refId, string $desc, DateTimeImmutable $at): array
    {
        return [
            '_mitra' => $mitra, 'jenis_mutasi' => $jenis, 'kategori_transaksi' => $kategori, 'nominal' => $nominal,
            'referensi_tipe' => $refType, 'referensi_id' => $refId, 'deskripsi' => mb_substr($desc, 0, 255), 'created_at' => $this->dt($at),
        ];
    }
}
