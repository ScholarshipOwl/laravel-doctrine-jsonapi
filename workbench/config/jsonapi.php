<?php

return [
    'resources' => [
        App\Entities\Page::class,
        App\Entities\PageComment::class,
        App\Entities\User::class,
        App\Entities\UserStatus::class,
        App\Entities\Role::class,
        App\Entities\UserConfig::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Routing configurations
    |--------------------------------------------------------------------------
    */

    'routing' => [
        /**
         * Prefix for all the JSON:API route names.
         */
        'name' => 'jsonapi.',

        /**
         * Prefix for the route path.
         *
         * We must set and use prefix from config as it is used not only in routing
         * but also in docs generation, links generation and in resource type extractors.
         */
        'prefix' => 'api',
    ],

    /*
    |--------------------------------------------------------------------------
    | Scribe
    |--------------------------------------------------------------------------
    |
    | Configuration of the Scribe package if you are using our strategies.
    |
    */
    'scribe' => [
        /**
         * Middleware that assigned to all jsona:api routes.
         * It's used to identify does the route is jsona:api route.
         *
         * If you change the value of `apiPrefix` in the `bootstrap/app.php` make sure update the value of `middleware` here.
         */
        'middleware' => 'api',
    ],
];
