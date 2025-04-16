<?php

namespace Tests\App\Providers;

use Database\Seeders\SetUpSeeder;
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
        Scribe::bootstrap(function () {
            // Run database migrations before scribe docs generation.
            $this->app['Illuminate\Contracts\Console\Kernel']->call('doctrine:migrations:migrate -n');

            // Run seeder before scribe docs generation.
            $seeder = new SetUpSeeder();
            $seeder->run($this->app['em']);
        });
    }
}
