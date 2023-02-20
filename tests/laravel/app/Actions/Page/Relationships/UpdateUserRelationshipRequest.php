<?php

namespace Tests\App\Actions\Page\Relationships;

use Sowl\JsonApi\JsonApiRequest;
use Sowl\JsonApi\Rules\PrimaryDataRule;
use Tests\App\Entities\User;

class UpdateUserRelationshipRequest extends JsonApiRequest
{
    public function dataRules(): array
    {
        return [
            'data' => [new PrimaryDataRule(User::class)]
        ];
    }
}
