<?php namespace Sowl\JsonApi\Action\Resource;

use Sowl\JsonApi\AbstractAction;
use Sowl\JsonApi\Action\CreatesResourceTrait;
use Sowl\JsonApi\JsonApiResponse;

class CreateResource extends AbstractAction
{
    use CreatesResourceTrait;

    public function handle(): JsonApiResponse
    {
        $this->authorize();

        $resource = $this->createResource();

        return response()->created($resource, $this->transformer());
    }
}
