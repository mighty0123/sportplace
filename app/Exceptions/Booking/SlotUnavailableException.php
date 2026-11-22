<?php

namespace App\Exceptions\Booking;

use RuntimeException;

/** Slot sudah dipegang booking lain. Pesannya aman ditampilkan ke penyewa. */
class SlotUnavailableException extends RuntimeException
{
    public function __construct(
        public readonly int $fieldId,
        public readonly string $date,
        public readonly string $time,
        public readonly ?string $holderCode = null,
    ) {
        parent::__construct(sprintf('Slot jam %s pada %s sudah dipesan atau sedang dalam proses pembayaran oleh pengguna lain.', substr($time, 0, 5), $date));
    }
}