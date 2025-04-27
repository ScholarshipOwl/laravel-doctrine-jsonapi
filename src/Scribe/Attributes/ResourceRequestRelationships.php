<?php

namespace Sowl\JsonApi\Scribe\Attributes;

use Attribute;

/**
 * Attribute to mark a controller method as handling relationship requests for a resource (e.g., /users/{id}/relationships/{relation}) in JSON:API.
 *
 * Usage:
 *   - Place on methods that manage relationships endpoints.
 *
 * Properties:
 *   - resourceType: string|null - JSON:API resource type.
 *   - idType: string|null - Type of the resource identifier.
 *   - idExample: mixed - Example value for the resource ID.
 *   - idParam: string - Name of the route parameter for the resource ID (default: 'id').
 *   - acceptHeaders: array - List of accepted content types (default: ['application/vnd.api+json']).
 */
#[Attribute(Attribute::TARGET_METHOD)]
class ResourceRequestRelationships extends ResourceRequest
{
    public function __construct(
        public ?string $resourceType = null,
        public ?string $idType = null,
        public $idExample = null,
        public string $idParam = 'id',
        public array $acceptHeaders = ['application/vnd.api+json']
    ) {}
}
