<?php

namespace Sowl\JsonApi\Action\Resource;

use Sowl\JsonApi\AbstractAction;
use Sowl\JsonApi\Action\CreatesResourceTrait;
use Sowl\JsonApi\JsonApiResponse;

class CreateResources extends AbstractAction
{
    use CreatesResourceTrait;

    public function handle(): JsonApiResponse
    {
        $this->authorize();

        $resources = $this->createResources();

        return response()->collection(
            $resources,
            $this->repository()->getResourceKey(),
            $this->transformer()
        );
    }
}
