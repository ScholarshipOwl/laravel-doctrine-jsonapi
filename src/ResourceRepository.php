<?php

namespace Sowl\JsonApi;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use Sowl\JsonApi\Exceptions\BadRequestException;
use Sowl\JsonApi\Exceptions\ResourceNotFoundException;

use InvalidArgumentException;
use Sowl\JsonApi\Exceptions\JsonApiException;
use UnexpectedValueException;

class ResourceRepository extends EntityRepository
{
    const RESOURCE_TYPE_METHOD = 'getResourceKey';
    const RESOURCE_TRANSFORMER_METHOD = 'transformer';

    protected ?string $alias = null;

    public function __construct(EntityManagerInterface $em, ClassMetadata $class)
    {
        parent::__construct($em, $class);
        ResourceManager::verifyResourceInterface($this->getClassName());
    }

    public static function create(string $class): self
    {
        return new static(app('em'), app('em')->getClassMetadata($class));
    }

    public function transformer(): AbstractTransformer
    {
        return call_user_func(sprintf('%s::%s', $this->getClassName(), static::RESOURCE_TRANSFORMER_METHOD));
    }

    public function em(): EntityManager
    {
        return parent::getEntityManager();
    }

    public function metadata(): ClassMetadata
    {
        return parent::getClassMetadata();
    }

    public function resourceQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder($this->alias());
    }

    public function getResourceKey(): string
    {
        return call_user_func(sprintf('%s::%s', $this->getClassName(), static::RESOURCE_TYPE_METHOD));
    }

    /**
     * Base root alias for queries.
     */
    public function alias(): string
    {
        if ($this->alias === null) {
            $shortName = $this->getClassMetadata()->getReflectionClass()->getShortName();
            $this->alias = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $shortName));
        }

        return $this->alias;
    }

    public function findById(string|int $id): ResourceInterface
    {
        if (null === ($entity = $this->find($id))) {
            throw new ResourceNotFoundException($id, $this->getResourceKey());
        }

        return $entity;
    }

    public function getReference(string|int $id): ResourceInterface
    {
        return $this->em()->getReference($this->getClassName(), $id);
    }

    public function findByObjectIdentifier(array $data, string $scope = "/data"): ResourceInterface
    {
        if (!isset($data['id']) || !isset($data['type'])) {
            throw BadRequestException::create('Relation item without identifiers.')
                ->error(400, ['pointer' => $scope], 'Relation item without `id` or `type`.');
        }

        if ($this->getResourceKey() !== $data['type']) {
            throw BadRequestException::create('Wrong type provided.')
                ->error(400, ['pointer' => $scope], 'Type is not in sync with relation.');
        }

        if (null === ($resource = $this->find($data['id']))) {
            throw JsonApiException::create('Resource is not found', 404)
                ->error(404, ['pointer' => $scope], sprintf(
                    'Resource not found by primary data %s(%s)',
                    $data['type'], $data['id']
                ));
        }

        return $resource;
    }
}
