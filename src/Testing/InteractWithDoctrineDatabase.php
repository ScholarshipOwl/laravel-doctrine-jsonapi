<?php

namespace Sowl\JsonApi\Testing;

use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Database\Connection;
use LaravelDoctrine\ORM\IlluminateRegistry;

/**
 * Allow interacting with the Doctrine database.
 * If you would like to also use assertDatabaseHas in your tests.
 *
 * Sync the PDO instance from all Doctrine managers to Laravel DBAL connections (if using PDO).
 *
 * @see https://laravel-doctrine-orm-official.readthedocs.io/en/latest/troubleshooting.html#the-databasetransactions-trait-is-not-working-for-tests
 */
trait InteractWithDoctrineDatabase
{
    /**
     * Sync the PDO instance from all Doctrine managers to Laravel DBAL connections (if using PDO).
     */
    protected function interactsWithDoctrineDatabase(): void
    {
        /** @var IlluminateRegistry $registry */
        $registry = $this->app->make(IlluminateRegistry::class);

        /** @var EntityManagerInterface $manager */
        foreach ($registry->getManagers() as $managerName => $manager) {
            $connectionName = config('doctrine.managers.'.$managerName.'.connection');
            $connection = $this->app['db']->connection($connectionName);
            $emConnection = $manager->getConnection()->getNativeConnection();

            try {
                if ($connection instanceof Connection && $emConnection instanceof \PDO) {
                    $connection->setPdo($emConnection);
                }
            } catch (\Throwable $e) {
            }
        }
    }
}
