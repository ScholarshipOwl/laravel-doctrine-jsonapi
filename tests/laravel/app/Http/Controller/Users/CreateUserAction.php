<?php

namespace Tests\App\Http\Controller\Users;

use Ramsey\Uuid\Uuid;
use Sowl\JsonApi\AbstractAction;
use Sowl\JsonApi\Response;
use Tests\App\Entities\Role;
use Tests\App\Entities\User;
use Tests\App\Entities\UserConfig;
use Tests\App\Entities\UserStatus;

class CreateUserAction extends AbstractAction
{
    public function handle(): Response
    {
        /** @var User $user */
        $user = $this->manipulator()->hydrateResource(new User, $this->request()->getData());
        $user->setId(Uuid::uuid4()->toString());
        $user->addRole(Role::user());
        $user->setStatus($this->em()->getReference(UserStatus::class, UserStatus::ACTIVE));

        $this->em()->persist($user);

        // Create default UserConfig for the new user
        $userConfig = new UserConfig;
        $userConfig->setUser($user);
        $userConfig->setTheme('light');
        $userConfig->setNotificationsEnabled(true);
        $userConfig->setLanguage('en');
        $this->em()->persist($userConfig);

        $this->em()->flush();

        return response()->created($user);
    }
}
