<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'no_hp', 'role',
    ];

    protected $hidden = [
        'password',
    ];

    // Relasi User ke Mitra Profile (1 to 1)
    public function mitraProfile()
    {
        return $this->hasOne(MitraProfile::class);
    }

    // Relasi User ke Wallet (1 to 1)
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }
}