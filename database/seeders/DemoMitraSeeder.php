<?php

namespace Database\Seeders;

use DateTimeImmutable;
use DateTimeZone;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/** Akun dan profil mitra beserta dokumen placeholder. */
class DemoMitraSeeder extends DemoBase
{
    public function run(): void
    {
        $this->ensureStarted();
        $this->seedMitra();
    }

    private function seedMitra(): void
    {
        foreach ($this->mitraSpecs() as $key => $s) {
            $userId = $this->upsertUser($s['email'], $s['name'], $s['phone'], 'mitra', $s['created']);
            $created = $this->at($s['created'])->setTime(mt_rand(8, 17), mt_rand(0, 59), 0);
            $submitted = isset($s['submit']) ? $this->now->modify($s['submit']) : $created->modify('+3 hours');
            $reviewed = null;
            $checklist = null;
            $reason = null;

            if ($s['status'] === 'approved') {
                $reviewed = $submitted->modify('+' . mt_rand(20, 70) . ' hours');
                $checklist = ['manual' => ['foto_ktp' => true, 'nik_sesuai' => true, 'dokumen_usaha' => true]];
            } elseif ($s['status'] === 'rejected') {
                $reviewed = isset($s['review']) ? $this->now->modify($s['review']) : $submitted->modify('+1 day');
                $checklist = ['manual' => ['foto_ktp' => false, 'nik_sesuai' => false, 'dokumen_usaha' => true]];
                $reason = $s['alasan'];
            }

            $docs = [];
            foreach (['ktp' => 'KTP', 'npwp' => 'NPWP', 'nib' => 'NIB', 'sewa-lahan' => 'Sewa lahan'] as $slug => $label) {
                $docs[$slug] = $this->documentFile($key, $slug, $label, $s['usaha']);
            }

            DB::table('mitra_profiles')->updateOrInsert(['user_id' => $userId], [
                'nama_usaha' => $s['usaha'],
                'bentuk_badan_usaha' => $s['badan'],
                'kota_operasional' => $s['kota'],
                'deskripsi_usaha' => $s['desk'],
                'bank_nama' => $s['bank'],
                'nomor_rekening' => $s['rek'],
                'nama_rekening' => $s['an'],
                'no_ktp' => $s['ktp'],
                'npwp' => $s['npwp'],
                'status' => $s['status'],
                'alasan_penolakan' => $reason,
                'submitted_at' => $this->dt($submitted),
                'reviewed_by' => $reviewed ? $this->adminId : null,
                'reviewed_at' => $reviewed ? $this->dt($reviewed) : null,
                'review_checklist' => $checklist ? json_encode($checklist) : null,
                'file_ktp' => $docs['ktp'],
                'file_npwp' => $docs['npwp'],
                'file_nib' => $docs['nib'],
                'file_sewa_lahan' => $docs['sewa-lahan'],
                'created_at' => $this->dt($created),
                'updated_at' => $this->dt($reviewed ?? $submitted),
            ]);

            $this->mitra[$key] = $s + [
                'user_id' => $userId,
                'id' => (int) DB::table('mitra_profiles')->where('user_id', $userId)->value('id'),
                'submitted' => $submitted,
                'reviewed' => $reviewed,
            ];
        }
    }

