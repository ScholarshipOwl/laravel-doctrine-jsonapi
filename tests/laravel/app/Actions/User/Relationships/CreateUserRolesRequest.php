<?php

namespace Tests\App\Actions\User\Relationships;

use Illuminate\Support\Facades\Gate;
use Sowl\JsonApi\Default\AbilitiesInterface;
use Sowl\JsonApi\Request;
use Tests\App\Actions\User\Rules\UserRoleAssignRule;
use Tests\App\Entities\User;

class CreateUserRolesRequest extends Request
{
    public function authorize(): bool
    {
        return Gate::allows(AbilitiesInterface::CREATE_RELATIONSHIPS, [User::class, 'roles']);
    }

    public function dataRules(): array
    {
        return [
            'data' => 'array|required',
            'data.*' => [new UserRoleAssignRule($this)]
        ];
    }
}
