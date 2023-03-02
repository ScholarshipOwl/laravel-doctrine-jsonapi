<?php

namespace Sowl\JsonApi\Default;

use Sowl\JsonApi\Action\Resource\CreateResourceAction;
use Sowl\JsonApi\Default\Request\DefaultCreateRequest;
use Sowl\JsonApi\Response;

trait WithCreateTrait
{
    public function create(DefaultCreateRequest $request): Response
    {
        return (new CreateResourceAction())
            ->dispatch($request);
    }
}
