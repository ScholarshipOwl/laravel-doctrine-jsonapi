<?php

namespace Tests\App\Actions\User;

use Sowl\JsonApi\AbstractAction;
use Sowl\JsonApi\JsonApiResponse;
use Tests\App\Entities\Role;
use Tests\App\Entities\User;
use Tests\App\Repositories\UsersRepository;

class CreateUserAction extends AbstractAction
{
    public function handle(): JsonApiResponse
    {
        /** @var User $user */
        $user = $this->manipulator()->hydrateResource(new User(), $this->request()->getData());
        $user->addRoles(Role::user());

        $this->em()->persist($user);
        $this->em()->flush();

        return response()->created($user);
    }
}
