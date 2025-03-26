<?php

namespace Sowl\JsonApi\Scribe\Metadata;

use Knuckles\Camel\Extraction\ExtractedEndpointData;
use Sowl\JsonApi\Scribe\AbstractStrategy;
use Sowl\JsonApi\Scribe\JsonApiEndpointData;

/**
 * Strategy to extract metadata from JSON:API routes
 */
class GetFromJsonApiRouteStrategy extends AbstractStrategy
{
    /**
     * @inheritDoc
     */
    public function __invoke(ExtractedEndpointData $endpointData, array $routeRules = []): array
    {
        if (!$this->initJsonApiEndpointData($endpointData)) {
            // Not a JSON:API route, skip
            return [];
        }

        $title = $this->generateActionTitle();
        $description = $this->generateActionDescription();
        $resourceType = $this->jsonApiEndpointData->resourceType;
        $actionType = $this->jsonApiEndpointData->actionType;
        $relationshipName = $this->jsonApiEndpointData->relationshipName;

        $isRelationships = $this->jsonApiEndpointData->isRelationships;
        $isRelated = in_array($actionType, [
            JsonApiEndpointData::ACTION_SHOW_RELATED_TO_ONE,
            JsonApiEndpointData::ACTION_SHOW_RELATED_TO_MANY
        ]);

        // Determine subgroup based on action type and relationship presence
        if ($isRelationships) {
            // $subgroup = 'Relationship Actions'; // Existing subgroup for managing relationships
            $subgroup = sprintf('Relationship %s', $relationshipName);
        } else {
            // $subgroup = 'Resource Actions'; // Default subgroup for primary resource actions
            $subgroup = null;
        }

        $groupName = $this->convertToDisplay($resourceType);

        // Group all JSON:API routes together under the resource type
        return [
            'groupName' => $groupName,
            'groupDescription' => '', // Scribe uses groupName for display by default
            'title' => $title,
            'description' => $description,
            'subgroup' => $subgroup,
            'subgroupDescription' => '', // Keep subgroup descriptions clean
        ];
    }

    /**
     * Generate a title for the action based on its type, resource, and relationship.
     */
    protected function generateActionTitle(): string
    {
        $actionType = $this->jsonApiEndpointData->actionType;

        $displayTypeSingular = $this->convertToDisplay($this->jsonApiEndpointData->resourceType, false);
        $displayTypePlural = $this->convertToDisplay($this->jsonApiEndpointData->resourceType, true);
        $displayRelationshipSingular = $this->convertToDisplay($this->jsonApiEndpointData->relationshipName, false);
        $displayRelationshipPlural = $this->convertToDisplay($this->jsonApiEndpointData->relationshipName, true);

        $transParams = [
            'displayTypeSingular' => $displayTypeSingular,
            'displayTypePlural' => $displayTypePlural,
            'displayRelationshipSingular' => $displayRelationshipSingular,
            'displayRelationshipPlural' => $displayRelationshipPlural,
            'actionType' => $actionType,
        ];

        return match ($actionType) {
            JsonApiEndpointData::ACTION_LIST => __('jsonapi::metadata.list', $transParams),
            JsonApiEndpointData::ACTION_SHOW => __('jsonapi::metadata.show', $transParams),
            JsonApiEndpointData::ACTION_CREATE => __('jsonapi::metadata.create', $transParams),
            JsonApiEndpointData::ACTION_UPDATE => __('jsonapi::metadata.update', $transParams),
            JsonApiEndpointData::ACTION_DELETE => __('jsonapi::metadata.delete', $transParams),
            JsonApiEndpointData::ACTION_SHOW_RELATED_TO_ONE => __('jsonapi::metadata.show_related_to_one', $transParams),
            JsonApiEndpointData::ACTION_SHOW_RELATED_TO_MANY => __('jsonapi::metadata.show_related_to_many', $transParams),
            JsonApiEndpointData::ACTION_SHOW_RELATIONSHIP_TO_ONE => __('jsonapi::metadata.show_relationship_to_one', $transParams),
            JsonApiEndpointData::ACTION_UPDATE_RELATIONSHIP_TO_ONE => __('jsonapi::metadata.update_relationship_to_one', $transParams),
            JsonApiEndpointData::ACTION_SHOW_RELATIONSHIP_TO_MANY => __('jsonapi::metadata.show_relationship_to_many', $transParams),
            JsonApiEndpointData::ACTION_ADD_RELATIONSHIP_TO_MANY => __('jsonapi::metadata.add_relationship_to_many', $transParams),
            JsonApiEndpointData::ACTION_UPDATE_RELATIONSHIP_TO_MANY => __('jsonapi::metadata.update_relationship_to_many', $transParams),
            JsonApiEndpointData::ACTION_REMOVE_RELATIONSHIP_TO_MANY => __('jsonapi::metadata.remove_relationship_to_many', $transParams),
            default => __('jsonapi::metadata.default_title', $transParams)
        };
    }

