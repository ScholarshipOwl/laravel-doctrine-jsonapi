<?php

namespace Sowl\JsonApi\Scribe\BodyParameters;

use Knuckles\Camel\Extraction\ExtractedEndpointData;
use Sowl\JsonApi\Scribe\AbstractStrategy;

/**
 * Strategy to add JSON:API request body parameters based on route
 */
class GetFromJsonApiRouteStrategy extends AbstractStrategy
{
    /**
     * @inheritDoc
     */
    public function __invoke(ExtractedEndpointData $endpointData, array $routeRules = []): array
    {
        $route = $endpointData->route;
        if (!$route || !$this->isJsonApiRoute($route)) {
            // Not a JSON:API route, skip
            return [];
        }

        // Skip routes that don't have a request body
        if (in_array('GET', $endpointData->httpMethods) || in_array('DELETE', $endpointData->httpMethods)) {
            return [];
        }

        $methodName = $this->getMethodNameFromRoute($route);
        if (!$methodName) {
            return [];
        }

        $actionType = $this->determineActionType($methodName);
        $resourceType = $this->extractResourceTypeFromRoute($route);

        if (!$resourceType) {
            return [];
        }

        // Use 'users' as a placeholder for {resourceType}
        $exampleType = $resourceType === '{resourceType}' ? 'users' : $resourceType;

        // Structure body parameters based on action type
        if (in_array($actionType, ['create', 'update'])) {
            $parameters = [
                'data' => [
                    'description' => 'Resource object',
                    'required' => true,
                    'type' => 'object',
                    'example' => [
                        'type' => $exampleType,
                        'attributes' => [
                            'name' => 'Example Name',
                            'email' => 'example@example.com',
                        ],
                    ],
                ],
            ];

            // For update actions, add ID to the example
            if ($actionType === 'update') {
                $parameters['data']['example']['id'] = '550e8400-e29b-41d4-a716-446655440000';
            }

            return $parameters;
        } elseif (strpos($actionType, 'relationship') !== false) {
            $isToMany = in_array($actionType, ['create-relationships', 'update-relationships']);

            return [
                'data' => [
                    'description' => $isToMany ? 'Resource identifier objects' : 'Resource identifier object',
                    'required' => true,
                    'type' => $isToMany ? 'array' : 'object',
                    'example' => $isToMany ? [
                        [
                            'type' => 'roles',
                            'id' => '550e8400-e29b-41d4-a716-446655440000',
                        ],
                    ] : [
                        'type' => 'roles',
                        'id' => '550e8400-e29b-41d4-a716-446655440000',
                    ],
                ],
            ];
        }

        return [];
    }
}
