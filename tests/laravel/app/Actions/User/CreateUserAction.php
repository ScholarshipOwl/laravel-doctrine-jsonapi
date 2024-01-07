<?php

namespace Tests\App\Actions\User;

use Ramsey\Uuid\Uuid;
use Sowl\JsonApi\AbstractAction;
use Sowl\JsonApi\Response;
use Tests\App\Entities\Role;
use Tests\App\Entities\User;

class CreateUserAction extends AbstractAction
{
    public function handle(): Response
    {
        /** @var User $user */
        $user = $this->manipulator()->hydrateResource(new User(), $this->request()->getData());
        $user->setId(Uuid::uuid4()->toString());
        $user->addRole(Role::user());

        $this->em()->persist($user);
        $this->em()->flush();

        return response()->created($user);
    }
}
