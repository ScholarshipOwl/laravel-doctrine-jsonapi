<?php

namespace Sowl\JsonApi\Relationships;

trait MemoizeRelationshipsTrait
{
    private static RelationshipsCollection $__relationships;

    public static function memoizeRelationships(callable $cb): RelationshipsCollection
    {
        if (!isset(static::$__relationships)) {
            static::$__relationships = new RelationshipsCollection($cb());
        }

        return static::$__relationships;
    }
}