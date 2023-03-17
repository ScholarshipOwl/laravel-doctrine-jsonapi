<?php

namespace Tests\App\Actions\User\Relationships;

use Sowl\JsonApi\Request;
use Tests\App\Actions\User\Rules\DetachRoleFromUserRule;

class DetachRolesFromUserRequest extends Request
{
    public function dataRules(): array
    {
        return [
            'data' => 'array|required',
            'data.*' => [new DetachRoleFromUserRule($this)]
        ];
    }
}
