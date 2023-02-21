<?php

namespace Sowl\JsonApi;


use Illuminate\Contracts\View\Factory as ViewFactoryContract;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Routing\ResponseFactory as ResponseFactoryContract;

class JsonApiServiceProvider extends ServiceProvider
{

    public function boot()
    {
        // We will assign to the alias "request.jsonapi" all the resolved request
        $this->app->afterResolving(AbstractRequest::class, function (AbstractRequest $request) {
            $this->app->instance('request.jsonapi', $request);
        });

        $this->app->singleton(ResponseFactoryContract::class, function (Application $app) {
            return new ResponseFactory(
                $app[ViewFactoryContract::class],
                $app['redirect'],
                $app['request.jsonapi']
            );
        });
    }
}
