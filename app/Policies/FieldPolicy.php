<?php

namespace App\Policies;

use App\Models\Field;
use App\Models\User;

class FieldPolicy
{
    /**
     * Determine whether the user can view any fields.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['mitra', 'admin'], true);
    }

    /**
     * Determine whether the user can view the field (BR-023).
     */
    public function view(User $user, Field $field): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $user->role === 'mitra' && $field->venue?->mitra_id === $user->mitraProfile?->id;
    }

    /**
     * Determine whether the user can create fields (BR-002: mitra approved).
     */
    public function create(User $user): bool
    {
        return $user->role === 'mitra' && $user->isMitraApproved();
    }

    /**
     * Determine whether the user can update the field (BR-023).
     */
    public function update(User $user, Field $field): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $user->role === 'mitra' && $field->venue?->mitra_id === $user->mitraProfile?->id;
    }

    /**
     * Determine whether the user can delete/deactivate the field (BR-023).
     */
    public function delete(User $user, Field $field): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $user->role === 'mitra' && $field->venue?->mitra_id === $user->mitraProfile?->id;
    }
}

