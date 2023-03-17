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
     * Allow a "root" user to perform any action.
     */
    public function before(User $user, $ability)
    {
        if ($user->isRoot()) {
            return true;
        }
    }

    /**
     * Allow user to view any other user.
     */
    public function view(User $user, User $other): bool
    {
        return true;
    }

    /**
     * Prohibit viewing a list of all users.
     */
    public function viewAny(): bool
    {
        return false;
    }

    /**
     * Allow user to update themselves.
     */
    public function update(User $user, User $other): bool
    {
        return $user === $other;
    }

    /**
     * Allow user to delete themselves.
     */
    public function delete(User $user, User $other): bool
    {
        return $user === $other;
    }

    /**
     * Allow a user to view their own roles.
     */
    public function viewAnyRoles(User $user, User $from): bool
    {
        return $user === $from;
    }

    /**
     * Prohibit attaching roles to a user.
     */
    public function attachRoles(User $user, User $to): bool
    {
        return false;
    }

    /**
     * Prohibit detaching roles from a user.
     */
    public function detachRoles(User $user, User $from): bool
    {
        return false;
    }

    /**
     * Prohibit assigning roles to a user.
     */
    public function assignRole(User $user, User $to, Role $role): bool
    {
        return false;
    }

    /**
     * Prohibit updating a user's roles.
     */
    public function updateRoles(User $user, User $from): bool
    {
        return false;
    }
}
