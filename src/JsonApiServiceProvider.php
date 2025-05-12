<?php

namespace Sowl\JsonApi;

use Doctrine\Persistence\Proxy;
use Illuminate\Contracts\Routing\ResponseFactory as ResponseFactoryContract;
use Illuminate\Contracts\View\Factory as ViewFactoryContract;
use Illuminate\Foundation\Application;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
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
        $this->setupPoliciesGuesser();
    }

    public function bootConfig(): void
    {
        $this->registerConfig();
        $this->registerRoutes();

        $this->configureTranslations();
    }

    protected function setupPoliciesGuesser(): void
    {
        Gate::guessPolicyNamesUsing(function (string $class) {
            /**
             * We have a bug with the doctine, as we pass not proxy class of doctrine.
             * The default guesser generates the wrong policy class name.
             *
             * That's why we convert the proxy class to the real class.
             */
            if (in_array(Proxy::class, class_implements($class), true)) {
                $class = get_parent_class($class);
            }

            $classDirname = str_replace('/', '\\', dirname(str_replace('\\', '/', $class)));

            $classDirnameSegments = explode('\\', $classDirname);

            return Arr::wrap(
                Collection::times(
                    count($classDirnameSegments),
                    function ($index) use ($class, $classDirnameSegments) {
                        $classDirname = implode('\\', array_slice($classDirnameSegments, 0, $index));

                        return $classDirname . '\\Policies\\' . class_basename($class) . 'Policy';
                    }
                )->when(
                    str_contains($classDirname, '\\Models\\'),
                    function ($collection) use ($class, $classDirname) {
                        $modelsPoliciesDirname = str_replace('\\Models\\', '\\Policies\\', $classDirname);
                        $modelsPolicy = $modelsPoliciesDirname . '\\' . class_basename($class) . 'Policy';

                        $modelsModelsPoliciesDirname = str_replace('\\Models\\', '\\Models\\Policies\\', $classDirname);
                        $modelsModelsPolicy = $modelsModelsPoliciesDirname . '\\' . class_basename($class) . 'Policy';

                        return $collection->concat([
                            $modelsPolicy,
                            $modelsModelsPolicy,
                        ]);
                    }
                )->reverse()->values()->first(function ($class) {
                    return class_exists($class);
                }) ?: [$classDirname . '\\Policies\\' . class_basename($class) . 'Policy']
            );
        });
    }

    protected function registerConfig(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/jsonapi.php' => config_path('jsonapi.php'),
            ], 'jsonapi-config');
        }
    }

    protected function registerRoutes(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../routes/jsonapi.php' => base_path('routes/jsonapi.php'),
            ], 'jsonapi-routes');
        }
    }

    protected function configureTranslations(): void
    {
        if ($this->app->runningInConsole()) {
            // Register Scribe strategies translations
            if ($this->isScribeInstalled()) {
                $this->publishes([
                    __DIR__ . '/../lang' => $this->app->langPath('jsonapi'),
                ], 'jsonapi-scribe-translations');

                $this->loadTranslationsFrom($this->app->langPath('jsonapi'), 'jsonapi');
                $this->loadTranslationsFrom(realpath(__DIR__ . '/../lang'), 'jsonapi');
            }
        }
    }

    public function register(): void
    {
        $this->registerRequest();
        $this->registerResponseFactory();
        $this->registerResourceManager();
        $this->registerResourceManipulator();
        $this->registerDocumentation();

        if (! app()->configurationIsCached()) {
            $this->mergeConfigFrom(__DIR__ . '/../config/jsonapi.php', 'jsonapi');
        }
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
        $this->app->singleton(ResponseFactory::class, function (Application $app) {
            return new ResponseFactory(
                $app[ViewFactoryContract::class],
                $app['redirect']
            );
        });

        $this->app->alias(ResponseFactory::class, ResponseFactoryContract::class);
    }

    public function registerResourceManager(): void
    {
        $this->app->singleton(ResourceManager::class, function (Application $app) {
            return new ResourceManager($app['registry'], config('jsonapi.resources'));
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
