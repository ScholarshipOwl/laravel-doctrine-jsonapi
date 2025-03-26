<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Scribe Query Parameters Strategy Language Lines
    |--------------------------------------------------------------------------
    |
    | Used in AddJsonApiQueryParametersStrategy to generate descriptions
    | for standard JSON:API query parameters.
    |
    */

    'include' => [
        'description' => 'Include related resources. ([Spec](:specUrl))',
        'default' => ' Default: :default',
        'available' => '**Available includes:** :includes',
        'defaults_title' => '**Default includes:** :defaults',
    ],
    'fields' => [
        'description' => 'Sparse fieldsets - specify which fields to include in the response for each resource type. ([Spec](:specUrl))',
        'available' => '**Available fields for :resourceType:** :fields',
    ],
    'exclude' => [
        'description' => 'Exclude fieldsets - exclude specific fields for each resource type.',
        'available' => '**Available excludes:** :excludes',
    ],
    'meta' => [
        'description' => 'Additional metadata to be included with the response by resource type.',
        'available' => '**Available meta fields for :resourceType:** :metas',
    ],
    'filter' => [
        'description' => 'Filter the resources by attributes. ([Spec](:specUrl))',
    ],
    'page' => [
        'description' => 'Pagination parameters. ([Spec](:specUrl))',
        'number_description' => 'Page number.',
        'size_description' => 'Number of results per page.',
        'limit_description' => 'Maximum number of results to return.',
        'offset_description' => 'Number of results to skip.',
    ],
    'sort' => [
        'description' => 'Sort the results by attributes. Prefix with `-` for descending order. ([Spec](:specUrl))',
        'available' => '**Available sort fields for :resourceType:** :fields',
    ],
];
