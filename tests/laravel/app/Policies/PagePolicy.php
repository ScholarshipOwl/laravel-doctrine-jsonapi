<?php

namespace Tests\App\Policies;

use Tests\App\Entities\Page;
use Tests\App\Entities\Role;
use Tests\App\Entities\User;

class PagePolicy
{
    public function show(User $user, Page $page): bool
    {
        return true;
    }

    public function updateUser(User $user, Page $page): bool
    {
        return $user->getRoles()->contains(Role::moderator());
    }

    public function showUser(User $user, Page $page): bool
    {
        return true;
    }
}
