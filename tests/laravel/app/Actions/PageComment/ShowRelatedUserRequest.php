<?php

namespace Tests\App\Actions\PageComment;

use Illuminate\Contracts\Auth\Access\Gate;
use Sowl\JsonApi\Request\Relationships\ToOne\AbstractShowRelatedRequest;
use Sowl\JsonApi\ResourceRepository;
use Tests\App\Entities\PageComment;
use Tests\App\Entities\User;

class ShowRelatedUserRequest extends AbstractShowRelatedRequest
{
    public function repository(): ResourceRepository
    {
        return app('em')->getRepository(PageComment::class);
    }

    public function relationRepository(): ResourceRepository
    {
        return app('em')->getRepository(User::class);
    }

    public function authorize(Gate $gate): bool
    {
        return true;
    }
}