    private function mitraSpecs(): array
    {
        return [
            'champion' => [
                'email' => 'mitra@sportplace.com', 'name' => 'Mitra Approved', 'phone' => '08222222222', 'status' => 'approved',
                'usaha' => 'Champion Futsal Club', 'badan' => 'CV / Badan Usaha', 'kota' => 'Denpasar',
                'desk' => 'Pusat futsal, badminton, dan mini soccer di Denpasar dengan lantai standar kompetisi dan lampu LED anti silau.',
                'bank' => 'BCA', 'rek' => '8205128901', 'an' => 'CV Champion Sport', 'ktp' => '5171021505850001', 'npwp' => '01.234.567.8-901.000', 'created' => -215,
            ],
            'viva' => [
                'email' => 'wayan.suardana@vivasports.id', 'name' => 'I Wayan Suardana', 'phone' => '081338123456', 'status' => 'approved',
                'usaha' => 'Viva Sports Group', 'badan' => 'PT / Perseroan Terbatas', 'kota' => 'Denpasar',
                'desk' => 'Jaringan arena futsal dan badminton di kawasan Renon dengan fasilitas kantin dan area parkir luas.',
                'bank' => 'Mandiri', 'rek' => '1450009876543', 'an' => 'PT Viva Sportindo Bali', 'ktp' => '5171011203780002', 'npwp' => '02.345.678.9-902.000', 'created' => -190,
            ],
            'smash' => [
                'email' => 'made.wirawan@smashcenter.id', 'name' => 'I Made Wirawan', 'phone' => '081805551234', 'status' => 'approved',
                'usaha' => 'Smash Center Bali', 'badan' => 'CV / Badan Usaha', 'kota' => 'Badung',
                'desk' => 'Gedung olahraga raket di Jimbaran: badminton dan tenis meja dengan pencahayaan standar turnamen.',
                'bank' => 'BNI', 'rek' => '0987654321', 'an' => 'CV Smash Center Bali', 'ktp' => '5103032007820003', 'npwp' => '03.456.789.0-903.000', 'created' => -150,
            ],
            'hoops' => [
                'email' => 'kadek.mahendra@hoopshall.id', 'name' => 'I Kadek Mahendra', 'phone' => '085737001122', 'status' => 'approved',
                'usaha' => 'Hoops & Volley Hall', 'badan' => 'Perorangan', 'kota' => 'Badung',
                'desk' => 'Hall basket dan voli indoor di Kuta Selatan, cocok untuk latihan tim sekolah dan komunitas.',
                'bank' => 'BRI', 'rek' => '012301009988776', 'an' => 'I Kadek Mahendra', 'ktp' => '5103041010900004', 'npwp' => '04.567.890.1-904.000', 'created' => -130,
            ],
            'tabanan' => [
                'email' => 'nyoman.adnyana@tabanantennis.id', 'name' => 'I Nyoman Adnyana', 'phone' => '082144556677', 'status' => 'approved',
                'usaha' => 'Tabanan Tennis Club', 'badan' => 'Perorangan', 'kota' => 'Tabanan',
                'desk' => 'Klub tenis di Kediri, Tabanan, dengan tiga lapangan dan pemandangan sawah.',
                'bank' => 'BCA', 'rek' => '7761234509', 'an' => 'I Nyoman Adnyana', 'ktp' => '5102051508750005', 'npwp' => '05.678.901.2-905.000', 'created' => -120,
            ],
            'gianyar' => [
                'email' => 'ketut.darmawan@gianyarsport.id', 'name' => 'I Ketut Darmawan', 'phone' => '087862003344', 'status' => 'approved',
                'usaha' => 'Gianyar Sport Complex', 'badan' => 'CV / Badan Usaha', 'kota' => 'Gianyar',
                'desk' => 'Kompleks olahraga serbaguna di Gianyar: futsal, mini soccer, dan voli pasir.',
                'bank' => 'BSI', 'rek' => '7123456780', 'an' => 'CV Gianyar Sport Complex', 'ktp' => '5104061112830006', 'npwp' => '06.789.012.3-906.000', 'created' => -80,
            ],
            'prima' => [
                'email' => 'mitra_pending@sportplace.com', 'name' => 'Mitra Pending', 'phone' => '08222222223', 'status' => 'pending',
                'usaha' => 'Mitra Futsal Prima', 'badan' => 'Perorangan', 'kota' => 'Denpasar',
                'desk' => 'Arena futsal dan mini soccer yang baru mendaftar di Denpasar Utara.',
                'bank' => 'Mandiri', 'rek' => '1370001234567', 'an' => 'Mitra Pending', 'ktp' => '5171010202900007', 'npwp' => '07.890.123.4-907.000', 'created' => -3,
                'submit' => '-5 hours',
            ],
            'singaraja' => [
                'email' => 'putu.pramana@singarajaarena.id', 'name' => 'I Putu Pramana', 'phone' => '081239887766', 'status' => 'pending',
                'usaha' => 'Singaraja Arena', 'badan' => 'CV / Badan Usaha', 'kota' => 'Buleleng',
                'desk' => 'Arena futsal dan badminton di pusat kota Singaraja, Buleleng.',
                'bank' => 'BNI', 'rek' => '0765432198', 'an' => 'CV Singaraja Arena', 'ktp' => '5108071505880008', 'npwp' => '08.901.234.5-908.000', 'created' => -2,
                'submit' => '-29 hours',
            ],
            'gelora' => [
                'email' => 'mitra_rejected@sportplace.com', 'name' => 'Mitra Rejected', 'phone' => '08222222224', 'status' => 'rejected',
                'usaha' => 'Gelora Sports Hub', 'badan' => 'Perorangan', 'kota' => 'Denpasar',
                'desk' => 'Fasilitas olahraga bulutangkis di Denpasar Barat.',
                'bank' => 'BRI', 'rek' => '012301001234509', 'an' => 'Mitra Rejected', 'ktp' => '5171031010910009', 'npwp' => '09.012.345.6-909.000', 'created' => -12,
                'submit' => '-9 days', 'review' => '-8 days',
                'alasan' => 'Dokumen KTP buram dan tidak terbaca jelas.',
            ],
        ];
    }
}
