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

    public function updateRelationships(User $user, Page $page, string $relationship): bool
    {
        return match ($relationship) {
            'user' => $user->getRoles()->contains(Role::moderator()),
            default => false,
        };
    }

    public function showRelationships(User $user, Page $page, string $relationship): bool
    {
        return match ($relationship) {
            'user' => true,
            default => false
        };
    }
}
