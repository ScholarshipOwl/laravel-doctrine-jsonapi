<?php namespace Sowl\JsonApi\FilterParsers\BuilderChain;

use Doctrine\Common\Collections\Criteria;

class CriteriaChain extends Chain
{
    public function process(mixed $object = null): Criteria
    {
        if ($object === null) {
            $object = new Criteria();
        }

        return parent::process($object);
    }
}
