<?php

namespace App\Http\Controller\Users\Relationships;

use App\Http\Controller\Users\Rules\DetachRoleFromUserRule;
use Sowl\JsonApi\Request;

class DetachRolesFromUserRequest extends Request
{
    public function dataRules(): array
    {
        return [
            'data' => 'array|required',
            'data.*' => [new DetachRoleFromUserRule($this)],
        ];
    }
}
