<?php

namespace Sowl\JsonApi\Relationships;

use Sowl\JsonApi\ResourceManager;

class ToManyRelationship extends AbstractRelationship
{
    protected ?string $searchProperty = null;
    protected array $filterable = [];

    public function __construct(
        protected string $name,
        protected string $class,
        protected string $mappedBy,
        ?string $field = null,
    ) {
        ResourceManager::verifyResourceInterface($this->class);
        $this->field = $field ?: $this->name;
    }

    public static function create(string $name, string $class, string $mappedBy, ?string $field = null): static
    {
        return new static($name, $class, $mappedBy, $field);
    }

    public function mappedBy(): string
    {
        return $this->mappedBy;
    }

    public function setSearchProperty(?string $searchProperty): static
    {
        $this->searchProperty = $searchProperty;
        return $this;
    }

    public function getSearchProperty(): ?string
    {
        return $this->searchProperty;
    }

    public function setFilterable(array $filterable): static
    {
        $this->filterable = $filterable;
        return $this;
    }

    public function getFilterable(): array
    {
        return $this->filterable;
    }
}