<?php

namespace Tests\App\Policies;

use Tests\App\Entities\Page;
use Tests\App\Entities\Role;
use Tests\App\Entities\User;

/**
 * This policy controls access to Page entities.
 */
class PagePolicy
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
     * Prohibit viewing a list of all pages.
     */
    public function viewAny(): bool
    {
        return false;
    }

    /**
     * Allow the authenticated user to view a page.
     */
    public function view(User $authenticated, Page $page): bool
    {
        return true;
    }

    /**
     * Allow the authenticated user with moderator role to update a page.
     */
    public function updateUser(User $authenticated, Page $page): bool
    {
        return $authenticated->hasRole(Role::moderator());
    }

    /**
     * Prohibit detaching a user from a page.
     */
    public function detachUser(User $authenticated, Page $page): bool
    {
        return false;
    }
}
