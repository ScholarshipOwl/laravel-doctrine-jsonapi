<?php namespace Sowl\JsonApi;

use Sowl\JsonApi\Relationships\RelationshipsCollection;
use Sowl\JsonApi\Relationships\ToManyRelationship;
use Sowl\JsonApi\Relationships\ToOneRelationship;

interface ResourceInterface
{
    /**
     * Get fractal resource key.
     * JSON API `type`
     */
    public static function getResourceKey(): string;

    /**
     * JSON API `id`
     */
    public function getId(): null|string|int;

    public static function transformer(): AbstractTransformer;

    /**
     * Provide map of to one relationship name to its resource class.
     * @return RelationshipsCollection<string, ToOneRelationship|ToManyRelationship>
     */
    public static function relationships(): RelationshipsCollection;
}
