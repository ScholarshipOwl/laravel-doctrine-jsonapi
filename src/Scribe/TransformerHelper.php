<?php

namespace Sowl\JsonApi\Scribe;

use Knuckles\Scribe\Extracting\DatabaseTransactionHelpers;
use Knuckles\Scribe\Tools\ConsoleOutputUtils as c;
use Knuckles\Scribe\Tools\DocumentationConfig;
use Knuckles\Scribe\Tools\ErrorHandlingUtils as e;
use League\Fractal\Resource\Item;
use Sowl\JsonApi\Fractal\Fractal;
use Sowl\JsonApi\Fractal\FractalOptions;
use Sowl\JsonApi\ResourceManager;

trait TransformerHelper
{
    use DatabaseTransactionHelpers;
    use InstantiatesExampleResources;

    public function fetchTransformedResponse(string $resourceType, FractalOptions $fractalOptions = null): ?array
    {
        $this->startDbTransaction();

        try {

            $resourceClass = $this->rm()->classByResourceType($resourceType);
            $resource = $this->instantiateExampleResource($resourceClass);
            $transformer = $resource->transformer();
            $fractal = new Fractal($fractalOptions ?: new FractalOptions());

            return $fractal
                ->createData(new Item($resource, $transformer, $resource->getResourceType()))
                ->toArray();

        } catch (\Throwable $e) {
            c::warn("Couldn't transform '{$resourceType}'.");
            e::dumpExceptionIfVerbose($e, true);
        }

        $this->endDbTransaction();
        return null;
    }
}