<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Resource Manager
    |--------------------------------------------------------------------------
    |
    | The entities listed here will be added to ResourceManager and will be
    | used for handling default endpoints. Each entity must implement
    | ResourceInterface.
    |
    */

    'resources' => [
        App\Entities\User::class,
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
         * This prefix is determining the JSON:API route names.
         * Json API scribe strategies will consider route is a JSON:API route
         * if it's name starts with this prefix.
         */
        'routeNamePrefix' => 'jsonapi.'
    ]
];
