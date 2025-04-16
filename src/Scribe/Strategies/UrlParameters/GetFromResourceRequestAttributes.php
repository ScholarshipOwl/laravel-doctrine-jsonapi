<?php

namespace Sowl\JsonApi\Scribe\Strategies\UrlParameters;

use Knuckles\Scribe\Tools\ConsoleOutputUtils as c;
use Knuckles\Camel\Extraction\ExtractedEndpointData;
use Sowl\JsonApi\Scribe\Attributes\ResourceRequest;
use Sowl\JsonApi\Scribe\Strategies\AbstractStrategy;
use Sowl\JsonApi\Scribe\Strategies\ReadsPhpAttributes;

class GetFromResourceRequestAttributes extends AbstractStrategy
{
    use ReadsPhpAttributes;

    public function __invoke(ExtractedEndpointData $endpointData, array $settings = []): array
    {
        $this->initJsonApiEndpointData($endpointData);

        [$attributesOnMethod, $attributesOnFormRequest, $attributesOnController] =
            $this->getAttributes($endpointData->method, $endpointData->controller);

        return $this->extractFromAttributes($attributesOnMethod, $attributesOnFormRequest, $attributesOnController);
    }

    protected static function readAttributes(): array
    {
        return [
            ResourceRequest::class,
        ];
    }

    protected function extractFromAttributes(
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

        foreach ($allAttributes as $attribute) {
            $routeParameters = match (true) {
                $attribute instanceof ResourceRequest =>
                    $this->getParametersFromResourceRequest($attribute),
            };

            $parameters = array_merge($parameters, $routeParameters);
        }

        return $parameters;
    }

    protected function getParametersFromResourceRequest(ResourceRequest $attribute): array
    {
        $resourceType = $attribute->resourceType ?? $this->jsonApiEndpointData->resourceType;
        if (empty($resourceType)) {
            return [];
        }

        $paramName = $attribute->idParam ?? 'id';
        $parameterNames = $this->endpointData->route->parameterNames();
        if (!in_array($paramName, $parameterNames)) {
            c::warn("Parameter [$paramName] not found on route [{$this->endpointData->route->uri()}]");
            return [];
        }

        // Use idType from attribute or extract if not provided
        $resourceClass = $this->rm()->classByResourceType($resourceType);
        $idType = $attribute->idType ?? $this->extractIdType($resourceClass);
        $example = $attribute->idExample ?? $this->generateExampleForIdType($idType);

        return [
            $paramName => [
                'description' => "The unique identifier of the '{$resourceType}' resource",
                'required' => true,
                'example' => $example,
                'type' => $idType,
            ]
        ];
    }

    protected function extractIdType(string $class): string
    {
        $type = 'guid';
        $em = $this->rm()->registry()->getManagerForClass($class);
        $metadata = $em->getClassMetadata($class);
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
