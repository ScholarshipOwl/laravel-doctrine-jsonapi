<?php

namespace Sowl\JsonApi\Action;

use Sowl\JsonApi\AbstractTransformer;
use Sowl\JsonApi\RelationshipsTransformer;

trait RelationshipsActionTrait
{
    public function transformer(): AbstractTransformer
    {
        return new RelationshipsTransformer(parent::transformer());
    }
}
