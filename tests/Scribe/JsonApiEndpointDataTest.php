<?php

namespace Tests\Scribe;

use Illuminate\Routing\Route;
use Knuckles\Camel\Extraction\ExtractedEndpointData;
use Sowl\JsonApi\Scribe\JsonApiEndpointData;
use Tests\TestCase;

class JsonApiEndpointDataTest extends TestCase
{
    public function testDeterminesActionTypeCorrectly(): void
    {
        // Standard resource endpoints
        // List action
        $this->assertEndpointData(
            ['GET'],
            'users',
            'users',
            null,
            false,
            JsonApiEndpointData::ACTION_LIST,
            ['as' => 'jsonapi.users.list']
        );
        // Show action
        $this->assertEndpointData(
            ['GET'],
            'users/{user_id}',
            'users',
            null,
            false,
            JsonApiEndpointData::ACTION_SHOW,
            ['as' => 'jsonapi.users.show']
        );
        // Create action
        $this->assertEndpointData(
            ['POST'],
            'users',
            'users',
            null,
            false,
            JsonApiEndpointData::ACTION_CREATE,
            ['as' => 'jsonapi.users.create']
        );
        // Update action
        $this->assertEndpointData(
            ['PATCH'],
            'users/{user_id}',
            'users',
            null,
            false,
            JsonApiEndpointData::ACTION_UPDATE,
            ['as' => 'jsonapi.users.update']
        );
        // Delete action
        $this->assertEndpointData(
            ['DELETE'],
            'users/{user_id}',
            'users',
            null,
            false,
            JsonApiEndpointData::ACTION_DELETE,
            ['as' => 'jsonapi.users.delete']
        );
        // Relationship endpoints
        // Show to-one relationship
        $this->assertEndpointData(
            ['GET'],
            'users/{user_id}/relationships/status',
            'users',
            'status',
            true,
            JsonApiEndpointData::ACTION_SHOW_RELATIONSHIP_TO_ONE,
            ['as' => 'jsonapi.users.relationships.status.show']
        );
        // Update to-one relationship
        $this->assertEndpointData(
            ['PATCH'],
            'users/{user_id}/relationships/status',
            'users',
            'status',
            true,
            JsonApiEndpointData::ACTION_UPDATE_RELATIONSHIP_TO_ONE,
            ['as' => 'jsonapi.users.relationships.status.update']
        );
        // Show to-many relationship
        $this->assertEndpointData(
            ['GET'],
            'users/{user_id}/relationships/roles',
            'users',
            'roles',
            true,
            JsonApiEndpointData::ACTION_SHOW_RELATIONSHIP_TO_MANY,
            ['as' => 'jsonapi.users.relationships.roles.show']
        );
        // Add to-many relationship
        $this->assertEndpointData(
            ['POST'],
            'users/{user_id}/relationships/roles',
            'users',
            'roles',
            true,
            JsonApiEndpointData::ACTION_ADD_RELATIONSHIP_TO_MANY,
            ['as' => 'jsonapi.users.relationships.roles.add']
        );
        // Update to-many relationship
        $this->assertEndpointData(
            ['PATCH'],
            'users/{user_id}/relationships/roles',
            'users',
            'roles',
            true,
            JsonApiEndpointData::ACTION_UPDATE_RELATIONSHIP_TO_MANY,
            ['as' => 'jsonapi.users.relationships.roles.update']
        );
        // Remove to-many relationship
        $this->assertEndpointData(
            ['DELETE'],
            'users/{user_id}/relationships/roles',
            'users',
            'roles',
            true,
            JsonApiEndpointData::ACTION_REMOVE_RELATIONSHIP_TO_MANY,
            ['as' => 'jsonapi.users.relationships.roles.remove']
        );
        // Related resource endpoints
        // Show to-one related
        $this->assertEndpointData(
            ['GET'],
            'users/{user_id}/status',
            'users',
            'status',
            false,
            JsonApiEndpointData::ACTION_SHOW_RELATED_TO_ONE,
            ['as' => 'jsonapi.users.status.show']
        );
        // Show to-many related
        $this->assertEndpointData(
            ['GET'],
            'users/{user_id}/roles',
            'users',
            'roles',
            false,
            JsonApiEndpointData::ACTION_SHOW_RELATED_TO_MANY,
            ['as' => 'jsonapi.users.roles.show']
        );
        // Custom action endpoints
        // Count comments custom action
        $this->assertEndpointData(
            ['GET'],
            'users/{user_id}/count-comments',
            'users',
            null,
            false,
            JsonApiEndpointData::ACTION_CUSTOM,
            ['as' => 'jsonapi.users.count-comments']
        );
        // Verify email custom action
        $this->assertEndpointData(
            ['POST'],
            'users/{user_id}/verify-email',
            'users',
            null,
            false,
            JsonApiEndpointData::ACTION_CUSTOM,
            ['as' => 'jsonapi.users.verify-email']
        );
        // Reset password custom action
        $this->assertEndpointData(
            ['POST'],
            'users/{user_id}/reset-password',
            'users',
            null,
            false,
            JsonApiEndpointData::ACTION_CUSTOM,
            ['as' => 'jsonapi.users.reset-password']
        );
        // Toggle status custom action
        $this->assertEndpointData(
            ['PATCH'],
            'users/{user_id}/toggle-status',
            'users',
            null,
            false,
            JsonApiEndpointData::ACTION_CUSTOM,
            ['as' => 'jsonapi.users.toggle-status']
        );
        // Calculate metrics custom action
        $this->assertEndpointData(
            ['GET'],
            'pages/{page_id}/calculate-metrics',
            'pages',
            null,
            false,
            JsonApiEndpointData::ACTION_CUSTOM,
            ['as' => 'jsonapi.pages.calculate-metrics']
        );
        // Publish custom action
        $this->assertEndpointData(
            ['POST'],
            'pages/{page_id}/publish',
            'pages',
            null,
            false,
            JsonApiEndpointData::ACTION_CUSTOM,
            ['as' => 'jsonapi.pages.publish']
        );
        // Archive custom action
        $this->assertEndpointData(
            ['POST'],
            'pages/{page_id}/archive',
            'pages',
            null,
            false,
            JsonApiEndpointData::ACTION_CUSTOM,
            ['as' => 'jsonapi.pages.archive']
        );
        // Bulk delete custom action
        $this->assertEndpointData(
            ['DELETE'],
            'users/bulk-delete',
            'users',
            null,
            false,
            JsonApiEndpointData::ACTION_CUSTOM,
            ['as' => 'jsonapi.users.bulk-delete']
        );
        // Import custom action
        $this->assertEndpointData(
            ['POST'],
            'users/import',
            'users',
            null,
            false,
            JsonApiEndpointData::ACTION_CUSTOM,
            ['as' => 'jsonapi.users.import']
        );
        // Export custom action
        $this->assertEndpointData(
            ['GET'],
            'users/export',
            'users',
            null,
            false,
            JsonApiEndpointData::ACTION_CUSTOM,
            ['as' => 'jsonapi.users.export']
        );
    }

