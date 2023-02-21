<?php

namespace Tests\App\Actions\Role;

use Sowl\JsonApi\Request\Resource\AbstractShowRequest;
use Tests\App\Entities\Role;
use Tests\App\Repositories\RolesRepository;

class ShowRoleRequest extends AbstractShowRequest
{
    public function repository(): RolesRepository
    {
        return app('em')->getRepository(Role::class);
    }
}