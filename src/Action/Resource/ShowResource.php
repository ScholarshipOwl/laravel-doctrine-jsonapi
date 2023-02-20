<?php

namespace Sowl\JsonApi\Action\Resource;

use Sowl\JsonApi\AbstractAction;
use Sowl\JsonApi\Action\ShowsResourceTrait;
use Sowl\JsonApi\JsonApiResponse;

/**
 * Example of action to show one single resource.
 *
 * For example:
 *   /user/1
 */
class ShowResource extends AbstractAction
{
    use ShowsResourceTrait;

    public function handle(): JsonApiResponse
    {
        $resource = $this->repository()->findById($this->request()->getId());

        $this->authorize($resource);

        return response()->item($resource, $this->transformer());
    }
}
