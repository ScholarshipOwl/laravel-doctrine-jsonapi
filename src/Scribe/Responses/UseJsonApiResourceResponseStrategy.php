<?php

namespace Sowl\JsonApi\Scribe\Responses;

use Knuckles\Camel\Extraction\ExtractedEndpointData;
use Sowl\JsonApi\Scribe\AbstractStrategy;
use Sowl\JsonApi\Scribe\JsonApiEndpointData;
use Sowl\JsonApi\Scribe\ResponseGenerationHelper;

/**
 * Strategy to generate JSON:API compliant response examples using Doctrine entities
 */
class UseJsonApiResourceResponseStrategy extends AbstractStrategy
{
    use ResponseGenerationHelper;

    /**
     * @inheritDoc
     */
    public function __invoke(ExtractedEndpointData $endpointData, array $settings = []): array
    {
        if (!$this->initJsonApiEndpointData($endpointData)) {
            // Not a JSON:API route, skip
            return [];
        }

        $responses = [];

        // Add successful response
        if ($response = $this->generateResponse()) {
            $responses[] = $response;
        }

        return array_merge($responses, $this->generateBasicResponses());
    }

    /**
     * Generate response content based on action type and resource type
     */
    protected function generateResponse(): ?array
    {
        $actionType = $this->jsonApiEndpointData->actionType;
        $resourceType = $this->jsonApiEndpointData->resourceType;

        // For delete operations, return empty response
        if (
            in_array(
                $actionType,
                [
                    JsonApiEndpointData::ACTION_DELETE,
                    JsonApiEndpointData::ACTION_REMOVE_RELATIONSHIP_TO_MANY
                ]
            )
        ) {
            return [
                'status' => 204,
                'description' => __('jsonapi::responses.description.no_content'),
                'content' => null
            ];
        }

        if ($actionType == JsonApiEndpointData::ACTION_LIST) {
            return [
                'status' => 200,
                'description' => __('jsonapi::responses.description.success'),
                'content' => $this->generateCollectionContent($resourceType),
            ];
        }

        if (
            in_array(
                $actionType,
                [
                    JsonApiEndpointData::ACTION_SHOW,
                    JsonApiEndpointData::ACTION_CREATE,
                    JsonApiEndpointData::ACTION_UPDATE
                ]
            )
        ) {
            return [
                'status' => $actionType === JsonApiEndpointData::ACTION_CREATE ? 201 : 200,
                'description' => __('jsonapi::responses.description.success'),
                'content' => $this->generateSingleContent($resourceType),
            ];
        }

        if ($this->isRelationshipAction($actionType)) {
            return [
                'status' => 200,
                'description' => __('jsonapi::responses.description.success'),
                'content' => $this->generateRelationshipsContent($resourceType),
            ];
        }

        return [
            'status' => 200,
            'description' => __('jsonapi::responses.description.success'),
            'content' => [
                'data' => null
            ],
        ];
    }

    protected function generateBasicResponses(): array
    {
        $responses = [];

        // Add validation error for operations with request body
        if ($this->needsValidationErrorResponse()) {
            $responses[] = [
                'status' => 422,
                'description' => __('jsonapi::responses.description.validation_error'),
                'content' => $this->generateValidationErrorResponse()
            ];
        }

        $responses[] = [
            'status' => 404,
            'description' => __('jsonapi::responses.description.not_found'),
            'content' => $this->generateErrorResponse(
                404,
                __('jsonapi::responses.error.not_found.title'),
                __('jsonapi::responses.error.not_found.detail')
            )
        ];

        return $responses;
    }

    /**
     * Get the appropriate status code for an action type
     */
    protected function getStatusCodeForAction(): int
    {
        $actionType = $this->jsonApiEndpointData->actionType;

        if ($actionType === JsonApiEndpointData::ACTION_CREATE) {
            return 201; // Created
        }

        if (in_array($actionType, [
            JsonApiEndpointData::ACTION_DELETE,
            JsonApiEndpointData::ACTION_REMOVE_RELATIONSHIP_TO_MANY
        ])) {
            return 204; // No Content
        }

        return 200; // OK for all other actions
    }

    /**
     * Determine if an action type needs a validation error response
     */
    protected function needsValidationErrorResponse(): bool
    {
        $actionType = $this->jsonApiEndpointData->actionType;
        return in_array($actionType, [JsonApiEndpointData::ACTION_CREATE, JsonApiEndpointData::ACTION_UPDATE])
            || str_contains($actionType, JsonApiEndpointData::ACTION_CREATE) // Covers relationship adds
            || str_contains($actionType, JsonApiEndpointData::ACTION_UPDATE); // Covers relationship updates
    }

    /**
     * Generate a JSON:API error response
     */
    protected function generateErrorResponse(
        int $status,
        string $title,
        string $detail,
        ?array $source = null
    ): array {
        $errors = [
            [
                'status' => (string)$status,
                'title' => $title,
                'detail' => $detail,
            ]
        ];

        if ($source) {
            $errors[0]['source'] = $source;
        }

        return [
            'errors' => $errors
        ];
    }

    /**
     * Generate a validation error response
     */
    protected function generateValidationErrorResponse(): array
    {
        return $this->generateErrorResponse(
            422,
            __('jsonapi::responses.error.validation.title'),
            __('jsonapi::responses.error.validation.detail'),
            ['pointer' => '/data/attributes/email'] // Example pointer, might need refinement
        );
    }

}
