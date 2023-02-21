<?php

namespace Tests\App\Actions\PageComment;

use Illuminate\Contracts\Auth\Access\Gate;
use Sowl\JsonApi\Request\Resource\AbstractShowRequest;
use Tests\App\Entities\PageComment;
use Tests\App\Repositories\PageCommentsRepository;

class ShowPageCommentRequest extends AbstractShowRequest
{
    public function repository(): PageCommentsRepository
    {
        return app('em')->getRepository(PageComment::class);
    }

    public function authorize(Gate $gate): bool
    {
        return true;
    }
}