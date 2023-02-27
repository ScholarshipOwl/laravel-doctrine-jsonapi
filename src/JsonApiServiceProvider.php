<?php

namespace Sowl\JsonApi;

use Illuminate\Contracts\View\Factory as ViewFactoryContract;
use Illuminate\Foundation\Application;
use Illuminate\Routing\Redirector;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Routing\ResponseFactory as ResponseFactoryContract;

class JsonApiServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/jsonapi.php' => config_path('jsonapi.php'),
        ]);

        $this->app->resolving(Request::class, function (Request $request, Application $app) {
            $request = Request::createFrom($app['request'], $request);
            $request->setContainer($app);
        });

        // We will assign to the alias "request.jsonapi" all the resolved request
        $this->app->afterResolving(Request::class, function (Request $request) {
            $this->app->instance('request.jsonapi', $request);
        });

        $this->app->singleton(ResponseFactoryContract::class, function (Application $app) {
            return new ResponseFactory(
                $app[ViewFactoryContract::class],
                $app['redirect'],
                $app->resolved('request.jsonapi') ? $app['request.jsonapi'] : null
            );
        });
    }

    public function register()
    {
        $this->app->singleton(ResourceManager::class, function (Application $app) {
            return new ResourceManager($app['em'], config('jsonapi.resources'));
        });

        $this->app->singleton(ResourceManipulator::class, function (Application $app) {
            return new ResourceManipulator($app['em'], $app[ResourceManager::class]);
        });
    }
}
