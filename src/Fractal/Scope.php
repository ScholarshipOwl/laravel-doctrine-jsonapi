<?php

namespace Sowl\JsonApi\Fractal;

use League\Fractal\Manager;
use Sowl\JsonApi\AbstractTransformer;

/**
 * The Scope class extends the League\Fractal\Scope class and adds custom behavior to support additional functionality
 * when dealing with resources and transformations. In particular, it allows you to manage and process metadata
 * associated with a specific resource type.
 *
 * The Scope class can be used to enhance the functionality of the Fractal library by allowing you to manage metasets
 * and include them in the transformed output. This can be helpful in cases where you want to include additional
 * metadata for a specific resource type or when you want to manipulate the output of a transformation further.
 */
class Scope extends \League\Fractal\Scope
{
    protected Manager $manager;

    /**
     * Returns an array of the requested metasets for the current resource type or null if none are set.
     * It uses the getManager() method to retrieve the Fractal manager and calls the getMetaset() method with
     * the resource type.
     */
    public function getRequestedMetasets(): ?array
    {
        return $this->getManager()->getMetaset($this->getResourceType());
    }

    /**
     * Takes an instance of AbstractTransformer and the data to be transformed.
     * It calls the parent's fireTransformer method to get the transformed data and included data.
     *
     * If the transformer has available metasets, it processes them by calling the processMetasets() method on the
     * transformer with the current scope and data. If the processed metasets are not null, it merges them with the
     * existing transformed data's meta field, ensuring that the metadata is included in the final transformed output.
     *
     * It returns the transformed data and included data as an array.
     *
     * @param AbstractTransformer $transformer
     * @param mixed $data
     * @return array
     */
    protected function fireTransformer($transformer, $data): array
    {
        list($transformedData, $includedData) = parent::fireTransformer($transformer, $data);

        if (!empty($transformer->getAvailableMetas())) {
            if (null !== ($meta = $transformer->processMetasets($this, $data))) {
                $transformedData['meta'] = (object) array_merge(
                    $transformedData['meta'] ?? [], $meta
                );
            }
        }

        return [$transformedData, $includedData];
    }

    /**
     * Returns the Fractal manager instance associated with the current scope.
     */
    public function getManager(): Fractal
    {
        return $this->manager;
    }
}
