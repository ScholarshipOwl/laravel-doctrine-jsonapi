<?php

namespace Tests\App\Providers;

use Illuminate\Support\ServiceProvider;
use Knuckles\Scribe\Scribe;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Run database migrations before scribe docs generation.
        Scribe::bootstrap(function () {
            $this->app['Illuminate\Contracts\Console\Kernel']->call('doctrine:migrations:migrate -n');
        });
    }
}
