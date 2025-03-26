<?php

namespace Tests\Scribe\Extraction;

use Illuminate\Routing\Route;
use Knuckles\Camel\Extraction\ExtractedEndpointData;
use Sowl\JsonApi\Scribe\JsonApiEndpointData;
use Tests\TestCase;

class JsonApiEndpointDataTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideEndpointData
     */
    public function testDeterminesActionTypeCorrectly(
        array $methods,
        string $uri,
        string $expectedResourceType,
        ?string $expectedRelationship,
        bool $expectedIsRelationships,
        string $expectedActionType,
        array $routeOptions = []
    ) {
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

    public function provideEndpointData(): array
    {
        return [
            // Single resource endpoints
            'list' => [
                ['GET'],
                'users',
                'users',
                null,
                false,
                JsonApiEndpointData::ACTION_LIST,
                ['as' => 'jsonapi.users.list']
            ],
            'show' => [
                ['GET'],
                'users/{user_id}',
                'users',
                null,
                false,
                JsonApiEndpointData::ACTION_SHOW,
                ['as' => 'jsonapi.users.show']
            ],
            'create' => [
                ['POST'],
                'users',
                'users',
                null,
                false,
                JsonApiEndpointData::ACTION_CREATE,
                ['as' => 'jsonapi.users.create']
            ],
            'update' => [
                ['PATCH'],
                'users/{user_id}',
                'users',
                null,
                false,
                JsonApiEndpointData::ACTION_UPDATE,
                ['as' => 'jsonapi.users.update']
            ],
            'delete' => [
                ['DELETE'],
                'users/{user_id}',
                'users',
                null,
                false,
                JsonApiEndpointData::ACTION_DELETE,
                ['as' => 'jsonapi.users.delete']
            ],
            'show to-one relationship' => [
                ['GET'],
                'users/{user_id}/relationships/status',
                'users',
                'status',
                true,
                JsonApiEndpointData::ACTION_SHOW_RELATIONSHIP_TO_ONE,
                ['as' => 'jsonapi.users.relationships.status.show']
            ],
            'update to-one relationship' => [
                ['PATCH'],
                'users/{user_id}/relationships/status',
                'users',
                'status',
                true,
                JsonApiEndpointData::ACTION_UPDATE_RELATIONSHIP_TO_ONE,
                ['as' => 'jsonapi.users.relationships.status.update']
            ],
            'show to-many relationship' => [
                ['GET'],
                'users/{user_id}/relationships/roles',
                'users',
                'roles',
                true,
                JsonApiEndpointData::ACTION_SHOW_RELATIONSHIP_TO_MANY,
                ['as' => 'jsonapi.users.relationships.roles.show']
            ],
            'add to-many relationship' => [
                ['POST'],
                'users/{user_id}/relationships/roles',
                'users',
                'roles',
                true,
                JsonApiEndpointData::ACTION_ADD_RELATIONSHIP_TO_MANY,
                ['as' => 'jsonapi.users.relationships.roles.add']
            ],
            'update to-many relationship' => [
                ['PATCH'],
                'users/{user_id}/relationships/roles',
                'users',
                'roles',
                true,
                JsonApiEndpointData::ACTION_UPDATE_RELATIONSHIP_TO_MANY,
                ['as' => 'jsonapi.users.relationships.roles.update']
            ],
            'remove to-many relationship' => [
                ['DELETE'],
                'users/{user_id}/relationships/roles',
                'users',
                'roles',
                true,
                JsonApiEndpointData::ACTION_REMOVE_RELATIONSHIP_TO_MANY,
                ['as' => 'jsonapi.users.relationships.roles.remove']
            ],
            'show to-one related' => [
                ['GET'],
                'users/{user_id}/status',
                'users',
                'status',
                false,
                JsonApiEndpointData::ACTION_SHOW_RELATED_TO_ONE,
                ['as' => 'jsonapi.users.status.show']
            ],
            'show to-many related' => [
                ['GET'],
                'users/{user_id}/roles',
                'users',
                'roles',
                false,
                JsonApiEndpointData::ACTION_SHOW_RELATED_TO_MANY,
                ['as' => 'jsonapi.users.roles.show']
            ],
            'pages_list' => [
                ['GET'],
                'pages',
                'pages',
                null,
                false,
                JsonApiEndpointData::ACTION_LIST,
                ['as' => 'jsonapi.pages.list']
            ],
            'pages_create' => [
                ['POST'],
                'pages',
                'pages',
                null,
                false,
                JsonApiEndpointData::ACTION_CREATE,
                ['as' => 'jsonapi.pages.create']
            ],
            'pages_show' => [
                ['GET'],
                'pages/{id}',
                'pages',
                null,
                false,
                JsonApiEndpointData::ACTION_SHOW,
                ['as' => 'jsonapi.pages.show']
            ],
            'pages_update' => [
                ['PATCH'],
                'pages/{id}',
                'pages',
                null,
                false,
                JsonApiEndpointData::ACTION_UPDATE,
                ['as' => 'jsonapi.pages.update']
            ],
            'pages_delete' => [
                ['DELETE'],
                'pages/{id}',
                'pages',
                null,
                false,
                JsonApiEndpointData::ACTION_DELETE,
                ['as' => 'jsonapi.pages.delete']
            ],

            'pageComments_list' => [
                ['GET'],
                'pageComments',
                'pageComments',
                null,
                false,
                JsonApiEndpointData::ACTION_LIST,
                ['as' => 'jsonapi.pageComments.list']
            ],
            'pageComments_create' => [
                ['POST'],
                'pageComments',
                'pageComments',
                null,
                false,
                JsonApiEndpointData::ACTION_CREATE,
                ['as' => 'jsonapi.pageComments.create']
            ],
            'pageComments_show' => [
                ['GET'],
                'pageComments/{id}',
                'pageComments',
                null,
                false,
                JsonApiEndpointData::ACTION_SHOW,
                ['as' => 'jsonapi.pageComments.show']
            ],
            'pageComments_update' => [
                ['PATCH'],
                'pageComments/{id}',
                'pageComments',
                null,
                false,
                JsonApiEndpointData::ACTION_UPDATE,
                ['as' => 'jsonapi.pageComments.update']
            ],
            'pageComments_delete' => [
                ['DELETE'],
                'pageComments/{id}',
                'pageComments',
                null,
                false,
                JsonApiEndpointData::ACTION_DELETE,
                ['as' => 'jsonapi.pageComments.delete']
            ],

            'users_list' => [
                ['GET'],
                'users',
                'users',
                null,
                false,
                JsonApiEndpointData::ACTION_LIST,
                ['as' => 'jsonapi.users.list']
            ],
            'users_create' => [
                ['POST'],
                'users',
                'users',
                null,
                false,
                JsonApiEndpointData::ACTION_CREATE,
                ['as' => 'jsonapi.users.create']
            ],
            'users_show' => [
                ['GET'],
                'users/{id}',
                'users',
                null,
                false,
                JsonApiEndpointData::ACTION_SHOW,
                ['as' => 'jsonapi.users.show']
            ],
            'users_update' => [
                ['PATCH'],
                'users/{id}',
                'users',
                null,
                false,
                JsonApiEndpointData::ACTION_UPDATE,
                ['as' => 'jsonapi.users.update']
            ],
            'users_delete' => [
                ['DELETE'],
                'users/{id}',
                'users',
                null,
                false,
                JsonApiEndpointData::ACTION_DELETE,
                ['as' => 'jsonapi.users.delete']
            ],

            'roles_list' => [
                ['GET'],
                'roles',
                'roles',
                null,
                false,
                JsonApiEndpointData::ACTION_LIST,
                ['as' => 'jsonapi.roles.list']
            ],
            'roles_create' => [
                ['POST'],
                'roles',
                'roles',
                null,
                false,
                JsonApiEndpointData::ACTION_CREATE,
                ['as' => 'jsonapi.roles.create']
            ],
            'roles_show' => [
                ['GET'],
                'roles/{id}',
                'roles',
                null,
                false,
                JsonApiEndpointData::ACTION_SHOW,
                ['as' => 'jsonapi.roles.show']
            ],
            'roles_update' => [
                ['PATCH'],
                'roles/{id}',
                'roles',
                null,
                false,
                JsonApiEndpointData::ACTION_UPDATE,
                ['as' => 'jsonapi.roles.update']
            ],
            'roles_delete' => [
                ['DELETE'],
                'roles/{id}',
                'roles',
                null,
                false,
                JsonApiEndpointData::ACTION_DELETE,
                ['as' => 'jsonapi.roles.delete']
            ],

            'user-statuses_list' => [
                ['GET'],
                'user-statuses',
                'user-statuses',
                null,
                false,
                JsonApiEndpointData::ACTION_LIST,
                ['as' => 'jsonapi.user-statuses.list']
            ],
            'user-statuses_create' => [
                ['POST'],
                'user-statuses',
                'user-statuses',
                null,
                false,
                JsonApiEndpointData::ACTION_CREATE,
                ['as' => 'jsonapi.user-statuses.create']
            ],
            'user-statuses_show' => [
                ['GET'],
                'user-statuses/{id}',
                'user-statuses',
                null,
                false,
                JsonApiEndpointData::ACTION_SHOW,
                ['as' => 'jsonapi.user-statuses.show']
            ],
            'user-statuses_update' => [
                ['PATCH'],
                'user-statuses/{id}',
                'user-statuses',
                null,
                false,
                JsonApiEndpointData::ACTION_UPDATE,
                ['as' => 'jsonapi.user-statuses.update']
            ],
            'user-statuses_delete' => [
                ['DELETE'],
                'user-statuses/{id}',
                'user-statuses',
                null,
                false,
                JsonApiEndpointData::ACTION_DELETE,
                ['as' => 'jsonapi.user-statuses.delete']
            ],

            // Related resource endpoints
            'pages_user_related' => [
                ['GET'],
                'pages/{id}/user',
                'pages',
                'user',
                false,
                JsonApiEndpointData::ACTION_SHOW_RELATED_TO_ONE,
                ['as' => 'jsonapi.pages.user.show']
            ],
            'pages_pageComments_related' => [
                ['GET'],
                'pages/{id}/pageComments',
                'pages',
                'pageComments',
                false,
                JsonApiEndpointData::ACTION_SHOW_RELATED_TO_MANY,
                ['as' => 'jsonapi.pages.pageComments.show']
            ],

            'pageComments_user_related' => [
                ['GET'],
                'pageComments/{id}/user',
                'pageComments',
                'user',
                false,
                JsonApiEndpointData::ACTION_SHOW_RELATED_TO_ONE,
                ['as' => 'jsonapi.pageComments.user.show']
            ],
            'pageComments_page_related' => [
                ['GET'],
                'pageComments/{id}/page',
                'pageComments',
                'page',
                false,
                JsonApiEndpointData::ACTION_SHOW_RELATED_TO_ONE,
                ['as' => 'jsonapi.pageComments.page.show']
            ],

            'users_roles_related' => [
                ['GET'],
                'users/{id}/roles',
                'users',
                'roles',
                false,
                JsonApiEndpointData::ACTION_SHOW_RELATED_TO_MANY,
                ['as' => 'jsonapi.users.roles.show']
            ],
            'users_status_related' => [
                ['GET'],
                'users/{id}/status',
                'users',
                'status',
                false,
                JsonApiEndpointData::ACTION_SHOW_RELATED_TO_ONE,
                ['as' => 'jsonapi.users.status.show']
            ],

            // To-one relationship endpoints
            'pages_user_relationship_show' => [
                ['GET'],
                'pages/{id}/relationships/user',
                'pages',
                'user',
                true,
                JsonApiEndpointData::ACTION_SHOW_RELATIONSHIP_TO_ONE,
                ['as' => 'jsonapi.pages.relationships.user.show']
            ],
            'pages_user_relationship_update' => [
                ['PATCH'],
                'pages/{id}/relationships/user',
                'pages',
                'user',
                true,
                JsonApiEndpointData::ACTION_UPDATE_RELATIONSHIP_TO_ONE,
                ['as' => 'jsonapi.pages.relationships.user.update']
            ],

            'pageComments_user_relationship_show' => [
                ['GET'],
                'pageComments/{id}/relationships/user',
                'pageComments',
                'user',
                true,
                JsonApiEndpointData::ACTION_SHOW_RELATIONSHIP_TO_ONE,
                ['as' => 'jsonapi.pageComments.relationships.user.show']
            ],
            'pageComments_user_relationship_update' => [
                ['PATCH'],
                'pageComments/{id}/relationships/user',
                'pageComments',
                'user',
                true,
                JsonApiEndpointData::ACTION_UPDATE_RELATIONSHIP_TO_ONE,
                ['as' => 'jsonapi.pageComments.relationships.user.update']
            ],
            'pageComments_page_relationship_show' => [
                ['GET'],
                'pageComments/{id}/relationships/page',
                'pageComments',
                'page',
                true,
                JsonApiEndpointData::ACTION_SHOW_RELATIONSHIP_TO_ONE,
                ['as' => 'jsonapi.pageComments.relationships.page.show']
            ],
            'pageComments_page_relationship_update' => [
                ['PATCH'],
                'pageComments/{id}/relationships/page',
                'pageComments',
                'page',
                true,
                JsonApiEndpointData::ACTION_UPDATE_RELATIONSHIP_TO_ONE,
                ['as' => 'jsonapi.pageComments.relationships.page.update']
            ],

            'users_status_relationship_show' => [
                ['GET'],
                'users/{id}/relationships/status',
                'users',
                'status',
                true,
                JsonApiEndpointData::ACTION_SHOW_RELATIONSHIP_TO_ONE,
                ['as' => 'jsonapi.users.relationships.status.show']
            ],
            'users_status_relationship_update' => [
                ['PATCH'],
                'users/{id}/relationships/status',
                'users',
                'status',
                true,
                JsonApiEndpointData::ACTION_UPDATE_RELATIONSHIP_TO_ONE,
                ['as' => 'jsonapi.users.relationships.status.update']
            ],

            // To-many relationship endpoints
            'pages_pageComments_relationship_show' => [
                ['GET'],
                'pages/{id}/relationships/pageComments',
                'pages',
                'pageComments',
                true,
                JsonApiEndpointData::ACTION_SHOW_RELATIONSHIP_TO_MANY,
                ['as' => 'jsonapi.pages.relationships.pageComments.show']
            ],
            'pages_pageComments_relationship_add' => [
                ['POST'],
                'pages/{id}/relationships/pageComments',
                'pages',
                'pageComments',
                true,
                JsonApiEndpointData::ACTION_ADD_RELATIONSHIP_TO_MANY,
                ['as' => 'jsonapi.pages.relationships.pageComments.add']
            ],
            'pages_pageComments_relationship_update' => [
                ['PATCH'],
                'pages/{id}/relationships/pageComments',
                'pages',
                'pageComments',
                true,
                JsonApiEndpointData::ACTION_UPDATE_RELATIONSHIP_TO_MANY,
                ['as' => 'jsonapi.pages.relationships.pageComments.update']
            ],
            'pages_pageComments_relationship_remove' => [
                ['DELETE'],
                'pages/{id}/relationships/pageComments',
                'pages',
                'pageComments',
                true,
                JsonApiEndpointData::ACTION_REMOVE_RELATIONSHIP_TO_MANY,
                ['as' => 'jsonapi.pages.relationships.pageComments.remove']
            ],

            'users_roles_relationship_show' => [
                ['GET'],
                'users/{id}/relationships/roles',
                'users',
                'roles',
                true,
                JsonApiEndpointData::ACTION_SHOW_RELATIONSHIP_TO_MANY,
                ['as' => 'jsonapi.users.relationships.roles.show']
            ],
            'users_roles_relationship_add' => [
                ['POST'],
                'users/{id}/relationships/roles',
                'users',
                'roles',
                true,
                JsonApiEndpointData::ACTION_ADD_RELATIONSHIP_TO_MANY,
                ['as' => 'jsonapi.users.relationships.roles.add']
            ],
            'users_roles_relationship_update' => [
                ['PATCH'],
                'users/{id}/relationships/roles',
                'users',
                'roles',
                true,
                JsonApiEndpointData::ACTION_UPDATE_RELATIONSHIP_TO_MANY,
                ['as' => 'jsonapi.users.relationships.roles.update']
            ],
            'users_roles_relationship_remove' => [
                ['DELETE'],
                'users/{id}/relationships/roles',
                'users',
                'roles',
                true,
                JsonApiEndpointData::ACTION_REMOVE_RELATIONSHIP_TO_MANY,
                ['as' => 'jsonapi.users.relationships.roles.remove']
            ],
        ];
    }

    /**
     * @test
     */
    public function testEmptyUriThrowsException()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No resource type found, are you sure this is a JSON:API endpoint?');

        // Create route instance with empty URI
        $route = new Route(
            ['GET'],
            '',
            [
                'as' => 'api.root',
                'uses' => fn () => null
            ]
        );

        // Create base endpoint data using fromRoute
        $endpointData = ExtractedEndpointData::fromRoute($route);

        // This should throw an exception
        JsonApiEndpointData::fromEndpointData($endpointData);
    }

    /**
     * @test
     */
    public function testNonJsonApiRouteThrowsException()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No resource type found, are you sure this is a JSON:API endpoint?');

        // Create route instance with empty URI
        $route = new Route(
            ['GET'],
            '',
            [
                'as' => 'empty.route',
                'uses' => fn () => null
            ]
        );

        // Create base endpoint data using fromRoute
        $endpointData = ExtractedEndpointData::fromRoute($route);

        // This should throw an exception
        JsonApiEndpointData::fromEndpointData($endpointData);
    }
}
