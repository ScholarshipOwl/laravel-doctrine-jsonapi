<?php

namespace Sowl\JsonApi\Scribe\Strategies\QueryParameters;

use Knuckles\Camel\Extraction\ExtractedEndpointData;
use Sowl\JsonApi\FilterParsers\AbstractFilterParser;
use Sowl\JsonApi\Request;
use Sowl\JsonApi\Resource\FilterableInterface;
use Sowl\JsonApi\Scribe\Attributes\ResourceRequest;
use Sowl\JsonApi\Scribe\Attributes\ResourceRequestList;
use Sowl\JsonApi\Scribe\Attributes\ResourceRequestCreate;
use Sowl\JsonApi\Scribe\Attributes\ResourceRequestRelationships;
use Sowl\JsonApi\Scribe\Strategies\AbstractStrategy;
use Sowl\JsonApi\Scribe\Strategies\ReadsPhpAttributes;
use Sowl\JsonApi\Scribe\Strategies\TransformerHelper;

/**
 * Strategy to add common JSON:API query parameters to GET routes using attributes.
 */
class GetFromResourceRequestAttributes extends AbstractStrategy
{
    use ReadsPhpAttributes;
    use TransformerHelper;

    private const SPEC_URL_INCLUDES = 'https://jsonapi.org/format/#fetching-includes';
    private const SPEC_URL_SPARSE_FIELDSETS = 'https://jsonapi.org/format/#fetching-sparse-fieldsets';
    private const SPEC_URL_SORTING = 'https://jsonapi.org/format/#fetching-sorting';
    private const SPEC_URL_PAGINATION = 'https://jsonapi.org/format/#fetching-pagination';
    private const SPEC_URL_FILTERING = 'https://jsonapi.org/format/#fetching-filtering';

    protected static function readAttributes(): array
    {
        return [
            ResourceRequest::class,
            ResourceRequestList::class,
            ResourceRequestCreate::class,
        ];
    }

    public function __invoke(ExtractedEndpointData $endpointData, array $settings = []): array
    {
        $this->initJsonApiEndpointData($endpointData);

        [$attributesOnMethod, $attributesOnFormRequest, $attributesOnController] =
            $this->getAttributes($endpointData->method, $endpointData->controller);

        return $this->extractFromAttributes($attributesOnMethod, $attributesOnFormRequest, $attributesOnController);
    }

    protected function extractFromAttributes(
        array $attributesOnMethod,
        array $attributesOnFormRequest = [],
        array $attributesOnController = []
    ): array {
        $allAttributes = [
            ...$attributesOnController,
            ...$attributesOnFormRequest,
            ...$attributesOnMethod
        ];

        if (empty(array_intersect($this->endpointData->httpMethods, $this->allowedMethods()))) {
            return [];
        }

        $queryParameters = [];
        foreach ($allAttributes as $attribute) {
            if ($attribute instanceof ResourceRequest) {
                $queryParameters = array_merge($queryParameters, $this->getFromResourceRequest($attribute));
            }

            if ($attribute instanceof ResourceRequestList) {
                $queryParameters = array_merge($queryParameters, $this->getFromResourceListRequest($attribute));
            }
        }

        return $queryParameters;
    }

    protected function getFromResourceRequest(ResourceRequest $attribute): array
    {
        return array_merge(
            $this->buildFieldsQueryParameter($attribute),
            $this->buildMetaQueryParameter($attribute),
            $this->buildIncludeQueryParameter($attribute),
            $this->buildExcludeQueryParameter($attribute),
        );
    }

    protected function getFromResourceListRequest(ResourceRequestList $attribute): array
    {
        return array_merge(
            $this->buildPageQueryParameter($attribute),
            $this->buildSortQueryParameter($attribute),
            $this->buildFilterQueryParameter($attribute),
        );
    }

