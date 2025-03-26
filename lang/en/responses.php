<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Scribe Responses Strategy Language Lines
    |--------------------------------------------------------------------------
    |
    | Used in UseJsonApiResourceResponseStrategy to generate descriptions
    | and error messages for API responses.
    |
    */

    'description' => [
        'success' => 'Successful response.',
        'validation_error' => 'Validation errors occurred.',
        'not_found' => 'The requested resource was not found.',
    ],

    'error' => [
        'not_found' => [
            'title' => 'Resource Not Found',
            'detail' => 'The requested resource does not exist.',
        ],
        'validation' => [
            'title' => 'Validation Error',
            'detail' => 'The provided data failed validation checks.',
        ],
    ],
];
