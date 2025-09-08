<?php

namespace App\Policies;

use App\Models\Atc;
use App\Models\User;

class AtcPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'operations_manager', 'staff']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Atc $atc): bool
    {
        return in_array($user->role, ['admin', 'operations_manager', 'staff']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'operations_manager']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Atc $atc): bool
    {
        return in_array($user->role, ['admin', 'operations_manager']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Atc $atc): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Atc $atc): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Atc $atc): bool
    {
        return $user->role === 'admin';
    }
}
