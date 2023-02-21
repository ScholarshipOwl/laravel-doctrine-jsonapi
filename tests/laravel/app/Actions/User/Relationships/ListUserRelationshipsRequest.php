<?php

namespace Tests\App\Actions\User\Relationships;

use Sowl\JsonApi\Request\Relationships\ToMany\AbstractListRelationshipsRequest;
use Sowl\JsonApi\ResourceRepository;
use Tests\App\Actions\User\HasUsersRepositoryTrait;
use Tests\App\Entities\Role;

class ListUserRelationshipsRequest extends AbstractListRelationshipsRequest
{
    use HasUsersRepositoryTrait;

    public function relationRepository(): ResourceRepository
    {
        return app('em')->getRepository(Role::class);
    }
}