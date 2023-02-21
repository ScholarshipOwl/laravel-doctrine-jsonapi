<?php

namespace Tests\App\Actions\User\Relationships;

use Sowl\JsonApi\Request\Relationships\ToMany\AbstractRemoveRelationshipsRequest;
use Sowl\JsonApi\ResourceRepository;
use Tests\App\Actions\User\HasUsersRepositoryTrait;
use Tests\App\Actions\User\Rules\UserRoleRemoveRule;
use Tests\App\Entities\Role;

class RemoveUserRolesRequest extends AbstractRemoveRelationshipsRequest
{
    use HasUsersRepositoryTrait;

    public function relationRepository(): ResourceRepository
    {
        return app('em')->getRepository(Role::class);
    }

    public function dataRules(): array
    {
        return [
            'data' => 'array|required',
            'data.*' => [new UserRoleRemoveRule($this)],
        ];
    }
}
