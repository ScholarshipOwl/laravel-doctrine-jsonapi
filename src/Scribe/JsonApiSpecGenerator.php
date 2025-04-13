<?php

namespace Sowl\JsonApi\Scribe;

use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Contracts\View\Factory as ViewFactoryContract;
use Knuckles\Camel\Output\OutputEndpointData;
use Knuckles\Scribe\Extracting\DatabaseTransactionHelpers;
use Knuckles\Scribe\Tools\DocumentationConfig;
use Knuckles\Scribe\Writing\OpenApiSpecGenerators\OpenApiGenerator;
use Knuckles\Scribe\Tools\ConsoleOutputUtils as c;
use Knuckles\Scribe\Tools\ErrorHandlingUtils as e;
use LaravelDoctrine\ORM\Testing\Factory;
use LaravelDoctrine\ORM\Testing\Factory as DoctrineTestingFactory;
use League\Fractal\Resource\Item;
use Sowl\JsonApi\Fractal\Fractal;
use Sowl\JsonApi\Fractal\FractalOptions;
use Sowl\JsonApi\Request;
use Sowl\JsonApi\ResourceInterface;
use Sowl\JsonApi\ResourceManager;
use Sowl\JsonApi\ResponseFactory;

class JsonApiSpecGenerator extends OpenApiGenerator
{
    use DatabaseTransactionHelpers;
    use InstantiatesExampleResources;

    protected ResponseFactory $responseFactory;

    protected const DEEP_OBJECT_PARAMS = [
        'fields',
        'meta',
        'filter',
        'page',
    ];

    public function __construct(
        protected DocumentationConfig $config,
        protected ResourceManager $rm,
    )
    {
        parent::__construct($config);

        $this->responseFactory = new ResponseFactory(
            app(ViewFactoryContract::class),
            app('redirect'),
            // TODO: Implement proper request creation.
            Request::create('/')
        );
    }

    public function getConfig(): DocumentationConfig
    {
        return $this->config;
    }

    /**
     * Get the ResourceManager instance needed by InstantiatesExampleResources trait.
     */
    protected function getResourceManager(): ResourceManager
    {
        return $this->rm;
    }

    /**
     * Get the Doctrine testing factory instance needed by InstantiatesExampleResources trait.
     */
    protected function getDoctrineFactory(): DoctrineTestingFactory
    {
        return app(Factory::class);
    }

    public function root(array $root, array $groupedEndpoints): array
    {
        $resourcesSchemas = $this->generateResourcesSchemas($root, $groupedEndpoints);

        $root['components'] = [
            'schemas' => array_merge($root['components']['schemas'] ?? [], $resourcesSchemas),
        ];

        return $root;
    }

