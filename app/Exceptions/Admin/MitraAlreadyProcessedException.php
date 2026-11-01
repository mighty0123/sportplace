<?php

namespace App\Exceptions\Admin;

use RuntimeException;

/** Dilempar saat admin memutuskan pengajuan yang sudah tidak berstatus 'pending'. */
class MitraAlreadyProcessedException extends RuntimeException
{
    public function __construct(string $message = 'Pengajuan ini sudah diproses oleh admin lain.')
    {
        parent::__construct($message);
    }
}