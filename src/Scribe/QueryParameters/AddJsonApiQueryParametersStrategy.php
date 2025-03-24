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

        // Only add query parameters for GET routes
        if (!in_array('GET', $endpointData->httpMethods)) {
            return [];
        }

        $queryParameters = [];

        // Filter parameters
        $queryParameters['filter'] = [
            'description' => 'Filter the resources by attributes',
            'required' => false,
            'example' => 'filter[name]=John'
        ];

        // Pagination parameters
        $queryParameters['page[number]'] = [
            'description' => 'Page number for pagination',
            'required' => false,
            'example' => '1'
        ];

        $queryParameters['page[size]'] = [
            'description' => 'Number of items per page',
            'required' => false,
            'example' => '10'
        ];

        $queryParameters['page[limit]'] = [
            'description' => 'Limit the number of items per page (alternative to size)',
            'required' => false,
            'example' => '15'
        ];

        $queryParameters['page[offset]'] = [
            'description' => 'Offset for pagination (zero-based)',
            'required' => false,
            'example' => '0'
        ];

        // Sorting
        $queryParameters['sort'] = [
            'description' => 'Sort the resources by attributes. Prefix with - for descending order.',
            'required' => false,
            'example' => '-created_at,name'
        ];

        // Include related resources
        $queryParameters['include'] = [
            'description' => 'Include related resources',
            'required' => false,
            'example' => 'roles,status'
        ];

        // Sparse fieldsets
        $queryParameters['fields'] = [
            'description' => 'Sparse fieldsets - only return specific fields',
            'required' => false,
            'example' => 'fields[users]=name,email'
        ];

        return $queryParameters;
    }
}
