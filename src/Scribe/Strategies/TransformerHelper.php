<?php

namespace Sowl\JsonApi\Scribe\Strategies;

use Knuckles\Scribe\Tools\ConsoleOutputUtils as c;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Sowl\JsonApi\Fractal\Fractal;
use Sowl\JsonApi\Fractal\FractalOptions;
use Sowl\JsonApi\Fractal\RelationshipsTransformer;

trait TransformerHelper
{
    use InstantiatesExampleResources;

    public function fetchTransformedResponse(
        string $resourceType,
        ?FractalOptions $fractalOptions = null,
        bool $isRelationship = false
    ): ?array {
        try {
            $resourceClass = $this->rm()->classByResourceType($resourceType);
            $transformer = $this->rm()->transformerByResourceType($resourceType);

            if ($isRelationship) {
                $transformer = new RelationshipsTransformer($transformer);
            }

            $resource = $this->instantiateExampleResource($resourceClass);

            return (new Fractal($fractalOptions ?: new FractalOptions()))
                ->createData(new Item($resource, $transformer, $resource->getResourceType()))
                ->toArray();
        } catch (\Throwable $e) {
            c::warn(
                "Failed to generate response example for resource type: [$resourceType] "
                . "because of: {$e->getMessage()}"
            );

            return null;
        }
    }

    public function fetchTransformedCollectionResponse(
        string $resourceType,
        ?FractalOptions $fractalOptions = null,
        int $pageNumber = 1,
        int $pageSize = 3,
        bool $isRelationship = false
    ): ?array {
        try {
            $resourceClass = $this->rm()->classByResourceType($resourceType);
            $transformer = $this->rm()->transformerByResourceType($resourceType);

            if ($isRelationship) {
                $transformer = new RelationshipsTransformer($transformer);
            }

            $resources = $this->instantiateExampleResource($resourceClass, $pageSize);

            $collection = new Collection($resources, $transformer, $resourceType);
            $collection->setPaginator(new ExamplePaginator(
                // TODO: Properly implement the path
                path: $resourceType,
                currentPage: $pageNumber,
                perPage: $pageSize
            ));

            return (new Fractal($fractalOptions ?: new FractalOptions()))
                ->createData($collection)
                ->toArray();
        } catch (\Throwable $e) {
            c::warn(
                "Failed to generate response example for resource type: [$resourceType] "
                . "because of: {$e->getMessage()}"
            );

            return null;
        }
    }
}
