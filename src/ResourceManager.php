<?php

namespace Sowl\JsonApi;

use Doctrine\ORM\EntityManager;

use Doctrine\ORM\Mapping\ClassMetadata;
use InvalidArgumentException;
use UnexpectedValueException;

class ResourceManager
{
    const RESOURCE_TYPE_METHOD = 'getResourceKey';
    const RESOURCE_TRANSFORMER_METHOD = 'transformer';

    /** @var array<string, ResourceInterface>  */
    protected array $resources;

    public function __construct(protected EntityManager $em, array $resources = [])
    {
        $this->registerResources($resources);
    }

    /**
     * Make sure class is implements resource interface.
     */
    public static function verifyResourceInterface(string $class): void
    {
        if (!class_exists($class)) {
            throw new InvalidArgumentException(sprintf('%s - is not a class', $class));
        }

        if (!isset(class_implements($class)[ResourceInterface::class])) {
            throw new UnexpectedValueException(sprintf(
                '%s - not implements %s', $class, ResourceInterface::class
            ));
        }
    }

    public static function resourceInterfaceKey(string $class): string
    {
        static::verifyResourceInterface($class);
        return call_user_func(sprintf('%s::%s', $class, static::RESOURCE_TYPE_METHOD));
    }

    public function registerResource(string $class): static
    {
        $resourceKey = static::resourceInterfaceKey($class);

        $this->resources[$resourceKey] = $class;

        return $this;
    }

    public function registerResources(array $resources): static
    {
        array_map(fn ($resource) => $this->registerResource($resource), $resources);

        return $this;
    }

    public function classByResourceKey(string $resourceKey): string
    {
        if (!isset($this->resources[$resourceKey])) {
            throw new InvalidArgumentException(sprintf('%s - is not registered resource key', $resourceKey));
        }

        return $this->resources[$resourceKey];
    }

    public function repositoryByResourceKey(string $resourceKey): ResourceRepository
    {
        $class = $this->classByResourceKey($resourceKey);

        return $this->repositoryByClass($class);
    }

    public function repositoryByClass(string $class): ResourceRepository
    {
        $metadata = $this->em->getClassMetadata($class);
        $repositoryClass = $this->resourceRepositoryClass($metadata);

        return new $repositoryClass($this->em, $metadata);
    }

    public function objectIdentifierToResource(array $data, string $expectedClass = null): ResourceInterface
    {
        if (!isset($data['id'])) {
            throw new InvalidArgumentException('Object identifier missing "id" field.');
        }

        if (!isset($data['type'])) {
            throw new InvalidArgumentException('Object identifier missing "type" field.');
        }

        $class = $this->classByResourceKey($data['type']);

        if (!is_null($expectedClass)) {
            $expectedType = static::resourceInterfaceKey($expectedClass);

            if ($data['type'] !== $expectedType) {
                throw new InvalidArgumentException(sprintf(
                    'Invalid type "%s" expecting "%s".', $data['type'], $expectedType
                ));
            }
        }

        $resource = $this->repositoryByClass($class)->find($data['id']);

        if (is_null($resource)) {
            throw new InvalidArgumentException(sprintf(
                'Resource not found by object identifier "%s(%s)"',
                $data['type'], $data['id']
            ));
        }

        return $resource;
    }

    public function transformerByResourceKey(string $resourceKey): AbstractTransformer
    {
        $class = $this->classByResourceKey($resourceKey);
        return call_user_func(sprintf('%s::%s', $class, static::RESOURCE_TRANSFORMER_METHOD));
    }

    private function resourceRepositoryClass(ClassMetadata $metadata): string
    {
        if (is_subclass_of($metadata->customRepositoryClassName, ResourceRepository::class)) {
            return $metadata->customRepositoryClassName;
        }

        return ResourceRepository::class;
    }
}
