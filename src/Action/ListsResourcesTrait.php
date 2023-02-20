<?php

namespace Sowl\JsonApi\Action;

use Doctrine\ORM\QueryBuilder;
use Sowl\JsonApi\AbilitiesInterface;
use Sowl\JsonApi\Action\AuthorizeResourceTrait;
use Sowl\JsonApi\Action\FiltersResourceTrait;
use Sowl\JsonApi\Action\PaginatesResourceTrait;
use Sowl\JsonApi\ResourceRepository;

trait ListsResourcesTrait
{
    use AuthorizeResourceTrait;
    use FiltersResourceTrait;
    use PaginatesResourceTrait;

    abstract protected function repository(): ResourceRepository;

    protected function resourceQueryBuilder(): QueryBuilder
    {
        return $this->repository()->resourceQueryBuilder();
    }

    protected function resourceAccessAbility(): string
    {
        return AbilitiesInterface::LIST_RESOURCES;
    }
}
