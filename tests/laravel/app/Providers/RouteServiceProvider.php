<?php

namespace Tests\App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->routes(function () {
            $middleware = config('jsonapi.middleware');
            $prefix = config('jsonapi.prefix');

            Route::middleware($middleware)
                ->prefix($prefix)
                ->group(base_path('routes/jsonapi.php'));
        });
    }
}