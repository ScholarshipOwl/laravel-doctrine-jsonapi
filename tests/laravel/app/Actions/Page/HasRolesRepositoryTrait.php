<?php

namespace Tests\App\Actions\Page;

use Tests\App\Entities\Role;
use Tests\App\Repositories\RolesRepository;

trait HasRolesRepositoryTrait
{
    public function repository(): RolesRepository
    {
        return app('em')->getRepository(Role::class);
    }
}