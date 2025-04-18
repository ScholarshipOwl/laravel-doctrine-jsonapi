<?php

namespace Sowl\JsonApi;

use Doctrine\Persistence\Mapping\ClassMetadata;
use InvalidArgumentException;
use LaravelDoctrine\ORM\IlluminateRegistry;
use Sowl\JsonApi\Relationships\RelationshipsCollection;
use Sowl\JsonApi\Relationships\ToOneRelationship;
use UnexpectedValueException;

/**
 * ResourceManager is responsible for managing and accessing resources in a JSON:API.
 * It provides various methods for registering and accessing resources, repositories, transformers, and relationships.
 *
 * The class depends on EntityManager object and an optional array of resource classes.
 * If resource classes are provided, it registers them using the "registerResource" method.
 */
class ResourceManager
{
    /** @var array<string, class-string<ResourceInterface>>  */
    protected array $resources;

    public function __construct(protected IlluminateRegistry $registry, array $resources = [])
    {
        foreach ($resources as $resource) {
            $this->registerResource($resource);
        }
    }

    public function registry(): IlluminateRegistry
    {
        return $this->registry;
    }

    /**
     * Returns an array of all registered resources.
     * @return array<string, class-string<ResourceInterface>>
     */
    public function resources(): array
    {
        return $this->resources;
    }

    /**
     * Method takes a string that represents the name of a class and verifies if it implements the ResourceInterface.
     * It throws an exception if the class is not found or does not implement the interface.
     */
    public static function verifyResourceInterface(string $class): void
    {
        if (!class_exists($class)) {
            throw new InvalidArgumentException(sprintf('%s - is not a class', $class));
        }

        if (!isset(class_implements($class)[ResourceInterface::class])) {
            throw new UnexpectedValueException(sprintf(
                '%s - not implements %s',
                $class,
                ResourceInterface::class
            ));
        }
    }

    /**
     * Register new resource class in the manager.
     * Method takes a string that represents the name of a resource class and registers it with the resource type.
     */
    public function registerResource(string $class): static
    {
        $resourceType = static::resourceType($class);
        $this->resources[$resourceType] = $class;

        return $this;
    }

    /**
     * method takes a string that represents the name of a resource class and returns the resource type
     * by calling the "getResourceType" static method on the resource class.
     */
    public static function resourceType(string $class): string
    {
        static::verifyResourceInterface($class);
        return call_user_func("$class::getResourceType");
    }

    /**
     * Method takes a string that represents a resource type and checks if it is registered with the ResourceManager.
     */
    public function hasResourceType(string $resourceType): bool
    {
        return isset($this->resources[$resourceType]);
    }

    /**
     * Method takes a string that represents a resource type and returns the name of the corresponding resource class.
     * @throws InvalidArgumentException
     */
    public function classByResourceType(string $resourceType): string
    {
        if (!$this->hasResourceType($resourceType)) {
            throw new InvalidArgumentException(sprintf('%s - is not registered resource key', $resourceType));
        }

        return $this->resources[$resourceType];
    }

    /**
     * method takes a string that represents the name of a resource class and returns the
     * corresponding ResourceRepository. In case for resource class have defined custom repository
     * that inherits ResourceRepository that repository will be returned.
     */
    public function repositoryByClass(string $class): ResourceRepository
    {
        $em = $this->registry->getManagerForClass($class);
        $metadata = $em->getClassMetadata($class);
        $repositoryClass = $this->resourceRepositoryClass($metadata);

        return new $repositoryClass($em, $metadata);
    }

    /**
     * Method takes an array that represents the object identifier and an optional string that represents
     * the expected class of the resource. It returns the corresponding resource by finding it
     * in the repository using the object identifier.
     *
     * @param array{type: string, id: string} $data Object identifier object
     * @link https://jsonapi.org/format/#document-resource-object-identification
     */
    public function objectIdentifierToResource(array $data, string $expectedClass = null): ResourceInterface
    {
        if (!isset($data['id'])) {
            throw new InvalidArgumentException('Object identifier missing "id" field.');
        }

        if (!isset($data['type'])) {
            throw new InvalidArgumentException('Object identifier missing "type" field.');
        }

        $class = $this->classByResourceType($data['type']);

        if (!is_null($expectedClass)) {
            $expectedType = static::resourceType($expectedClass);

            if ($data['type'] !== $expectedType) {
                throw new InvalidArgumentException(sprintf(
                    'Invalid type "%s" expecting "%s".',
                    $data['type'],
                    $expectedType
                ));
            }
        }

        $resource = $this->repositoryByClass($class)->find($data['id']);

        if (is_null($resource)) {
            throw new InvalidArgumentException(sprintf(
                'Resource not found by object identifier "%s(%s)"',
                $data['type'],
                $data['id']
            ));
        }

        return $resource;
    }

    /**
     * Method takes a string that represents a resource type and returns the corresponding transformer by calling
     * the "transformer" static method on the resource class.
     */
    public function transformerByResourceType(string $resourceType): AbstractTransformer
    {
        $class = $this->classByResourceType($resourceType);
        return call_user_func("$class::transformer");
    }

    /**
     * Method takes a string that represents a resource type and returns the corresponding
     * RelationshipsCollection by calling the "relationships" static method on the resource class.
     */
    public function relationshipsByResourceType(string $resourceType): RelationshipsCollection
    {
        $class = $this->classByResourceType($resourceType);
        return call_user_func("$class::relationships");
    }


    /**
     * Method takes a string that represents the name of a resource class and returns the corresponding
     * RelationshipsCollection by calling the "relationships" static method on the resource class.
     */
    public function relationshipsByClass(string $class): RelationshipsCollection
    {
        static::verifyResourceInterface($class);
        return call_user_func("$class::relationships");
    }

    /**
     * Method takes a ClassMetadata object and returns the name of the corresponding ResourceRepository class.
     * It checks if the custom repository class for the entity extends the ResourceRepository class and
     * returns it if it does. Otherwise, it returns the ResourceRepository class.
     */
    private function resourceRepositoryClass(ClassMetadata $metadata): string
    {
        if (is_subclass_of($metadata->customRepositoryClassName, ResourceRepository::class)) {
            return $metadata->customRepositoryClassName;
        }

        return ResourceRepository::class;
    }
}
