<?php

namespace Sowl\JsonApi\Scribe;

use Illuminate\Support\ServiceProvider;
use Sowl\JsonApi\ResourceManager;

/**
 * Service provider for JSON:API documentation strategies for Scribe
 */
class ScribeServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Register the entity extractor
        $this->app->bind(DoctrineEntityExtractor::class, function ($app) {
            return new DoctrineEntityExtractor(
                $app['em'],
                $app[ResourceManager::class]
            );
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Nothing to boot
    }
}