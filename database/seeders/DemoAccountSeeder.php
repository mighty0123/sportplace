<?php

namespace Database\Seeders;

use DateTimeImmutable;
use DateTimeZone;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/** Akun admin, penyewa, dan akun bawaan tim. */
class DemoAccountSeeder extends DemoBase
{
    public function run(): void
    {
        $this->ensureStarted();
        $this->seedAccounts();
    }

    private function seedAccounts(): void
    {
        $admin = $this->upsertUser('admin@sportplace.com', 'Admin Sportplace', '08111111111', 'admin', -260);
        $this->adminId = $admin;
        // Akun bawaan yang sudah dikenal tim (kata sandi sama dengan UserSeeder).
        $this->upsertUser('mitra@sportplace.com', 'Mitra Approved', '08222222222', 'mitra', -215);
        $this->upsertUser('mitra_pending@sportplace.com', 'Mitra Pending', '08222222223', 'mitra', -3);
        $this->upsertUser('mitra_rejected@sportplace.com', 'Mitra Rejected', '08222222224', 'mitra', -12);

        $this->customers = [];
        $budi = $this->upsertUser('customer@sportplace.com', 'Customer Budi', '08333333333', 'customer', -200);
        $dimas = $this->upsertUser('dimas@sportplace.com', 'Dimas Pratama', '08333333334', 'customer', -185);
        $this->customers[] = ['id' => $budi, 'name' => 'Customer Budi', 'created' => $this->userCreated[$budi], 'weight' => 4.0, 'bank' => 'BCA'];
        $this->customers[] = ['id' => $dimas, 'name' => 'Dimas Pratama', 'created' => $this->userCreated[$dimas], 'weight' => 3.5, 'bank' => 'BRI'];

        $baliM = ['I Wayan', 'I Made', 'I Nyoman', 'I Ketut', 'I Putu', 'I Kadek', 'I Komang', 'I Gede', 'Gusti'];
        $baliF = ['Ni Luh', 'Ni Made', 'Ni Putu', 'Ni Kadek', 'Ni Komang', 'Ayu'];
        $baliLastM = ['Suardana', 'Wirawan', 'Artha', 'Mahendra', 'Pradnyana', 'Darmawan', 'Santika', 'Adnyana', 'Wiguna', 'Saputra', 'Permana', 'Yasa', 'Sudiarta', 'Pramana', 'Mertha', 'Putra', 'Utama', 'Jaya'];
        $baliLastF = ['Dewi', 'Sari', 'Suartini', 'Wahyuni', 'Puspita', 'Lestari', 'Ningsih', 'Suarni'];
        $umumM = ['Rizky', 'Andi', 'Fajar', 'Agung', 'Bayu', 'Eko', 'Hendra', 'Indra', 'Yoga', 'Wahyu', 'Galih', 'Reza', 'Tegar', 'Aldi', 'Arif'];
        $umumF = ['Rina', 'Siti', 'Nadia', 'Citra', 'Fitri', 'Maya', 'Putri', 'Dian', 'Vina'];
        $umumLastM = ['Pratama', 'Santoso', 'Wibowo', 'Hidayat', 'Kurniawan', 'Nugroho', 'Setiawan', 'Firmansyah', 'Ramadhan', 'Susanto', 'Maulana'];
        $umumLastF = ['Lestari', 'Rahmawati', 'Anggraini', 'Puspita', 'Ningsih', 'Wulandari', 'Kusumawati'];
        $domains = ['gmail.com', 'gmail.com', 'gmail.com', 'yahoo.co.id', 'outlook.com', 'icloud.com'];
        $banks = ['BCA', 'BRI', 'Mandiri', 'BNI', 'BSI'];

        $used = ['Customer Budi' => true, 'Dimas Pratama' => true];
        $rank = 2;
        while (count($this->customers) < 46) {
            $isBali = $this->chance(0.62);
            $female = $this->chance(0.45);
            if ($isBali) {
                $first = $female ? $this->pick($baliF) : $this->pick($baliM);
                $last = $female ? $this->pick($baliLastF) : $this->pick($baliLastM);
            } else {
                $first = $female ? $this->pick($umumF) : $this->pick($umumM);
                $last = $female ? $this->pick($umumLastF) : $this->pick($umumLastM);
            }
            $name = $first . ' ' . $last;
            if (isset($used[$name])) {
                continue;
            }
            $used[$name] = true;
            $slug = strtolower(preg_replace('/^(I|Ni) /', '', $first));
            $email = $slug . '.' . strtolower($last) . mt_rand(1, 99) . '@' . $this->pick($domains);
            $created = -mt_rand(12, 240);
            $id = $this->upsertUser($email, $name, $this->phone(), 'customer', $created);
            $this->customers[] = [
                'id' => $id, 'name' => $name, 'created' => $this->userCreated[$id],
                'weight' => 1 / pow($rank, 0.75) * 6, 'bank' => $this->pick($banks),
            ];
            $rank++;
        }

        // Dua akun bermasalah (jarang aktif) supaya halaman Manajemen Pengguna punya variasi status.
        $n = count($this->customers);
        foreach ([[$n - 1, 'suspend'], [$n - 2, 'banned']] as [$idx, $status]) {
            DB::table('users')->where('id', $this->customers[$idx]['id'])->update(['status_akun' => $status]);
            $this->customers[$idx]['blocked'] = $status;
            $this->customers[$idx]['weight'] = 0;
        }
    }
}