    public function pathItem(array $pathItem, array $groupedEndpoints, OutputEndpointData $endpoint): array
    {
        $pathItem = $this->appendDeepObjectStyle($pathItem);
        $pathItem = $this->extendPageParamSchema($pathItem);

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
                                        'minimum' => 1
                                    ],
                                    'size' => [
                                        'type' => 'integer',
                                        'description' => __(
                                            'jsonapi::query_params.page.size_description'
                                        ),
                                        'example' => 10,
                                        'minimum' => 1
                                    ],
                                ],
                                'required' => ['number', 'size']
                            ],
                            [
                                'properties' => [
                                    'limit' => [
                                        'type' => 'integer',
                                        'description' => __(
                                            'jsonapi::query_params.page.limit_description'
                                        ),
                                        'example' => 10,
                                        'minimum' => 1
                                    ],
                                    'offset' => [
                                        'type' => 'integer',
                                        'description' => __(
                                            'jsonapi::query_params.page.offset_description'
                                        ),
                                        'example' => 0,
                                        'minimum' => 0
                                    ],
                                ],
                                'required' => ['limit', 'offset']
                            ]
                        ]
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
        if (!isset($pathItem['parameters'])) {
            foreach ($pathItem['parameters'] as &$param) {
                if (in_array($param['name'], self::DEEP_OBJECT_PARAMS)) {
                    $param['style'] = 'deepObject';
                    $param['explode'] = true;
                }
            }
        }

        return $pathItem;
    }

    protected function generateResourcesSchemas(array $root, array $groupedEndpoints): array
    {
        $schemas = [];

        foreach ($this->rm->resources() as $resourceType => $resourceClass) {
            $schemas[$resourceType] = [
                'type' => 'object',
                'required' => ['data'],
                'properties' => [
                    'data' => [
                        'type' => 'object',
                        'required' => ['id', 'type'],
                        'properties' => array_merge_recursive(
                            $this->specObjectIdentifier($resourceType),
                            $this->specAttributes($resourceClass),
                            $this->specRelationships($resourceClass),
                        )
                    ]
                ]
            ];
        }

        return $schemas;
    }

    protected function specAttributes(string $resourceClass): array
    {
        $spec = [];

        $this->startDbTransaction();

        try {
            /** @var ResourceInterface $resource */
            $resource = $this->instantiateExampleResource($resourceClass);
            $transformer = $resource->transformer();

            $fractal = new Fractal(
                new FractalOptions(meta: [
                    $resource->getResourceType() => $transformer->getAvailableMetas()
                ])
            );

            $response = $fractal
                ->createData(new Item($resource, $transformer, $resource->getResourceType()))
                ->toArray();

            if (!empty($attributes = $response['data']['attributes'] ?? [])) {
                $spec['attributes'] = [
                    'type' => 'object',
                    'properties' => $this->convertToOpenApiSchema($attributes)
                ];
            }

            if (!empty($meta = $response['data']['meta'] ?? [])) {
                $spec['meta'] = [
                    'type' => 'object',
                    'properties' => $this->convertToOpenApiSchema((array) $meta)
                ];
            }

        } catch (\Throwable $e) {
            c::warn("Couldn't generate attributes for {$resourceClass}");
            e::dumpExceptionIfVerbose($e, true);
        }

        $this->endDbTransaction();

        return $spec;
    }

    /**
     * Convert PHP array to OpenAPI schema with types and examples
     */
    protected function convertToOpenApiSchema(array $data): array
    {
        $schema = [];

        foreach ($data as $key => $value) {
            $type = $this->getOpenApiType($value);

            if ($type === 'object' && is_array($value)) {
                $schema[$key] = [
                    'type' => 'object',
                    'properties' => $this->convertToOpenApiSchema($value)
                ];
            } elseif ($type === 'array' && !empty($value)) {
                $itemType = $this->getOpenApiType($value[0]);
                $schema[$key] = [
                    'type' => 'array',
                    'items' => [
                        'type' => $itemType
                    ]
                ];

                if ($value[0] !== null) {
                    $schema[$key]['items']['example'] = $value[0];
                }
            } else {
                $schema[$key] = [
                    'type' => $type
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
        if (is_null($value)) return 'null';
        if (is_int($value)) return 'integer';
        if (is_float($value)) return 'number';
        if (is_bool($value)) return 'boolean';
        if (is_string($value)) return 'string';
        if (is_array($value)) {
            return array_is_list($value) ? 'array' : 'object';
        }

        return 'string';
    }

    /**
     * @param class-string<ResourceInterface> $resourceClass
     */
    protected function specRelationships(string $resourceClass): array
    {
        $relationshipsProperties = [];

        foreach ($this->rm->relationshipsByClass($resourceClass)->all() as $relationship) {
            $relationshipClass = $relationship->class();
            $relationshipType = ResourceManager::resourceType($relationshipClass);

            $relationshipsProperties[$relationship->name()] = [
                'type' => 'object',
                'properties' => [
                    'data' => [
                        'type' => 'object',
                        'required' => ['id', 'type'],
                        'properties' => $this->specObjectIdentifier($relationshipType)
                    ]
                ]
            ];
        }

        return empty($relationshipsProperties) ? [] : [
            'relationships' => [
                'type' => 'object',
                'properties' => $relationshipsProperties
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
}