<?php

namespace Sowl\JsonApi;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Sowl\JsonApi\Exceptions\BadRequestException;
use Sowl\JsonApi\Exceptions\MissingDataException;
use Sowl\JsonApi\Exceptions\MissingDataMembersException;
use Sowl\JsonApi\Exceptions\JsonApiException;
use Sowl\JsonApi\Exceptions\UnknownAttributeException;
use Sowl\JsonApi\Exceptions\UnknownRelationException;

class ResourceManipulator
{
    public function __construct(
        protected EntityManager $em,
        protected ResourceManager $rm,
    ) {}

    public function hydrateResource(
        ResourceInterface $resource,
        array $data,
        string $scope = "/data",
        bool $throwOnMissing = false
    ): ResourceInterface
    {
        if ($throwOnMissing && !isset($data['attributes']) && !isset($data['relationships'])) {
            throw new MissingDataMembersException($scope);
        }

        if (isset($data['attributes']) && is_array($data['attributes'])) {
            $this->hydrateAttributes($resource, $data['attributes'], "$scope/attributes");
        }

        if (isset($data['relationships']) && is_array($data['relationships'])) {
            $this->hydrateRelationships($resource, $data['relationships'], "$scope/relationships");
        }

        return $resource;
    }

    public function hydrateAttributes(ResourceInterface $resource, array $attributes, string $scope): void
    {
        $metadata = $this->em->getClassMetadata(ClassUtils::getClass($resource));

        foreach ($attributes as $name => $value) {
            if (!isset($metadata->reflFields[$name])) {
                throw new UnknownAttributeException("$scope/$name");
            }

            $this->setProperty($resource, $name, $value);
        }
    }

    public function hydrateRelationships(ResourceInterface $resource, array $relationships, string $scope): void
    {
        $metadata = $this->em->getClassMetadata(ClassUtils::getClass($resource));

        foreach ($relationships as $name => $data) {
            if (!isset($metadata->associationMappings[$name])) {
                throw new UnknownRelationException("$scope/$name");
            }

            $mapping = $metadata->associationMappings[$name];

            if (!is_array($data) || !array_key_exists('data', $data)) {
                throw new MissingDataException("$scope/$name");
            }

            // To-One relation update
            if (in_array($mapping['type'], [ClassMetadataInfo::ONE_TO_ONE, ClassMetadataInfo::MANY_TO_ONE])) {
                $this->setProperty($resource, $name,
                    $this->objectIdentifierToResource($mapping['targetEntity'], $data['data'], "$scope/$name")
                );
            }

            // To-Many relation update
            if (in_array($mapping['type'], [ClassMetadataInfo::ONE_TO_MANY, ClassMetadataInfo::MANY_TO_MANY])) {
                $this->hydrateToManyRelation($resource, $name, $mapping['targetEntity'], $data['data'], "$scope/$name");
            }
        }
    }

    public function hydrateToManyRelation(ResourceInterface $resource, string $name, string $targetEntity, mixed $data, string $scope): void
    {
        if (!is_array($data)) {
            throw new MissingDataException($scope);
        }

        $collection = new ArrayCollection(array_map(
            fn ($item, $index) => $this->objectIdentifierToResource($targetEntity, $item, "$scope/$index"),
            $data,
            array_keys($data)
        ));

        $this->replaceResourceCollection($resource, $name, $collection);
    }

    public function replaceResourceCollection(ResourceInterface $resource, string $property, Collection $replace): void
    {
        /** @var Collection $current */
        $current = $this->getProperty($resource, $property);

        // Remove relationships that not exists anymore
        foreach ($current as $currentResource) {
            if (!$replace->contains($currentResource)) {
                $this->removeRelationItem($resource, $property, $currentResource);
            }
        }

        // Add new relationships
        foreach ($replace as $replaceResource) {
            if (!$current->contains($replaceResource)) {
                $this->addRelationItem($resource, $property, $replaceResource);
            }
        }
    }

    public function objectIdentifierToResource(string $class, mixed $data, string $scope): ?ResourceInterface
    {
        if (is_null($data)) {
            return null;
        }

        if (is_object($data)) {
            return $data;
        }

        try {
            if (is_array($data)) {
                return $this->rm->objectIdentifierToResource($data, $class);
            }
        } catch (\InvalidArgumentException $e) {
            throw BadRequestException::create()
                ->error(400, ['pointer' => $scope], $e->getMessage());
        }

        throw JsonApiException::create('Wrong primary data provided.', 400)
            ->error(400, ['pointer' => $scope], 'Wrong primary data provided.');
    }

    public function hasProperty(ResourceInterface $resource, string $property): bool
    {
        $getter = $this->buildGetter($property);

        return method_exists($resource, $getter);
    }

    public function getProperty(ResourceInterface $resource, string $property): mixed
    {
        $getter = $this->buildGetter($property);

        if (!method_exists($resource, $getter)) {
            throw (new BadRequestException())->error(
                    'missing-getter',
                    ['getter' => sprintf('%s::%s', ClassUtils::getClass($resource), $getter)],
                    'Missing field getter.'
                );
        }

        return $resource->$getter();
    }

    public function setProperty(ResourceInterface $resource, string $property, mixed $value): ResourceInterface
    {
        $setter = 'set' . ucfirst($property);

        if (!method_exists($resource, $setter)) {
            throw (new BadRequestException())->error(
                400,
                ['setter' => sprintf('%s::%s', ClassUtils::getClass($resource), $setter)],
                'Missing field setter.'
            );
        }

        return $resource->$setter($value);
    }

    public function addRelationItem(object $resource, string $property, mixed $item): object
    {
        $adder = 'add' . ucfirst($property);

        if (!method_exists($resource, $adder)) {
            throw (new BadRequestException())->error(
                'missing-adder',
                ['adder' => sprintf('%s::%s', ClassUtils::getClass($resource), $adder)],
                'Missing collection adder.'
            );
        }

        return $resource->$adder($item);
    }

    public function removeRelationItem(ResourceInterface $resource, string $property, mixed $item): ResourceInterface
    {
        $remover = 'remove' . ucfirst($property);

        if (!method_exists($resource, $remover)) {
            throw (new BadRequestException())->error(
                'missing-remover',
                ['remover' => sprintf('%s::%s', ClassUtils::getClass($resource), $remover)],
                'Missing collection remover.'
            );
        }

        return $resource->$remover($item);
    }

    protected function buildGetter(string $property): string
    {
        return 'get' . ucfirst($property);
    }
}
