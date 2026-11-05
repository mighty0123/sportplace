<?php

namespace App\Exceptions\Admin;

use RuntimeException;

/** Dilempar saat admin mencoba menyetujui listing yang punya pemeriksaan memblokir tidak lolos. */
class ListingBlockedByChecksException extends RuntimeException
{
}