<?php

namespace Tests\App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->routes(function () {
            Route::middleware('jsonapi')
                ->prefix('')
                ->group(base_path('routes/jsonapi.php'));
        });
    }
}