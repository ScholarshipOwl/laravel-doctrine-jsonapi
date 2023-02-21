<?php namespace Sowl\JsonApi\Action\Resource;

use Sowl\JsonApi\AbstractAction;
use Sowl\JsonApi\JsonApiResponse;

class UpdateResource extends AbstractAction
{
    public function handle(): JsonApiResponse
    {
        $resource = $this->request()->resource();
        $resource = $this->manipulator()->hydrateResource($resource, $this->request()->getData());

        $this->repository()->em()->flush();

        return response()->item($resource);
    }
}