    private function buildFieldsQueryParameter(ResourceRequest $attribute): array
    {
        if ($this->isRelationships($attribute)) {
            return [];
        }

        $resourceType = $attribute->resourceType ?? $this->jsonApiEndpointData->resourceType;
        if (empty($resourceType)) {
            return [];
        }

        $response = $this->fetchTransformedResponse($resourceType);
        $fields = array_keys($response['data']['attributes'] ?? []);

        $description = __(
            'jsonapi::query_params.fields.description',
            ['specUrl' => self::SPEC_URL_SPARSE_FIELDSETS]
        );

        if (!empty($fields)) {
            $description .= "\n\n" . __(
                'jsonapi::query_params.fields.available',
                [
                    'resourceType' => $resourceType,
                    'fields' => implode(', ', array_map(fn($field) => "`$field`", $fields))
                ]
            );
        }

        $fieldsParameter = [
            'type' => 'string',
            'required' => false,
            'description' => $description,
        ];

        // if (!empty($fields)) {
        //     $fieldsParameter['example'] = implode(',', $fields);
        // }

        // In order to support nested fields, we need to use a custom name
        // ( Scalar does not support nested parameters )
        // Wait for scalar and Scribe to support nested parameters ( style: deepObject )
        return ["fields[$resourceType]" => $fieldsParameter];
    }

    private function buildMetaQueryParameter(ResourceRequest $attribute): array
    {
        if ($this->isRelationships($attribute)) {
            return [];
        }

        $resourceType = $attribute->resourceType ?? $this->jsonApiEndpointData->resourceType;
        if (empty($resourceType)) {
            return [];
        }

        $transformer = $this->jsonApiEndpointData->resourceTransformer();
        $metas = $transformer->getAvailableMetas();

        if (empty($metas)) {
            return [];
        }

        $description = __('jsonapi::query_params.meta.description');
        $description .= "\n\n" . __(
            'jsonapi::query_params.meta.available',
            [
                'resourceType' => $resourceType,
                'metas' => implode(', ', array_map(fn($meta) => "`$meta`", $metas))
            ]
        );

        $metaParameter = [
            'type' => 'string',
            'required' => false,
            'description' => $description,
            'example' => implode(',', $metas)
        ];

        return ["meta[$resourceType]" => $metaParameter];
    }

    private function buildIncludeQueryParameter(ResourceRequest $attribute): array
    {
        if ($this->isRelationships($attribute)) {
            return [];
        }

        $resourceType = $attribute->resourceType ?? $this->jsonApiEndpointData->resourceType;
        if (empty($resourceType)) {
            return [];
        }

        $transformer = $this->jsonApiEndpointData->resourceTransformer();
        $includes = $transformer->getAvailableIncludes();
        $default = $transformer->getDefaultIncludes();

        if (empty($includes)) {
            return [];
        }

        $includeDescription = __(
            'jsonapi::query_params.include.description',
            ['specUrl' => self::SPEC_URL_INCLUDES]
        ) . "\n\n";

        $availableIncludesText = implode(', ', array_map(fn ($include) => "`$include`", $includes));
        $includeDescription .= __(
            'jsonapi::query_params.include.available',
            ['includes' => $availableIncludesText]
        );

        if ($default) {
            $defaultIncludesText = implode(', ', array_map(fn ($include) => "`$include`", $default));
            $includeDescription .= "\n\n" . __(
                'jsonapi::query_params.include.defaults_title',
                ['defaults' => $defaultIncludesText]
            );
        }

        $includeExample = implode(',', array_slice($includes, 0, 2));

        $includeParameter = [
            'description' => $includeDescription,
            'required' => false,
            'example' => $includeExample,
        ];

        return ['include' => $includeParameter];
    }

