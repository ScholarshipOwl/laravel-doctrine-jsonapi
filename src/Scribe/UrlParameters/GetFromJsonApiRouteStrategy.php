<?php

namespace Sowl\JsonApi\Scribe\UrlParameters;

use Knuckles\Camel\Extraction\ExtractedEndpointData;
use Sowl\JsonApi\Scribe\AbstractStrategy;

/**
 * Strategy to extract URL parameters from JSON:API routes
 */
class GetFromJsonApiRouteStrategy extends AbstractStrategy
{
    public function __invoke(ExtractedEndpointData $endpointData, array $routeRules = []): array
    {
        if (!$this->initJsonApiEndpointData($endpointData)) {
            // Not a JSON:API route, skip
            return [];
        }

        $parameters = [];

        // Extract parameter names from route
        $parameterNames = $this->endpointData->route->parameterNames();

        if (in_array('id', $parameterNames)) {
            $parameters['id'] = $this->generateIdParameterInfo();
        }

        return $parameters;
    }

    /**
     * Generate parameter information based on parameter name
     */
    protected function generateIdParameterInfo(): array
    {
        $resourceType = $this->jsonApiEndpointData->resourceType;
        $class = $this->rm()->classByResourceType($resourceType);
        $repository = $this->rm()->repositoryByClass($class);
        $metadata = $repository->metadata();

        // Get the ID field information from metadata
        $idFieldName = $metadata->identifier[0] ?? 'id';
        $idFieldMapping = $metadata->fieldMappings[$idFieldName] ?? [];

        // Determine the ID type from the field mapping
        $idType = $idFieldMapping['type'] ?? 'guid';
        $description = "The unique identifier of the '{$resourceType}' resource";

        // Generate an example value based on the ID type
        $example = $this->generateExampleForIdType($idType);

        return [
            'description' => $description,
            'required' => true,
            'example' => $example,
            'type' => $this->mapDoctrineTypeToOpenApiType($idType),
        ];
    }

    /**
     * Generate an example value for the given ID type
     */
    protected function generateExampleForIdType(string $idType): mixed
    {
        switch ($idType) {
            case 'integer':
            case 'smallint':
            case 'bigint':
                return 1;

            case 'guid':
            case 'uuid':
                return '12345678-1234-1234-1234-123456789012';

            case 'string':
            default:
                return 'abc123';
        }
    }

    /**
     * Map Doctrine type to OpenAPI type
     */
    protected function mapDoctrineTypeToOpenApiType(string $doctrineType): string
    {
        $typeMap = [
            'integer' => 'integer',
            'smallint' => 'integer',
            'bigint' => 'integer',
            'boolean' => 'boolean',
            'decimal' => 'number',
            'float' => 'number',
            'string' => 'string',
            'text' => 'string',
            'guid' => 'string',
            'uuid' => 'string',
            'date' => 'string',
            'datetime' => 'string',
            'datetimetz' => 'string',
            'time' => 'string',
            'array' => 'array',
            'json' => 'object',
            'json_array' => 'array',
        ];

        return $typeMap[$doctrineType] ?? 'string';
    }
}
