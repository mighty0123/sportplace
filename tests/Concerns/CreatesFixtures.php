<?php

namespace Tests\Concerns;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/** Pembuat data uji lewat tabel langsung, supaya tidak bergantung pada $fillable atau factory model */
trait CreatesFixtures
{
    private static int $seq = 0;

    protected function makeUser(string $role = 'customer', array $o = []): User
    {
        $n = ++self::$seq;

        $id = DB::table('users')->insertGetId(array_merge([
            'name' => ucfirst($role) . " Uji {$n}", 'email' => "{$role}{$n}-" . Str::lower(Str::random(6)) . '@example.test',
            'password' => Hash::make('Password123'), 'no_hp' => '08' . random_int(1000000000, 9999999999),
            'role' => $role, 'status_akun' => 'aktif', 'email_verified_at' => now(), 'created_at' => now(), 'updated_at' => now(),
        ], $o));

        return User::findOrFail($id);
    }

    protected function nik(): string
    {
        return '317401150190' . str_pad((string) (++self::$seq % 10000), 4, '0', STR_PAD_LEFT);
    }

    protected function mitraProfile(User $owner, array $o = []): int
    {
        return DB::table('mitra_profiles')->insertGetId(array_merge([
            'user_id' => $owner->id, 'nama_usaha' => 'Usaha Uji', 'bentuk_badan_usaha' => 'CV', 'kota_operasional' => 'Denpasar',
            'deskripsi_usaha' => 'Usaha uji', 'bank_nama' => 'BCA', 'nomor_rekening' => '1234567890', 'nama_rekening' => $owner->name,
            'no_ktp' => $this->nik(), 'npwp' => null, 'status' => 'pending', 'file_ktp' => null,
            'submitted_at' => now(), 'created_at' => now(), 'updated_at' => now(),
        ], $o));
    }

    /** @return array{0:int,1:int} [id kota, id olahraga] */
    protected function cityAndSport(): array
    {
        $city = DB::table('master_cities')->insertGetId(['nama_kota' => 'Denpasar', 'provinsi' => 'Bali', 'status_kota' => 'aktif']);
        $sport = DB::table('master_sports')->insertGetId([
            'nama_olahraga' => 'Futsal', 'slug' => 'futsal-' . Str::lower(Str::random(4)), 'icon_url' => '/icons/futsal.png', 'status_olahraga' => 'aktif',
        ]);

        return [$city, $sport];
    }

    protected function venue(int $mitraId, int $cityId, array $o = []): int
    {
        return DB::table('venues')->insertGetId(array_merge([
            'mitra_id' => $mitraId, 'city_id' => $cityId, 'nama_venue' => 'Venue Uji', 'alamat_lengkap' => 'Jl. Uji No. 1',
            'deskripsi' => 'Venue uji dengan deskripsi yang cukup panjang untuk lolos pemeriksaan.', 'map_url' => 'https://maps.example.test/uji',
            'jam_buka' => '08:00:00', 'jam_tutup' => '22:00:00', 'status_vanue' => 'pending_review',
            'created_at' => now(), 'updated_at' => now(), 'submitted_at' => now(),
        ], $o));
    }

    protected function field(int $venueId, int $sportId, array $o = []): int
    {
        return DB::table('fields')->insertGetId(array_merge([
            'venue_id' => $venueId, 'sport_id' => $sportId, 'nama_lapangan' => 'Lapangan Uji', 'harga_perjam' => 150000, 'meta_harga' => null,
            'status_lapangan' => 'pending_review', 'created_at' => now(), 'updated_at' => now(), 'submitted_at' => now(),
        ], $o));
    }

    protected function booking(User $customer, array $o = []): int
    {
        return DB::table('booking_headers')->insertGetId(array_merge([
            'kode_booking' => 'SP-' . Str::upper(Str::random(6)), 'penyewa_id' => $customer->id, 'total_tagihan' => 100000,
            'status' => 'pending', 'created_at' => now(), 'expires_at' => null,
        ], $o));
    }

    protected function bookingDetail(int $headerId, int $fieldId, array $o = []): int
    {
        return DB::table('booking_details')->insertGetId(array_merge([
            'header_id' => $headerId, 'field_id' => $fieldId, 'tanggal_sewa' => now()->addDay(), 'jam_mulai' => '19:00:00',
            'jam_selesai' => '20:00:00', 'harga_slot' => 100000, 'status_item' => 'ongoing', 'created_at' => now(),
        ], $o));
    }

    protected function payment(int $headerId, array $o = []): int
    {
        return DB::table('payments')->insertGetId(array_merge([
            'header_id' => $headerId, 'pg_transaction_id' => 'TST-' . Str::upper(Str::random(8)), 'metode_pembayaran' => 'qris',
            'nominal_bayar' => 100000, 'status_pembayaran' => 'succes', 'paid_at' => now(), 'created_at' => now(),
        ], $o));
    }

    protected function refund(int $headerId, int $detailId, int $paymentId, array $o = []): int
    {
        return DB::table('refunds')->insertGetId(array_merge([
            'header_id' => $headerId, 'detail_id' => $detailId, 'payment_id' => $paymentId, 'alasan_refund' => 'Uji refund.',
            'nominal_refund' => 100000, 'metode_refund' => 'bank_transfer', 'rekening_tujuan' => 'BCA 1234567890', 'status_refund' => 'completed',
        ], $o));
    }

    /** Booking lengkap: satu slot dan satu pembayaran berhasil, dengan nominal yang konsisten */
    protected function paidBooking(User $customer, int $fieldId, int $amount = 100000, array $o = []): int
    {
        $id = $this->booking($customer, array_merge(['total_tagihan' => $amount, 'status' => 'payment'], $o));
        $this->bookingDetail($id, $fieldId, ['harga_slot' => $amount]);
        $this->payment($id, ['nominal_bayar' => $amount]);

        return $id;
    }
}