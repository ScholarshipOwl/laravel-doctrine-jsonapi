<?php

namespace Tests\App\Policies;

use Tests\App\Entities\Page;
use Tests\App\Entities\PageComment;
use Tests\App\Entities\Role;
use Tests\App\Entities\User;

class PageCommentPolicy
{
    public function show(User $user, PageComment $comment): bool
    {
        return true;
    }

    public function showUser(User $user, PageComment $comment): bool
    {
        return true;
    }

    public function showPage(User $user, PageComment $comment): bool
    {
        return true;
    }
}
