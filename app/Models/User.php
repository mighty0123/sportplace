<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'no_hp', 'role',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'password' => 'hashed',
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

    // Relasi User ke kode OTP (1 to many; satu aktif per tujuan — GAP-09)
    public function otpCodes()
    {
        return $this->hasMany(OtpCode::class);
    }

    public function isMitraApproved(): bool
    {
        return $this->mitraProfile?->status === 'approved';
    }
}
