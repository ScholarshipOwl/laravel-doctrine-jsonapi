<?php

namespace Tests\App\Actions\PageComment;

use Illuminate\Contracts\Auth\Access\Gate;
use Sowl\JsonApi\Request\Relationships\ToOne\AbstractShowRelationshipRequest;
use Sowl\JsonApi\ResourceRepository;
use Tests\App\Entities\Page;
use Tests\App\Entities\PageComment;

class ShowPageRelationshipRequest extends AbstractShowRelationshipRequest
{
    public function repository(): ResourceRepository
    {
        return app('em')->getRepository(PageComment::class);
    }

    public function relationRepository(): ResourceRepository
    {
        return app('em')->getRepository(Page::class);
    }

    public function authorize(Gate $gate): bool
    {
        return true;
    }
}