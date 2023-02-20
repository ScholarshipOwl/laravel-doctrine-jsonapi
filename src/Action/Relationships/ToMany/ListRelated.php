<?php

namespace Sowl\JsonApi\Action\Relationships\ToMany;

use Sowl\JsonApi\AbstractTransformer;
use Sowl\JsonApi\AbstractAction;
use Sowl\JsonApi\Action\ListsRelatedResourcesTrait;
use Sowl\JsonApi\JsonApiResponse;
use Sowl\JsonApi\ResourceRepository;

/**
* Action for providing collection (list or array) of data with API.
*/
class ListRelated extends AbstractAction
{
    use ListsRelatedResourcesTrait;

    public function __construct(
        ResourceRepository           $repository,
        protected ResourceRepository $relatedResourceRepository,
        AbstractTransformer          $transformer,
        protected string             $resourceMappedBy,
    ) {
        parent::__construct($repository, $transformer);
    }

    public function handle(): JsonApiResponse
    {
        $resource = $this->repository()->findById($this->request()->getId());

        $this->authorize($resource);

        $qb = $this->relatedQueryBuilder($resource);
        $this->applyFilter($qb);
        $this->applyPagination($qb);

        return response()->query($qb,
            $this->relatedResourceRepository()->getResourceKey(),
            $this->transformer()
        );
    }
}
