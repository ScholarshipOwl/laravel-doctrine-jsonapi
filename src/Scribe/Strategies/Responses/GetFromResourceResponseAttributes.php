<?php

namespace Sowl\JsonApi\Scribe\Strategies\Responses;

use Knuckles\Camel\Extraction\ExtractedEndpointData;
use Sowl\JsonApi\Fractal\FractalOptions;
use Sowl\JsonApi\Relationships\ToManyRelationship;
use Sowl\JsonApi\Relationships\ToOneRelationship;
use Sowl\JsonApi\Scribe\Attributes\ResourceResponseRelated;
use Sowl\JsonApi\Scribe\Attributes\ResourceResponseRelationships;
use Sowl\JsonApi\Scribe\Strategies\AbstractStrategy;
use Sowl\JsonApi\Scribe\Attributes\ResourceResponse;
use Sowl\JsonApi\Scribe\Strategies\ReadsPhpAttributes;
use Sowl\JsonApi\Scribe\Strategies\TransformerHelper;

/**
 * Scribe strategy to extract response examples and schemas for JSON:API endpoints from resource response attributes.
 *
 * Scans for ResourceResponse, ResourceResponseRelated, and ResourceResponseRelationships attributes on controllers/methods
 * to generate response documentation, including example data and schemas for JSON:API endpoints.
 *
 * Used by Scribe to provide detailed response documentation for each endpoint in the generated API docs.
 *
 * @see docs/Scribe.md for attribute usage and integration details
 */
class GetFromResourceResponseAttributes extends AbstractStrategy
{
    use ReadsPhpAttributes;
    use TransformerHelper;

    public function __invoke(ExtractedEndpointData $endpointData, array $settings = []): ?array
    {
        $this->initJsonApiEndpointData($endpointData);

        [$attributesOnMethod, $attributesOnFormRequest, $attributesOnController] =
            $this->getAttributes($endpointData->method, $endpointData->controller);

        return $this->extractFromAttributes($attributesOnMethod, $attributesOnFormRequest, $attributesOnController);
    }

    protected static function readAttributes(): array
    {
        return [
            ResourceResponse::class,
            ResourceResponseRelated::class,
            ResourceResponseRelationships::class,
        ];
    }

    protected function extractFromAttributes(
        array $attributesOnMethod,
        array $attributesOnFormRequest = [],
        array $attributesOnController = []
    ): ?array {
        $responses = [];
        $allAttributes = [
            ...$attributesOnController,
            ...$attributesOnFormRequest,
            ...$attributesOnMethod
        ];

        foreach ($allAttributes as $attributeInstance) {
            $response = match (true) {
                $attributeInstance instanceof ResourceResponseRelated,
                $attributeInstance instanceof ResourceResponseRelationships =>
                    $this->getResourceRelationshipOrRelatedResponse($attributeInstance),
                $attributeInstance instanceof ResourceResponse =>
                    $this->getResourceResponse($attributeInstance),
                default => []
            };

            if (!empty($response)) {
                $responses[] = $response;
            }
        }

        return $responses;
    }

    protected function getResourceResponse(ResourceResponse $attributeInstance): array
    {
        $resourceType = $attributeInstance->resourceType ?? $this->jsonApiEndpointData->resourceType;
        if (empty($resourceType)) {
            return [];
        }

        $fractalOptions = FractalOptions::fromArray($attributeInstance->fractalOptions);
        $response = $attributeInstance->collection
            ? $this->fetchTransformedCollectionResponse(
                $resourceType,
                $fractalOptions,
                $attributeInstance->pageNumber,
                $attributeInstance->pageSize,
            )
            : $this->fetchTransformedResponse($resourceType, $fractalOptions);

        return [
            'status' => $attributeInstance->status ?? 200,
            'description' => $attributeInstance->description ?? '',
            'content' => $response
        ];
    }

    protected function getResourceRelationshipOrRelatedResponse(
        ResourceResponseRelated|ResourceResponseRelationships $attributeInstance
    ): array {
        $resourceType = $attributeInstance->resourceType ?? $this->jsonApiEndpointData->resourceType;
        if (empty($resourceType)) {
            return [];
        }

        $resourceClass = $this->rm()->classByResourceType($resourceType);
        $relationshipName = $attributeInstance->relationshipName ?? $this->jsonApiEndpointData->relationshipName;
        $relationship = $this->rm()->relationshipsByClass($resourceClass)->get($relationshipName);
        $isRelationships = $attributeInstance instanceof ResourceResponseRelationships;

        if (!$relationship) {
            throw new \InvalidArgumentException(
                "Relationship $relationshipName on resource $resourceClass does not exist"
            );
        }

        if ($relationship instanceof ToOneRelationship) {
            $content = $this->fetchTransformedResponse(
                $relationship->resourceType(),
                FractalOptions::fromArray($attributeInstance->fractalOptions),
                isRelationship: $isRelationships
            );
        }

        if ($relationship instanceof ToManyRelationship) {
            $content = $this->fetchTransformedCollectionResponse(
                $relationship->resourceType(),
                FractalOptions::fromArray($attributeInstance->fractalOptions),
                $attributeInstance->pageNumber,
                $attributeInstance->pageSize,
                isRelationship: $isRelationships
            );
        }

        return [
            'status' => $attributeInstance->status ?? 200,
            'description' => $attributeInstance->description,
            'content' => $content
        ];
    }
}
