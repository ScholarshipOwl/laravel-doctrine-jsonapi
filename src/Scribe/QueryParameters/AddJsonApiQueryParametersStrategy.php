<?php

namespace Sowl\JsonApi\Scribe\QueryParameters;

use Knuckles\Camel\Extraction\ExtractedEndpointData;
use Sowl\JsonApi\Scribe\AbstractStrategy;

/**
 * Strategy to add common JSON:API query parameters to GET routes
 */
class AddJsonApiQueryParametersStrategy extends AbstractStrategy
{
    /**
     * @inheritDoc
     */
    public function __invoke(ExtractedEndpointData $endpointData, array $settings = []): array
    {
        if (!$this->isJsonApi($endpointData)) {
            // Not a JSON:API route, skip
            return [];
        }

        if (empty(array_intersect($endpointData->httpMethods, $this->allowedMethods()))) {
            // No matching methods, skip
            return [];
        }

        $queryParameters = [
            'include' => [
                'description' => 'Include related resources',
                'required' => false,
                'example' => 'roles,status'
            ],
            'fields' => [
                'description' => 'Sparse fieldsets - only return specific fields',
                'required' => false,
                'example' => 'fields[users]=name,email'
            ],
            'meta' => [
                'description' => 'Sparse meta fieldsets - only return specific fields',
                'required' => false,
                'example' => 'meta[users]=stats'
            ]
        ];

        if ($this->isListRoute($endpointData)) {
            $queryParameters = [...$queryParameters, ...$this->listRoutes()];
        }

        return $queryParameters;
    }

    protected function allowedMethods(): array
    {
        return [
            'GET',
            'POST',
            'PATCH',
            'PUT'
        ];
    }

    protected function listRoutes(): array
    {
        return [
            'filter' => [
                'description' => 'Filter the resources by attributes',
                'required' => false,
                'example' => 'filter[name]=John'
            ],
            'page[number]' => [
                'description' => 'Page number for pagination',
                'required' => false,
                'example' => '1'
            ],
            'page[size]' => [
                'description' => 'Number of items per page',
                'required' => false,
                'example' => '10'
            ],
            'page[limit]' => [
                'description' => 'Limit the number of items per page (alternative to size)',
                'required' => false,
                'example' => '15'
            ],
            'page[offset]' => [
                'description' => 'Offset for pagination (zero-based)',
                'required' => false,
                'example' => '0'
            ],
            'sort' => [
                'description' => 'Sort the resources by attributes. Prefix with - for descending order.',
                'required' => false,
                'example' => '-created_at,name'
            ]
        ];
    }
}
