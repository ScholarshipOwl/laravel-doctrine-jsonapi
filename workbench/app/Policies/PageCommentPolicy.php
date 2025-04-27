<?php

namespace App\Policies;

use App\Entities\PageComment;
use App\Entities\User;

/**
 * This policy controls access to PageComment entities.
 */
class PageCommentPolicy
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
     * Allow any user to view any PageComment.
     */
    public function view(User $user, PageComment $comment): bool
    {
        return true;
    }

    /**
     * Allow any user to create a new PageComment.
     */
    public function create(User $user): bool
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
     * Allow a user to view any PageComment author.
     */
    public function viewUser(User $user, PageComment $comment): bool
    {
        return true;
    }

    /**
     * Allow a user to view any PageComment's parent Page.
     */
    public function viewPage(User $user, PageComment $comment): bool
    {
        return true;
    }
}
