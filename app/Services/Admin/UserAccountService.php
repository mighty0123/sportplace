<?php

namespace App\Services\Admin;

use App\Exceptions\Admin\AccountActionNotAllowedException;
use App\Exceptions\Admin\AccountAlreadyChangedException;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Manajemen akun pengguna. Hanya status akun yang bisa diubah
 */
class UserAccountService
{
    public const ROLES  = ['mitra' => 'Mitra', 'customer' => 'Penyewa', 'admin' => 'Admin'];
    public const STATUS = ['aktif' => 'Aktif', 'suspend' => 'Ditangguhkan', 'banned' => 'Diblokir'];

    private const AUDIT_ACTION = ['aktif' => 'aktifkan', 'suspend' => 'suspend', 'banned' => 'banned'];

    public function find(int $id): ?object
    {
        return DB::table('users')->where('id', $id)->first();
    }

    /** Ringkasan aktivitas menurut peran, untuk halaman detail dan dialog konfirmasi */
    public function overview(object $u): array
    {
        $out = ['sessions' => null, 'mitra' => null, 'customer' => null];

        if (config('session.driver') === 'database') {
            $out['sessions'] = DB::table(config('session.table', 'sessions'))->where('user_id', $u->id)->count();
        }

        if ($u->role === 'mitra') {
            $profile = DB::table('mitra_profiles')->where('user_id', $u->id)->orderBy('id')->first(['id', 'nama_usaha', 'status']);
            $venues = $profile
                ? DB::table('venues')->where('mitra_id', $profile->id)->selectRaw("COUNT(*) as total, COALESCE(SUM(status_vanue = 'aktif'), 0) as aktif")->first()
                : null;
            $fields = $profile
                ? DB::table('fields as f')->join('venues as v', 'v.id', '=', 'f.venue_id')->where('v.mitra_id', $profile->id)
                    ->selectRaw("COUNT(*) as total, COALESCE(SUM(f.status_lapangan = 'aktif'), 0) as aktif")->first()
                : null;

            $out['mitra'] = [
                'profile' => $profile,
                'venues'  => $venues,
                'fields'  => $fields,
                'wallet'  => DB::table('wallets')->where('user_id', $u->id)->first(['saldo_aktif', 'saldo_tertahan']),
            ];
        }

        if ($u->role === 'customer') {
            $bookings = DB::table('booking_headers')->where('penyewa_id', $u->id)
                ->selectRaw("COUNT(*) as total, COALESCE(SUM(status = 'payment'), 0) as paid, COALESCE(SUM(status = 'pending'), 0) as pending")->first();

            $out['customer'] = [
                'bookings' => $bookings,
                'spent'    => (float) DB::table('payments as p')->join('booking_headers as h', 'h.id', '=', 'p.header_id')
                    ->where('h.penyewa_id', $u->id)->where('p.status_pembayaran', 'succes')->sum('p.nominal_bayar'),
                'upcoming' => DB::table('booking_details as d')->join('booking_headers as h', 'h.id', '=', 'd.header_id')
                    ->where('h.penyewa_id', $u->id)->where('h.status', 'payment')->where('d.tanggal_sewa', '>=', now()->startOfDay())->count(),
                'refunds'  => DB::table('refunds as r')->join('booking_headers as h', 'h.id', '=', 'r.header_id')
                    ->where('h.penyewa_id', $u->id)->whereIn('r.status_refund', ['pending', 'processing'])->count(),
            ];
        }

        return $out;
    }

    public function history(int $id): Collection
    {
        return AuditLog::query()->with('user:id,name')
            ->whereIn('entitas_type', ['User', 'App\\Models\\User'])
            ->where('entitas_id', $id)
            ->latest('created_at')->latest('id')->get();
    }

    /** Apakah akun ini boleh diubah statusnya oleh admin yang sedang masuk? */
    public function canAct(User $admin, object $target): bool
    {
        return (int) $target->id !== (int) $admin->id && $target->role !== 'admin';
    }

    /** @return object akun sebelum diubah, untuk pesan sukses */
    public function changeStatus(User $admin, int $id, string $status, ?string $reason): object
    {
        abort_unless(array_key_exists($status, self::STATUS), 422);

        return DB::transaction(function () use ($admin, $id, $status, $reason) {
            // Kunci baris: dua admin yang bertindak bersamaan tidak bisa saling menimpa
            $target = DB::table('users')->where('id', $id)->lockForUpdate()->first();
            abort_if(! $target, 404);

            if (! $this->canAct($admin, $target)) {
                throw new AccountActionNotAllowedException(
                    (int) $target->id === (int) $admin->id
                        ? 'Kamu tidak bisa mengubah status akunmu sendiri.'
                        : 'Akun admin tidak dapat diubah dari halaman ini.'
                );
            }
            if ($target->status_akun === $status) {
                throw new AccountAlreadyChangedException('Akun ini sudah berstatus ' . mb_strtolower(self::STATUS[$status]) . '. Mungkin sudah diubah admin lain.');
            }

            DB::table('users')->where('id', $id)->update(['status_akun' => $status, 'updated_at' => now()]);

            // Keluarkan dari semua perangkat. Tanpa ini, sesi yang sudah ada berlaku sampai habis
            if ($status !== 'aktif' && config('session.driver') === 'database') {
                DB::table(config('session.table', 'sessions'))->where('user_id', $id)->delete();
            }

            // Satu transaksi dengan perubahan status: bila pencatatan gagal, perubahan ikut dibatalkan.
            AuditLog::record($admin, 'User', $id, self::AUDIT_ACTION[$status], $target->status_akun, $status, $reason ?: null);

            return $target;
        });
    }
}