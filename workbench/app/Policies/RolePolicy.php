<?php

namespace App\Policies;

use App\Entities\Role;
use App\Entities\User;

/**
 * This policy controls access to Role entities.
 */
class RolePolicy
{
    /**
     * Allow a "root" user to perform any action.
     */
    public function before(User $user, $ability)
    {
        if ($user->isRoot()) {
            return true;
        }
    }

    /**
     * Allow a user to view a specific role if assigned to them.
     */
    public function view(User $user, Role $role): bool
    {
        return $user->hasRole($role);
    }

    /**
     * Prohibit viewing a list of all roles.
     */
    public function viewAny(): bool
    {
        return false;
    }
}
