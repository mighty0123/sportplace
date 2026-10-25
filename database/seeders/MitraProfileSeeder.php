<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MitraProfileSeeder extends Seeder
{
    public function run(): void
    {
        $mitraApproved = DB::table('users')->where('email', 'mitra@sportplace.com')->first();
        $mitraPending = DB::table('users')->where('email', 'mitra_pending@sportplace.com')->first();
        $mitraRejected = DB::table('users')->where('email', 'mitra_rejected@sportplace.com')->first();

        if ($mitraApproved) {
            DB::table('mitra_profiles')->updateOrInsert(
                ['user_id' => $mitraApproved->id],
                [
                    'no_ktp' => '3170000000000001',
                    'npwp' => '01.000.000.0-000.000',
                    'status' => 'approved',
                    'alasan_penolakan' => null,
                ]
            );
        }

        if ($mitraPending) {
            DB::table('mitra_profiles')->updateOrInsert(
                ['user_id' => $mitraPending->id],
                [
                    'no_ktp' => '3170000000000002',
                    'npwp' => '02.000.000.0-000.000',
                    'status' => 'pending',
                    'alasan_penolakan' => null,
                ]
            );
        }

        if ($mitraRejected) {
            DB::table('mitra_profiles')->updateOrInsert(
                ['user_id' => $mitraRejected->id],
                [
                    'no_ktp' => '3170000000000003',
                    'npwp' => '03.000.000.0-000.000',
                    'status' => 'rejected',
                    'alasan_penolakan' => 'Dokumen KTP buram dan tidak terbaca jelas.',
                ]
            );
        }
    }
}
