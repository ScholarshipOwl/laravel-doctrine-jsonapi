<?php

namespace Sowl\JsonApi\Scribe;

use Illuminate\Routing\Route;
use Knuckles\Camel\Extraction\ExtractedEndpointData;
use Knuckles\Scribe\Extracting\Strategies\Strategy;
use ReflectionClass;
use Sowl\JsonApi\ResourceManager;

/**
 * Abstract base strategy for JSON:API documentation.
 * Provides common utilities for other JSON:API Scribe strategies.
 */
abstract class AbstractStrategy extends Strategy
{
    protected ResourceManager $resourceManager;

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
    }

    /**
     * Check if a given route is a JSON:API route
     */
    public function isJsonApi(ExtractedEndpointData $endpointData): bool
    {
        return \Str::startsWith($endpointData->name(), 'jsonapi.');
    }

    public function isListRoute(ExtractedEndpointData $endpointData): bool
    {
        if (\Str::endsWith($endpointData->name(), '.list')) {
            return true;
        }

        if (in_array($endpointData->name(), $this->getListRelationshipsRouteSuffixes())) {
            // Extract the relationship name from the route
            $routeParts = explode('.', $endpointData->name());
            $relationshipName = $routeParts[count($routeParts) - 2] ?? null;
            
            // Get the resource type from route
            $resourceType = $this->extractResourceTypeFromRoute($endpointData->route);
            
            if ($resourceType && $relationshipName) {
                // Check if the resource exists and if the relationship is to-many
                try {
                    if ($this->resourceManager->hasResourceType($resourceType)) {
                        // Get relationships collection for the resource type
                        $relationships = $this->resourceManager->relationshipsByResourceType($resourceType);
                        
                        // Check if the relationship exists and if it's to-many
                        if ($relationships->has($relationshipName)) {
                            $relationship = $relationships->get($relationshipName);
                            // Check if it's a to-many relationship by checking the instance type
                            return $relationship instanceof \Sowl\JsonApi\Relationships\ToManyRelationship;
                        }
                    }
                } catch (\Exception $e) {
                    // If we can't determine the relationship type, default to previous behavior
                    return true;
                }
            }
            
            // If we can't determine the relationship type, default to previous behavior
            return true;
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
     * Extract resource type from route parameters or URI
     *
     * @param Route $route
     * @return string|null
     */
    protected function extractResourceTypeFromRoute(Route $route): ?string
    {
        // First check if this is a specific resource route
        $uri = $route->uri();
        if (preg_match('/^(\w+)\//', $uri, $matches)) {
            // Specific resource like 'users', 'roles', etc.
            return $matches[1];
        }

        // Check if this is a generic {resourceType} route
        $parameterNames = $route->parameterNames();
        if (in_array('resourceType', $parameterNames)) {
            return '{resourceType}';
        }

        return null;
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
