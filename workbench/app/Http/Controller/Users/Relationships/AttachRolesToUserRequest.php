<?php

namespace App\Http\Controller\Users\Relationships;

use App\Http\Controller\Users\Rules\AttachRoleToUserRule;
use Sowl\JsonApi\Request;

class AttachRolesToUserRequest extends Request
{
    public function dataRules(): array
    {
        return [
            'data' => 'array|required',
            'data.*' => [new AttachRoleToUserRule($this)],
        ];
    }
}
