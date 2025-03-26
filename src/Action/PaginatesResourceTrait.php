<?php

namespace Sowl\JsonApi\Action;

use Sowl\JsonApi\Request;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\QueryBuilder;

trait PaginatesResourceTrait
{
    abstract protected function request(): Request;

    /**
     * Apply pagination criteria to the query builder.
     * @throws QueryException
     */
    protected function applyPagination(QueryBuilder $qb): static
    {
        $qb->addCriteria(
            new Criteria(
                null,
                $this->request()->getSort(),
                $this->request()->getFirstResult(),
                $this->request()->getMaxResults()
            )
        );

        return $this;
    }
}
