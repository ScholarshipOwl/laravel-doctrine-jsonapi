<?php

namespace Sowl\JsonApi\Scribe\Strategies\QueryParameters;

use Knuckles\Camel\Extraction\ExtractedEndpointData;
use Sowl\JsonApi\Scribe\Strategies\AbstractStrategy;
use Sowl\JsonApi\Scribe\JsonApiEndpointData;
use Sowl\JsonApi\Scribe\Strategies\TransformerHelper;

/**
 * Strategy to add common JSON:API query parameters to GET routes
 */
class AddJsonApiQueryParametersStrategy extends AbstractStrategy
{
    use TransformerHelper;

    private const SPEC_URL_INCLUDES = 'https://jsonapi.org/format/#fetching-includes';
    private const SPEC_URL_SPARSE_FIELDSETS = 'https://jsonapi.org/format/#fetching-sparse-fieldsets';
    private const SPEC_URL_SORTING = 'https://jsonapi.org/format/#fetching-sorting';
    private const SPEC_URL_PAGINATION = 'https://jsonapi.org/format/#fetching-pagination';
    private const SPEC_URL_FILTERING = 'https://jsonapi.org/format/#fetching-filtering';

    /**
     * @inheritDoc
     */
    public function __invoke(ExtractedEndpointData $endpointData, array $settings = []): array
    {
        if (!$this->initJsonApiEndpointData($endpointData)) {
            // Not a JSON:API route, skip
            return [];
        }

        if (empty(array_intersect($endpointData->httpMethods, $this->allowedMethods()))) {
            // No matching methods, skip
            return [];
        }

        $queryParameters = [];

        if ($this->jsonApiEndpointData->isCollectionRoute()) {
            $queryParameters = array_merge(
                $queryParameters,
                $this->buildPageQueryParameter(),
                $this->buildSortQueryParameter(),
                $this->buildFilterQueryParameter(),
            );
        }

        $queryParameters = array_merge(
            $queryParameters,
            $this->buildFieldsQueryParameter(),
            $this->buildMetaQueryParameter(),
            $this->buildIncludeQueryParameter(),
            $this->buildExcludeQueryParameter(),
        );

        return $queryParameters;
    }

    private function buildFieldsQueryParameter(): array
    {
        if (!$this->isDataWillBeReturned()) {
            return [];
        }

        $resourceType = $this->jsonApiEndpointData->resourceType;
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
            'example' => implode(',', $fields)
        ];

        // In order to support nested fields, we need to use a custom name
        // ( Scalar does not support nested parameters )
        // Wait for scalar and Scribe to support nested parameters ( style: deepObject )
        return ["fields[$resourceType]" => $fieldsParameter];
    }

    private function buildIncludeQueryParameter(): array
    {
        if (!$this->isDataWillBeReturned()) {
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

    private function buildExcludeQueryParameter(): array
    {
        if (!$this->isDataWillBeReturned()) {
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
            'example' => '', // Provide a meaningful example if possible
        ];

        return ['exclude' => $excludeParameter];
    }

    private function buildMetaQueryParameter(): array
    {
        if (!$this->isDataWillBeReturned()) {
            return [];
        }

        $resourceType = $this->jsonApiEndpointData->resourceType;
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

    private function buildPageQueryParameter(): array
    {
        // Scribe doesn't fully support complex nested objects with 'oneOf' like OpenAPI spec allows.
        // We define each sub-parameter individually.
        return [
            'page[number]' => [
                'description' => __('jsonapi::query_params.page.number_description', [
                    'specUrl' => self::SPEC_URL_PAGINATION
                ]),
                'required' => false,
                'type' => 'integer',
                'example' => 1,
            ],
            'page[size]' => [
                'description' => __('jsonapi::query_params.page.size_description', [
                    'specUrl' => self::SPEC_URL_PAGINATION
                ]),
                'required' => false,
                'type' => 'integer',
                'example' => 10,
            ],
            'page[limit]' => [
                'description' => __('jsonapi::query_params.page.limit_description', [
                    'specUrl' => self::SPEC_URL_PAGINATION
                ]),
                'required' => false,
                'type' => 'integer',
                'example' => 10,
            ],
            'page[offset]' => [
                'description' => __('jsonapi::query_params.page.offset_description', [
                    'specUrl' => self::SPEC_URL_PAGINATION
                ]),
                'required' => false,
                'type' => 'integer',
                'example' => 0,
            ],
        ];
    }

    private function buildSortQueryParameter(): array
    {
        $response = $this->fetchTransformedResponse($this->jsonApiEndpointData->resourceType);
        $fields = array_keys($response['data']['attributes'] ?? []);

        $description = __(
            'jsonapi::query_params.sort.description',
            ['specUrl' => self::SPEC_URL_SORTING]
        );

        if (!empty($fields)) {
            $description .= "\n\n" . __(
                'jsonapi::query_params.sort.available',
                [
                    'resourceType' => $this->jsonApiEndpointData->resourceType,
                    'fields' => implode(', ', array_map(fn($field) => "`$field`", $fields))
                ]
            );
        }

        $sortParam = [
            'description' => $description,
            'required' => false,
            'example' => implode(',', array_slice($fields, 0, 2))
        ];

        return ['sort' => $sortParam];
    }

    private function buildFilterQueryParameter(): array
    {
        $description = __(
            'jsonapi::query_params.filter.description',
            ['specUrl' => self::SPEC_URL_FILTERING]
        );

        $filterParam = [
            'type' => 'string',
            'required' => false,
            'description' => $description,
            'example' => 'TODO',
        ];

        return ['filter' => $filterParam];
    }

    protected function isDataWillBeReturned(): bool
    {
        return in_array($this->jsonApiEndpointData->actionType, [
            JsonApiEndpointData::ACTION_LIST,
            JsonApiEndpointData::ACTION_SHOW,
            JsonApiEndpointData::ACTION_CREATE,
            JsonApiEndpointData::ACTION_UPDATE,
            JsonApiEndpointData::ACTION_SHOW_RELATED_TO_ONE,
            JsonApiEndpointData::ACTION_SHOW_RELATED_TO_MANY,
        ]);
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
