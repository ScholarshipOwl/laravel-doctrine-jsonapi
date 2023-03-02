<?php

namespace Sowl\JsonApi\Default;

use Sowl\JsonApi\Action\Resource\UpdateResource;
use Sowl\JsonApi\Default\Request\DefaultUpdateRequest;
use Sowl\JsonApi\Response;

trait WithUpdateTrait
{
    public function update(DefaultUpdateRequest $request): Response
    {
        return (new UpdateResource())
            ->dispatch($request);
    }
}
