<?php

namespace Sowl\JsonApi;

use Illuminate\Contracts\View\Factory as ViewFactoryContract;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Routing\ResponseFactory as ResponseFactoryContract;

class JsonApiServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->bootConfig();
        $this->bootRoutes();
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/jsonapi.php', 'jsonapi');
        $this->registerRequest();
        $this->registerResponseFactory();
        $this->registerResourceManager();
        $this->registerResourceManipulator();
    }

    public function bootConfig()
    {
        $this->publishes([
            __DIR__.'/../config/jsonapi.php' => config_path('jsonapi.php'),
            __DIR__.'/../routes/jsonapi.php' => base_path('routes/jsonapi.php')
        ]);

    }

    public function bootRoutes()
    {
        Route::group([
            'as' => 'jsonapi.',
            'prefix' => config('jsonapi.prefix', 'jsonapi'),
            'middleware' => config('jsonapi.middleware', []),
        ], function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/jsonapi.php');
        });
    }

    public function registerRequest()
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

    public function registerResponseFactory()
    {
        $this->app->singleton(ResponseFactoryContract::class, function (Application $app) {
            return new ResponseFactory(
                $app[ViewFactoryContract::class],
                $app['redirect'],
                $app->resolved('request.jsonapi') ? $app['request.jsonapi'] : null
            );
        });
    }

    public function registerResourceManager()
    {
        $this->app->singleton(ResourceManager::class, function (Application $app) {
            return new ResourceManager($app['em'], config('jsonapi.resources'));
        });
    }

    public function registerResourceManipulator()
    {
        $this->app->singleton(ResourceManipulator::class, function (Application $app) {
            return new ResourceManipulator($app['em'], $app[ResourceManager::class]);
        });
    }
}
