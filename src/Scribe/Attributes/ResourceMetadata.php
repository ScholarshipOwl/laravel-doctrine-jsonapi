<?php

namespace Sowl\JsonApi\Scribe\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_CLASS)]
class ResourceMetadata
{
    public function __construct(
        public ?string $groupName = null,
        public ?string $groupDescription = null,
        public ?string $subgroup = null,
        public ?string $subgroupDescription = null,
        public ?string $title = null,
        public ?string $description = null,
        public bool $authenticated = false,
    ) {
    }
}
