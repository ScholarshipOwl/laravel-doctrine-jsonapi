<?php

namespace Tests\App\Actions\User\Relationships;

use Illuminate\Support\Facades\Gate;
use Sowl\JsonApi\Default\AbilitiesInterface;
use Sowl\JsonApi\Request;
use Tests\App\Actions\User\Rules\UserRoleRemoveRule;
use Tests\App\Entities\User;

class RemoveUserRolesRequest extends Request
{
    public function authorize(): bool
    {
        return Gate::allows(AbilitiesInterface::REMOVE_RELATIONSHIPS, [User::class, 'roles']);
    }

    public function dataRules(): array
    {
        return [
            'data' => 'array|required',
            'data.*' => [new UserRoleRemoveRule($this)]
        ];
    }
}
