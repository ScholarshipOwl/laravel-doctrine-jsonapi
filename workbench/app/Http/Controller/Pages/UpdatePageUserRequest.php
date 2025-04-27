<?php

namespace App\Http\Controller\Pages;

use App\Entities\User;
use Sowl\JsonApi\Request;
use Sowl\JsonApi\Rules\ResourceIdentifierRule;

class UpdatePageUserRequest extends Request
{
    public function dataRules(): array
    {
        return [
            'data' => [new ResourceIdentifierRule(User::class)],
        ];
    }
}
