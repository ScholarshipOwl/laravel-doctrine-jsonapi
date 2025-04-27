<?php

namespace App\Http\Controller\Users\Rules;

use App\Entities\Role;
use App\Entities\User;
use Illuminate\Support\Facades\Gate;
use Sowl\JsonApi\Request;
use Sowl\JsonApi\Rules\ResourceIdentifierRule;

/**
 * The rule will verify that authorized user have access to assign role on the user.
 */
class AttachRoleToUserRule extends ResourceIdentifierRule
{
    public function __construct(protected Request $request)
    {
        parent::__construct(Role::class, \Closure::fromCallable([$this, 'allowedAssignRole']));
    }

    protected function allowedAssignRole($attr, Role $role, $fail): void
    {
        $user = $this->em()->getReference(User::class, $this->request->getId());

        if (! Gate::allows('assignRole', [$user, $role])) {
            $fail('User not allowed to assign new roles to the user.');
        }
    }
}
