<?php

namespace Tests\App\Actions\User;

use Tests\App\Entities\User;
use Tests\App\Repositories\UsersRepository;

trait HasUsersRepositoryTrait
{
    public function repository(): UsersRepository
    {
        return app('em')->getRepository(User::class);
    }
}