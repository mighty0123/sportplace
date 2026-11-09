<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MitraProfileSeeder extends Seeder
{
    public function run(): void
    {
        $admin = DB::table('users')->where('email', 'admin@sportplace.com')->first();
        $mitraApproved = DB::table('users')->where('email', 'mitra@sportplace.com')->first();
        $mitraPending = DB::table('users')->where('email', 'mitra_pending@sportplace.com')->first();
        $mitraRejected = DB::table('users')->where('email', 'mitra_rejected@sportplace.com')->first();

        if ($mitraApproved) {
            DB::table('mitra_profiles')->updateOrInsert(
                ['user_id' => $mitraApproved->id],
                [
                    'nama_usaha' => 'Champion Futsal Club',
                    'bentuk_badan_usaha' => 'CV / Badan Usaha',
                    'kota_operasional' => 'Jakarta Selatan',
                    'deskripsi_usaha' => 'Pusat futsal dan badminton modern di Jakarta Selatan.',
                    'bank_nama' => 'BCA',
                    'nomor_rekening' => '8205128901',
                    'nama_rekening' => 'CV Champion Sport',
                    'no_ktp' => '3170000000000001',
                    'npwp' => '01.000.000.0-000.000',
                    'status' => 'approved',
                    'alasan_penolakan' => null,
                    'submitted_at' => now()->subDays(3),
                    'reviewed_by' => $admin?->id,
                    'reviewed_at' => now()->subDays(2),
                    'created_at' => now()->subDays(3),
                    'updated_at' => now()->subDays(2),
                ]
            );
        }

        if ($mitraPending) {
            DB::table('mitra_profiles')->updateOrInsert(
                ['user_id' => $mitraPending->id],
                [
                    'nama_usaha' => 'Mitra Futsal Prima',
                    'bentuk_badan_usaha' => 'Perorangan',
                    'kota_operasional' => 'Jakarta Selatan',
                    'deskripsi_usaha' => 'Arena olahraga futsal dan mini soccer.',
                    'bank_nama' => 'Mandiri',
                    'nomor_rekening' => '1370001234567',
                    'nama_rekening' => 'Mitra Pending',
                    'no_ktp' => '3170000000000002',
                    'npwp' => '02.000.000.0-000.000',
                    'status' => 'pending',
                    'alasan_penolakan' => null,
                    'submitted_at' => now()->subHours(5),
                    'created_at' => now()->subHours(5),
                    'updated_at' => now()->subHours(5),
                ]
            );
        }

        if ($mitraRejected) {
            DB::table('mitra_profiles')->updateOrInsert(
                ['user_id' => $mitraRejected->id],
                [
                    'nama_usaha' => 'Gelora Sports Hub',
                    'bentuk_badan_usaha' => 'Perorangan',
                    'kota_operasional' => 'Jakarta Selatan',
                    'deskripsi_usaha' => 'Fasilitas olahraga bulutangkis.',
                    'bank_nama' => 'BRI',
                    'nomor_rekening' => '012301001234509',
                    'nama_rekening' => 'Mitra Rejected',
                    'no_ktp' => '3170000000000003',
                    'npwp' => '03.000.000.0-000.000',
                    'status' => 'rejected',
                    'alasan_penolakan' => 'Dokumen KTP buram dan tidak terbaca jelas.',
                    'submitted_at' => now()->subDays(2),
                    'reviewed_by' => $admin?->id,
                    'reviewed_at' => now()->subDay(),
                    'review_checklist' => json_encode(['manual' => ['foto_ktp' => false, 'nik_sesuai' => false, 'dokumen_usaha' => true]]),
                    'created_at' => now()->subDays(2),
                    'updated_at' => now()->subDay(),
                ]
            );
        }
    }
}
