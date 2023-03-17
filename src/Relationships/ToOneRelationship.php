<?php

namespace Sowl\JsonApi\Relationships;

use Sowl\JsonApi\ResourceManager;

/**
 * Class represents a to-one relationship between resources in a JSON:API implementation.
 */
class ToOneRelationship
{
    use RelationshipTrait;

    public function __construct(
        protected string $name,
        protected string $class,
        ?string          $property = null,
    ) {
        ResourceManager::verifyResourceInterface($this->class);
        $this->property = $property ?: $this->name;
    }

    /**
     * Creates a new ToOneRelationship object.
     */
    public static function create(string $name, string $class, ?string $property = null): static
    {
        return new static($name, $class, $property);
    }
}