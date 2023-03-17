<?php

namespace Sowl\JsonApi;

use Sowl\JsonApi\Relationships\RelationshipsCollection;

/**
 * Interface must be implemented by entities in order to become a JSON:API resource.
 * By implementing this interface, an entity can be used in a JSON:API response with minimal additional configuration.
 *
 * @link https://jsonapi.org/format/#document-resource-objects
 */
interface ResourceInterface
{
    /**
     * Method that must return a string representing the resource type that the entity represents
     * This is used to identify the type of the resource in the JSON:API response.
     */
    public static function getResourceType(): string;

    /**
     * List of resource relationships.
     * Can be used for handling default relationship endpoints.
     */
    public static function relationships(): RelationshipsCollection;

    /**
     * Return Fractal Transformer implementation for the current resource.
     * The transformer is used for serialization of the entity into a JSON:API response.
     *
     * @link https://jsonapi.org/format/#document-resource-objects Transformers documentation.
     */
    public static function transformer(): AbstractTransformer;

    /**
     * Method that must return the "id" identifier value for the resource
     * This is used to identify the resource in the JSON:API response.
     */
    public function getId(): null|string|int;
}
