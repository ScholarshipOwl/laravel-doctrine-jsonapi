<?php

namespace Sowl\JsonApi\Action;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;
use Sowl\JsonApi\AbilitiesInterface;
use Sowl\JsonApi\Action\AuthorizeRelationshipsTrait;
use Sowl\JsonApi\Action\FiltersResourceTrait;
use Sowl\JsonApi\Action\PaginatesResourceTrait;
use Sowl\JsonApi\Action\RelatedActionTrait;
use Sowl\JsonApi\ResourceInterface;

trait ListsRelatedResourcesTrait
{
    use FiltersResourceTrait;
    use PaginatesResourceTrait;
    use AuthorizeRelationshipsTrait;
    use RelatedActionTrait;

    /**
     * Creates query builder from related repository and applies condition "related.mappedBy = resource.id".
     */
    protected function relatedQueryBuilder(ResourceInterface $resource): QueryBuilder
    {
        $mappedBy = $this->resourceMappedBy();
        $relatedRepo = $this->relatedResourceRepository();

        return $relatedRepo->resourceQueryBuilder()
            ->innerJoin(sprintf('%s.%s', $relatedRepo->alias(), $mappedBy), $mappedBy)
            ->addCriteria(
                Criteria::create()->andWhere(Criteria::expr()->eq($mappedBy, $resource->getId()))
            );
    }

    protected function resourceAccessAbility(): string
    {
        return AbilitiesInterface::SHOW_RESOURCE;
    }

    public function relatedResourceAccessAbility(): string
    {
        return AbilitiesInterface::LIST_RELATIONSHIPS;
    }
}
