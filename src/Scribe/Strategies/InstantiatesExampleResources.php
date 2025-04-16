<?php

declare(strict_types=1);

namespace Sowl\JsonApi\Scribe\Strategies;

use Doctrine\ORM\EntityManager;
use Knuckles\Scribe\Tools\DocumentationConfig;
use Knuckles\Scribe\Tools\ConsoleOutputUtils as c;
use Knuckles\Scribe\Tools\ErrorHandlingUtils as e;
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
                $em = $this->rm()->registry()->getManagerForClass($resourceClass);
                if ($model = $this->wrapInTransaction($em, fn () => $strategies[$strategy]())) {
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
        $em = $this->rm()->registry()->getManagerForClass($resourceClass);
        $repository = $em->getRepository($resourceClass);
        return $times > 1 ? $repository->findBy([], [], $times) : $repository->findOneBy([]);
    }

    /**
     * Strategy: Use the Doctrine testing factory `make` method.
     */
    protected function getFromDoctrineFactoryMake(string $resourceClass, int $times): mixed
    {
        $factory = $times > 1 ? entity($resourceClass, $times) : entity($resourceClass);

        return $factory->make();
    }

    /**
     * Strategy: Use the Doctrine testing factory `create` method.
     */
    protected function getFromDoctrineFactoryCreate(string $resourceClass, int $times): mixed
    {
        $factory = $times > 1 ? entity($resourceClass, $times) : entity($resourceClass);

        return $factory->create();
    }

    /**
     * Wrap a callback in a database transaction
     *
     * @template T
     * @param callable(): T $callback
     * @return T|null
     */
    private function wrapInTransaction(EntityManager $em, callable $callback)
    {
        try {
            $em->beginTransaction();
            $result = $callback();
            return $result;
        } catch (\Throwable $e) {
            c::warn(sprintf(
                'Failed in transaction wrapper: %s',
                $e->getMessage()
            ));

            throw $e;
        } finally {
            if ($em->getConnection()->isTransactionActive()) {
                $em->rollback();
            }
        }
    }
}
