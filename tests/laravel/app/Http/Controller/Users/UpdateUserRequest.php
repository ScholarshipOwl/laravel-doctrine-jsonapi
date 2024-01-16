<?php

namespace Tests\App\Http\Controller\Users;

use Illuminate\Support\Facades\Gate;
use Sowl\JsonApi\Default\AbilitiesInterface;
use Sowl\JsonApi\Request;
use Sowl\JsonApi\Rules\ResourceIdentifierRule;
use Tests\App\Entities\UserStatus;
use Tests\App\Http\Controller\Users\Rules\AttachRoleToUserRule;

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

            'data.relationships.status.data' => ['sometimes', new ResourceIdentifierRule(UserStatus::class)],

            'data.relationships.roles.data' => 'sometimes|array',
            'data.relationships.roles.data.*' => [new AttachRoleToUserRule($this)]
        ];
    }

    public function attributes(): array
    {
        return [
            'data.attributes.name' => 'name',
            'data.attributes.email' => 'email',
            'data.attributes.password' => 'password',
        ];
    }
}
