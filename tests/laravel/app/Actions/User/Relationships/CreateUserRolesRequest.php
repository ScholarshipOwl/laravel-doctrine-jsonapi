<?php

namespace Tests\App\Actions\User\Relationships;

use Sowl\JsonApi\Request\Relationships\ToMany\AbstractCreateRelationshipsRequest;
use Sowl\JsonApi\ResourceRepository;
use Tests\App\Actions\User\HasUsersRepositoryTrait;
use Tests\App\Actions\User\Rules\UserRoleAssignRule;
use Tests\App\Entities\Role;

class CreateUserRolesRequest extends AbstractCreateRelationshipsRequest
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
            'data.*' => [new UserRoleAssignRule($this)]
        ];
    }
}
