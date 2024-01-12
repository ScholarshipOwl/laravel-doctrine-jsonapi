<?php

namespace Tests\App\Http\Controller\Users\Relationships;

use Sowl\JsonApi\Request;
use Tests\App\Http\Controller\Users\Rules\AttachRoleToUserRule;

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
