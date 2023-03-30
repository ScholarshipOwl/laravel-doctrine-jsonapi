<?php

namespace Tests\App\Policies;

use Tests\App\Entities\PageComment;
use Tests\App\Entities\User;

/**
 * This policy controls access to PageComment entities.
 */
class PageCommentPolicy
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
     * Allow the authenticated user to view any PageComment.
     */
    public function view(User $authenticated, PageComment $comment): bool
    {
        return true;
    }

    /**
     * Prohibit viewing a list of all PageComments.
     */
    public function viewAny(): bool
    {
        return false;
    }

    /**
     * Allow the authenticated user to view any PageComment author.
     */
    public function viewUser(User $authenticated, PageComment $comment): bool
    {
        return true;
    }

    /**
     * Allow the authenticated  user to view any PageComment's parent Page.
     */
    public function viewPage(User $authenticated, PageComment $comment): bool
    {
        return true;
    }
}
