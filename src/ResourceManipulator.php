<?php

namespace Sowl\JsonApi;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManager;
use Illuminate\Support\Str;
use Sowl\JsonApi\Exceptions\BadRequestException;
use Sowl\JsonApi\Exceptions\JsonApiException;
use Sowl\JsonApi\Relationships\ToManyRelationship;
use Sowl\JsonApi\Relationships\ToOneRelationship;

/**
 * ResourceManipulator class provides method for manipulate resource objects in a JSON:API context.
 */
class ResourceManipulator
{
    public function __construct(
        protected EntityManager $em,
        protected ResourceManager $rm,
    ) {}

    /**
     * Method is used to hydrate a resource object with data from a JSON:API request.
     *
     * The method takes the resource object, an array of data, a pointer (used for error reporting), and
     * a boolean flag to indicate whether to throw an exception if the data is missing.
     *
     * The method uses the hydrateAttributes() and hydrateRelationships() methods to set the
     * attributes and relationships on the resource object.
     */
    public function hydrateResource(
        ResourceInterface $resource,
        array             $data,
        string            $pointer = "/data",
        bool              $throwOnMissing = false,
    ): ResourceInterface
    {
        if ($throwOnMissing && !isset($data['attributes']) && !isset($data['relationships'])) {
            throw (new BadRequestException())
                ->detail('Missing or not array `/data/attributes` or `/data/relationships.', $pointer);
        }

        if (isset($data['attributes']) && is_array($data['attributes'])) {
            $this->hydrateAttributes($resource, $data['attributes'], "$pointer/attributes");
        }

        if (isset($data['relationships']) && is_array($data['relationships'])) {
            $this->hydrateRelationships($resource, $data['relationships'], "$pointer/relationships");
        }

        return $resource;
    }

    /**
     * The method sets the attributes on the resource object using the setProperty() method.
     *
     * Method takes a resource object, an array of attributes from the request data and a pointer that
     * used for error reporting in case of problems.
     */
    public function hydrateAttributes(
        ResourceInterface $resource,
        array             $attributes,
        string            $pointer,
    ): ResourceInterface
    {
        foreach ($attributes as $name => $value) {
            try {
                $this->setProperty($resource, $name, $value);
            } catch (BadRequestException $e) {
                throw BadRequestException::create()
                    ->detail('Unknown attribute.', "$pointer/$name")
                    ->errorsFromException($e);
            }
        }

        return $resource;
    }

    /**
     * The method sets the relationships on the resource object using the setProperty() method in case to-one
     * relationship and hydrateToManyRelation() used for setting to-many.
     *
     * Method takes a resource object, an array of relationships from request data, and a pointer for error reporting
     * and pointing to the problematic place.
     */
    public function hydrateRelationships(
        ResourceInterface $resource,
        array             $relationships,
        string            $pointer,
    ): ResourceInterface
    {
        foreach ($relationships as $name => $data) {
            $relationPointer = "$pointer/$name";

            if (is_null($relationship = $resource::relationships()->get($name))) {
                throw BadRequestException::create(sprintf('Unknown relationship "%s".', $name))
                    ->detail('Unknown relationship.', $relationPointer);
            }

            if (!is_array($data) || !array_key_exists('data', $data)) {
                throw (new BadRequestException('Wrong data.'))
                    ->detail('Data is missing or not an array.', $relationPointer);
            }

            $relationData = $data['data'];

            if ($relationship instanceof ToOneRelationship) {
                $this->hydrateToOneRelation($resource, $relationship, $relationData, $relationPointer);
            }

            if ($relationship instanceof ToManyRelationship) {
                $this->hydrateToManyRelation($resource, $relationship, $relationData, $relationPointer);
            }
        }

        return $resource;
    }

    /**
     * Handles the hydration of to-one relationships.
     */
    public function hydrateToOneRelation(
        ResourceInterface $resource,
        ToOneRelationship $relationship,
        mixed             $data,
        string            $pointer,
    ): ResourceInterface
    {
        $relationshipObject = $this->objectIdentifierToResource($data, $pointer, $relationship->class());

        $this->setProperty($resource, $relationship->property(), $relationshipObject);

        return $resource;
    }

