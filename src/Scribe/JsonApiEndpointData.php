<?php

namespace Sowl\JsonApi\Scribe;

use Illuminate\Routing\Route;
use Knuckles\Camel\Extraction\ExtractedEndpointData;
use Sowl\JsonApi\AbstractTransformer;
use Sowl\JsonApi\Relationships\RelationshipInterface;
use Sowl\JsonApi\Relationships\ToManyRelationship;
use Sowl\JsonApi\ResourceManager;
use Sowl\JsonApi\Routing\RelationshipNameExtractor;
use Sowl\JsonApi\Routing\ResourceTypeExtractor;

/**
 * Wraps ExtractedEndpointData with JSON:API specific data.
 *
 * This class adds JSON:API specific data extraction capabilities by wrapping ExtractedEndpointData.
 * It uses ResourceTypeExtractor and RelationshipNameExtractor to determine the resource type and relationship
 * information from the route.
 */
class JsonApiEndpointData
{
    // Resource actions
    public const ACTION_LIST = 'list';
    public const ACTION_SHOW = 'show';
    public const ACTION_CREATE = 'create';
    public const ACTION_UPDATE = 'update';
    public const ACTION_DELETE = 'delete';

    // Related resource actions
    public const ACTION_SHOW_RELATED_TO_ONE = 'show-related-to-one';
    public const ACTION_SHOW_RELATED_TO_MANY = 'show-related-to-many';

    // To-One relationship actions
    public const ACTION_SHOW_RELATIONSHIP_TO_ONE = 'show-relationship-to-one';
    public const ACTION_UPDATE_RELATIONSHIP_TO_ONE = 'update-relationship-to-one';

    // To-Many relationship actions
    public const ACTION_SHOW_RELATIONSHIP_TO_MANY = 'show-relationship-to-many';
    public const ACTION_ADD_RELATIONSHIP_TO_MANY = 'add-relationship-to-many';
    public const ACTION_UPDATE_RELATIONSHIP_TO_MANY = 'update-relationship-to-many';
    public const ACTION_REMOVE_RELATIONSHIP_TO_MANY = 'remove-relationship-to-many';

    // Custom action implemented by user
    public const ACTION_CUSTOM = 'custom';

    /**
     * The base endpoint data
     *
     * @var ExtractedEndpointData
     */
    public ExtractedEndpointData $endpointData;

    /**
     * The JSON:API resource type for this endpoint
     *
     * @var string|null
     */
    public ?string $resourceType = null;

    /**
     * The JSON:API relationship name for this endpoint (if it's a relationship endpoint)
     *
     * @var string|null
     */
    public ?string $relationshipName = null;

    public ?RelationshipInterface $relationship = null;

    /**
     * Whether this endpoint is a relationships endpoint (e.g. /articles/1/relationships/comments)
     *
     * @var bool
     */
    public bool $isRelationships = false;

    /**
     * The JSON:API action type for this endpoint (e.g. list, show, create, update, delete)
     */
    public ?string $actionType = null;

    private ResourceManager $rm;

    /**
     * Create a new instance from an existing ExtractedEndpointData.
     *
     * @param ExtractedEndpointData $endpointData The base endpoint data
     */
    private function __construct(ExtractedEndpointData $endpointData)
    {
        $this->endpointData = $endpointData;
        $this->rm = app(ResourceManager::class);
        $this->extractJsonApiData();
        $this->determineActionType();
    }

    /**
     * Create a new instance from an existing ExtractedEndpointData.
     *
     * @param ExtractedEndpointData $endpointData The base endpoint data
     *
     * @return self
     */
    public static function fromEndpointData(ExtractedEndpointData $endpointData): self
    {
        return new self($endpointData);
    }

    /**
     * Determine if the route is a list route (returns multiple resources)
     */
    public function isCollectionRoute(): bool
    {
        // Routes explicitly marked as list routes
        if ($this->actionType === self::ACTION_LIST) {
            return true;
        }

        if ($this->resourceType && $this->relationshipName) {
            $relationship = $this->rm
                ->relationshipsByResourceType($this->resourceType)
                ->get($this->relationshipName);

            return $relationship instanceof ToManyRelationship;
        }

        return false;
    }

