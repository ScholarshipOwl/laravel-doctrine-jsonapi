<?php

namespace Sowl\JsonApi\Relationships;

use Sowl\JsonApi\ResourceManager;

class ToOneRelationship extends AbstractRelationship
{
    public static function create(string $name, string $class, ?string $field = null): static
    {
        return new static($name, $class, $field);
    }

    public function __construct(
        protected string $name,
        protected string $class,
        ?string $field = null,
    ) {
        ResourceManager::verifyResourceInterface($this->class);
        $this->field = $field ?: $this->name;
    }
}