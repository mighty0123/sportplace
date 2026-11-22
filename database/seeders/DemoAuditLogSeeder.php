<?php

namespace Database\Seeders;

use DateTimeImmutable;
use DateTimeZone;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/** Log audit untuk umpan aktivitas dashboard admin, lalu ringkasan. */
class DemoAuditLogSeeder extends DemoBase
{
    public function run(): void
    {
        $this->ensureStarted();
        $this->seedAuditLogs();
        $this->printSummary();
    }

    private function seedAuditLogs(): void
    {
        $a = [];
        $add = function (DateTimeImmutable $at, ?int $uid, string $role, string $type, int $id, string $aksi, ?string $old, ?string $new, ?string $note) use (&$a) {
            $a[] = [
                'user_id' => $uid, 'aktor_role' => $role, 'entitas_type' => $type, 'entitas_id' => $id, 'aksi' => $aksi,
                'status_lama' => $old, 'status_baru' => $new, 'catatan' => $note,
                'ip_address' => $role === 'sistem' ? '127.0.0.1' : mt_rand(110, 125) . '.' . mt_rand(0, 255) . '.' . mt_rand(0, 255) . '.' . mt_rand(1, 254),
                'created_at' => $this->dt($at),
            ];
        };

        foreach ($this->mitra as $m) {
            $add($m['submitted']->modify('-1 hour'), $m['user_id'], 'mitra', 'MitraProfile', $m['id'], 'pendaftaran', null, 'pending', 'Pendaftaran mitra ' . $m['usaha'] . ' diterima.');
            if ($m['reviewed']) {
                $add($m['reviewed']->modify('-20 minutes'), $this->adminId, 'admin', 'MitraProfile', $m['id'], 'lihat_nik', null, null, 'Menampilkan nik lengkap.');
                $add($m['reviewed']->modify('-12 minutes'), $this->adminId, 'admin', 'MitraProfile', $m['id'], 'lihat_dokumen', null, null, 'Membuka dokumen KTP.');
                $approved = $m['status'] === 'approved';
                $add($m['reviewed'], $this->adminId, 'admin', 'MitraProfile', $m['id'], $approved ? 'setujui' : 'tolak', 'pending', $m['status'], $approved ? 'Dokumen lengkap dan sesuai data pendaftaran.' : $m['alasan']);
            }
        }

        $venues = DB::table('venues')->get();
        foreach ($venues as $v) {
            $mitraUser = DB::table('mitra_profiles')->where('id', $v->mitra_id)->value('user_id');
            if ($v->submitted_at) {
                $add(new DateTimeImmutable($v->submitted_at, $this->now->getTimezone()), $mitraUser, 'mitra', 'Venue', $v->id, 'ajukan', 'draft', 'pending_review', 'Venue ' . $v->nama_venue . ' diajukan untuk ditinjau.');
            }
            if ($v->reviewed_at) {
                $ok = $v->status_vanue !== 'rejected';
                $add(new DateTimeImmutable($v->reviewed_at, $this->now->getTimezone()), $this->adminId, 'admin', 'Venue', $v->id, $ok ? 'setujui' : 'tolak', 'pending_review', $ok ? 'aktif' : 'rejected', $ok ? 'Data dan foto venue sesuai.' : $v->alasan_penolakan);
            }
        }
        foreach (DB::table('fields')->whereNotNull('reviewed_at')->get() as $f) {
            $ok = $f->status_lapangan !== 'rejected';
            $add(new DateTimeImmutable($f->reviewed_at, $this->now->getTimezone()), $this->adminId, 'admin', 'Field', $f->id, $ok ? 'setujui' : 'tolak', 'pending_review', $ok ? 'aktif' : 'rejected', $ok ? 'Spesifikasi dan foto lapangan sesuai.' : $f->alasan_penolakan);
        }
        foreach (DB::table('fields')->where('status_lapangan', 'pending_review')->get() as $f) {
            $add(new DateTimeImmutable($f->submitted_at, $this->now->getTimezone()), DB::table('mitra_profiles')->where('id', DB::table('venues')->where('id', $f->venue_id)->value('mitra_id'))->value('user_id'), 'mitra', 'Field', $f->id, 'ajukan', 'draft', 'pending_review', $f->nama_lapangan . ' diajukan untuk ditinjau.');
        }

        // Master data
        $add($this->at(-255), $this->adminId, 'admin', 'MasterCity', $this->cityIds['Gianyar'], 'tambah', null, 'aktif', 'Kota Gianyar ditambahkan.');
        $add($this->at(-250), $this->adminId, 'admin', 'MasterCity', $this->cityIds['Tabanan'], 'tambah', null, 'aktif', 'Kota Tabanan ditambahkan.');
        $add($this->at(-95), $this->adminId, 'admin', 'MasterCity', $this->cityIds['Karangasem'], 'nonaktifkan', 'aktif', 'nonaktif', 'Belum ada mitra di Karangasem.');
        $add($this->at(-60), $this->adminId, 'admin', 'MasterFacility', $this->facilityIds['Wi-Fi Gratis'], 'ubah', 'aktif', 'aktif', 'Ikon fasilitas Wi-Fi diperbarui.');
        $add($this->at(-33), $this->adminId, 'admin', 'MasterSport', $this->sportIds['tenis-meja'], 'ubah', 'aktif', 'aktif', 'Nama cabang olahraga dirapikan menjadi Tenis Meja.');

        // Akun
        foreach ($this->customers as $c) {
            if (isset($c['blocked'])) {
                $suspend = $c['blocked'] === 'suspend';
                $add($this->now->modify('-' . mt_rand(6, 20) . ' days'), $this->adminId, 'admin', 'User', $c['id'], $suspend ? 'suspend' : 'banned', 'aktif', $c['blocked'],
                    $suspend ? 'Pemesanan ganda berulang tanpa pembayaran.' : 'Pelanggaran ketentuan layanan: penyalahgunaan akun.');
            }
        }

        // Tarif komisi
        $prev = '20%';
        foreach ($this->rates as $r) {
            if ($r['id'] === 1) {
                continue;
            }
            $new = $r['persen'] === null ? 'ikuti global' : rtrim(rtrim($r['persen'], '0'), '.') . '%';
            $add(new DateTimeImmutable($r['created'], $this->now->getTimezone()), $this->adminId, 'admin', 'CommissionRate', $r['id'], 'tambah_tarif', $prev, $new, $r['note']);
            if ($r['mitra'] === null) {
                $prev = $new;
            }
        }

        // Booking: kedaluwarsa otomatis (10 hari terakhir), pending refund, dan beberapa kejadian khusus.
        $headers = [];
        foreach ($this->headers as $h) {
            $headers[$h['id']] = $h;
        }
        foreach ($this->headers as $h) {
            if ($h['status'] === 'expired' && new DateTimeImmutable($h['expires_at'], $this->now->getTimezone()) > $this->now->modify('-10 days')) {
                $add((new DateTimeImmutable($h['expires_at'], $this->now->getTimezone()))->modify('+1 minute'), null, 'sistem', 'BookingHeader', $h['id'], 'kedaluwarsa', 'pending', 'expired', 'Batas waktu pembayaran habis, slot dilepas.');
            }
        }
        foreach ($this->refunds as $r) {
            if ($r['status_refund'] === 'pending') {
                $add((new DateTimeImmutable($r['created_at'], $this->now->getTimezone()))->modify('+2 minutes'), null, 'sistem', 'BookingHeader', $r['header_id'], 'refund_perlu_ditinjau', 'payment', 'canceled', 'Permintaan refund menunggu tinjauan admin.');
            }
        }
        $paid = array_values(array_filter($this->headers, fn ($h) => $h['status'] === 'payment'));
        if (count($paid) > 20) {
            $x = $paid[mt_rand(0, count($paid) - 1)];
            $add($this->now->modify('-' . mt_rand(30, 90) . ' hours'), null, 'sistem', 'BookingHeader', $x['id'], 'dibayar_terlambat', 'expired', 'payment', 'Pembayaran diterima setelah batas waktu; slot masih tersedia sehingga booking dilanjutkan.');
            $y = $paid[mt_rand(0, count($paid) - 1)];
            $add($this->now->modify('-' . mt_rand(100, 160) . ' hours'), null, 'sistem', 'BookingHeader', $y['id'], 'slot_bentrok', null, null, 'Dua pembayaran masuk untuk slot yang sama; satu booking dikembalikan otomatis.');
        }

        usort($a, fn ($p, $q) => strcmp($p['created_at'], $q['created_at']));
        $this->bulk('audit_logs', $a, 200);
        $this->report['audit'] = count($a);
    }

