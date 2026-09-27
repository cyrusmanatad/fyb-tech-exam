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
        // Super Admin can update anyone. Gate::before also grants this role.
        if ($user->hasRole(User::ROLE_SUPER_ADMIN)) {
            return true;
        }

        // A user with "edit users" still cannot modify an Admin or Super Admin.
        if ($target->hasRole([User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN])) {
            return false;
        }

        return $user->hasPermissionTo('edit users');
    }
}
