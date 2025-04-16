<?php

namespace Sowl\JsonApi\Scribe\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class ResourceRequestCreate extends ResourceRequest
{
    public function __construct(
        public ?string $resourceType = null,
        public array $acceptHeaders = ['application/vnd.api+json']
    ) {
    }
}