    /**
     * Get the transformer for the resource type.
     */
    public function resourceTransformer(): AbstractTransformer
    {
        return $this->rm->transformerByResourceType($this->resourceType);
    }

    /**
     * Extract JSON:API specific data from the endpoint.
     */
    private function extractJsonApiData(): void
    {
        // Get the route from endpoint data
        $route = $this->endpointData->route;

        // Create extractors
        $resourceTypeExtractor = new ResourceTypeExtractor();
        $relationshipExtractor = new RelationshipNameExtractor();

        // Extract JSON:API specific data
        if (
            empty($resourceType = $resourceTypeExtractor->extract($route)) ||
            !$this->rm->hasResourceType($resourceType)
        ) {
            throw new \RuntimeException('No resource type found, are you sure this is a JSON:API endpoint?');
        }

        $this->resourceType = $resourceType;
        $this->isRelationships = $relationshipExtractor->isRelationships($route);
        $this->relationshipName = $relationshipExtractor->extract($route);
        $this->relationship = $this->relationshipName
            ? $this->rm
                ->relationshipsByResourceType($this->resourceType)
                ->get($this->relationshipName)
            : null;
    }

    /**
     * Determine the JSON:API action type for this endpoint.
     */
    private function determineActionType(): void
    {
        $route = $this->endpointData->route;
        $httpMethod = $route->methods()[0];
        $uri = $route->uri();

        if ($this->isRelationships) {
            $this->determineRelationshipAction($httpMethod);
            return;
        }

        if ($this->relationship !== null) {
            if (preg_match('/^' . $this->resourceType . '\/\{[^}]+\}\/[^\/]+/', $uri)) {
                $this->determineRelatedResourceAction();
                return;
            }
        }

        $this->determineResourceAction($httpMethod, $uri);

        if ($this->actionType === null) {
            $this->actionType = self::ACTION_CUSTOM;
        }
    }

    private function determineRelationshipAction(string $httpMethod): void
    {
        if ($this->relationship === null) {
            $this->actionType = self::ACTION_CUSTOM;
            return;
        }

        switch ($httpMethod) {
            case 'GET':
                $this->actionType = $this->relationship->isToOne()
                ? self::ACTION_SHOW_RELATIONSHIP_TO_ONE
                : self::ACTION_SHOW_RELATIONSHIP_TO_MANY;
                break;
            case 'POST':
                $this->actionType = self::ACTION_ADD_RELATIONSHIP_TO_MANY;
                break;
            case 'PATCH':
                $this->actionType = $this->relationship->isToOne()
                ? self::ACTION_UPDATE_RELATIONSHIP_TO_ONE
                : self::ACTION_UPDATE_RELATIONSHIP_TO_MANY;
                break;
            case 'DELETE':
                $this->actionType = self::ACTION_REMOVE_RELATIONSHIP_TO_MANY;
                break;
            default:
                $this->actionType = self::ACTION_CUSTOM;
        }
    }

    private function determineRelatedResourceAction(): void
    {
        if ($this->relationship === null) {
            $this->actionType = self::ACTION_CUSTOM;
            return;
        }

        $this->actionType = $this->relationship->isToOne()
            ? self::ACTION_SHOW_RELATED_TO_ONE
            : self::ACTION_SHOW_RELATED_TO_MANY;
    }

    private function determineResourceAction(string $httpMethod, string $uri): void
    {
        if (preg_match('/^' . $this->resourceType . '\/?$/', $uri) && $httpMethod === 'GET') {
            $this->actionType = self::ACTION_LIST;
            return;
        }

        switch ($httpMethod) {
            case 'GET':
                $this->actionType = self::ACTION_SHOW;
                break;
            case 'POST':
                $this->actionType = self::ACTION_CREATE;
                break;
            case 'PATCH':
            case 'PUT':
                $this->actionType = self::ACTION_UPDATE;
                break;
            case 'DELETE':
                $this->actionType = self::ACTION_DELETE;
                break;
            default:
                $this->actionType = self::ACTION_CUSTOM;
        }
    }

    /**
     * Get controller method name from route
     */
    private function getMethodNameFromRoute(Route $route): ?string
    {
        if (method_exists($route, 'getActionMethod')) {
            return $route->getActionMethod();
        }

        return null;
    }
}
