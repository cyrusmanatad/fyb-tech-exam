<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $target): bool
    {
        // Super admin can update anyone
        if ($user->hasRole('super-admin')) return true;

        // Admin cannot update another admin or super-admin
        if ($target->hasRole(['admin', 'super-admin'])) return false;

        return $user->hasPermissionTo('edit users');
    }
}
