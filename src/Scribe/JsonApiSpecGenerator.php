<?php

namespace Sowl\JsonApi\Scribe;

use Knuckles\Camel\Output\OutputEndpointData;
use Knuckles\Scribe\Tools\DocumentationConfig;
use Knuckles\Scribe\Writing\OpenApiSpecGenerators\OpenApiGenerator;
use Sowl\JsonApi\ResourceInterface;
use Sowl\JsonApi\ResourceManager;

class JsonApiSpecGenerator extends OpenApiGenerator
{
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
                            $this->specObjectIdentifierProperties($resourceType),
                            // TODO: Implement the attributes
                            //                            [
                            //                                'attributes' => [
                            //                                    'type' => 'object',
                            //                                ],
                            //                            ],
                            $this->specRelationships($resourceClass),
                            //  TODO: Implement meta params
                        )
                    ]
                ]
            ];
        }

        return $schemas;
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
                        'properties' => $this->specObjectIdentifierProperties($relationshipType)
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

    protected function specObjectIdentifierProperties(string $resourceType): array
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