    /**
     * Generate a description for the action based on its type, resource, and relationship.
     */
    protected function generateActionDescription(): string
    {
        $actionType = $this->jsonApiEndpointData->actionType;

        $displayTypeSingular = $this->convertToDisplay($this->jsonApiEndpointData->resourceType, false);
        $displayTypePlural = $this->convertToDisplay($this->jsonApiEndpointData->resourceType, true);
        $displayRelationshipSingular = $this->convertToDisplay($this->jsonApiEndpointData->relationshipName, false);
        $displayRelationshipPlural = $this->convertToDisplay($this->jsonApiEndpointData->relationshipName, true);

        $transParams = [
            'displayTypeSingular' => $displayTypeSingular,
            'displayTypePlural' => $displayTypePlural,
            'displayRelationshipSingular' => $displayRelationshipSingular,
            'displayRelationshipPlural' => $displayRelationshipPlural,
            'actionType' => $actionType,
        ];

        return match ($actionType) {
            JsonApiEndpointData::ACTION_LIST => __('jsonapi::metadata.description.list', $transParams),
            JsonApiEndpointData::ACTION_SHOW => __('jsonapi::metadata.description.show', $transParams),
            JsonApiEndpointData::ACTION_CREATE => __('jsonapi::metadata.description.create', $transParams),
            JsonApiEndpointData::ACTION_UPDATE => __('jsonapi::metadata.description.update', $transParams),
            JsonApiEndpointData::ACTION_DELETE => __('jsonapi::metadata.description.delete', $transParams),
            JsonApiEndpointData::ACTION_SHOW_RELATED_TO_ONE => __('jsonapi::metadata.description.show_related_to_one', $transParams),
            JsonApiEndpointData::ACTION_SHOW_RELATED_TO_MANY => __('jsonapi::metadata.description.show_related_to_many', $transParams),
            JsonApiEndpointData::ACTION_SHOW_RELATIONSHIP_TO_ONE => __('jsonapi::metadata.description.show_relationship_to_one', $transParams),
            JsonApiEndpointData::ACTION_UPDATE_RELATIONSHIP_TO_ONE => __('jsonapi::metadata.description.update_relationship_to_one', $transParams),
            JsonApiEndpointData::ACTION_SHOW_RELATIONSHIP_TO_MANY => __('jsonapi::metadata.description.show_relationship_to_many', $transParams),
            JsonApiEndpointData::ACTION_ADD_RELATIONSHIP_TO_MANY => __('jsonapi::metadata.description.add_relationship_to_many', $transParams),
            JsonApiEndpointData::ACTION_UPDATE_RELATIONSHIP_TO_MANY => __('jsonapi::metadata.description.update_relationship_to_many', $transParams),
            JsonApiEndpointData::ACTION_REMOVE_RELATIONSHIP_TO_MANY => __('jsonapi::metadata.description.remove_relationship_to_many', $transParams),
            default => __('jsonapi::metadata.description.default_description', $transParams),
        };
    }

    private function convertToDisplay(?string $value, bool $plural = true): ?string
    {
        if (!$value) {
            return null;
        }

        $display = \Str::headline(\Str::singular(\Str::snake($value)));
        $display = ucfirst(strtolower($display));
        return $plural ? \Str::plural($display) : \Str::singular($display);
    }
}
