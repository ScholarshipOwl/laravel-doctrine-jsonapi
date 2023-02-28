<?php

namespace Sowl\JsonApi\Relationships;

use Sowl\JsonApi\ResourceManager;
use Sowl\JsonApi\ResourceRepository;
use Sowl\JsonApi\Rules\ObjectIdentifierRule;

class AbstractRelationship
{
    protected string $name;
    protected string $class;
    protected string $field;

    public function name(): string
    {
        return $this->name;
    }

    public function class(): string
    {
        return $this->class;
    }

    public function field(): string
    {
        return $this->field;
    }

    public function repository(): ResourceRepository
    {
        return $this->rm()->repositoryByClass($this->class());
    }

    public function rm(): ResourceManager
    {
        return app(ResourceManager::class);
    }

    public function objectIdentifierRule(): ObjectIdentifierRule
    {
        return new ObjectIdentifierRule($this->class());
    }
}