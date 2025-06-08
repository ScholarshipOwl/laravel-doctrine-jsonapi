<?php

namespace Sowl\JsonApi\Scribe;

use Illuminate\Support\Arr;
use Knuckles\Camel\Output\OutputEndpointData;
use Knuckles\Camel\Output\Parameter;
use Knuckles\Scribe\Tools\ConsoleOutputUtils as c;
use Knuckles\Scribe\Tools\DocumentationConfig;
use Knuckles\Scribe\Tools\ErrorHandlingUtils as e;
use Knuckles\Scribe\Tools\Utils;
use Knuckles\Scribe\Writing\OpenApiSpecGenerators\OpenApiGenerator;
use Sowl\JsonApi\Fractal\FractalOptions;
use Sowl\JsonApi\ResourceInterface;
use Sowl\JsonApi\ResourceManager;
use Sowl\JsonApi\Specable;
use Sowl\JsonApi\Scribe\Strategies\TransformerHelper;
use Illuminate\Support\Str;
use stdClass;

class JsonApiSpecGenerator extends OpenApiGenerator
{
    use TransformerHelper;

    protected const DEEP_OBJECT_PARAMS = [
        'fields',
        'meta',
        'filter',
        'page',
    ];

    protected array $root;
    protected array $groupedEndpoints;

    /**
     * @var array<string, array>
     */
    protected array $mappedResourceTypeBodyParams = [];

    public function __construct(
        protected DocumentationConfig $config,
        protected ResourceManager $rm,
    ) {
        parent::__construct($config);
    }

    public function getConfig(): DocumentationConfig
    {
        return $this->config;
    }

    /**
     * Get the ResourceManager instance needed by InstantiatesExampleResources trait.
     */
    protected function rm(): ResourceManager
    {
        return $this->rm;
    }

    public function root(array $root, array $groupedEndpoints): array
    {
        $this->root = $root;
        $this->groupedEndpoints = $groupedEndpoints;

        $this->mapResourceTypeBodyParams();

        $resourcesSchemas = $this->generateResourcesSchemas();

        $root['components'] = [
            'schemas' => array_merge($root['components']['schemas'] ?? [], $resourcesSchemas),
        ];

        return $root;
    }

    public function pathItem(array $pathItem, array $groupedEndpoints, OutputEndpointData $endpoint): array
    {
        // $pathItem = $this->appendDeepObjectStyle($pathItem);
        // $pathItem = $this->extendPageParamSchema($pathItem);
        // $pathItem = $this->setExamplesForBodyParametersFromResponse($pathItem);
        $pathItem = $this->removeNulExamplesFromQueryParams($pathItem);
        $pathItem = $this->removeEmptyExamplesFromBodyParams($pathItem);

        return $pathItem;
    }

    /**
     * Extends `page` query parameter with proper Open API spec, that is not supported by scribe.
     */
    protected function extendPageParamSchema(array $pathItem): array
    {
        if (isset($pathItem['parameters'])) {
            foreach ($pathItem['parameters'] as &$param) {
                if ($param['name'] === 'page') {
                    $param['schema'] = [
                        'oneOf' => [
                            [
                                'properties' => [
                                    'number' => [
                                        'type' => 'integer',
                                        'description' => __(
                                            'jsonapi::query_params.page.number_description'
                                        ),
                                        'example' => 1,
                                        'minimum' => 1,
                                    ],
                                    'size' => [
                                        'type' => 'integer',
                                        'description' => __(
                                            'jsonapi::query_params.page.size_description'
                                        ),
                                        'example' => 10,
                                        'minimum' => 1,
                                    ],
                                ],
                                'required' => ['number', 'size'],
                            ],
                            [
                                'properties' => [
                                    'limit' => [
                                        'type' => 'integer',
                                        'description' => __(
                                            'jsonapi::query_params.page.limit_description'
                                        ),
                                        'example' => 10,
                                        'minimum' => 1,
                                    ],
                                    'offset' => [
                                        'type' => 'integer',
                                        'description' => __(
                                            'jsonapi::query_params.page.offset_description'
                                        ),
                                        'example' => 0,
                                        'minimum' => 0,
                                    ],
                                ],
                                'required' => ['limit', 'offset'],
                            ],
                        ],
                    ];
                }
            }
        }

        return $pathItem;
    }

    /**
     * In JSON:API parameters are deepObjects. But Scribe is not supports it out of the box.
     * https://swagger.io/docs/specification/v3_0/serialization/
     */
    protected function appendDeepObjectStyle(array $pathItem): array
    {
        if (is_array($pathItem['parameters'])) {
            foreach ($pathItem['parameters'] as &$param) {
                if (in_array($param['name'], self::DEEP_OBJECT_PARAMS)) {
                    $param['style'] = 'deepObject';
                    $param['explode'] = true;
                }
            }
        }

        return $pathItem;
    }

