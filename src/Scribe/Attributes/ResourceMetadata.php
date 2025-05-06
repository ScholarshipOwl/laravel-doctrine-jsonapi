<?php

namespace Sowl\JsonApi\Scribe\Attributes;

use Attribute;

/**
 * Attribute to provide grouping and descriptive metadata for JSON:API endpoints or resource classes.
 *
 * Usage:
 *   - Place on controller classes or methods to customize group names, descriptions, titles, and authentication
 *     requirements in Scribe docs.
 *
 * Properties:
 *   - groupName: string|null - The group name for the endpoint or resource.
 *   - groupDescription: string|null - Description for the group.
 *   - subgroup: string|null - Subgroup name for further grouping.
 *   - subgroupDescription: string|null - Description for the subgroup.
 *   - title: string|null - Title of the endpoint or resource.
 *   - description: string|null - Description of the endpoint or resource.
 *   - authenticated: bool - Whether the endpoint requires authentication.
 */
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
