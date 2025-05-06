<?php

namespace Sowl\JsonApi\FilterParsers\BuilderChain;

use Doctrine\Common\Collections\Criteria;

/**
 * Class extends the Chain class and is specifically designed to work with Doctrine's Criteria objects.
 * The primary difference between CriteriaChain and its parent Chain class is the process method,
 * which ensures that the object being processed is an instance of Doctrine\Common\Collections\Criteria.
 *
 * By using the CriteriaChain class, you can create a processing pipeline specifically tailored for Criteria objects.
 * This allows you to apply a series of transformations or filters to a Criteria object in a sequential and
 * organized manner, while ensuring that the object being processed is always an instance
 * of Doctrine\Common\Collections\Criteria.
 */
class CriteriaChain extends Chain
{
    /**
     * Method accepts Criteria as its argument.
     * If the input object is null, it initializes a new Criteria object.
     *
     * Then it calls the parent process() method to apply each member of the chain sequentially.
     * Once all members have been applied, the method returns the final processed Criteria object.
     *
     * @param  Criteria|null  $object
     */
    public function process(mixed $object = null): Criteria
    {
        if ($object === null) {
            $object = new Criteria();
        }

        return parent::process($object);
    }
}
