<?php

namespace Tests\App\Actions\User\Relationships;

use Sowl\JsonApi\Request\Relationships\ToMany\AbstractListRelatedRequest;
use Sowl\JsonApi\ResourceRepository;
use Tests\App\Actions\User\HasUsersRepositoryTrait;
use Tests\App\Entities\Role;

class ListRelatedRolesRequest extends AbstractListRelatedRequest
{
    use HasUsersRepositoryTrait;

    public function relationRepository(): ResourceRepository
    {
        return app('em')->getRepository(Role::class);
    }
}