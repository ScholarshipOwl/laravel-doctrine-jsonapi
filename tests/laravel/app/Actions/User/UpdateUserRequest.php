<?php

namespace Tests\App\Actions\User;

use Illuminate\Support\Facades\Gate;
use Sowl\JsonApi\Default\AbilitiesInterface;
use Sowl\JsonApi\Request;
use Tests\App\Actions\User\Rules\UserRoleAssignRule;

class UpdateUserRequest extends Request
{
    public function authorize(): bool
    {
        return Gate::allows(AbilitiesInterface::UPDATE, $this->resource());
    }

    public function dataRules(): array
    {
        return [
            'data.attributes.name' => 'sometimes|required|string',
            'data.attributes.email' => 'sometimes|required|email',
            'data.attributes.password' => 'sometimes|required|string',

            'data.relationships.roles.data' => 'sometimes|array',
            'data.relationships.roles.data.*' => [new UserRoleAssignRule($this)]
        ];
    }
}
