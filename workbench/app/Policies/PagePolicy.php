<?php

namespace App\Policies;

use App\Entities\Page;
use App\Entities\Role;
use App\Entities\User;

/**
 * This policy controls access to Page entities.
 */
class PagePolicy
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
     * Allow any user to view a Page.
     */
    public function view(User $user, ?Page $page): bool
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
     * Allow a user with moderator role to update a Page.
     */
    public function updateUser(User $user, Page $page): bool
    {
        return $user->hasRole(Role::moderator());
    }

    public function viewAnyPageComments(User $user, Page $page): bool
    {
        return $user === $page->getUser();
    }
}
