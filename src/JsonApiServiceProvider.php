<?php

namespace Sowl\JsonApi;

use Illuminate\Contracts\Routing\ResponseFactory as ResponseFactoryContract;
use Illuminate\Contracts\View\Factory as ViewFactoryContract;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Knuckles\Camel\Extraction\ExtractedEndpointData;
use Knuckles\Scribe\Scribe;
use Sowl\JsonApi\Scribe\DeepObjectQueryHelper;

/**
 * JsonApi package Laravel service provider.
 */
class JsonApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->bootConfig();
    }

    public function bootConfig(): void
    {
        $this->registerConfig();
        $this->registerRoutes();

        $this->configureTranslations();
    }

    protected function registerConfig(): void
    {
        $this->publishes([
            __DIR__ . '/../config/jsonapi.php' => config_path('jsonapi.php'),
        ], 'jsonapi-config');
    }

    protected function registerRoutes(): void
    {
        $this->publishes([
            __DIR__ . '/../routes/jsonapi.php' => base_path('routes/jsonapi.php'),
        ], 'jsonapi-routes');
    }

    protected function configureTranslations(): void
    {
        // Register Scribe strategies translations
        if ($this->isScribeInstalled()) {
            $this->publishes([
                __DIR__ . '/../lang' => $this->app->langPath('jsonapi'),
            ], 'jsonapi-scribe-translations');

            $this->loadTranslationsFrom($this->app->langPath('jsonapi'), 'jsonapi');
            $this->loadTranslationsFrom(realpath(__DIR__ . '/../lang'), 'jsonapi');
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/jsonapi.php', 'jsonapi');

        $this->registerRequest();
        $this->registerResponseFactory();
        $this->registerResourceManager();
        $this->registerResourceManipulator();
        $this->registerDocumentation();
    }

    public function registerRequest(): void
    {
        $this->app->resolving(Request::class, function (Request $request, Application $app) {
            $request = Request::createFrom($app['request'], $request);
            $request->setContainer($app);
        });

        // We will assign to the alias "request.jsonapi" all the resolved request
        $this->app->afterResolving(Request::class, function (Request $request) {
            $this->app->instance('request.jsonapi', $request);
        });
    }

    public function registerResponseFactory(): void
    {
        $this->app->singleton(ResponseFactoryContract::class, function (Application $app) {
            return new ResponseFactory(
                $app[ViewFactoryContract::class],
                $app['redirect']
            );
        });
    }

    public function registerResourceManager(): void
    {
        $this->app->singleton(ResourceManager::class, function (Application $app) {
            return new ResourceManager($app['em'], config('jsonapi.resources'));
        });
    }

    public function registerResourceManipulator(): void
    {
        $this->app->singleton(ResourceManipulator::class, function (Application $app) {
            return new ResourceManipulator($app['em'], $app[ResourceManager::class]);
        });
    }

    /**
     * Register documentation services if Scribe is available
     *
     * @return void
     */
    protected function registerDocumentation(): void
    {
        if ($this->isScribeInstalled()) {
            /**
             * Fix the Scribe beforeResponseCall hook.
             *
             * We generate deepObject param names that is not working in the requests.
             * Example: `fields[pageComments]=content` instead of `["fields"]["pageComments"] = "content"`
             *
             * We need to convert deepObjects into the php array format.
             *
             * @see \Knuckles\Scribe\Extracting\Strategies\Responses\ResponseCalls
             */
            Scribe::beforeResponseCall(function (
                \Illuminate\Http\Request $request,
                ExtractedEndpointData $endpointData
            ) {
                $newQuery = DeepObjectQueryHelper::convert($request->query->all());
                $request->query->replace($newQuery);
            });
        }
    }

    private function isScribeInstalled(): bool
    {
        return class_exists('Knuckles\Scribe\ScribeServiceProvider');
    }
}
