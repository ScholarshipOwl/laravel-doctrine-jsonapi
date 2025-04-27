<?php

namespace Sowl\JsonApi\Scribe\Attributes;

use Attribute;

/**
 * Attribute to describe the response for a relationships endpoint (e.g., /users/{id}/relationships/roles) in JSON:API.
 *
 * Usage:
 *   - Place on methods returning relationship data.
 *
 * Properties:
 *   - resourceType: string|null - JSON:API resource type.
 *   - relationshipName: string|null - Name of the relationship.
 *   - status: int - HTTP status code.
 *   - description: string|null - Description of the response.
 *   - fractalOptions: array - Options for Fractal transformation.
 *   - collection: bool - Whether the response is a collection.
 *   - pageNumber: int - Example page number.
 *   - pageSize: int - Example page size.
 *   - contentTypeHeaders: array - List of content-type headers (default: ['application/vnd.api+json']).
 */
#[Attribute(Attribute::TARGET_METHOD)]
class ResourceResponseRelationships extends ResourceResponse
{
    public function __construct(
        public ?string $resourceType = null,
        public ?string $relationshipName = null,
        public int $status = 200,
        public ?string $description = null,
        public array $fractalOptions = [],
        public bool $collection = false,
        public int $pageNumber = 1,
        public int $pageSize = 3,
        public array $contentTypeHeaders = ['application/vnd.api+json']
    ) {}
}
