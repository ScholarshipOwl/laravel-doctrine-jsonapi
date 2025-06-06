<?php

namespace Sowl\JsonApi\Relationships;

use Illuminate\Support\Str;
use Sowl\JsonApi\ResourceManager;

/**
 * Class represents a to-many relationship between resources in a JSON:API implementation.
 */
final class ToManyRelationship implements RelationshipInterface
{
    use RelationshipTrait;

    public function __construct(
        protected string $name,
        protected string $class,
        protected string $mappedBy,
        ?string $property = null,
    ) {
        ResourceManager::verifyResourceInterface($this->class);
        $this->property = $property ?: Str::camel($this->name);
    }

    /**
     * Creates a new ToManyRelationship object.
     */
    public static function create(string $name, string $class, string $mappedBy, ?string $property = null): static
    {
        return new self($name, $class, $mappedBy, $property);
    }

    /**
     * Name of the association-field on the owning side of the relation.
     */
    public function mappedBy(): string
    {
        return $this->mappedBy;
    }

    public function isToOne(): bool
    {
        return false;
    }

    public function isToMany(): bool
    {
        return true;
    }
}