    protected function generateResourcesSchemas(): array
    {
        $schemas = [];

        foreach ($this->rm->resources() as $resourceType => $resourceClass) {
            $transformer = $this->rm->transformerByResourceType($resourceType);

            $properties = $transformer instanceof Specable ? $transformer->spec() : array_merge_recursive(
                $this->specObjectIdentifier($resourceType),
                $this->specAttributes($resourceType),
                $this->specRelationships($resourceClass),
            );

            $schemas[$resourceType] = [
                'type' => 'object',
                'required' => ['data'],
                'properties' => [
                    'data' => [
                        'type' => 'object',
                        'required' => ['id', 'type'],
                        'properties' => $properties,
                    ],
                ],
            ];
        }

        return $schemas;
    }

    protected function specAttributes(string $resourceType): array
    {
        $requestsSpec = [];
        $transformerSpec = [];

        try {
            if (isset($this->mappedResourceTypeBodyParams[$resourceType])) {
                $bodyParams = $this->mappedResourceTypeBodyParams[$resourceType];

                if (isset($bodyParams['data']['__fields']['attributes'])) {
                    $requestsSpec['attributes'] =
                        $this->generateFieldData($bodyParams['data']['__fields']['attributes']);
                }

                if (isset($bodyParams['data']['__fields']['meta'])) {
                    $requestsSpec['meta'] =
                        $this->generateFieldData($bodyParams['data']['__fields']['meta']);
                }
            }
        } catch (\Throwable $e) {
            c::warn("Couldn't generate attributes for '{$resourceType}'.");
            e::dumpExceptionIfVerbose($e);
        }

        try {
            $transformer = $this->rm()->transformerByResourceType($resourceType);
            $response = $this->fetchTransformedResponse($resourceType, new FractalOptions(meta: [
                $resourceType => $transformer->getAvailableMetas(),
            ]));

            if (! empty($attributes = $response['data']['attributes'] ?? [])) {
                $transformerSpec['attributes'] = [
                    'type' => 'object',
                    'properties' => $this->convertToOpenApiSchema($attributes),
                ];
            }

            if (! empty($meta = (array) ($response['data']['meta'] ?? []))) {
                $transformerSpec['meta'] = [
                    'type' => 'object',
                    'properties' => $this->convertToOpenApiSchema($meta),
                ];
            }
        } catch (\Throwable $e) {
            c::warn("Couldn't generate attributes for '{$resourceType}'.");
            e::dumpExceptionIfVerbose($e);
        }

        return array_replace_recursive($requestsSpec, $transformerSpec);
    }

    /**
     * Convert PHP array to OpenAPI schema with types and examples
     * TODO: If there are null values we can't extract the type, that's why we can extract types from validation
     *       rules or from the doctrine entity.
     */
    protected function convertToOpenApiSchema(array $data): array
    {
        $schema = [];

        foreach ($data as $key => $value) {
            $type = $this->getOpenApiType($value);

            if ($type === 'object' && is_array($value)) {
                $schema[$key] = [
                    'type' => 'object',
                    'properties' => $this->convertToOpenApiSchema($value),
                ];
            } elseif ($type === 'array' && ! empty($value)) {
                $itemType = $this->getOpenApiType($value[0]);
                $schema[$key] = [
                    'type' => 'array',
                    'items' => [
                        'type' => $itemType,
                    ],
                ];

                if ($value[0] !== null) {
                    $schema[$key]['items']['example'] = $value[0];
                }
            } else {
                $schema[$key] = [
                    'type' => $type,
                ];

                if ($value !== null) {
                    $schema[$key]['example'] = $value;
                }
            }
        }

        return $schema;
    }

    /**
     * Get OpenAPI type from PHP value
     */
    protected function getOpenApiType($value): string
    {
        if (is_null($value)) {
            return 'null';
        }
        if (is_int($value)) {
            return 'integer';
        }
        if (is_float($value)) {
            return 'number';
        }
        if (is_bool($value)) {
            return 'boolean';
        }
        if (is_string($value)) {
            return 'string';
        }
        if (is_array($value)) {
            return array_is_list($value) ? 'array' : 'object';
        }

        return 'string';
    }

