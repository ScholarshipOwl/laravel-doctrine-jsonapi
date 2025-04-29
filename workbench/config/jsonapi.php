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
         * Middleware to apply to all JSON:API routes.
         */
        'rootMiddleware' => 'jsonapi',

        /**
         * Prefix for all the JSON:API route names.
         */
        'rootNamePrefix' => 'jsonapi.',

        /**
         * Prefix for the route path.
         */
        'rootPathPrefix' => '',
    ],

    /*
    |--------------------------------------------------------------------------
    | Scribe
    |--------------------------------------------------------------------------
    |
    | Configuration of the Scribe package if you are using our strategies.
    |
    */
    'scribe' => [],
];
