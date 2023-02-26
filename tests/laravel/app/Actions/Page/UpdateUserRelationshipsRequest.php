<?php

namespace Tests\App\Actions\Page;

use Sowl\JsonApi\Request\Relationships\ToOne\AbstractUpdateRelationshipRequest;
use Sowl\JsonApi\ResourceRepository;
use Sowl\JsonApi\Rules\ObjectIdentifierRule;
use Sowl\JsonApi\Rules\PrimaryDataRule;
use Tests\App\Entities\Page;
use Tests\App\Entities\User;

class UpdateUserRelationshipsRequest extends AbstractUpdateRelationshipRequest
{
    public function repository(): ResourceRepository
    {
        return app('em')->getRepository(Page::class);
    }

    public function relationRepository(): ResourceRepository
    {
        return app('em')->getRepository(User::class);
    }

    public function dataRules(): array
    {
        return [
            'data' => [new ObjectIdentifierRule(User::class)]
        ];
    }
}
