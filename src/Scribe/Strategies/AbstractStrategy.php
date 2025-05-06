<?php

namespace Sowl\JsonApi\Scribe\Strategies;

use Illuminate\Container\Container;
use Illuminate\Support\Str;
use Knuckles\Camel\Extraction\ExtractedEndpointData;
use Knuckles\Scribe\Extracting\Strategies\Strategy;
use Sowl\JsonApi\ResourceManager;
use Sowl\JsonApi\Routing\RelationshipNameExtractor;
use Sowl\JsonApi\Routing\ResourceTypeExtractor;
use Sowl\JsonApi\Scribe\JsonApiEndpointData;

/**
 * Abstract base strategy for JSON:API documentation in Scribe.
 *
 * Provides common utilities and helpers for all custom Scribe strategies in this package.
 * Handles detection of JSON:API endpoints, resource and relationship extraction, and shared configuration logic.
 *
 * @see https://github.com/knuckleswtf/scribe for Scribe documentation
 * @see docs/Scribe.md for package integration details and attribute usage
 */
abstract class AbstractStrategy extends Strategy
{
    protected ResourceManager $resourceManager;

    protected ResourceTypeExtractor $resourceTypeExtractor;

    protected RelationshipNameExtractor $relationshipNameExtractor;

    protected ?JsonApiEndpointData $jsonApiEndpointData = null;

    /**
     * Constructor
     *
     * @param  mixed  $config  The strategy configuration
     * @param  ResourceManager|null  $resourceManager  Resource manager instance (will use app container if null)
     */
    public function __construct(
        $config,
        ?ResourceManager $resourceManager = null,
    ) {
        parent::__construct($config);
        $this->resourceManager = $resourceManager ?? app(ResourceManager::class);
        $this->resourceTypeExtractor = new ResourceTypeExtractor();
        $this->relationshipNameExtractor = new RelationshipNameExtractor();
    }

    public function initJsonApiEndpointData(ExtractedEndpointData $endpointData): bool
    {
        $this->endpointData = $endpointData;

        if ($isJsonApi = $this->isJsonApi()) {
            $this->jsonApiEndpointData = JsonApiEndpointData::fromEndpointData($endpointData);
        }

        return $isJsonApi;
    }

    /**
     * Check if a given route is a JSON:API route
     */
    public function isJsonApi(): bool
    {
        if (config('jsonapi.scribe.middleware')) {
            // hack: We need to set container for getting the middleware.
            //       Cloning because don't want to change the real route container, as it may affect future behavior.
            $routeMiddleware = (clone $this->endpointData->route)
                ->setContainer(new Container())
                ->gatherMiddleware();

            return in_array(config('jsonapi.scribe.middleware'), $routeMiddleware);
        }

        if (config('jsonapi.routing.prefix')) {
            $routeName = $this->endpointData->route->getName();

            return Str::startsWith($routeName, config('jsonapi.routing.prefix'));
        }

        return false;
    }

    protected function rm(): ResourceManager
    {
        return $this->resourceManager;
    }
}
