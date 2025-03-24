<?php

namespace Sowl\JsonApi\Scribe\UrlParameters;

use Knuckles\Camel\Extraction\ExtractedEndpointData;
use Sowl\JsonApi\Scribe\AbstractStrategy;

/**
 * Strategy to extract URL parameters from JSON:API routes
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

        $parameters = [];

        // Extract parameter names from route
        $parameterNames = $route->parameterNames();

        foreach ($parameterNames as $name) {
            $parameters[$name] = $this->generateParameterInfo($name);
        }

        return $parameters;
    }

    /**
     * Generate parameter information based on parameter name
     *
     * @param string $paramName
     * @return array
     */
    protected function generateParameterInfo(string $paramName): array
    {
        $description = match($paramName) {
            'resourceType' => 'The type of the resource',
            'id' => 'The UUID of the resource',
            'relationship' => 'The name of the relationship',
            default => 'Parameter ' . $paramName
        };

        return [
            'description' => $description,
            'required' => true,
            'example' => match($paramName) {
                'resourceType' => 'users',
                'id' => '550e8400-e29b-41d4-a716-446655440000',
                'relationship' => 'roles',
                default => 'example-value'
            }
        ];
    }
}
