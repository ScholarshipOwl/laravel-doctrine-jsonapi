<?php

namespace Sowl\JsonApi\Scribe\Strategies\UrlParameters;

use Knuckles\Camel\Extraction\ExtractedEndpointData;
use Sowl\JsonApi\Scribe\Attributes\ResourceRequest;
use Sowl\JsonApi\Scribe\Strategies\AbstractStrategy;
use Sowl\JsonApi\Scribe\Strategies\ReadsPhpAttributes;

class GetFromResourceRequestAttributes extends AbstractStrategy
{
    use ReadsPhpAttributes;

    public function __invoke(ExtractedEndpointData $endpointData, array $settings = []): array
    {
        if (!$this->initJsonApiEndpointData($endpointData)) {
            return [];
        }

        [$attributesOnMethod, $attributesOnFormRequest, $attributesOnController] =
            $this->getAttributes($endpointData->method, $endpointData->controller);

        return $this->extractFromAttributes($endpointData, $attributesOnMethod, $attributesOnFormRequest, $attributesOnController);
    }

    protected static function readAttributes(): array
    {
        return [
            ResourceRequest::class,
        ];
    }

    protected function extractFromAttributes(
        ExtractedEndpointData $endpointData,
        array $attributesOnMethod,
        array $attributesOnFormRequest = [],
        array $attributesOnController = []
    ): array {
        $parameters = [];
        $allAttributes = [
            ...$attributesOnController,
            ...$attributesOnFormRequest,
            ...$attributesOnMethod
        ];

        foreach ($allAttributes as $attributeInstance) {
            if ($attributeInstance instanceof ResourceRequest) {
                $paramName = $attributeInstance->idParam ?? 'id';
                $parameterNames = $endpointData->route->parameterNames();
                if (in_array($paramName, $parameterNames)) {
                    $parameters[$paramName] = $this->generateIdParameterInfo($attributeInstance);
                }
            }
        }
        return $parameters;
    }

    protected function generateIdParameterInfo(ResourceRequest $attribute): array
    {
        $resourceType = $attribute->resourceType ?? $this->jsonApiEndpointData->resourceType;
        $resourceClass = $this->rm()->classByResourceType($resourceType);

        // Use idType from attribute or extract if not provided
        $idType = $attribute->idType ?? $this->extractIdType($resourceClass);
        $example = $attribute->idExample ?? $this->generateExampleForIdType($idType);

        return [
            'description' => "The unique identifier of the '{$resourceType}' resource",
            'required' => true,
            'example' => $example,
            'type' => $idType,
        ];
    }

    protected function extractIdType(string $class): string
    {
        $type = 'guid';
        $metadata = $this->rm()->em()->getClassMetadata($class);
        $idFieldName = $metadata->identifier[0] ?? 'id';

        if (isset($metadata->fieldMappings[$idFieldName])) {
            $type = $metadata->fieldMappings[$idFieldName]['type'] ?? $type;
        } elseif (isset($metadata->associationMappings[$idFieldName])) {
            return $this->extractIdType($metadata->associationMappings[$idFieldName]['targetEntity']);
        }

        return $this->mapDoctrineTypeToOpenApiType($type);
    }

    protected function generateExampleForIdType(string $idType): mixed
    {
        switch ($idType) {
            case 'integer':
            case 'smallint':
            case 'bigint':
            case 'number':
                return 1;
            case 'guid':
            case 'uuid':
            case 'string':
            default:
                return '12345678-1234-1234-1234-123456789012';
        }
    }

    protected function mapDoctrineTypeToOpenApiType(string $doctrineType): string
    {
        $typeMap = [
            'integer' => 'number',
            'smallint' => 'number',
            'bigint' => 'number',
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
            'array' => 'string',
            'json' => 'string',
            'json_array' => 'string',
        ];
        return $typeMap[$doctrineType] ?? 'string';
    }
}
