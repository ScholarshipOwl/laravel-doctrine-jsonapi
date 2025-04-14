<?php

declare(strict_types=1);

namespace Sowl\JsonApi\Scribe\Strategies;

use Knuckles\Scribe\Tools\DocumentationConfig;
use Knuckles\Scribe\Tools\ConsoleOutputUtils as c;
use Knuckles\Scribe\Tools\ErrorHandlingUtils as e;
use LaravelDoctrine\ORM\Testing\Factory;
use Sowl\JsonApi\ResourceInterface;
use Sowl\JsonApi\ResourceManager;
use Throwable;

trait InstantiatesExampleResources
{
    /**
     * Get the ResourceManager instance.
     * Classes using this trait must implement this method.
     */
    abstract protected function rm(): ResourceManager;

    abstract public function getConfig(): DocumentationConfig;

    /**
     * Attempt to instantiate an example Doctrine entity for documentation using multiple strategies.
     *
     * @template T of ResourceInterface
     * @param class-string<T> $resourceClass The FQCN of the Doctrine entity.
     * @return T|array<T>
     */
    protected function instantiateExampleResource(
        string $resourceClass,
        int $times = 1
    ): mixed
    {
        $strategies = [
            'doctrineFactoryMake' => fn() => $this->getFromDoctrineFactoryMake($resourceClass, $times),
            'doctrineFactoryCreate' => fn() => $this->getFromDoctrineFactoryCreate($resourceClass, $times),
            'doctrineRepositoryFirst' => fn() => $this->getFromDatabaseFirst($resourceClass, $times),
        ];

        $configuredStrategies = array_intersect(
            $this->getConfig()->get('examples.models_source', [
                'doctrineFactoryCreate',
                'doctrineFactoryMake',
                'doctrineRepositoryFirst',
            ]),
            array_keys($strategies)
        );


        foreach ($configuredStrategies as $strategy) {
            try {
                if ($model = $this->wrapInTransaction(fn () => $strategies[$strategy]())) {
                    return $model;
                }
            } catch (Throwable $e) {
                c::warn(sprintf(
                    "Failed to instantiate resource %s via %s: %s",
                    $resourceClass,
                    $strategy,
                    $e->getMessage()
                ));
                e::dumpExceptionIfVerbose($e, true);
            }
        }

        return null;
    }

    /**
     * Strategy: Get the first model instance from the database.
     */
    protected function getFromDatabaseFirst(string $resourceClass, int $times): mixed
    {
        $entityManager = $this->rm()->em();
        $repository = $entityManager->getRepository($resourceClass);
        return $times > 1 ? $repository->findOneBy([]) : $repository->findBy([], [], $times);
    }

    /**
     * Strategy: Use the Doctrine testing factory `make` method.
     */
    protected function getFromDoctrineFactoryMake(string $resourceClass, int $times): mixed
    {
        $factory = $this->factory()->of($resourceClass);
        if ($times > 1) {
            $factory = $factory->times($times);
        }

        return $factory->make();
    }

    /**
     * Strategy: Use the Doctrine testing factory `create` method.
     */
    protected function getFromDoctrineFactoryCreate(string $resourceClass, int $times): mixed
    {
        $factory = $this->factory()->of($resourceClass);
        if ($times > 1) {
            $factory = $factory->times($times);
        }

        return $factory->create();
    }

    protected function factory(): Factory
    {
        return app(Factory::class);
    }


    /**
     * Wrap a callback in a database transaction
     *
     * @template T
     * @param callable(): T $callback
     * @return T|null
     */
    private function wrapInTransaction(callable $callback)
    {
        $em = $this->rm()->em();

        try {
            $em->beginTransaction();
            $result = $callback();
            return $result;
        } catch (\Throwable $e) {
            c::warn(sprintf(
                'Fail to instantiate resource %s: %s',
                $this->jsonApiEndpointData->resourceType,
                $e->getMessage()
            ));

            throw $e;
        } finally {
            if ($em->getConnection()->isTransactionActive()) {
                $em->rollback();
            }

            $em->clear();
        }
    }
}
