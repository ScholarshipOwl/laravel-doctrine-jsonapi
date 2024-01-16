<?php

namespace Sowl\JsonApi;

use League\Fractal\Resource\Item;
use League\Fractal\Resource\Primitive;
use League\Fractal\TransformerAbstract;
use Sowl\JsonApi\Fractal\Scope;

/**
 * AbstractTransformer abstract class that must be inherited by resource transformers.
 * It's used by the Fractal package for serialization doctrine entity into the JSON:API response data.
 *
 * @link https://fractal.thephpleague.com/transformers/
 */
abstract class AbstractTransformer extends TransformerAbstract
{
    protected array $availableMetas = [];

    /**
     * Creates a new instance of the transformer.
     */
    public static function create(...$args): static
    {
        return new static(...$args);
    }

    /**
     * Returns the list of available metadata fields for the transformer.
     */
    public function getAvailableMetas(): array
    {
        return $this->availableMetas;
    }

    /**
     * Sets the list of available metadata fields for the transformer.
     */
    public function setAvailableMetas(array $availableMetas): static
    {
        $this->availableMetas = $availableMetas;
        return $this;
    }

    /**
     * Processes the metasets for the given scope and data, and returns the requested metadata.
     * It's includes only metadata requested by the metadata field sets.
     */
    public function processMetasets(Scope $scope, mixed $data): ?array
    {
        $requestedMetaset = $scope->getRequestedMetasets();

        if (is_null($requestedMetaset)) {
            return null;
        }

        $filteredMeta = array_filter($this->getAvailableMetas(), fn (string $meta) => in_array($meta, $requestedMetaset));

        $meta = [];
        foreach ($filteredMeta as $metaField) {
            $methodName = 'meta'.str_replace(
                    ' ',
                    '',
                    ucwords(str_replace(
                        '_',
                        ' ',
                        str_replace(
                            '-',
                            ' ',
                            $metaField
                        )
                    ))
                );

            if (!method_exists($this, $methodName)) {
                throw new \RuntimeException(sprintf(
                    "Method '%s::%s' must be implemented to support '%s'.",
                    static::class,
                    $methodName,
                    $metaField
                ));
            }

            $meta[$metaField] = call_user_func([$this, $methodName], $data);
        }

        return $meta;
    }

    protected function resource(ResourceInterface $resource): Item
    {
        return parent::item($resource, $resource->transformer(), $resource->getResourceType());
    }

    /**
     * Primitive values are not supported in this transformer. This method will throw an exception.
     */
    protected function primitive($data, $transformer = null, $resourceKey = null): Primitive
    {
        throw new \RuntimeException('Primitive values is not supported.');
    }
}
