<?php

namespace Sowl\JsonApi\Action\Resource;

use Sowl\JsonApi\AbstractAction;
use Sowl\JsonApi\Action\FiltersResourceTrait;
use Sowl\JsonApi\Action\PaginatesResourceTrait;
use Sowl\JsonApi\Response;

/**
 * Action for providing collection (list or array) of data with API.
 */
class ListResourcesAction extends AbstractAction
{
    use FiltersResourceTrait;
    use PaginatesResourceTrait;

    public function handle(): Response
    {
        $qb = $this->repository()->resourceQueryBuilder();
        $this->applyFilter($qb);
        $this->applyPagination($qb);

        $resourceType = $this->repository()->getResourceType();
        $transformer = $this->repository()->transformer();

        return $this->response()->query($qb, $resourceType, $transformer);
    }
}
