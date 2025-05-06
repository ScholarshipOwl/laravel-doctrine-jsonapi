<?php

namespace Sowl\JsonApi\Action\Resource;

use Sowl\JsonApi\AbstractAction;
use Sowl\JsonApi\Action\FiltersResourceTrait;
use Sowl\JsonApi\Action\PaginatesResourceTrait;
use Sowl\JsonApi\Default\AbilitiesInterface;
use Sowl\JsonApi\Request;
use Sowl\JsonApi\ResourceRepository;
use Sowl\JsonApi\Response;

/**
 * Action for providing collection (list or array) of data with API.
 */
class ListResourcesAction extends AbstractAction
{
    use FiltersResourceTrait;
    use PaginatesResourceTrait;

    public function __construct(
        protected Request $request,
    ) {
    }

    public function authorize(): void
    {
        $this->gate()->authorize(AbilitiesInterface::LIST, $this->repository()->getClassName());
    }

    protected function request(): Request
    {
        return $this->request;
    }

    public function repository(): ResourceRepository
    {
        return $this->request->repository();
    }

    public function handle(): Response
    {
        $qb = $this->request->repository()->resourceQueryBuilder();
        $this->applyFilter($qb);
        $this->applyPagination($qb);

        $resourceType = $this->request->repository()->getResourceType();
        $transformer = $this->request->repository()->transformer();

        return $this->response()->query($qb, $resourceType, $transformer);
    }
}
