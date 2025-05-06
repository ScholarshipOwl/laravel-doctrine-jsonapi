<?php

namespace Sowl\JsonApi\Scribe\Attributes;

use Attribute;

/**
 * Attribute to describe the response for a resource endpoint in JSON:API.
 *
 * Usage:
 *   - Place on methods to specify response details for a resource.
 *
 * Properties:
 *   - resourceType: string|null - JSON:API resource type.
 *   - status: int - HTTP status code (default: 200).
 *   - description: string|null - Description of the response.
 *   - fractalOptions: array - Options for Fractal transformation.
 *   - collection: bool - Whether the response is a collection.
 *   - pageNumber: int - Example page number for paginated responses.
 *   - pageSize: int - Example page size for paginated responses.
 *   - contentTypeHeaders: array - List of content-type headers (default: ['application/vnd.api+json']).
 */
#[Attribute(Attribute::TARGET_METHOD)]
class ResourceResponse
{
    public function __construct(
        public ?string $resourceType = null,
        public int $status = 200,
        public ?string $description = '',
        public array $fractalOptions = [],
        public bool $collection = false,
        public int $pageNumber = 1,
        public int $pageSize = 3,
        public array $contentTypeHeaders = ['application/vnd.api+json']
    ) {
    }
}
