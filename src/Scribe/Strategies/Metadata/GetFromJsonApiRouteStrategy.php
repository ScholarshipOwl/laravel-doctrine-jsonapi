<?php

namespace Sowl\JsonApi\Scribe\Strategies\Metadata;

use Knuckles\Camel\Extraction\ExtractedEndpointData;
use Sowl\JsonApi\Scribe\Strategies\AbstractStrategy;
use Sowl\JsonApi\Scribe\DisplayHelper;
use Sowl\JsonApi\Scribe\JsonApiEndpointData;

/**
 * Strategy to extract metadata from JSON:API routes
 */
class GetFromJsonApiRouteStrategy extends AbstractStrategy
{
    use DisplayHelper;

    protected array $transParams;

    /**
     * @inheritDoc
     */
    public function __invoke(ExtractedEndpointData $endpointData, array $settings = []): array
    {
        if (!$this->initJsonApiEndpointData($endpointData)) {
            // Not a JSON:API route, skip
            return [];
        }

        $this->transParams = $this->defaultTransParams();

        // Group all JSON:API routes together under the resource type
        return [
            'title'         => $endpointData->metadata->title       ?: $this->generateActionTitle(),
            'description'   => $endpointData->metadata->description ?: $this->generateActionDescription(),
            'groupName'     => $endpointData->metadata->groupName   ?: $this->generateGroupName(),
            'subgroup'      => $endpointData->metadata->subgroup    ?: $this->generateSubgroupName(),
        ];
    }

    /**
     * Generate a title for the action based on its type, resource, and relationship.
     */
    protected function generateActionTitle(): string
    {
        return match ($this->jsonApiEndpointData->actionType) {
            JsonApiEndpointData::ACTION_LIST =>
                __('jsonapi::metadata.list', $this->transParams),
            JsonApiEndpointData::ACTION_SHOW =>
                __('jsonapi::metadata.show', $this->transParams),
            JsonApiEndpointData::ACTION_CREATE =>
                __('jsonapi::metadata.create', $this->transParams),
            JsonApiEndpointData::ACTION_UPDATE =>
                __('jsonapi::metadata.update', $this->transParams),
            JsonApiEndpointData::ACTION_DELETE =>
                __('jsonapi::metadata.delete', $this->transParams),
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
            default =>
                __('jsonapi::metadata.default_title', $this->transParams)
        };
    }

    /**
     * Generate a description for the action based on its type, resource, and relationship.
     */
    protected function generateActionDescription(): string
    {
        return match ($this->jsonApiEndpointData->actionType) {
            JsonApiEndpointData::ACTION_LIST =>
                __('jsonapi::metadata.description.list', $this->transParams),
            JsonApiEndpointData::ACTION_SHOW =>
                __('jsonapi::metadata.description.show', $this->transParams),
            JsonApiEndpointData::ACTION_CREATE =>
                __('jsonapi::metadata.description.create', $this->transParams),
            JsonApiEndpointData::ACTION_UPDATE =>
                __('jsonapi::metadata.description.update', $this->transParams),
            JsonApiEndpointData::ACTION_DELETE =>
                __('jsonapi::metadata.description.delete', $this->transParams),
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
            default =>
                __('jsonapi::metadata.description.default_description', $this->transParams),
        };
    }
    protected function generateGroupName(): ?string
    {
        return  $this->displayResourceType($this->jsonApiEndpointData->resourceType);
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
