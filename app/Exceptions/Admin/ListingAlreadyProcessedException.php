<?php

namespace App\Exceptions\Admin;

use RuntimeException;

/** Dilempar saat listing sudah diputuskan admin lain, atau induknya tidak lagi memenuhi syarat. */
class ListingAlreadyProcessedException extends RuntimeException
{
    public function __construct(string $message = 'Pengajuan ini sudah diproses admin lain, atau induknya tidak lagi memenuhi syarat.')
    {
        parent::__construct($message);
    }
}