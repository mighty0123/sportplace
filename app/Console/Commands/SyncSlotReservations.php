<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Services\Booking\SlotReservationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncSlotReservations extends Command
{
    protected $signature = 'slots:sync {--dry-run : Hanya melaporkan, tanpa mengubah data} {--prune=0 : Hapus riwayat reservasi yang slotnya lewat lebih dari sekian hari}';

    protected $description = 'Selaraskan reservasi slot dengan status booking, dan laporkan booking yang bentrok';

    public function handle(SlotReservationService $slots): int
    {
        $dry = (bool) $this->option('dry-run');
        $r = $slots->sync($dry);

        $this->info(($dry ? '[simulasi] ' : '') . "Dilepas: {$r['released']} baris, direservasi: {$r['reserved']} booking.");

        foreach ($r['conflicts'] as $c) {
            $holder = $c['holder'] ? " (dipegang {$c['holder']})" : '';
            $this->warn("Bentrok {$c['kode']} [{$c['status']}]{$holder}: {$c['message']}");

            // Satu catatan per booking supaya tampil di aktivitas dashboard dan tidak berulang tiap jam.
            if (! $dry && ! DB::table('audit_logs')->where('entitas_type', 'BookingHeader')->where('entitas_id', $c['id'])->where('aksi', 'slot_bentrok')->exists()) {
                AuditLog::record(null, 'BookingHeader', $c['id'], 'slot_bentrok', null, null, $c['message'] . ($holder ? " Slot dipegang {$c['holder']}." : ''));
            }
        }
        foreach ($r['invalid'] as $c) {
            $this->warn("Jam tidak selaras {$c['kode']} [{$c['status']}]: {$c['message']}");
        }

        if (! $dry && ($days = (int) $this->option('prune')) > 0) {
            $this->info('Riwayat dibuang: ' . $slots->prune($days) . ' baris.');
        }

        return self::SUCCESS;
    }
}