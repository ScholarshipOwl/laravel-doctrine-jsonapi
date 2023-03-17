<?php

namespace Sowl\JsonApi\Relationships;

use Sowl\JsonApi\ResourceManager;

/**
 * Class represents a to-many relationship between resources in a JSON:API implementation.
 */
class ToManyRelationship
{
    use RelationshipTrait;

    protected ?string $searchProperty = null;
    protected array $filterable = [];

    public function __construct(
        protected string $name,
        protected string $class,
        protected string $mappedBy,
        ?string          $property = null,
    ) {
        ResourceManager::verifyResourceInterface($this->class);
        $this->property = $property ?: $this->name;
    }

    /**
     * Creates a new ToManyRelationship object.
     */
    public static function create(string $name, string $class, string $mappedBy, ?string $property = null): static
    {
        return new static($name, $class, $mappedBy, $property);
    }

    /**
     * Name of the association-field on the owning side of the relation.
     */
    public function mappedBy(): string
    {
        return $this->mappedBy;
    }

    /**
     * Sets the search property for the relationship.
     * Search property will be used for "search" filter when retrieving list.
     */
    public function setSearchProperty(?string $searchProperty): static
    {
        $this->searchProperty = $searchProperty;
        return $this;
    }

    /**
     * Returns the search property for the relationship.
     */
    public function getSearchProperty(): ?string
    {
        return $this->searchProperty;
    }

    /**
     * Sets the filterable properties for the relationship.
     * List of fields that can be filtered when retrieving related.
     */
    public function setFilterable(array $filterable): static
    {
        $this->filterable = $filterable;
        return $this;
    }

    /**
     * Returns the filterable properties for the relationship.
     */
    public function getFilterable(): array
    {
        return $this->filterable;
    }
}