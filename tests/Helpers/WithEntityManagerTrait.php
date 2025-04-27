<?php

namespace Tests\Helpers;

use Doctrine\ORM\EntityManager;
use App\Entities\Page;
use App\Entities\PageComment;
use App\Entities\Role;
use App\Entities\User;
use App\Repositories\PageCommentsRepository;
use App\Repositories\RolesRepository;
use App\Repositories\UsersRepository;

trait WithEntityManagerTrait
{
    protected EntityManager $em;

    public function em(): EntityManager
    {
        return $this->em ?? $this->em = app(EntityManager::class);
    }

    public function usersRepo(): UsersRepository
    {
        return $this->em()->getRepository(User::class);
    }

    public function rolesRepo(): RolesRepository
    {
        return $this->em()->getRepository(Role::class);
    }

    public function pageRepo(): RolesRepository
    {
        return $this->em()->getRepository(Page::class);
    }

    public function pageCommentsRepo(): PageCommentsRepository
    {
        return $this->em()->getRepository(PageComment::class);
    }
}
