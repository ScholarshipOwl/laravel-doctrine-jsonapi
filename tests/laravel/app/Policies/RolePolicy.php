<?php

namespace Tests\App\Policies;

use Tests\App\Entities\Role;
use Tests\App\Entities\User;

/**
 * This policy controls access to Role entities.
 */
class RolePolicy
{
    /**
     * Allow the authenticated user to perform any action
     * if he is "root".
     */
    public function before(User $authenticated, $ability)
    {
        if ($authenticated->isRoot()) {
            return true;
        }
    }

    /**
     * Allow the authenticated user to view a specific role if assigned to them.
     */
    public function view(User $authenticated, Role $role): bool
    {
        return $authenticated->hasRole($role);
    }

    /**
     * Prohibit viewing a list of all roles.
     */
    public function viewAny(): bool
    {
        return false;
    }
}
