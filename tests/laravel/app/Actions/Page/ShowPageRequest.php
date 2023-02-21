<?php

namespace Tests\App\Actions\Page;

use Sowl\JsonApi\Request\Resource\AbstractShowRequest;
use Tests\App\Entities\Page;
use Illuminate\Contracts\Auth\Access\Gate;
use Tests\App\Repositories\PagesRepository;

class ShowPageRequest extends AbstractShowRequest
{
    public function repository(): PagesRepository
    {
        return app('em')->getRepository(Page::class);
    }

    /**
     * Everyone can see pages without authentication.
     */
    public function authorize(Gate $gate): bool
    {
        return true;
    }
}