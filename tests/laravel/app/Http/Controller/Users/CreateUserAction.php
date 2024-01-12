<?php

namespace Tests\App\Http\Controller\Users;

use Ramsey\Uuid\Uuid;
use Sowl\JsonApi\AbstractAction;
use Sowl\JsonApi\Response;
use Tests\App\Entities\Role;
use Tests\App\Entities\User;
use Tests\App\Entities\UserStatus;

class CreateUserAction extends AbstractAction
{
    public function handle(): Response
    {
        /** @var User $user */
        $user = $this->manipulator()->hydrateResource(new User(), $this->request()->getData());
        $user->setId(Uuid::uuid4()->toString());
        $user->addRole(Role::user());
        $user->setStatus($this->em()->getReference(UserStatus::class, UserStatus::ACTIVE));

        $this->em()->persist($user);
        $this->em()->flush();

        return response()->created($user);
    }
}
