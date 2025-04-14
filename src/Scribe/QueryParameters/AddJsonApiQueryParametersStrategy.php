<?php

namespace Sowl\JsonApi\Scribe\QueryParameters;

use Knuckles\Camel\Extraction\ExtractedEndpointData;
use Sowl\JsonApi\Scribe\AbstractStrategy;
use Sowl\JsonApi\Scribe\JsonApiEndpointData;
use Sowl\JsonApi\Scribe\TransformerHelper;

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

        if (($fields = $this->buildFieldsQueryParameter()) !== null) {
            $queryParameters['fields'] = $fields;
        }

        if (($include = $this->buildIncludeQueryParameter()) !== null) {
            $queryParameters['include'] = $include;
        }

        if (($exclude = $this->buildExcludeQueryParameter()) !== null) {
            $queryParameters['exclude'] = $exclude;
        }

        if (($meta = $this->buildMetaQueryParameter()) !== null) {
            $queryParameters['meta'] = $meta;
        }

        if ($this->jsonApiEndpointData->isCollectionRoute()) {
            $queryParameters = array_merge($queryParameters, [
                'page' => $this->buildPageQueryParameter(),
                'sort' => $this->buildSortQueryParameter(),
            ]);

            if (($filter = $this->buildFilterQueryParameter()) !== null) {
                $queryParameters['filter'] = $filter;
            }
        }

        return $queryParameters;
    }

    private function buildFieldsQueryParameter(): ?array
    {
        if (!$this->isDataWillBeReturned()) {
            return null;
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

        return [
            'type' => 'object',
            'required' => false,
            'description' => $description,
            'test' => 'test',
            'example' => [
                $resourceType => implode(',', $fields)
            ]
        ];
    }

    private function buildIncludeQueryParameter(): ?array
    {
        if (!$this->isDataWillBeReturned()) {
            return null;
        }

        $transformer = $this->jsonApiEndpointData->resourceTransformer();
        $includes = $transformer->getAvailableIncludes();
        $default = $transformer->getDefaultIncludes();

        if (empty($includes)) {
            return null;
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

        return [
            'description' => $includeDescription,
            'required' => false,
            'example' => $includeExample,
        ];
    }

    private function buildExcludeQueryParameter(): ?array
    {
        if (!$this->isDataWillBeReturned()) {
            return null;
        }

        $transformer = $this->jsonApiEndpointData->resourceTransformer();
        $excludes = $transformer->getDefaultIncludes();

        if (empty($excludes)) {
            return null;
        }

        $excludeDescription = __('jsonapi::query_params.exclude.description');
        $excludeDescription .= "\n\n";
        $excludeDescription .= __(
            'jsonapi::query_params.exclude.available',
            ['excludes' => implode(', ', array_map(fn ($exclude) => "`$exclude`", $excludes))]
        );

        return [
            'description' => $excludeDescription,
            'required' => false,
            'example' => '', // Provide a meaningful example if possible
        ];
    }

    private function buildMetaQueryParameter(): ?array
    {
        if (!$this->isDataWillBeReturned()) {
            return null;
        }

        $resourceType = $this->jsonApiEndpointData->resourceType;
        $transformer = $this->jsonApiEndpointData->resourceTransformer();
        $metas = $transformer->getAvailableMetas();

        if (empty($metas)) {
            return null;
        }

        $description = __('jsonapi::query_params.meta.description');
        $description .= "\n\n" . __(
            'jsonapi::query_params.meta.available',
            [
                'resourceType' => $resourceType,
                'metas' => implode(', ', array_map(fn($meta) => "`$meta`", $metas))
            ]
        );

        return [
            'type' => 'object',
            'required' => false,
            'description' => $description,
            'example' => [
                $resourceType => implode(',', $metas)
            ]
        ];
    }

    private function buildFilterQueryParameter(): array
    {
        // TODO: Add filter examples
        $description = __(
            'jsonapi::query_params.filter.description',
            ['specUrl' => self::SPEC_URL_FILTERING]
        );
        return [
            'description' => $description,
            'style' => 'deepObject',
            'explode' => true,
            'required' => false,
            'schema' => [
                'type' => 'object',
                'additionalProperties' => true
            ],
            'example' => [
                'name' => 'John',
            ]
        ];
    }

    private function buildPageQueryParameter(): array
    {
        $description = __(
            'jsonapi::query_params.page.description',
            ['specUrl' => self::SPEC_URL_PAGINATION]
        );

        return [
            'description' => $description,
            'type' => 'object',
            'example' => [
                'number' => 1,
                'size' => 10
            ],
        ];
    }

    private function buildSortQueryParameter(): array
    {
        $resourceType = $this->jsonApiEndpointData->resourceType;
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

        return [
            'description' => $description,
            'required' => false,
            'example' => $fields[0] ?? null,
        ];
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
