<?php

namespace Tests\App\Http\Controller\Users\Rules;

use Sowl\JsonApi\Request;
use Sowl\JsonApi\Rules\ResourceIdentifierRule;
use Tests\App\Entities\Role;
use Tests\App\Entities\User;

class DetachRoleFromUserRule extends ResourceIdentifierRule
{
    public function __construct(protected Request $request)
    {
        parent::__construct(Role::class, [
            \Closure::fromCallable([$this, 'roleAssignedOnUser']),
            // \Closure::fromCallable([$this, 'allowedRemoveRole']),
        ]);
    }

    public function roleAssignedOnUser($attr, Role $role, $fail): void
    {
        if (! $this->user()->hasRole($role)) {
            $fail(sprintf('User don\'t have assigned role "%s"', $role->getName()));
        }
    }

    protected function user(): User
    {
        return $this->em()->getReference(User::class, $this->request->getId());
    }
}
