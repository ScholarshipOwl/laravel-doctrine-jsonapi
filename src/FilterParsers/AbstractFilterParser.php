<?php namespace Sowl\JsonApi\FilterParsers;

use Sowl\JsonApi\FilterParsers\BuilderChain\MemberInterface;
use Sowl\JsonApi\JsonApiRequest;
use Doctrine\Common\Collections\Criteria;

abstract class AbstractFilterParser implements MemberInterface
{
    abstract public function applyFilter(Criteria $criteria): Criteria;

    public function __construct(protected JsonApiRequest $request) {}

    public function __invoke($object): Criteria
    {
        return $this->applyFilter($object);
    }
}
