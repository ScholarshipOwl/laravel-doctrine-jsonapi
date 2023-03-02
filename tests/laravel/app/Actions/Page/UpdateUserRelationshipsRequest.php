<?php

namespace Tests\App\Actions\Page;

use Sowl\JsonApi\Request;
use Sowl\JsonApi\Rules\ObjectIdentifierRule;
use Tests\App\Entities\User;

class UpdateUserRelationshipsRequest extends Request
{
    public function dataRules(): array
    {
        return [
            'data' => [new ObjectIdentifierRule(User::class)]
        ];
    }
}
