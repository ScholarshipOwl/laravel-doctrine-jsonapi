<?php

namespace Sowl\JsonApi\FilterParsers;

use Doctrine\Common\Collections\Criteria;
use Sowl\JsonApi\FilterParsers\BuilderChain\MemberInterface;
use Sowl\JsonApi\Request;

/**
 * Class is an abstract implementation of the MemberInterface and provides a structure for creating filter parsers.
 *
 * Filter parser is responsible for processing filter parameters from a request and applying them to a Criteria object.
 * By extending the AbstractFilterParser, you can create custom filter parsers for specific use cases.
 *
 * When creating a new filter parser, you should extend the AbstractFilterParser class and implement the applyFilter
 * method to define the specific filtering logic.
 */
abstract class AbstractFilterParser implements MemberInterface
{
    /**
     * This abstract method must be implemented by any class that extends AbstractFilterParser. It is responsible for
     * applying the filter to the provided Criteria object and returning the modified Criteria object.
     */
    abstract public function applyFilter(Criteria $criteria): Criteria;

    /**
     * The constructor accepts a Request object and stores it in the protected property $request.
     */
    public function __construct(protected Request $request)
    {
    }

    /**
     * This method is the implementation of the MemberInterface's __invoke method. It simply calls the applyFilter
     * method with the given $object, which should be a Criteria object, and returns the result.
     */
    public function __invoke($object): Criteria
    {
        return $this->applyFilter($object);
    }

    /**
     * We can provide Scribe query parametter documentation for the filter.
     */
    public function docSpec(): ?array
    {
        return null;
    }
}
