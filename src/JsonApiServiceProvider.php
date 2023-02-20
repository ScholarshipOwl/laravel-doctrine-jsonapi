<?php

namespace Sowl\JsonApi;


use Illuminate\Contracts\View\Factory as ViewFactoryContract;
use Sowl\JsonApi\Fractal\JsonApiSerializer;
use Sowl\JsonApi\Fractal\ScopeFactory;

use League\Fractal\Manager as Fractal;

use Illuminate\Container\Container;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Routing\ResponseFactory as ResponseFactoryContract;

class JsonApiServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->registerResponseFactory();
    }

    protected function registerResponseFactory()
    {
        $this->app->singleton(ResponseFactoryContract::class, function ($app) {
            return new ResponseFactory($app[ViewFactoryContract::class], $app['redirect']);
        });
    }
}
