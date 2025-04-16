<?php

namespace Sowl\JsonApi\Scribe\Attributes;

use Attribute;

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
