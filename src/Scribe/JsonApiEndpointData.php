<?php

namespace Sowl\JsonApi\Scribe;

use Knuckles\Camel\Extraction\ExtractedEndpointData;
use Sowl\JsonApi\AbstractTransformer;
use Sowl\JsonApi\Relationships\RelationshipInterface;
use Sowl\JsonApi\ResourceManager;
use Sowl\JsonApi\Routing\Concerns\HandlesRoutePrefixes;
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
    use HandlesRoutePrefixes;

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
     */
    public ExtractedEndpointData $endpointData;

    /**
     * The JSON:API resource type for this endpoint
     */
    public ?string $resourceType = null;

    /**
     * The JSON:API relationship name for this endpoint (if it's a relationship endpoint)
     */
    public ?string $relationshipName = null;

    public ?RelationshipInterface $relationship = null;

    /**
     * Whether this endpoint is a relationships endpoint (e.g. /articles/1/relationships/comments)
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
     * @param  ExtractedEndpointData  $endpointData  The base endpoint data
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
     * @param  ExtractedEndpointData  $endpointData  The base endpoint data
     */
    public static function fromEndpointData(ExtractedEndpointData $endpointData): self
    {
        return new self($endpointData);
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
        $resourceTypeExtractor = new ResourceTypeExtractor;
        $relationshipExtractor = new RelationshipNameExtractor;

        // Extract JSON:API specific data
        if (empty($resourceType = $resourceTypeExtractor->extract($route))) {
            throw new \RuntimeException(
                'No resource type found, are you sure this is a JSON:API endpoint?'
            );
        }

        if (! $this->rm->hasResourceType($resourceType)) {
            throw new \RuntimeException(
                sprintf(
                    'Resource type "%s" is not registered in the ResourceManager.',
                    $resourceType
                )
            );
        }

        $this->resourceType = $resourceType;
        $this->relationshipName = $relationshipExtractor->extract($route);
        $this->relationship = $this->relationshipName
            ? $this->rm
                ->relationshipsByResourceType($this->resourceType)
                ->get($this->relationshipName)
            : null;

        // Make sure to set isRelationships only if real relationship is registered.
        $this->isRelationships = $relationshipExtractor->isRelationships($route) && $this->relationship !== null;
    }

    /**
     * Determine the JSON:API action type for this endpoint.
     *
     * The algorithm follows this sequence of checks:
     * 1. If the endpoint is a relationships endpoint (/resource/{id}/relationships/relation),
     *    determine the relationship action based on HTTP method
     * 2. If there's a relationship detected (/resource/{id}/relation), determine
     *    the related resource action (show related to-one/to-many)
     * 3. If it's a resource instance URI (/resource/{id}), determine the standard
     *    instance action (show, update, delete) based on the HTTP method
     * 4. If it's a root resource URI (/resource), determine the collection
     *    action (list, create) based on the HTTP method
     * 5. Any other pattern is considered a custom action
     */
    private function determineActionType(): void
    {
        $route = $this->endpointData->route;
        $httpMethod = $route->methods()[0];
        $uri = $this->pathWithoutPrefix($route);

        $this->actionType = match (true) {
            // 1. Check if it's a relationships endpoint
            $this->isRelationships => $this->determineRelationshipAction($httpMethod),

            // 2. Check if it's a related resource endpoint (we have a relationship)
            $this->relationship !== null => $this->determineRelatedResourceAction(),

            // 3. Check for instance resource actions (show, update, delete)
            $this->isResourceInstanceUri($uri) => $this->determineInstanceResourceAction($httpMethod),

            // 4. Check for root resource actions (list, create)
            $this->isRootResourceUri($uri) => $this->determineRootResourceAction($httpMethod),

            // 5. All other patterns are custom actions
            default => self::ACTION_CUSTOM,
        };
    }

    /**
     * Check if the URI represents a root resource (e.g., /users, /posts)
     */
    private function isRootResourceUri(string $uri): bool
    {
        return preg_match('/^'.$this->resourceType.'\/?$/', $uri);
    }

    /**
     * Check if the URI represents a resource instance (e.g., /users/{id}, /posts/{post_id})
     */
    private function isResourceInstanceUri(string $uri): bool
    {
        // Matches patterns like: 'users/{user_id}', 'pages/{id}', 'comments/{comment}'
        return preg_match('/^'.$this->resourceType.'\/\{[^\/}]+\}$/', $uri);
    }

    private function determineRelationshipAction(string $httpMethod): string
    {
        return match ($httpMethod) {
            'GET' => $this->relationship->isToOne()
                ? self::ACTION_SHOW_RELATIONSHIP_TO_ONE
                : self::ACTION_SHOW_RELATIONSHIP_TO_MANY,
            'POST' => self::ACTION_ADD_RELATIONSHIP_TO_MANY,
            'PATCH' => $this->relationship->isToOne()
                ? self::ACTION_UPDATE_RELATIONSHIP_TO_ONE
                : self::ACTION_UPDATE_RELATIONSHIP_TO_MANY,
            'DELETE' => self::ACTION_REMOVE_RELATIONSHIP_TO_MANY,
            default => self::ACTION_CUSTOM,
        };
    }

    private function determineRelatedResourceAction(): string
    {
        return $this->relationship->isToOne()
            ? self::ACTION_SHOW_RELATED_TO_ONE
            : self::ACTION_SHOW_RELATED_TO_MANY;
    }

    private function determineRootResourceAction(string $httpMethod): string
    {
        return match ($httpMethod) {
            'GET' => self::ACTION_LIST,
            'POST' => self::ACTION_CREATE,
            default => self::ACTION_CUSTOM,
        };
    }

    private function determineInstanceResourceAction(string $httpMethod): string
    {
        return match ($httpMethod) {
            'GET' => self::ACTION_SHOW,
            'PATCH', 'PUT' => self::ACTION_UPDATE,
            'DELETE' => self::ACTION_DELETE,
            default => self::ACTION_CUSTOM,
        };
    }
}
