<?php

namespace Sowl\JsonApi\Scribe;

use Illuminate\Routing\Route;
use Knuckles\Camel\Extraction\ExtractedEndpointData;
use Knuckles\Scribe\Extracting\Strategies\Strategy;
use ReflectionClass;
use Sowl\JsonApi\ResourceManager;
use Sowl\JsonApi\Routing\RelationshipNameExtractor;
use Sowl\JsonApi\Routing\ResourceTypeExtractor;

/**
 * Abstract base strategy for JSON:API documentation.
 * Provides common utilities for other JSON:API Scribe strategies.
 */
abstract class AbstractStrategy extends Strategy
{
    protected ResourceManager $resourceManager;
    protected ResourceTypeExtractor $resourceTypeExtractor;
    protected RelationshipNameExtractor $relationshipNameExtractor;

    /**
     * Constructor
     *
     * @param mixed $config The strategy configuration
     * @param ResourceManager|null $resourceManager Resource manager instance (will use app container if null)
     */
    public function __construct($config, ?ResourceManager $resourceManager = null)
    {
        parent::__construct($config);
        $this->resourceManager = $resourceManager ?? app(ResourceManager::class);
        $this->resourceTypeExtractor = new ResourceTypeExtractor();
        $this->relationshipNameExtractor = new RelationshipNameExtractor();
    }

    /**
     * Check if a given route is a JSON:API route
     */
    public function isJsonApi(ExtractedEndpointData $endpointData): bool
    {
        $routeName = $endpointData->route->getName();
        return \Str::startsWith($routeName, 'jsonapi.');
    }

    /**
     * Determine if the route is a list route (returns multiple resources)
     *
     * @param ExtractedEndpointData $endpointData
     * @return bool
     */
    public function isListRoute(ExtractedEndpointData $endpointData): bool
    {
        // Routes explicitly marked as list routes
        if (\Str::endsWith($endpointData->route->getName(), '.list')) {
            return true;
        }

        // Get the resource type and relationship name from the route
        $resourceType = $this->resourceTypeExtractor->extract($endpointData->route);
        $relationshipName = $this->relationshipNameExtractor->extract($endpointData->route);

        if ($resourceType && $relationshipName) {
            $relationship = $this->resourceManager
                ->relationshipsByResourceType($resourceType)
                ->get($relationshipName);

            return $relationship instanceof \Sowl\JsonApi\Relationships\ToManyRelationship;
        }

        return false;
    }

    /**
     * Determine the action type based on method name
     *
     * @param string $methodName
     * @return string
     */
    protected function determineActionType(string $methodName): string
    {
        $actionTypeMap = [
            'list' => 'list',
            'show' => 'show',
            'create' => 'create',
            'update' => 'update',
            'remove' => 'delete',
            'showRelated' => 'show-related',
            'showRelationships' => 'show-relationships',
            'createRelationships' => 'create-relationships',
            'updateRelationships' => 'update-relationships',
            'removeRelationships' => 'remove-relationships',
        ];

        return $actionTypeMap[$methodName] ?? 'other';
    }

    /**
     * Get the extracted controller reflection class from route
     *
     * @param Route $route
     * @return ReflectionClass|null
     */
    protected function getControllerReflection(Route $route): ?ReflectionClass
    {
        try {
            $controller = $route->getController();
            if ($controller) {
                return new ReflectionClass(get_class($controller));
            }
        } catch (\Exception $e) {
            // Can't determine controller
        }

        return null;
    }

    /**
     * Get controller method name from route
     *
     * @param Route $route
     * @return string|null
     */
    protected function getMethodNameFromRoute(Route $route): ?string
    {
        if (method_exists($route, 'getActionMethod')) {
            return $route->getActionMethod();
        }

        return null;
    }

    protected function getListRelationshipsRouteSuffixes(): array
    {
        return [
            'list',
            'showRelated',
            'showRelationships',
            'createRelationships',
            'updateRelationships',
            'removeRelationships',
        ];
    }
}
