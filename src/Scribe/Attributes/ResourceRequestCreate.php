<?php

namespace Sowl\JsonApi\Scribe\Attributes;

use Attribute;

/**
 * Attribute to mark a controller method as handling resource creation for JSON:API.
 *
 * Usage:
 *   - Place on methods that create new resources.
 *
 * Properties:
 *   - resourceType: string|null - JSON:API resource type.
 *   - acceptHeaders: array - List of accepted content types (default: ['application/vnd.api+json']).
 */
#[Attribute(Attribute::TARGET_METHOD)]
class ResourceRequestCreate extends ResourceRequest
{
    public function __construct(
        public ?string $resourceType = null,
        public array $acceptHeaders = ['application/vnd.api+json']
    ) {
    }
}
