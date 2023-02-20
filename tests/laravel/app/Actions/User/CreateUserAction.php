<?php

namespace Tests\App\Actions\User;

use Sowl\JsonApi\AbstractAction;
use Sowl\JsonApi\Action\CreatesResourceTrait;
use Sowl\JsonApi\JsonApiResponse;
use Tests\App\Entities\Role;
use Tests\App\Entities\User;

class CreateUserAction extends AbstractAction
{
    use CreatesResourceTrait;

    public function handle(): JsonApiResponse
    {
        /** @var User $user */
        $user = $this->hydrateResource($this->request()->getData());
        $user->addRoles(Role::user());

        $this->em()->persist($user);
        $this->em()->flush();

        return response()->created($user, $this->transformer());
    }
}