    /**
     * @param  class-string<ResourceInterface>  $resourceClass
     */
    protected function specRelationships(string $resourceClass): array
    {
        $relationshipsProperties = [];

        foreach ($this->rm->relationshipsByClass($resourceClass)->all() as $relationship) {
            $relationshipsProperties[$relationship->name()] = $relationship->spec();
        }

        return empty($relationshipsProperties) ? [] : [
            'relationships' => [
                'type' => 'object',
                'properties' => $relationshipsProperties,
            ],
        ];
    }

    protected function specObjectIdentifier(string $resourceType): array
    {
        return [
            'id' => [
                'type' => 'string',
                'example' => '1',
            ],
            'type' => [
                'type' => 'string',
                'example' => $resourceType,
                'enum' => [$resourceType],
            ],
        ];
    }

    protected function removeNulExamplesFromQueryParams(array $pathItem)
    {
        foreach ($pathItem['parameters'] ?? [] as $i => $param) {
            if (isset($param['example']) && $param['example'] === null) {
                unset($pathItem['parameters'][$i]['example']);
            }
            if (isset($param['schema']['example']) && $param['schema']['example'] === null) {
                unset($pathItem['parameters'][$i]['schema']['example']);
            }
        }

        return $pathItem;
    }

    /**
     * Removes empty or null examples from requestBody parameters in OpenAPI path items.
     */
    protected function removeEmptyExamplesFromBodyParams(array $pathItem): array
    {
        if (isset($pathItem['requestBody']['content']) && is_array($pathItem['requestBody']['content'])) {
            foreach ($pathItem['requestBody']['content'] as $contentType => &$content) {
                if (isset($content['schema']) && is_array($content['schema'])) {
                    $this->removeRecursiveEmptyExamples($content['schema']);
                }
            }
        }

        return $pathItem;
    }

    /**
     * Sets OpenAPI examples for request body parameters based on the response examples.
     * This helps tools like Swagger UI to show the example payloads for requests.
     */
    protected function setExamplesForBodyParametersFromResponse(array $pathItem): array
    {
        if (
            !isset($pathItem['requestBody']) || !isset($pathItem['responses']) ||
            !is_array($pathItem['responses'])
        ) {
            return $pathItem;
        }

        // Build a map of successful responses examples by content type
        $responsesExamples = [];
        foreach ([201, 200, 204] as $status) {
            foreach ((array) ($pathItem['responses'][$status]['content'] ?? []) as $contentType => $response) {
                /** @var stdClass $example */
                $example = $response['schema']['example'] ?? null;

                // If the example is not a JSON:API response, skip it
                if (! isset($example->data->type) || ! isset($example->data->id)) {
                    continue;
                }

                // Remove id, links and meta from the example as they are not part of the request body
                unset($example->data->id);
                unset($example->data->links);
                unset($example->data->meta);

                // BaseGenerator sets 'application/json' for all the responses
                // We must convert it to JSON:API content type if this is a JSON:API endpoint.
                $responsesExamples['application/vnd.api+json'] = $example->data;
            }
        }

        // Set the example for the requestBody if the schema is present
        foreach ((array) ($pathItem['requestBody']['content'] ?? []) as $contentType => $requestBody) {
            $examplePath = 'schema.properties.data.example';
            $currentExample = Arr::get($requestBody, $examplePath);

            if (empty($currentExample) && isset($responsesExamples[$contentType])) {
                Arr::set($requestBody, $examplePath, $responsesExamples[$contentType]);
                $pathItem['requestBody']['content'][$contentType] = $requestBody;
            }
        }

        return $pathItem;
    }

    /**
     * Removes empty or null examples from requestBody parameters in OpenAPI path items.
     *
     * @param  array{ type?: string, properties?: array<array>, example?: mixed }  $propertySchema
     */
    protected function removeRecursiveEmptyExamples(array &$propertySchema): void
    {
        if (isset($propertySchema['type']) && $propertySchema['type'] === 'object') {
            if (isset($propertySchema['example']) && empty($propertySchema['example'])) {
                unset($propertySchema['example']);
            }

            if (isset($propertySchema['properties']) && is_array($propertySchema['properties'])) {
                foreach ($propertySchema['properties'] as &$childPropSchema) {
                    if (is_array($childPropSchema)) {
                        $this->removeRecursiveEmptyExamples($childPropSchema);
                    }
                }
            }
        }
    }

