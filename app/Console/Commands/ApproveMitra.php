<?php

namespace App\Console\Commands;

use App\Models\MitraProfile;
use App\Models\User;
use Illuminate\Console\Command;

class ApproveMitra extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mitra:approve {email : Email akun mitra yang akan disetujui}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setujui akun mitra (hanya untuk lingkungan non-produksi)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (app()->isProduction()) {
            $this->error('Perintah ini hanya dapat dijalankan pada lingkungan non-produksi!');

            return Command::FAILURE;
        }

        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("User dengan email [{$email}] tidak ditemukan.");

            return Command::FAILURE;
        }

        if ($user->role !== 'mitra') {
            $this->error("User [{$email}] bukan berstatus mitra (role: {$user->role}).");

            return Command::FAILURE;
        }

        $profile = MitraProfile::firstOrNew(['user_id' => $user->id]);
        $profile->status = 'approved';
        $profile->alasan_penolakan = null;
        if (! $profile->no_ktp) {
            $profile->no_ktp = '3170000000000000';
        }
        if (! $profile->npwp) {
            $profile->npwp = '01.000.000.0-000.000';
        }
        $profile->save();

        $this->info("Mitra [{$email}] berhasil disetujui (status: approved).");

        return Command::SUCCESS;
    }
}
