<?php

namespace Sowl\JsonApi\Action;

use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\QueryBuilder;
use Sowl\JsonApi\FilterParsers\BuilderChain\CriteriaChain;
use Sowl\JsonApi\Request;
use Sowl\JsonApi\Resource\FilterableInterface;
use Sowl\JsonApi\ResourceRepository;

trait FiltersResourceTrait
{
    abstract protected function repository(): ResourceRepository;
    abstract protected function request(): Request;

    /**
     * Apply filter criteria on the query builder.
     * @throws QueryException
     */
    protected function applyFilter(QueryBuilder $qb): static
    {
        $qb->addCriteria(
            CriteriaChain::create($this->filterParsers())->process()
        );

        return $this;
    }

    private function filterParsers(): array
    {
        $resourceClass = $this->repository()->getClassName();

        if (in_array(FilterableInterface::class, class_implements($resourceClass))) {
            return $resourceClass::filterParsers($this->request());
        }

        return [];
    }
}
