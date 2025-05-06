<?php

namespace Sowl\JsonApi\Scribe\Strategies\Metadata;

use Knuckles\Camel\Extraction\ExtractedEndpointData;
use Knuckles\Camel\Extraction\Metadata;
use Sowl\JsonApi\Scribe\Attributes\ResourceMetadata;
use Sowl\JsonApi\Scribe\DisplayHelper;
use Sowl\JsonApi\Scribe\JsonApiEndpointData;
use Sowl\JsonApi\Scribe\Strategies\AbstractStrategy;
use Sowl\JsonApi\Scribe\Strategies\ReadsPhpAttributes;

/**
 * Scribe strategy to extract endpoint/group metadata from ResourceMetadata attributes.
 *
 * Reads ResourceMetadata annotations on controllers and methods to generate grouping, titles, and descriptions
 * for JSON:API endpoints in the generated documentation.
 *
 * Used by Scribe to organize and describe endpoint groups, subgroups, and resource metadata in API docs.
 *
 * @see docs/Scribe.md for attribute usage and integration details
 */
class GetFromResourceMetadataAttribute extends AbstractStrategy
{
    use DisplayHelper;
    use ReadsPhpAttributes;

    protected array $transParams;

    protected static function readAttributes(): array
    {
        return [
            ResourceMetadata::class,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function __invoke(ExtractedEndpointData $endpointData, array $settings = []): array
    {
        if (! $this->initJsonApiEndpointData($endpointData)) {
            return [];
        }

        $this->transParams = $this->defaultTransParams();

        [$attributesOnMethod, $attributesOnFormRequest, $attributesOnController] =
            $this->getAttributes($endpointData->method, $endpointData->controller);

        return $this->extractFromAttributes($attributesOnMethod, $attributesOnFormRequest, $attributesOnController);
    }

    protected function extractFromAttributes(
        array $attributesOnMethod,
        array $attributesOnFormRequest = [],
        array $attributesOnController = []
    ): ?array {
        $attributesMetadata = [];

        $allAttributes = [
            ...$attributesOnController,
            ...$attributesOnFormRequest,
            ...$attributesOnMethod,
        ];

        foreach ($allAttributes as $attribute) {
            if ($attribute instanceof ResourceMetadata) {
                $attributesMetadata = array_merge($attributesMetadata, $this->getFromResourceMetadata($attribute));
            }
        }

        return $this->mergeWithEndpointMetadata($this->endpointData->metadata, $attributesMetadata);
    }

    protected function mergeWithEndpointMetadata(Metadata $endpointMetadata, array $attributesMetadata): array
    {
        return [
            'groupName' => $endpointMetadata->groupName ?:
                $attributesMetadata['groupName'] ??
                $this->generateGroupName(),
            'groupDescription' => $endpointMetadata->groupDescription ?:
                $attributesMetadata['groupDescription'] ?? '',
            'subgroup' => $endpointMetadata->subgroup ?:
                $attributesMetadata['subgroup'] ??
                $this->generateSubgroupName(),
            'subgroupDescription' => $endpointMetadata->subgroupDescription ?:
                $attributesMetadata['subgroupDescription'] ?? '',
            'title' => $endpointMetadata->title ?:
                $attributesMetadata['title'] ??
                $this->generateTitle(),
            'description' => $endpointMetadata->description ?:
                $attributesMetadata['description'] ??
                $this->generateDescription(),
            'authenticated' => $endpointMetadata->authenticated ?:
                $attributesMetadata['authenticated'] ?? false,
        ];
    }

    protected function getFromResourceMetadata(ResourceMetadata $attribute): array
    {
        $metadata = [];

        if ($attribute->title) {
            $metadata['title'] = $attribute->title;
        }

        if ($attribute->description) {
            $metadata['description'] = $attribute->description;
        }

        if ($attribute->groupName) {
            $metadata['groupName'] = $attribute->groupName;
        }

        if ($attribute->groupDescription) {
            $metadata['groupDescription'] = $attribute->groupDescription;
        }

        if ($attribute->subgroup) {
            $metadata['subgroup'] = $attribute->subgroup;
        }

        if ($attribute->subgroupDescription) {
            $metadata['subgroupDescription'] = $attribute->subgroupDescription;
        }

        if ($attribute->authenticated) {
            $metadata['authenticated'] = $attribute->authenticated;
        }

        return $metadata;
    }

    /**
     * Generate a title for the action based on its type, resource, and relationship.
     */
    protected function generateTitle(): string
    {
        return match ($this->jsonApiEndpointData->actionType) {
            JsonApiEndpointData::ACTION_LIST => __('jsonapi::metadata.list', $this->transParams),
            JsonApiEndpointData::ACTION_SHOW => __('jsonapi::metadata.show', $this->transParams),
            JsonApiEndpointData::ACTION_CREATE => __('jsonapi::metadata.create', $this->transParams),
            JsonApiEndpointData::ACTION_UPDATE => __('jsonapi::metadata.update', $this->transParams),
            JsonApiEndpointData::ACTION_DELETE => __('jsonapi::metadata.delete', $this->transParams),
            JsonApiEndpointData::ACTION_SHOW_RELATED_TO_ONE =>
                __('jsonapi::metadata.show_related_to_one', $this->transParams),
            JsonApiEndpointData::ACTION_SHOW_RELATED_TO_MANY =>
                __('jsonapi::metadata.show_related_to_many', $this->transParams),
            JsonApiEndpointData::ACTION_SHOW_RELATIONSHIP_TO_ONE =>
                __('jsonapi::metadata.show_relationship_to_one', $this->transParams),
            JsonApiEndpointData::ACTION_UPDATE_RELATIONSHIP_TO_ONE =>
                __('jsonapi::metadata.update_relationship_to_one', $this->transParams),
            JsonApiEndpointData::ACTION_SHOW_RELATIONSHIP_TO_MANY =>
                __('jsonapi::metadata.show_relationship_to_many', $this->transParams),
            JsonApiEndpointData::ACTION_ADD_RELATIONSHIP_TO_MANY =>
                __('jsonapi::metadata.add_relationship_to_many', $this->transParams),
            JsonApiEndpointData::ACTION_UPDATE_RELATIONSHIP_TO_MANY =>
                __('jsonapi::metadata.update_relationship_to_many', $this->transParams),
            JsonApiEndpointData::ACTION_REMOVE_RELATIONSHIP_TO_MANY =>
                __('jsonapi::metadata.remove_relationship_to_many', $this->transParams),
            default => __('jsonapi::metadata.default_title', $this->transParams)
        };
    }

    /**
     * Generate a description for the action based on its type, resource, and relationship.
     */
    protected function generateDescription(): string
    {
        return match ($this->jsonApiEndpointData->actionType) {
            JsonApiEndpointData::ACTION_LIST => __('jsonapi::metadata.description.list', $this->transParams),
            JsonApiEndpointData::ACTION_SHOW => __('jsonapi::metadata.description.show', $this->transParams),
            JsonApiEndpointData::ACTION_CREATE => __('jsonapi::metadata.description.create', $this->transParams),
            JsonApiEndpointData::ACTION_UPDATE => __('jsonapi::metadata.description.update', $this->transParams),
            JsonApiEndpointData::ACTION_DELETE => __('jsonapi::metadata.description.delete', $this->transParams),
            JsonApiEndpointData::ACTION_SHOW_RELATED_TO_ONE =>
                __('jsonapi::metadata.description.show_related_to_one', $this->transParams),
            JsonApiEndpointData::ACTION_SHOW_RELATED_TO_MANY =>
                __('jsonapi::metadata.description.show_related_to_many', $this->transParams),
            JsonApiEndpointData::ACTION_SHOW_RELATIONSHIP_TO_ONE =>
                __('jsonapi::metadata.description.show_relationship_to_one', $this->transParams),
            JsonApiEndpointData::ACTION_UPDATE_RELATIONSHIP_TO_ONE =>
                __('jsonapi::metadata.description.update_relationship_to_one', $this->transParams),
            JsonApiEndpointData::ACTION_SHOW_RELATIONSHIP_TO_MANY =>
                __('jsonapi::metadata.description.show_relationship_to_many', $this->transParams),
            JsonApiEndpointData::ACTION_ADD_RELATIONSHIP_TO_MANY =>
                __('jsonapi::metadata.description.add_relationship_to_many', $this->transParams),
            JsonApiEndpointData::ACTION_UPDATE_RELATIONSHIP_TO_MANY =>
                __('jsonapi::metadata.description.update_relationship_to_many', $this->transParams),
            JsonApiEndpointData::ACTION_REMOVE_RELATIONSHIP_TO_MANY =>
                __('jsonapi::metadata.description.remove_relationship_to_many', $this->transParams),
            default => __('jsonapi::metadata.description.default_description', $this->transParams),
        };
    }

    protected function generateGroupName(): ?string
    {
        return $this->displayResourceType($this->jsonApiEndpointData->resourceType);
    }

    protected function generateSubgroupName(): ?string
    {
        $relationshipName = $this->jsonApiEndpointData->relationshipName;
        $isRelationships = $this->jsonApiEndpointData->isRelationships;

        // Determine subgroup based on action type and relationship presence
        if ($isRelationships) {
            // $subgroup = 'Relationship Actions'; // Existing subgroup for managing relationships
            $subgroup = sprintf('Relationship %s', $relationshipName);
        } else {
            // $subgroup = 'Resource Actions'; // Default subgroup for primary resource actions
            $subgroup = null;
        }

        return $subgroup;
    }

    protected function defaultTransParams(): array
    {
        $endpoint = $this->jsonApiEndpointData;
        $displayTypeSingular = $this->displayResourceType($endpoint->resourceType, false);
        $displayTypePlural = $this->displayResourceType($endpoint->resourceType);
        $displayRelationshipSingular = $this->displayResourceType($endpoint->relationshipName, false);
        $displayRelationshipPlural = $this->displayResourceType($endpoint->relationshipName);

        return [
            'displayTypeSingular' => $displayTypeSingular,
            'displayTypePlural' => $displayTypePlural,
            'displayRelationshipSingular' => $displayRelationshipSingular,
            'displayRelationshipPlural' => $displayRelationshipPlural,
        ];
    }
}
