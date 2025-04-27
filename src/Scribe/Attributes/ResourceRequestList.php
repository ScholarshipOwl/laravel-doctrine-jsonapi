<?php

namespace Sowl\JsonApi\Scribe\Attributes;

use Attribute;

/**
 * Attribute to mark a controller method as handling a resource collection (list) request for JSON:API.
 *
 * Usage:
 *   - Place on methods that return lists of resources.
 *
 * Properties:
 *   - resourceType: string|null - JSON:API resource type.
 *   - acceptHeaders: array - List of accepted content types (default: ['application/vnd.api+json']).
 */
#[Attribute(Attribute::TARGET_METHOD)]
class ResourceRequestList extends ResourceRequest
{
    public function __construct(
        public ?string $resourceType = null,
        public array $acceptHeaders = ['application/vnd.api+json']
    ) {}
}
