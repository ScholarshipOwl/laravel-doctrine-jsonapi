<?php

namespace Sowl\JsonApi\Scribe\Metadata;

use Knuckles\Camel\Extraction\ExtractedEndpointData;
use Sowl\JsonApi\Scribe\AbstractStrategy;

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
        $route = $endpointData->route;
        if (!$route || !$this->isJsonApiRoute($route)) {
            // Not a JSON:API route, skip
            return [];
        }

        $methodName = $this->getMethodNameFromRoute($route);
        if (!$methodName) {
            return [];
        }

        $actionType = $this->determineActionType($methodName);
        $resourceType = $this->extractResourceTypeFromRoute($route);

        if (!$resourceType) {
            return [];
        }

        $title = $this->generateActionTitle($actionType, $resourceType);
        $description = $this->generateActionDescription($actionType, $resourceType);

        // Group all JSON:API routes together
        return [
            'groupName' => 'JSON:API Resources',
            'groupDescription' => 'Endpoints for managing resources using the JSON:API specification.',
            'title' => $title,
            'description' => $description,
        ];
    }

    /**
     * Generate a title for the action
     *
     * @param string $actionType
     * @param string $resourceType
     * @return string
     */
    protected function generateActionTitle(string $actionType, string $resourceType): string
    {
        // Normalize resource type (for display purposes)
        $displayType = $resourceType === '{resourceType}' ? 'Resource' : $resourceType;

        $actionTitles = [
            'list' => "List {$displayType}",
            'show' => "Get single {$displayType}",
            'create' => "Create {$displayType}",
            'update' => "Update {$displayType}",
            'delete' => "Delete {$displayType}",
            'show-related' => "Get related resource of {$displayType}",
            'show-relationships' => "Get relationships of {$displayType}",
            'create-relationships' => "Add relationships to {$displayType}",
            'update-relationships' => "Update relationships of {$displayType}",
            'remove-relationships' => "Remove relationships from {$displayType}",
        ];

        return $actionTitles[$actionType] ?? "Interact with {$displayType}";
    }

    /**
     * Generate a description for the action
     *
     * @param string $actionType
     * @param string $resourceType
     * @return string
     */
    protected function generateActionDescription(string $actionType, string $resourceType): string
    {
        // Normalize resource type (for display purposes)
        $displayType = $resourceType === '{resourceType}' ? 'resource' : $resourceType;

        $actionDescriptions = [
            'list' => "Returns a collection of {$displayType} resources.",
            'show' => "Returns a single {$displayType} resource identified by ID.",
            'create' => "Creates a new {$displayType} resource.",
            'update' => "Updates an existing {$displayType} resource.",
            'delete' => "Deletes a {$displayType} resource.",
            'show-related' => "Returns a related resource of the {$displayType}.",
            'show-relationships' => "Returns the relationships of the {$displayType}.",
            'create-relationships' => "Adds relationships to the {$displayType}.",
            'update-relationships' => "Updates relationships of the {$displayType}.",
            'remove-relationships' => "Removes relationships from the {$displayType}.",
        ];

        return $actionDescriptions[$actionType] ?? "Interacts with {$displayType} resources according to the JSON:API specification.";
    }
}
