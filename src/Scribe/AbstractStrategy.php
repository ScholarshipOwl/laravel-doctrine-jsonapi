<?php

namespace Sowl\JsonApi\Scribe;

use Knuckles\Camel\Extraction\ExtractedEndpointData;
use Knuckles\Scribe\Extracting\Strategies\Strategy;
use Sowl\JsonApi\ResourceManager;
use Sowl\JsonApi\ResourceManipulator;
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
    protected JsonApiEndpointData $jsonApiEndpointData;
    private string $jsonapiPrefix;

    /**
     * Constructor
     *
     * @param mixed $config The strategy configuration
     * @param ResourceManager|null $resourceManager Resource manager instance (will use app container if null)
     */
    public function __construct(
        $config,
        ?ResourceManager $resourceManager = null,
    ) {
        parent::__construct($config);
        $this->resourceManager = $resourceManager ?? app(ResourceManager::class);
        $this->resourceTypeExtractor = new ResourceTypeExtractor();
        $this->relationshipNameExtractor = new RelationshipNameExtractor();
        $this->jsonapiPrefix = config('jsonapi.scribe.routeNamePrefix', 'jsonapi.');
    }

    public function initJsonApiEndpointData(ExtractedEndpointData $endpointData): bool
    {
        $this->endpointData = $endpointData;
        $this->jsonApiEndpointData = JsonApiEndpointData::fromEndpointData($endpointData);

        return $this->isJsonApi();
    }

    /**
     * Check if a given route is a JSON:API route
     */
    public function isJsonApi(): bool
    {
        $routeName = $this->endpointData->route->getName();
        return \Str::startsWith($routeName, $this->jsonapiPrefix);
    }

    protected function rm(): ResourceManager
    {
        return $this->resourceManager;
    }
}
