<?php

namespace Sowl\JsonApi\Scribe;

use Knuckles\Camel\Output\OutputEndpointData;
use Knuckles\Scribe\Writing\OpenApiSpecGenerators\OpenApiGenerator;

class JsonApiSpecGenerator extends OpenApiGenerator
{
    private const DEEP_OBJECT_PARAMS = [
        'fields',
        'meta',
        'filter',
        'page',
    ];

    public function pathItem(array $pathItem, array $groupedEndpoints, OutputEndpointData $endpoint): array
    {
        $pathItem = $this->appendDeepObjectStyle($pathItem);
        $pathItem = $this->extendPageParamSchema($pathItem);

        return $pathItem;
    }

    private function extendPageParamSchema(array $pathItem): array
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
    private function appendDeepObjectStyle(array $pathItem): array
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
}