<?php

namespace App\Exceptions\Admin;

use RuntimeException;

/** Dilempar saat admin mencoba menyetujui pengajuan yang memiliki pemeriksaan data memblokir yang tidak lolos. */
class MitraBlockedByChecksException extends RuntimeException
{
}