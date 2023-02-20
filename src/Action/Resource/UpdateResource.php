<?php namespace Sowl\JsonApi\Action\Resource;

use Sowl\JsonApi\AbstractAction;
use Sowl\JsonApi\Action\UpdatesResourceTrait;
use Sowl\JsonApi\JsonApiResponse;

class UpdateResource extends AbstractAction
{
    use UpdatesResourceTrait;

    public function handle(): JsonApiResponse
    {
        $resource = $this->repository()->findById($this->request()->getId());

        $this->authorize($resource);
        $this->updateResource($resource);

        return response()->item($resource, $this->transformer());
    }
}
