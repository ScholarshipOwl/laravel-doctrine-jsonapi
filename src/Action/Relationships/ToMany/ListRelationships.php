<?php namespace Sowl\JsonApi\Action\Relationships\ToMany;


use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;
use Sowl\JsonApi\AbstractAction;
use Sowl\JsonApi\Action\FiltersResourceTrait;
use Sowl\JsonApi\Action\PaginatesResourceTrait;
use Sowl\JsonApi\Action\RelatedActionTrait;
use Sowl\JsonApi\JsonApiResponse;
use Sowl\JsonApi\ResourceInterface;
use Sowl\JsonApi\ResourceRepository;

class ListRelationships extends AbstractAction
{
    use RelatedActionTrait;
    use FiltersResourceTrait;
    use PaginatesResourceTrait;

    public function __construct(
        protected ResourceRepository $relationRepository,
        protected string             $resourceMappedBy,
    ) {}

    public function handle(): JsonApiResponse
    {
        $resource = $this->request()->resource();

        $qb = $this->relatedQueryBuilder($resource);
        $this->applyFilter($qb);
        $this->applyPagination($qb);

        $resourceKey = $this->relationRepository()->getResourceKey();
        $transformer = $this->relationRepository()->transformer();
        return response()->query($qb, $resourceKey, $transformer, relationship: true);
    }

    /**
     * Creates query builder from related repository and applies condition "related.mappedBy = resource.id".
     */
    protected function relatedQueryBuilder(ResourceInterface $resource): QueryBuilder
    {
        $mappedBy = $this->resourceMappedBy();
        $mappedByAlias = $mappedBy.'relation';
        $relatedRepo = $this->relationRepository();

        return $relatedRepo->resourceQueryBuilder()
            ->innerJoin(sprintf('%s.%s', $relatedRepo->alias(), $mappedBy), $mappedByAlias)
            ->addCriteria(
                Criteria::create()->andWhere(Criteria::expr()->eq($mappedByAlias, $resource->getId()))
            );
    }
}
