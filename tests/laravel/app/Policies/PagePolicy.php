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
     * Allow the authenticated user to view a Page.
     */
    public function view(User $authenticated, Page $page): bool
    {
        return true;
    }

    /**
     * Prohibit viewing a list of all Pages.
     */
    public function viewAny(): bool
    {
        return false;
    }

    /**
     * Allow the authenticated user with moderator role to update a Page.
     */
    public function updateUser(User $authenticated, Page $page): bool
    {
        return $authenticated->hasRole(Role::moderator());
    }
}