    protected function mapResourceTypeBodyParams(): void
    {
        $resources = array_keys($this->rm->resources());
        $endpointsByMethod = collect($this->groupedEndpoints)
            ->map(fn ($group) => $group['endpoints'])
            ->flatten(1)
            ->groupBy('httpMethods.0');

        /** @var array<string, OutputEndpointData> $createEndpoints */
        $createEndpoints = [];
        /** @var array<string, OutputEndpointData> $updateEndpoints */
        $updateEndpoints = [];

        foreach ($resources as $resourceType) {
            foreach ($endpointsByMethod['POST'] as $endpoint) {
                if (Str::endsWith($endpoint['uri'], $resourceType)) {
                    $createEndpoints[$resourceType] = $endpoint;
                    break;
                }
            }
        }

        foreach ($resources as $resourceType) {
            foreach ($endpointsByMethod['PATCH'] as $endpoint) {
                if (Str::endsWith($endpoint['uri'], $resourceType)) {
                    $updateEndpoints[$resourceType] = $endpoint;
                    break;
                }
            }
        }

        foreach ($resources as $resourceType) {
            if (isset($createEndpoints[$resourceType])) {
                $this->mappedResourceTypeBodyParams[$resourceType] =
                    $createEndpoints[$resourceType]->nestedBodyParameters;
            }

            if (isset($updateEndpoints[$resourceType])) {
                $this->mappedResourceTypeBodyParams[$resourceType] = collect(
                    $this->mappedResourceTypeBodyParams[$resourceType] ?? []
                )
                    ->mergeRecursive($updateEndpoints[$resourceType]->nestedBodyParameters)
                    ->toArray();
            }
        }
    }

    /**
     * @param Parameter|array $field
     *
     * @return array
     */
    protected function generateFieldData($field): array
    {
        if (is_array($field)) {
            $field = new Parameter($field);
        }

        if ($field->type === 'file') {
            // See https://swagger.io/docs/specification/describing-request-body/file-upload/
            return [
                'type' => 'string',
                'format' => 'binary',
                'description' => $field->description ?: '',
                'nullable' => $field->nullable,
            ];
        } elseif (Utils::isArrayType($field->type)) {
            $baseType = Utils::getBaseTypeFromArrayType($field->type);
            $baseItem = ($baseType === 'file') ? [
                'type' => 'string',
                'format' => 'binary',
            ] : ['type' => $baseType];

            if (!empty($field->enumValues)) {
                $baseItem['enum'] = $field->enumValues;
            }

            if ($field->nullable) {
                $baseItem['nullable'] = true;
            }

            $fieldData = [
                'type' => 'array',
                'description' => $field->description ?: '',
                'example' => $field->example,
                'items' => Utils::isArrayType($baseType)
                    ? $this->generateFieldData([
                        'name' => '',
                        'type' => $baseType,
                        'example' => ($field->example ?: [null])[0],
                        'nullable' => $field->nullable,
                    ])
                    : $baseItem,
            ];
            if (str_replace('[]', "", $field->type) === 'file') {
                // Don't include example for file params in OAS; it's hard to translate it correctly
                unset($fieldData['example']);
            }

            if ($baseType === 'object' && !empty($field->__fields)) {
                if ($fieldData['items']['type'] === 'object') {
                    $fieldData['items']['properties'] = [];
                }
                foreach ($field->__fields as $fieldSimpleName => $subfield) {
                    $fieldData['items']['properties'][$fieldSimpleName] = $this->generateFieldData($subfield);
                    if ($subfield['required']) {
                        $fieldData['items']['required'][] = $fieldSimpleName;
                    }
                }
            }

            return $fieldData;
        } elseif ($field->type === 'object') {
            $data = [
                'type' => 'object',
                'description' => $field->description ?: '',
                'example' => $field->example,
                'nullable' => $field->nullable,
                'properties' => $this->objectIfEmpty(
                    collect($field->__fields)
                        ->mapWithKeys(
                            fn ($subfield, $subfieldName) => [$subfieldName => $this->generateFieldData($subfield)]
                        )
                        ->all()
                ),
                'required' => collect($field->__fields)->filter(fn ($f) => $f['required'])->keys()->toArray(),
            ];
            // The spec doesn't allow for an empty `required` array. Must have something there.
            if (empty($data['required'])) {
                unset($data['required']);
            }
            return $data;
        } else {
            $schema = [
                'type' => static::normalizeTypeName($field->type),
                'description' => $field->description ?: '',
                'example' => $field->example,
                'nullable' => $field->nullable,
            ];
            if (!empty($field->enumValues)) {
                $schema['enum'] = $field->enumValues;
            }

            return $schema;
        }
    }

    /**
     * Given an array, return an object if the array is empty. To be used with fields that are
     * required by OpenAPI spec to be objects, since empty arrays get serialised as [].
     */
    protected function objectIfEmpty(array $field): array|stdClass
    {
        return count($field) > 0 ? $field : new stdClass();
    }
}
