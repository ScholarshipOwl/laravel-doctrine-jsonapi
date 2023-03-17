<?php

namespace Sowl\JsonApi\Fractal;

use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Scope;
use League\Fractal\TransformerAbstract;
use Sowl\JsonApi\AbstractTransformer;
use Sowl\JsonApi\ResourceInterface;

/**
 * Class extends the AbstractTransformer and is designed to be used for /resource/relationships/* actions.
 * In such responses, the attributes should not be included, and this transformer class ensures that only
 * relationship object identifiers is loaded for these types of requests.
 *
 * By using the RelationshipsTransformer class, you can ensure that only relationship data is loaded
 * in /resource/relationships/* actions, which is beneficial for performance and adheres to the JSON:API specification.
 */
class RelationshipsTransformer extends AbstractTransformer
{
    /**
     * A constant representing the relationships attribute key.
     */
    const ATTRIBUTE_RELATIONSHIPS = '$$relationships';

    /**
     * The constructor takes a TransformerAbstract instance as an argument and sets it as the $parent property.
     */
    public function __construct(protected TransformerAbstract $parent) {}

    /**
     * This method takes a ResourceInterface object and returns an array containing only the resource's ID and
     * an indication that this is a relationships' transformer.
     */
    public function transform(ResourceInterface $resource): array
    {
        return [
            'id' => $resource->getId(),
            self::ATTRIBUTE_RELATIONSHIPS => true,
        ];
    }

    /**
     * Returns the available includes of the parent transformer.
     */
    public function getAvailableIncludes(): array
    {
        return $this->parent->getAvailableIncludes();
    }

    /**
     * Returns the default includes of the parent transformer.
     */
    public function getDefaultIncludes(): array
    {
        return $this->parent->getDefaultIncludes();
    }

    /**
     * Returns the current scope of the parent transformer.
     */
    public function getCurrentScope(): ?Scope
    {
        return $this->parent->getCurrentScope();
    }

    /**
     * A magic method that allows method calls to be forwarded to the parent transformer if the method exists.
     */
    public function __call(string $name, array $arguments)
    {
        if (method_exists($this->parent, $name)) {
            return call_user_func_array([$this->parent, $name], $arguments);
        }
    }

    /**
     * A helper method that wraps the given data and transformer in a new Item instance with the
     * custom RelationshipsTransformer.
     */
    protected function item($data, $transformer, $resourceKey = null): Item
    {
        return new Item($data, new static($transformer), $resourceKey);
    }

    /**
     * A helper method that wraps the given data and transformer in a new Collection instance with the
     * custom RelationshipsTransformer.
     */
    protected function collection($data, $transformer, $resourceKey = null): Collection
    {
        return new Collection($data, new static($transformer), $resourceKey);
    }
}
