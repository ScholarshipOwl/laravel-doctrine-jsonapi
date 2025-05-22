<?php

namespace Sowl\JsonApi\Scribe\Strategies;

use Doctrine\ORM\EntityManager;
use Illuminate\Contracts\View\Factory as ViewFactoryContract;
use Knuckles\Scribe\Tools\ConsoleOutputUtils as c;
use LaravelDoctrine\ORM\Testing\Factory;
use Sowl\JsonApi\Relationships\ToManyRelationship;
use Sowl\JsonApi\Relationships\ToOneRelationship;
use Sowl\JsonApi\Request;
use Sowl\JsonApi\ResourceInterface;
use Sowl\JsonApi\ResourceManipulator;
use Sowl\JsonApi\ResponseFactory;

trait ResponseGenerationHelper
{
    protected function response(): ResponseFactory
    {
        return new ResponseFactory(
            app(ViewFactoryContract::class),
            app('redirect'),
            // TODO: Implement proper request creation.
            Request::create('/')
        );
    }

    protected function factory(): Factory
    {
        return app(Factory::class);
    }

    protected function em(): EntityManager
    {
        return $this->rm()->em();
    }

    protected function manipulator(): ResourceManipulator
    {
        return app(ResourceManipulator::class);
    }

    /**
     * Generate a single resource response
     */
    public function generateSingleContent(string $resourceType): ?array
    {
        return $this->wrapInTransaction(function () use ($resourceType) {
            $resourceClass = $this->rm()->classByResourceType($resourceType);
            $entity = ResponseGenerator::instance()->createSingleResource($resourceClass);
            $response = $this->response()->item($entity);

            return $response->original;
        });
    }

    /**
     * Generate a collection response
     */
    public function generateCollectionContent(string $resourceType): ?array
    {
        return $this->wrapInTransaction(function () use ($resourceType) {
            $resourceClass = $this->rm()->classByResourceType($resourceType);
            $transformer = $this->rm()->transformerByResourceType($resourceType);
            $resources = [
                ResponseGenerator::instance()->createSingleResource($resourceClass),
                ResponseGenerator::instance()->createSingleResource($resourceClass),
            ];

            $response = $this->response()->collection($resources, $resourceType, $transformer);

            return $response->original;
        });
    }

    /**
     * Generate a relationships response
     */
    public function generateRelationshipsContent(string $resourceType): ?array
    {
        return $this->wrapInTransaction(function () use ($resourceType) {
            $resourceClass = $this->rm()->classByResourceType($resourceType);
            $relationshipName = $this->jsonApiEndpointData->relationshipName;
            $isRelationships = $this->jsonApiEndpointData->isRelationships;

            $resource = ResponseGenerator::instance()->createSingleResource($resourceClass);
            $relationship = $this->rm()->relationshipsByClass($resourceClass)->get($relationshipName);

            if (! $relationship) {
                throw new \InvalidArgumentException(
                    "Relationship $relationshipName on resource $resourceClass does not exist"
                );
            }

            $value = $this->manipulator()->getRelationshipValue($resource, $relationship);

            if ($relationship instanceof ToOneRelationship) {
                if ($value) {
                    return $this->response()->item($value, relationship: $isRelationships)->original;
                }

                return $this->response()->null()->original;
            }

            if ($relationship instanceof ToManyRelationship) {
                $response = $this->response()->collection(
                    $value,
                    $resourceType,
                    $relationship->transformer(),
                    relationship: $isRelationships
                );

                return $response->original;
            }

            return ['data' => null];
        });
    }

    /**
     * Wrap a callback in a database transaction
     *
     * @template T
     *
     * @param  callable(): T  $callback
     * @return T|null
     */
    private function wrapInTransaction(callable $callback)
    {
        try {
            $this->em()->beginTransaction();
            $result = $callback();

            return $result;
        } catch (\Throwable $e) {
            c::warn(sprintf(
                'Failed to generate response for route %s: %s',
                $this->jsonApiEndpointData->endpointData->route->uri,
                $e->getMessage()
            ));

            return null;
        } finally {
            if ($this->em()->getConnection()->isTransactionActive()) {
                $this->em()->rollback();
            }
        }
    }
}
