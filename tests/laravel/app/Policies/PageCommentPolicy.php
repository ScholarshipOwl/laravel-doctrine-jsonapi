<?php

namespace Tests\App\Policies;

use Tests\App\Entities\Page;
use Tests\App\Entities\PageComment;
use Tests\App\Entities\Role;
use Tests\App\Entities\User;

class PageCommentPolicy
{
    public function view(User $user, PageComment $comment): bool
    {
        return true;
    }

    public function viewUser(User $user, PageComment $comment): bool
    {
        return true;
    }

    public function viewPage(User $user, PageComment $comment): bool
    {
        return true;
    }
}
