<?php

namespace Sowl\JsonApi\Scribe\Attributes;

use Attribute;

/**
 * Attribute to mark a controller method as handling a single resource request (show, update, delete) for JSON:API.
 *
 * Usage:
 *   - Place on methods handling single-resource endpoints.
 *
 * Properties:
 *   - resourceType: string|null - JSON:API resource type.
 *   - idType: string|null - Type of the resource identifier (e.g., string, int).
 *   - idExample: mixed - Example value for the resource ID.
 *   - idParam: string - Name of the route parameter for the resource ID (default: 'id').
 *   - acceptHeaders: array - List of accepted content types (default: ['application/vnd.api+json']).
 */
#[Attribute(Attribute::TARGET_METHOD)]
class ResourceRequest
{
    public function __construct(
        public ?string $resourceType = null,
        public ?string $idType = null,
        public $idExample = null,
        public string $idParam = 'id',
        public array $acceptHeaders = ['application/vnd.api+json']
    ) {
    }
}
