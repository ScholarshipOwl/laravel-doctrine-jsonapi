<?php namespace Sowl\JsonApi;

use Sowl\JsonApi\Relationships\RelationshipsCollection;

/**
 * Interface must be implemented by entity to become JSON:API resource.
 *
 * @link https://jsonapi.org/format/#document-resource-objects
 */
interface ResourceInterface
{
    /**
     * Method must return resource "type" that entity represents.
     */
    public static function getResourceType(): string;

    /**
     * List of resource relationships.
     * Can be used for handling default relationship endpoints.
     */
    public static function relationships(): RelationshipsCollection;

    /**
     * Return Fractal Transformer implementation for the current resource.
     * Transformer is used for serialization of entity into JSON:API response.
     *
     * @link https://jsonapi.org/format/#document-resource-objects Transformers documentation.
     */
    public static function transformer(): AbstractTransformer;

    /**
     * Method returns resource "id" identifier value.
     */
    public function getId(): null|string|int;
}
