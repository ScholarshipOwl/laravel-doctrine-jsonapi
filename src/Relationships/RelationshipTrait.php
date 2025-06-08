<?php

namespace Sowl\JsonApi\Relationships;

use Illuminate\Support\Str;
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

    public function pascalCaseName(): string
    {
        return Str::pascal($this->name());
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

    public function spec(): array
    {
        $dataSpec = [
            'type' => 'object',
            'required' => ['id', 'type'],
            'properties' => [
                'id' => [
                    'type' => 'string',
                    'example' => '123e4567-e89b-12d3-a456-426614174000',
                ],
                'type' => [
                    'type' => 'string',
                    'example' => $this->resourceType(),
                    'enum' => [$this->resourceType()],
                ],
            ],
        ];

        return [
            'type' => 'object',
            'properties' => [
                'data' => $this->isToOne() ? $dataSpec : [
                    'type' => 'array',
                    'items' => $dataSpec,
                ],
            ],
        ];
    }
}
