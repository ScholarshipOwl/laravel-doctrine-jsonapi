<?php

namespace Sowl\JsonApi\Relationships;

use Sowl\JsonApi\AbstractTransformer;
use Sowl\JsonApi\ResourceManager;
use Sowl\JsonApi\ResourceRepository;
use Sowl\JsonApi\Rules\ResourceIdentifierRule;

trait RelationshipTrait
{
    protected string $name;

    protected string $class;

    protected string $property;

    public function name(): string
    {
        return $this->name;
    }

    public function class(): string
    {
        return $this->class;
    }

    public function property(): string
    {
        return $this->property;
    }

    public function resourceType(): string
    {
        return $this->rm()->resourceType($this->class());
    }

    public function rm(): ResourceManager
    {
        return app(ResourceManager::class);
    }

    public function repository(): ResourceRepository
    {
        return $this->rm()->repositoryByClass($this->class());
    }

    public function transformer(): AbstractTransformer
    {
        return $this->repository()->transformer();
    }

    public function objectIdentifierRule(): ResourceIdentifierRule
    {
        return new ResourceIdentifierRule($this->class());
    }
}
