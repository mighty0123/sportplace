<?php

namespace App\Exceptions\Admin;

use RuntimeException;

/** Dilempar bila tindakan atas akun tidak diizinkan (akun sendiri atau akun admin) */
class AccountActionNotAllowedException extends RuntimeException
{
}