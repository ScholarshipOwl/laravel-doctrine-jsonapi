<?php

namespace Tests\App\Actions\User\Relationships;

use Sowl\JsonApi\Request;
use Tests\App\Actions\User\Rules\AttachRoleToUserRule;

class UpdateUserRolesRequest extends Request
{
    public function dataRules(): array
    {
        return [
            'data' => 'array|required',
            'data.*' => [new AttachRoleToUserRule($this)]
        ];
    }
}
