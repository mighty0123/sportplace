<?php

namespace App\Policies;

use App\Models\MitraProfile;
use App\Models\User;

class MitraProfilePolicy
{
    /**
     * Determine whether the user can view the mitra profile.
     */
    public function view(User $user, MitraProfile $profile): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $profile->user_id === $user->id;
    }

    /**
     * Determine whether the user can update the mitra profile.
     */
    public function update(User $user, MitraProfile $profile): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $profile->user_id === $user->id;
    }
}
