<?php

namespace Sowl\JsonApi\Testing;

/**
 * Refresh the database before each test.
 *
 * This trait uses Doctrine's built-in migration features to migrate the database
 * up and down before each test. This is useful for testing code that relies on a fresh database schema.
 *
 * To use this trait with Laravel, you must install the `laravel-doctrine/migrations` package
 * using composer.
 *
 * @see https://github.com/laravel-doctrine/migrations
 */
trait DoctrineRefreshDatabase
{
    /**
     * Define hooks to migrate the database before and after each test.
     */
    public function refreshDoctrineDatabase(): void
    {
        $this->beforeRefreshingDoctrineDatabase();

        $this->refreshDoctrineTestDatabase();

        $this->afterRefreshingDoctrineDatabase();
    }

    protected function doctrineRunMigrations(): bool
    {
        return true;
    }

    /**
     * Refresh the Doctrine test database schema.
     */
    protected function refreshDoctrineTestDatabase(): void
    {
        $em = $this->app->make('em');

        if ($this->doctrineRunMigrations()) {
            // Run Doctrine migrations using the built-in artisan() method
            // Making sure we run migrations before starting transaction,
            // so after rollback we keep migrations up to date and not running them again.
            $this->artisan('doctrine:migrations:migrate', [
                '--no-interaction' => true,
                '--quiet' => true,
            ]);

            // Run any hooks after migrations but before transaction start
            $this->seedDatabase();
        }

        // Start a Doctrine transaction before refreshing
        $em->beginTransaction();

        // Rollback transaction after test
        $this->beforeApplicationDestroyed(function () use ($em) {
            if ($em->getConnection()->isTransactionActive()) {
                $em->rollBack();
            }
        });
    }

    /**
     * Hook before refreshing Doctrine database.
     */
    protected function beforeRefreshingDoctrineDatabase(): void
    {
        // Place for custom logic before refresh (optional override)
    }

    /**
     * Hook after refreshing Doctrine database.
     */
    protected function afterRefreshingDoctrineDatabase(): void
    {
        // Place for custom logic after refresh (optional override)
    }

    /**
     * Hook after migrations but before transaction start.
     */
    protected function seedDatabase(): void
    {
        // Place for custom logic after migrations but before transaction start (optional override)
    }
}