    private function printSummary(): void
    {
        if (! $this->command) {
            return;
        }
        $paidTotal = array_sum(array_column(array_filter($this->payments, fn ($p) => $p['status_pembayaran'] === 'succes'), 'nominal_bayar'));
        $this->command->info('DemoSeeder selesai.');
        $this->command->line(sprintf(
            '  %d booking, %d pembayaran (GMV Rp %s), %d refund, %d penarikan, %d log audit.',
            $this->report['booking'] ?? 0, count($this->payments), number_format($paidTotal, 0, ',', '.'),
            count($this->refunds), $this->report['withdrawals'] ?? 0, $this->report['audit'] ?? 0,
        ));
        $rows = [['admin@sportplace.com', 'Admin'], ['mitra@sportplace.com', 'Mitra (data terbanyak)'], ['customer@sportplace.com', 'Penyewa'], ['dimas@sportplace.com', 'Penyewa'], ['mitra_pending@sportplace.com', 'Mitra menunggu verifikasi'], ['mitra_rejected@sportplace.com', 'Mitra ditolak']];
        $this->command->table(['Email', 'Peran'], $rows);
        $this->command->line('  Kata sandi semua akun: ' . self::PASSWORD);
        if ($this->missingImages) {
            $this->command->warn('Foto/dokumen belum lengkap (letakkan di public/images/demo/... lalu jalankan ulang seeder):');
            foreach (array_keys($this->missingImages) as $what) {
                $this->command->line('  - ' . $what);
            }
        }
    }
}
