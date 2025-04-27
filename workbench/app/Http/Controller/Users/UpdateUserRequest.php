<?php

namespace App\Http\Controller\Users;

use App\Entities\UserStatus;
use App\Http\Controller\Users\Rules\AttachRoleToUserRule;
use Illuminate\Support\Facades\Gate;
use Sowl\JsonApi\Default\AbilitiesInterface;
use Sowl\JsonApi\Request;
use Sowl\JsonApi\Rules\ResourceIdentifierRule;

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

            // Example: No Example
            'data.relationships.status.data' => ['sometimes', 'array', new ResourceIdentifierRule(UserStatus::class)],

            'data.relationships.roles.data' => 'sometimes|array',
            'data.relationships.roles.data.*' => [new AttachRoleToUserRule($this)],
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