    private function buildExcludeQueryParameter(ResourceRequest $attribute): array
    {
        if ($this->isRelationships($attribute)) {
            return [];
        }

        $resourceType = $attribute->resourceType ?? $this->jsonApiEndpointData->resourceType;
        if (empty($resourceType)) {
            return [];
        }

        $transformer = $this->jsonApiEndpointData->resourceTransformer();
        $excludes = $transformer->getDefaultIncludes();

        if (empty($excludes)) {
            return [];
        }

        $excludeDescription = __('jsonapi::query_params.exclude.description');
        $excludeDescription .= "\n\n";
        $excludeDescription .= __(
            'jsonapi::query_params.exclude.available',
            ['excludes' => implode(', ', array_map(fn ($exclude) => "`$exclude`", $excludes))]
        );

        $excludeParameter = [
            'description' => $excludeDescription,
            'required' => false,
        ];

        return ['exclude' => $excludeParameter];
    }

    private function buildPageQueryParameter(ResourceRequestList $attribute): array
    {
        // Scribe doesn't fully support complex nested objects with 'oneOf' like OpenAPI spec allows.
        // We define each sub-parameter individually.
        return [
            'page[number]' => [
                'type' => 'number',
                'required' => false,
                'description' => __('jsonapi::query_params.page.number_description', [
                    'specUrl' => self::SPEC_URL_PAGINATION
                ]),
                'example' => 1,
            ],
            'page[size]' => [
                'type' => 'number',
                'required' => false,
                'description' => __('jsonapi::query_params.page.size_description', [
                    'specUrl' => self::SPEC_URL_PAGINATION
                ]),
                'example' => 10,
            ],
            // 'page[limit]' => [
            //     'description' => __('jsonapi::query_params.page.limit_description', [
            //         'specUrl' => self::SPEC_URL_PAGINATION
            //     ]),
            //     'required' => false,
            //     'type' => 'integer',
            //     'example' => 10,
            // ],
            // 'page[offset]' => [
            //     'description' => __('jsonapi::query_params.page.offset_description', [
            //         'specUrl' => self::SPEC_URL_PAGINATION
            //     ]),
            //    'required' => false,
            //    'type' => 'integer',
            //    'example' => 0,
            //],
        ];
    }

    private function buildSortQueryParameter(ResourceRequestList $attribute): array
    {
        $resourceType = $attribute->resourceType ?? $this->jsonApiEndpointData->resourceType;
        if (empty($resourceType)) {
            return [];
        }

        $response = $this->fetchTransformedResponse($resourceType);
        $fields = array_keys($response['data']['attributes'] ?? []);

        $description = __(
            'jsonapi::query_params.sort.description',
            ['specUrl' => self::SPEC_URL_SORTING]
        );

        if (!empty($fields)) {
            $description .= "\n\n" . __(
                'jsonapi::query_params.sort.available',
                [
                    'resourceType' => $resourceType,
                    'fields' => implode(', ', array_map(fn($field) => "`$field`", $fields))
                ]
            );
        }

        $sortParam = [
            'type' => 'string',
            'required' => false,
            'description' => $description,
        ];

        return ['sort' => $sortParam];
    }

    private function buildFilterQueryParameter(ResourceRequestList $attribute): array
    {
        $resourceType = $attribute->resourceType ?? $this->jsonApiEndpointData->resourceType;
        if (empty($resourceType)) {
            return [];
        }

        /** @var class-string<FilterableInterface> $resourceClass */
        $resourceClass = $this->rm()->classByResourceType($resourceType);
        $filterParsers = in_array(FilterableInterface::class, class_implements($resourceClass))
            ? $resourceClass::filterParsers(new Request())
            : [];

        $params = [];
        foreach ($filterParsers as $filterParser) {
            if ($filterParser instanceof AbstractFilterParser) {
                $spec = $filterParser->docSpec();
                if (!empty($spec)) {
                    $params = array_merge($params, $spec);
                }
            }
        }

        return $params;
    }

    protected function isRelationships(ResourceRequest $attribute): bool
    {
        return $attribute instanceof ResourceRequestRelationships;
    }

    protected function allowedMethods(): array
    {
        return [
            'GET',
            'POST',
            'PATCH',
            'PUT'
        ];
    }
}
