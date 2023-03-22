<?php

namespace Tests\App\Actions\User\Rules;

use Illuminate\Support\Facades\Gate;
use Sowl\JsonApi\Request;
use Sowl\JsonApi\Rules\ObjectIdentifierRule;
use Tests\App\Entities\Role;
use Tests\App\Entities\User;

class RemoveRoleFromUserRule extends ObjectIdentifierRule
{
    public function __construct(protected Request $request)
    {
        parent::__construct(Role::class, [
            /* \Closure::fromCallable([$this, 'roleAssignedOnUser']), */
            \Closure::fromCallable([$this, 'allowRemoveRole']),
        ]);
    }

    public function allowRemoveRole($attr, Role $role, $fail): void
    {
        $user = $this->em()->getReference(User::class, $this->request->getId());

        /* if (!$user->hasRole($role)) { */
        /*     $fail(sprintf('User don\'t have assigned role "%s"', $role->getName())); */
        /* } */
        if (!Gate::allows('removeRole', [$user, $role])) {
            $fail(sprintf('User doesn\'t have the assigned role "%s"', $role->getName()));
        }
    }
}