    /**
     * Handles the hydration of to-many relationships.
     */
    public function hydrateToManyRelation(
        ResourceInterface  $resource,
        ToManyRelationship $relationship,
        mixed              $data,
        string             $pointer
    ): ResourceInterface
    {
        if (!is_array($data)) {
            throw (new BadRequestException())
                ->detail('Data is not an array', $pointer);
        }

        $collection = new ArrayCollection(array_map(
            fn ($item, $index) => $this->objectIdentifierToResource(
                $item, "$pointer/$index", $relationship->class()
            ),
            $data,
            array_keys($data)
        ));

        $this->replaceResourceCollection($resource, $relationship->property(), $collection);

        return $resource;
    }

    /**
     * Replaces a resource collection with a new one.
     * It takes a resource object, the name of the property to replace, and the new collection.
     * It will remove or add new relationships, making the changes optimized.
     */
    public function replaceResourceCollection(
        ResourceInterface $resource,
        string            $property,
        Collection        $replace,
    ): ResourceInterface
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

        return $resource;
    }

    /**
     * Converts an object identifier to a ResourceInterface object, if null provided null will be returned.
     */
    public function objectIdentifierToResource(
        mixed  $data,
        string $pointer,
        string $expectedClass = null
    ): ?ResourceInterface
    {
        if (is_null($data)) {
            return null;
        }

        try {
            if (is_array($data)) {
                return $this->rm->objectIdentifierToResource($data, $expectedClass);
            }
        } catch (\InvalidArgumentException $e) {
            throw BadRequestException::create()
                ->error(400, ['pointer' => $pointer], $e->getMessage());
        }

        throw JsonApiException::create('Wrong primary data provided.', 400)
            ->error(400, ['pointer' => $pointer], 'Wrong primary data provided.');
    }

    /**
     * Gets the value of a specified property from a resource object.
     */
    public function getProperty(ResourceInterface $resource, string $property): mixed
    {
        $getter = 'get' . ucfirst($property);

        if (!method_exists($resource, $getter)) {
            throw (new BadRequestException())->error(
                    'missing-getter',
                    ['getter' => sprintf('%s::%s', ClassUtils::getClass($resource), $getter)],
                    'Missing property getter.'
                );
        }

        return $resource->$getter();
    }

    /**
     * Sets the value of a specified property on a resource object.
     */
    public function setProperty(ResourceInterface $resource, string $property, mixed $value): ResourceInterface
    {
        $setter = 'set' . ucfirst($property);

        if (!method_exists($resource, $setter)) {
            throw (new BadRequestException())->error(
                400,
                ['setter' => sprintf('%s::%s', ClassUtils::getClass($resource), $setter)],
                'Missing property setter.'
            );
        }

        $resource->$setter($value);

        return $resource;
    }

    /**
     * Adds a relation item to the resource object's property.
     */
    public function addRelationItem(ResourceInterface $resource, string $property, mixed $item): ResourceInterface
    {
        $adder = 'add' . ucfirst(Str::singular($property));

        if (!method_exists($resource, $adder)) {
            throw (new BadRequestException())->error(
                'missing-adder',
                ['adder' => sprintf('%s::%s', ClassUtils::getClass($resource), $adder)],
                'Missing collection adder.'
            );
        }

        $resource->$adder($item);

        return $resource;
    }

    /**
     * Removes a relation item from the resource object's property.
     */
    public function removeRelationItem(ResourceInterface $resource, string $property, mixed $item): ResourceInterface
    {
        $remover = 'remove' . ucfirst(Str::singular($property));

        if (!method_exists($resource, $remover)) {
            throw (new BadRequestException())->error(
                'missing-remover',
                ['remover' => sprintf('%s::%s', ClassUtils::getClass($resource), $remover)],
                'Missing collection remover.'
            );
        }

        $resource->$remover($item);

        return $resource;
    }
}
