<?php

namespace App\Exceptions\Commission;

use RuntimeException;

/** Aturan tarif dilanggar. Pesannya aman ditampilkan, dan $field menentukan kolom formulir yang diberi galat. */
class CommissionRuleException extends RuntimeException
{
    public function __construct(public readonly string $field, string $message)
    {
        parent::__construct($message);
    }
}