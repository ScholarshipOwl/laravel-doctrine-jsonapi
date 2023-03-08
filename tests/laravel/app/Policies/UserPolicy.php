<?php namespace Tests\App\Policies;

use Tests\App\Entities\Role;
use Tests\App\Entities\User;

class UserPolicy
{
    public function view(User $user, User $resource): bool
    {
        return true;
    }

    public function viewAny(): bool
    {
        return false;
    }

    public function create(User $user, User $resource): bool
    {
        return $user === $resource;
    }

    public function update(User $user, User $resource): bool
    {
        return $user === $resource;
    }

    public function delete(User $user, User $resource): bool
    {
        return $user === $resource;
    }

    public function assignRole(User $user, User $resource, Role $role): bool
    {
        return false;
    }

    public function viewAnyRoles(User $user, User $resource): bool
    {
        return $user === $resource;
    }
}
