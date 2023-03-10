<?php

namespace Tests\App\Actions\User\Rules;

use Sowl\JsonApi\Request;
use Sowl\JsonApi\Rules\ObjectIdentifierRule;
use Tests\App\Entities\Role;
use Tests\App\Entities\User;

class UserRoleRemoveRule extends ObjectIdentifierRule
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
        if (!$this->user()->getRoles()->contains($role)) {
            $fail(sprintf('User don\'t have assigned role "%s"', $role->getName()));
        }
    }

    protected function user(): User
    {
        return $this->em()->getReference(User::class, $this->request->getId());
    }
}
