<?php

namespace Sowl\JsonApi\Default;

use Sowl\JsonApi\Action\Resource\RemoveResource;
use Sowl\JsonApi\Request;
use Sowl\JsonApi\Response;

trait WithRemoveTrait
{
    public function remove(Request $request): Response
    {
        return RemoveResource::create()
            ->dispatch($request);
    }
}