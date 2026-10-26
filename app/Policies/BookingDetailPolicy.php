<?php

namespace App\Policies;

use App\Models\BookingDetail;
use App\Models\User;

class BookingDetailPolicy
{
    /**
     * Determine whether the user can view the booking detail (BR-023).
     */
    public function view(User $user, BookingDetail $detail): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'customer') {
            return $detail->header?->user_id === $user->id;
        }

        return $user->role === 'mitra' && $detail->field?->venue?->mitra_id === $user->mitraProfile?->id;
    }

    /**
     * Determine whether the user (mitra) can update status (completed, no_show, dll.) (BR-023).
     */
    public function updateStatus(User $user, BookingDetail $detail): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $user->role === 'mitra' && $detail->field?->venue?->mitra_id === $user->mitraProfile?->id;
    }
}
