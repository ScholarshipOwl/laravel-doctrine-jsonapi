<?php

namespace Sowl\JsonApi\Scribe\Strategies\Responses;

use Knuckles\Camel\Extraction\ExtractedEndpointData;
use Sowl\JsonApi\Fractal\FractalOptions;
use Sowl\JsonApi\Relationships\ToManyRelationship;
use Sowl\JsonApi\Relationships\ToOneRelationship;
use Sowl\JsonApi\Scribe\Attributes\ResourceResponseRelated;
use Sowl\JsonApi\Scribe\Attributes\ResourceResponseRelatinships;
use Sowl\JsonApi\Scribe\Strategies\AbstractStrategy;
use Sowl\JsonApi\Scribe\Attributes\ResourceResponse;
use Sowl\JsonApi\Scribe\Strategies\ReadsPhpAttributes;
use Sowl\JsonApi\Scribe\Strategies\TransformerHelper;

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
            ResourceResponseRelatinships::class,
        ];
    }

    protected function extractFromAttributes(
        array $attributesOnMethod,
        array $attributesOnFormRequest = [],
        array $attributesOnController = []
    ): ?array
    {
        $responses = [];
        $allAttributes = [
            ...$attributesOnController,
            ...$attributesOnFormRequest,
            ...$attributesOnMethod
        ];

        foreach ($allAttributes as $attributeInstance) {
            $response = match (true) {
                $attributeInstance instanceof ResourceResponse =>
                    $this->getResourceResponse($attributeInstance),
                $attributeInstance instanceof ResourceResponseRelated,
                $attributeInstance instanceof ResourceResponseRelatinships =>
                    $this->getResourceRelationshipOrRelatedResponse($attributeInstance),
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
        ResourceResponseRelated|ResourceResponseRelatinships $attributeInstance
    ): array
    {
        $resourceType = $attributeInstance->resourceType ?? $this->jsonApiEndpointData->resourceType;
        if (empty($resourceType)) {
            return [];
        }

        $resourceClass = $this->rm()->classByResourceType($resourceType);
        $relationshipName = $attributeInstance->relationshipName ??$this->jsonApiEndpointData->relationshipName;
        $relationship = $this->rm()->relationshipsByClass($resourceClass)->get($relationshipName);
        $isRelationships = $attributeInstance instanceof ResourceResponseRelatinships;

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