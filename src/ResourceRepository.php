<?php

namespace Sowl\JsonApi;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Sowl\JsonApi\Exceptions\BadRequestException;
use Sowl\JsonApi\Exceptions\JsonApiException;
use Sowl\JsonApi\Exceptions\NotFoundException;

/**
 * ResourceRepository class is used for managing resources in a JSON:API context.
 *
 * @template TResource of ResourceInterface
 *
 * @extends EntityRepository<TResource>
 */
class ResourceRepository extends EntityRepository
{
    protected ?string $alias = null;

    /**
     * Constructor method that initializes the class with an EntityManagerInterface and a ClassMetadata object.
     * It also verifies if the class implements the ResourceInterface.
     */
    final public function __construct(EntityManagerInterface $em, ClassMetadata $class)
    {
        parent::__construct($em, $class);
        ResourceManager::verifyResourceInterface($this->getClassName());
    }

    /**
     * Static method that creates a new instance of the ResourceRepository with a specified class.
     *
     * @param  class-string<TResource>  $class
     * @return static<TResource>
     */
    public static function create(string $class): static
    {
        if (! $em = app(ManagerRegistry::class)->getManagerForClass($class)) {
            throw new \LogicException('No EntityManager found for class ' . $class);
        }

        /** @phpstan-ignore return.type */
        return new static($em, $em->getClassMetadata($class));
    }

    /**
     * Returns the EntityManager instance.
     */
    public function em(): EntityManagerInterface
    {
        return parent::getEntityManager();
    }

    /**
     * Returns the ClassMetadata of the resource.
     */
    public function metadata(): ClassMetadata
    {
        return parent::getClassMetadata();
    }

    /**
     * Returns an instance of AbstractTransformer class for the resource.
     */
    public function transformer(): AbstractTransformer
    {
        $class = $this->getClassName();

        return call_user_func("$class::transformer");
    }

    /**
     * Returns the resource type of the resource.
     */
    public function getResourceType(): string
    {
        $class = $this->getClassName();

        return call_user_func("$class::getResourceType");
    }

    /**
     * Finds and returns a resource object by its ID.
     * Throws a ResourceNotFoundException if the resource is not found.
     *
     * @return TResource
     */
    public function findById(string|int $id): ResourceInterface
    {
        if (null === ($entity = $this->find($id))) {
            throw NotFoundException::create()->detail(
                sprintf('Resource type "%s" and id "%s" is not found.', $this->getResourceType(), $id)
            );
        }

        return $entity;
    }

    /**
     * Gets a reference to a resource object by its ID.
     * It's not making a queries just creates an object attached to Entity Manager.
     *
     * @return TResource
     */
    public function getReference(string|int $id): ResourceInterface
    {
        return $this->em()->getReference($this->getClassName(), $id);
    }

    /**
     * The root alias for the query builder.
     */
    public function alias(): string
    {
        if ($this->alias === null) {
            $shortName = $this->getClassMetadata()->getReflectionClass()->getShortName();
            $this->alias = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $shortName));
        }

        return $this->alias;
    }

    /**
     * Create a query builder using the alias got from the `alias` method.
     * This query build is used for building queries in the "list" actions.
     * We need "alias" so that creating custom conditions will be easy.
     */
    public function resourceQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder($this->alias());
    }

    /**
     * Finds and returns a resource object using the provided object identifier data.
     * Throws a BadRequestException if the data is incomplete or incorrect,
     * or a JsonApiException if the resource is not found.
     *
     * @return TResource
     */
    public function findByObjectIdentifier(array $data, string $scope = '/data'): ResourceInterface
    {
        if (! isset($data['id']) || ! isset($data['type'])) {
            throw BadRequestException::create('Relation item without identifiers.')
                ->error(400, ['pointer' => $scope], 'Relation item without `id` or `type`.');
        }

        if ($this->getResourceType() !== $data['type']) {
            throw BadRequestException::create('Wrong type provided.')
                ->error(400, ['pointer' => $scope], 'Type is not in sync with relation.');
        }

        if (null === ($resource = $this->find($data['id']))) {
            throw JsonApiException::create('Resource is not found', 404)
                ->error(404, ['pointer' => $scope], sprintf(
                    'Resource not found by primary data %s(%s)',
                    $data['type'],
                    $data['id']
                ));
        }

        return $resource;
    }
}
