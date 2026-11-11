<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use RuntimeException;

final class BookingCode
{
    private const ALPHABET = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';

    public static function make(): string
    {
        $s = '';
        for ($i = 0; $i < 6; $i++) {
            $s .= self::ALPHABET[random_int(0, strlen(self::ALPHABET) - 1)];
        }

        return 'SP-' . $s;
    }

    /**
     * Kode yang belum dipakai. Indeks unik di database tetap penjaga akhir: pemanggil yang menyimpan
     * sebaiknya mengulang bila terjadi UniqueConstraintViolationException (dua permintaan memilih kode sama).
     */
    public static function unique(): string
    {
        for ($i = 0; $i < 10; $i++) {
            $code = self::make();
            if (! DB::table('booking_headers')->where('kode_booking', $code)->exists()) {
                return $code;
            }
        }

        throw new RuntimeException('Gagal membuat kode booking yang unik.');
    }
}