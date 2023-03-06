<?php

namespace Sowl\JsonApi\Relationships;

use Sowl\JsonApi\ResourceManager;

class ToOneRelationship extends AbstractRelationship
{
    public function __construct(
        protected string $name,
        protected string $class,
        ?string $field = null,
    ) {
        ResourceManager::verifyResourceInterface($this->class);
        $this->property = $field ?: $this->name;
    }

    public static function create(string $name, string $class, ?string $property = null): static
    {
        return new static($name, $class, $property);
    }
}