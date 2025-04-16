<?php

namespace Sowl\JsonApi\Scribe\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
final class ResourceCreateRequest
{
    public function __construct(
        public ?string $resourceType = null,
    ) {
    }
}
