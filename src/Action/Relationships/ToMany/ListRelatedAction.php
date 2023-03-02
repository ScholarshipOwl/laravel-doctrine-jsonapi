<?php

namespace Sowl\JsonApi\Action\Relationships\ToMany;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;
use Sowl\JsonApi\AbstractAction;
use Sowl\JsonApi\Action\FiltersResourceTrait;
use Sowl\JsonApi\Action\PaginatesResourceTrait;
use Sowl\JsonApi\Relationships\ToManyRelationship;
use Sowl\JsonApi\Response;
use Sowl\JsonApi\ResourceInterface;

/**
* Action for providing collection (list or array) of data with API.
*/
class ListRelatedAction extends AbstractAction
{
    use FiltersResourceTrait;
    use PaginatesResourceTrait;

    public function __construct(
        protected ToManyRelationship $relationship,
    ) {
        $this->setSearchProperty($this->relationship->getSearchProperty());
        $this->setFilterable($this->relationship->getFilterable());
    }

    public function handle(): Response
    {
        $resource = $this->request()->resource();
        $repository = $this->relationship->repository();

        $qb = $this->relatedQueryBuilder($resource);
        $this->applyFilter($qb);
        $this->applyPagination($qb);

        $resourceKey = $repository->getResourceKey();
        $transformer = $repository->transformer();
        return response()->query($qb, $resourceKey, $transformer);
    }

    /**
     * Creates query builder from related repository and applies condition "related.mappedBy = resource.id".
     */
    protected function relatedQueryBuilder(ResourceInterface $resource): QueryBuilder
    {
        $mappedBy = $this->relationship->mappedBy();
        $relatedRepo = $this->relationship->repository();
        $mappedByAlias = $mappedBy.'relation';

        return $relatedRepo->resourceQueryBuilder()
            ->innerJoin(sprintf('%s.%s', $relatedRepo->alias(), $mappedBy), $mappedByAlias)
            ->addCriteria(
                Criteria::create()->andWhere(Criteria::expr()->eq($mappedByAlias, $resource->getId()))
            );
    }
}
