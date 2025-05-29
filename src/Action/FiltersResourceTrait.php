<?php

namespace Sowl\JsonApi\Action;

use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\QueryBuilder;
use Sowl\JsonApi\FilterParsers\BuilderChain\CriteriaChain;
use Sowl\JsonApi\Request;
use Sowl\JsonApi\Resource\FilterableInterface;
use Sowl\JsonApi\ResourceInterface;
use Sowl\JsonApi\ResourceRepository;

trait FiltersResourceTrait
{
    abstract protected function repository(): ResourceRepository;

    abstract protected function request(): Request;

    /**
     * Apply filter criteria on the query builder.
     *
     * @throws QueryException
     */
    protected function applyFilter(QueryBuilder $qb): static
    {
        $qb->addCriteria(
            CriteriaChain::create($this->filterParsers())->process()
        );

        return $this;
    }

    /** @return class-string<ResourceInterface>|null */
    protected function filterableClass(): ?string
    {
        return $this->repository()->getClassName();
    }

    protected function filterParsers(): array
    {
        $resourceClass = $this->filterableClass();

        if (
            $resourceClass && class_exists($resourceClass) &&
            is_subclass_of($resourceClass, FilterableInterface::class)
        ) {
            return $resourceClass::filterParsers($this->request());
        }

        return [];
    }
}
