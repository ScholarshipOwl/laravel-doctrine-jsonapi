<?php namespace Sowl\JsonApi\Action\Resource;

use Sowl\JsonApi\AbstractAction;
use Sowl\JsonApi\Action\RemovesResourceTrait;
use Sowl\JsonApi\JsonApiResponse;

class RemoveResource extends AbstractAction
{
    use RemovesResourceTrait;

    public function handle(): JsonApiResponse
    {
        $resource = $this->repository()->findById($this->request()->getId());

        $this->authorize($resource);
        $this->removeResource($resource);

        return response()->noContent();
    }
}
