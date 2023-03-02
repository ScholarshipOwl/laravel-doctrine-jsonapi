<?php

namespace Sowl\JsonApi\Default;

use Sowl\JsonApi\Action\Resource\UpdateResource;
use Sowl\JsonApi\Request;
use Sowl\JsonApi\Response;

trait WithUpdateTrait
{
    public function update(Request $request): Response
    {
        return (new UpdateResource())
            ->dispatch($request);
    }
}