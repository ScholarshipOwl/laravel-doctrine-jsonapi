<?php

namespace Sowl\JsonApi\Relationships;

trait NoRelationships
{
    public static function relationships(): RelationshipsCollection
    {
        return new RelationshipsCollection([]);
    }
}
