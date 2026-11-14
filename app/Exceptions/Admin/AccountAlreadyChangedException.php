<?php

namespace App\Exceptions\Admin;

use RuntimeException;

/** Dilempar bila akun sudah berstatus yang diminta (misalnya diproses admin lain lebih dulu) */
class AccountAlreadyChangedException extends RuntimeException
{
}