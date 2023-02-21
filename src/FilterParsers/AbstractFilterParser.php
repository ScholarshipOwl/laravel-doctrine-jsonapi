<?php namespace Sowl\JsonApi\FilterParsers;

use Sowl\JsonApi\FilterParsers\BuilderChain\MemberInterface;
use Sowl\JsonApi\AbstractRequest;
use Doctrine\Common\Collections\Criteria;

abstract class AbstractFilterParser implements MemberInterface
{
    abstract public function applyFilter(Criteria $criteria): Criteria;

    public function __construct(protected AbstractRequest $request) {}

    public function __invoke($object): Criteria
    {
        return $this->applyFilter($object);
    }
}
