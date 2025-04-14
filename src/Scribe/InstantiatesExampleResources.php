<?php

declare(strict_types=1);

namespace Sowl\JsonApi\Scribe;

use Knuckles\Scribe\Tools\ConsoleOutputUtils as c;
use Knuckles\Scribe\Tools\DocumentationConfig;
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
     * @return T
     */
    protected function instantiateExampleResource(string $resourceClass): ?ResourceInterface
    {
        $strategies = [
            'doctrineFactoryMake' => fn() => $this->getExampleResourceFromDoctrineFactoryMake($resourceClass),
            'doctrineFactoryCreate' => fn() => $this->getExampleResourceFromDoctrineFactoryCreate($resourceClass),
            'doctrineRepositoryFirst' => fn() => $this->getExampleResourceFromDatabaseFirst($resourceClass),
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
                $model = $strategies[$strategy]();
                if ($model) {
                    return $model;
                }
            } catch (Throwable $e) {
                c::warn("Couldn't get example resource for {$resourceClass} via $strategy.");
                e::dumpExceptionIfVerbose($e, true);
            }
        }

        return new $resourceClass;
    }

    /**
     * Strategy: Get the first model instance from the database.
     */
    protected function getExampleResourceFromDatabaseFirst(string $resourceClass): ?object
    {
        $entityManager = $this->rm()->em();
        // TODO: investigate on possible different fix.
        $entityManager->clear();
        $repository = $entityManager->getRepository($resourceClass);
        return $repository->findOneBy([]); // Find the first available entity
    }

    /**
     * Strategy: Use the Doctrine testing factory `make` method.
     */
    protected function getExampleResourceFromDoctrineFactoryMake(string $resourceClass): ?object
    {
        $factory = $this->factory();
        return $factory->of($resourceClass)->make();
    }

    /**
     * Strategy: Use the Doctrine testing factory `create` method.
     */
    protected function getExampleResourceFromDoctrineFactoryCreate(string $resourceClass): ?object
    {
        $factory = $this->factory();
        // Note: This interacts with the database during factory creation
        return $factory->of($resourceClass)->create();
    }

    protected function factory(): Factory
    {
        return app(Factory::class);
    }
}
