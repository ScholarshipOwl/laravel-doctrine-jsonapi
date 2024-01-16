<?php

namespace Tests\App\Http\Controller\Pages;

use Sowl\JsonApi\Request;
use Sowl\JsonApi\Rules\ResourceIdentifierRule;
use Tests\App\Entities\User;

class UpdatePageUserRequest extends Request
{
    public function dataRules(): array
    {
        return [
            'data' => [new ResourceIdentifierRule(User::class)]
        ];
    }
}