    public function testEmptyUriThrowsException(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No resource type found, are you sure this is a JSON:API endpoint?');

        // Create route instance with empty URI
        $route = new Route(
            ['GET'],
            '',
            [
                'as' => 'api.root',
                'uses' => fn () => null,
            ]
        );

        // Create base endpoint data using fromRoute
        $endpointData = ExtractedEndpointData::fromRoute($route);

        // This should throw an exception
        JsonApiEndpointData::fromEndpointData($endpointData);
    }

    public function testNonJsonApiRouteThrowsException(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Resource type "non-jsonapi-route" is not registered in the ResourceManager.');

        // Create route instance with non-JSON:API URI
        $route = new Route(
            ['GET'],
            'non-jsonapi-route',
            [
                'as' => 'api.non-jsonapi',
                'uses' => fn () => null,
            ]
        );

        // Create base endpoint data using fromRoute
        $endpointData = ExtractedEndpointData::fromRoute($route);

        // This should throw an exception
        JsonApiEndpointData::fromEndpointData($endpointData);
    }

    /**
     * Helper method to create and test JsonApiEndpointData
     */
    private function assertEndpointData(
        array $methods,
        string $uri,
        string $expectedResourceType,
        ?string $expectedRelationship,
        bool $expectedIsRelationships,
        string $expectedActionType,
        array $routeOptions = []
    ): void {
        // Create route instance
        $route = new Route(
            $methods,
            $uri,
            array_merge(['uses' => fn () => null], $routeOptions)
        );

        // Create base endpoint data using fromRoute
        $endpointData = ExtractedEndpointData::fromRoute($route);

        // Create JsonApiEndpointData instance
        $jsonApiEndpointData = JsonApiEndpointData::fromEndpointData($endpointData);

        // Check resource type
        $this->assertEquals($expectedResourceType, $jsonApiEndpointData->resourceType);

        // Check relationship name
        $this->assertEquals($expectedRelationship, $jsonApiEndpointData->relationshipName);

        // Check isRelationships flag
        $this->assertEquals($expectedIsRelationships, $jsonApiEndpointData->isRelationships);

        // Check action type
        $this->assertEquals($expectedActionType, $jsonApiEndpointData->actionType);
    }
}
