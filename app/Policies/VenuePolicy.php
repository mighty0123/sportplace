<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Venue;

class VenuePolicy
{
    /**
     * Determine whether the user can view any venues.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['mitra', 'admin'], true);
    }

    /**
     * Determine whether the user can view the venue (BR-023).
     */
    public function view(User $user, Venue $venue): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $user->role === 'mitra' && $venue->mitra_id === $user->mitraProfile?->id;
    }

    /**
     * Determine whether the user can create venues (BR-002).
     */
    public function create(User $user): bool
    {
        return $user->role === 'mitra' && $user->isMitraApproved();
    }

    /**
     * Determine whether the user can update the venue (BR-023).
     */
    public function update(User $user, Venue $venue): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $user->role === 'mitra' && $venue->mitra_id === $user->mitraProfile?->id;
    }

    /**
     * Determine whether the user can delete/deactivate the venue (BR-023).
     */
    public function delete(User $user, Venue $venue): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $user->role === 'mitra' && $venue->mitra_id === $user->mitraProfile?->id;
    }
}

