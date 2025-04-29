<?php

namespace Sowl\JsonApi\Relationships;

/**
 * Trait provides a simple way to memoize and store a collection of relationships.
 * The trait is useful when you want to cache the relationships data for a specific class,
 * preventing unnecessary computations or function calls to define relationships each time
 * the relationships are accessed.
 */
trait MemoizeRelationshipsTrait
{
    /**
     * The method returns the cached instance of RelationshipsCollection if it exists; otherwise, it creates
     * a new instance and caches it before returning.
     */
    protected static RelationshipsCollection $memoizedRelationships;

    /**
     * Takes a callable as an argument, which is expected to return an array of relationships
     * (ToOneRelationship or ToManyRelationship instances).
     *
     * This method creates a new instance of the RelationshipsCollection class with the provided relationships array,
     * and stores it in a private static property named
     */
    public static function memoizeRelationships(?callable $cb = null): RelationshipsCollection
    {
        if (! isset(static::$memoizedRelationships)) {
            static::$memoizedRelationships = new RelationshipsCollection($cb ? $cb() : []);
        }

        return static::$memoizedRelationships;
    }
}
