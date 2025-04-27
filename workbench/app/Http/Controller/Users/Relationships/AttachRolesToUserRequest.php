<?php

namespace App\Http\Controller\Users\Relationships;

use Sowl\JsonApi\Request;
use App\Http\Controller\Users\Rules\AttachRoleToUserRule;

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
