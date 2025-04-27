<?php

namespace Sowl\JsonApi\Relationships;

use Sowl\JsonApi\ResourceManager;

/**
 * Class represents a to-one relationship between resources in a JSON:API implementation.
 */
final class ToOneRelationship implements RelationshipInterface
{
    use RelationshipTrait;

    public function __construct(
        protected string $name,
        protected string $class,
        ?string $property = null,
    ) {
        ResourceManager::verifyResourceInterface($this->class);
        $this->property = $property ?: $this->name;
    }

    /**
     * Creates a new ToOneRelationship object.
     */
    public static function create(string $name, string $class, ?string $property = null): static
    {
        return new self($name, $class, $property);
    }

    public function isToOne(): bool
    {
        return true;
    }

    public function isToMany(): bool
    {
        return false;
    }
}
