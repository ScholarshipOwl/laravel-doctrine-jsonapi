<?php

namespace Sowl\JsonApi\Action\Resource;

use Sowl\JsonApi\AbstractAction;
use Sowl\JsonApi\Action\ListsResourcesTrait;
use Sowl\JsonApi\JsonApiResponse;

/**
 * Action for providing collection (list or array) of data with API.
 */
class ListResources extends AbstractAction
{
    use ListsResourcesTrait;

    public function handle(): JsonApiResponse
    {
        $this->authorize();

        $qb = $this->resourceQueryBuilder();
        $this->applyFilter($qb);
        $this->applyPagination($qb);

        return response()->query($qb, $this->repository()->getResourceKey(), $this->transformer());
    }
}
