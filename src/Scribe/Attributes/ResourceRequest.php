<?php

namespace Sowl\JsonApi\Scribe\Attributes;

use Attribute;

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
