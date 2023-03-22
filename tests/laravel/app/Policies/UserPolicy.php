<?php

namespace Tests\App\Policies;

use Tests\App\Entities\Role;
use Tests\App\Entities\User;

/**
 * This policy controls access to User entities.
 */
class UserPolicy
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
     * Prohibit viewing a list of all users.
     */
    public function viewAny(): bool
    {
        return false;
    }

    /**
     * Allow the authenticated user to view any other user.
     */
    public function view(User $authenticated, User $user): bool
    {
        return true;
    }

    /**
     * Allow the authenticated user to update themselves.
     */
    public function update(User $authenticated, User $user): bool
    {
        return $authenticated === $user;
    }

    /**
     * Allow the authenticated user to delete themselves.
     */
    public function delete(User $authenticated, User $user): bool
    {
        return $authenticated === $user;
    }

    /**
     * Allow the authenticated user to view their own roles.
     */
    public function viewAnyRoles(User $authenticated, User $user): bool
    {
        return $authenticated === $user;
    }

    /**
     * Prohibit attaching roles to a user.
     */
    public function attachRoles(User $authenticated, User $user): bool
    {
        return false;
    }

    /**
     * Prohibit detaching roles from a user.
     */
    public function detachRoles(User $authenticated, User $user): bool
    {
        return false;
    }

    /**
     * Prohibit assigning roles to a user.
     */
    public function assignRole(User $authenticated, User $user, Role $role): bool
    {
        return false;
    }

    /**
     * Prohibit removing roles to a user.
     */
    public function removeRole(User $authenticated, User $user, Role $role): bool
    {
        return $user->hasRole($role);
    }

    /**
     * Prohibit updating a user's roles.
     */
    public function updateRoles(User $authenticated, User $user): bool
    {
        return false;
    }
}
