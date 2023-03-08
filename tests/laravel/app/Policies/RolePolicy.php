<?php

namespace Tests\App\Policies;


use Tests\App\Entities\Role;
use Tests\App\Entities\User;

class RolePolicy
{
    public function view(User $user, Role $role): bool
    {
        return $user->getRoles()->contains($role);
    }

    public function viewAny(): bool
    {
        return false;
    }
}
