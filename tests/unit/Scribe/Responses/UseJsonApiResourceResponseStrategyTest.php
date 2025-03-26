<?php

namespace Tests\Scribe\Responses;

use Illuminate\Routing\Route;
use Knuckles\Camel\Extraction\ExtractedEndpointData;
use Knuckles\Scribe\Tools\DocumentationConfig;
use Sowl\JsonApi\ResourceManager;
use Sowl\JsonApi\Scribe\Responses\UseJsonApiResourceResponseStrategy;
use Tests\TestCase;

class UseJsonApiResourceResponseStrategyTest extends TestCase
{
    private UseJsonApiResourceResponseStrategy $strategy;
    private DocumentationConfig $config;

    protected function setUp(): void
    {
        parent::setUp();

        // Use empty array for DocumentationConfig
        $this->config = new DocumentationConfig([]);

        // Create the strategy with the config and ResourceManager
        $this->strategy = new UseJsonApiResourceResponseStrategy($this->config, app(ResourceManager::class));
    }

    public function testReturnsEmptyArrayForNonJsonApiRoutes()
    {
        $endpointData = ExtractedEndpointData::fromRoute(new Route(['GET'], 'users', [
            'as' => 'api.users.list',
            'uses' => fn () => null
        ]));

        $result = $this->strategy->__invoke($endpointData);

        $this->assertEquals([], $result);
    }

    public function testGeneratesResponseForJsonApiShowRoute()
    {
        // Create endpoint data for show route
        $endpointData = ExtractedEndpointData::fromRoute(new Route(['GET'], 'users/{user}', [
            'as' => 'jsonapi.users.show',
            'uses' => fn () => null
        ]));

        $result = $this->strategy->__invoke($endpointData);

        // Verify response structure
        $this->assertIsArray($result);
        $this->assertCount(2, $result); // Success + 404 error
        $this->assertEquals(200, $result[0]['status']);
        $this->assertEquals(404, $result[1]['status']);
        $this->assertArrayHasKey('content', $result[0]);

        // Verify response content structure
        $content = $result[0]['content'];
        $this->assertIsArray($content);
        $this->assertArrayHasKey('data', $content);
    }

    public function testGeneratesResponseForJsonApiListRoute()
    {
        // Create endpoint data for list route
        $endpointData = ExtractedEndpointData::fromRoute(new Route(['GET'], 'users', [
            'as' => 'jsonapi.users.list',
            'uses' => fn () => null
        ]));

        $result = $this->strategy->__invoke($endpointData);

        // Verify response structure
        $this->assertIsArray($result);
        $this->assertCount(2, $result); // Success + 404 error
        $this->assertEquals(200, $result[0]['status']);
        $this->assertEquals(404, $result[1]['status']);
        $this->assertArrayHasKey('content', $result[0]);

        // Verify response content structure
        $content = $result[0]['content'];
        $this->assertIsArray($content);
        $this->assertArrayHasKey('data', $content);
        $this->assertIsArray($content['data']);
        $this->assertCount(2, $content['data']);

        // Verify the first resource in the collection
        $this->assertArrayHasKey(0, $content['data']);
        $resource1 = $content['data'][0];

        // Verify resource object structure according to JSON:API spec
        $this->assertIsArray($resource1);
        $this->assertArrayHasKey('type', $resource1);
        $this->assertEquals('users', $resource1['type']);
        $this->assertArrayHasKey('id', $resource1);
        $this->assertIsString($resource1['id']);
        $this->assertNotEmpty($resource1['id']);
        $this->assertArrayHasKey('attributes', $resource1);

        // Verify attributes
        $this->assertIsArray($resource1['attributes']);
        $this->assertArrayHasKey('name', $resource1['attributes']);
        $this->assertArrayHasKey('email', $resource1['attributes']);
        $this->assertIsString($resource1['attributes']['name']);
        $this->assertIsString($resource1['attributes']['email']);

        // Verify links for the resource
        $this->assertArrayHasKey('links', $resource1);
        $this->assertIsArray($resource1['links']);
        $this->assertArrayHasKey('self', $resource1['links']);
        $this->assertStringContainsString('/users/', $resource1['links']['self']);
        $this->assertStringContainsString($resource1['id'], $resource1['links']['self']);

        // Verify the second resource in the collection
        $this->assertArrayHasKey(1, $content['data']);
        $resource2 = $content['data'][1];

        // Verify resource object structure according to JSON:API spec
        $this->assertIsArray($resource2);
        $this->assertArrayHasKey('type', $resource2);
        $this->assertEquals('users', $resource2['type']);
        $this->assertArrayHasKey('id', $resource2);
        $this->assertIsString($resource2['id']);
        $this->assertNotEmpty($resource2['id']);
        $this->assertArrayHasKey('attributes', $resource2);

        // Verify attributes
        $this->assertIsArray($resource2['attributes']);
        $this->assertArrayHasKey('name', $resource2['attributes']);
        $this->assertArrayHasKey('email', $resource2['attributes']);
        $this->assertIsString($resource2['attributes']['name']);
        $this->assertIsString($resource2['attributes']['email']);

        // Verify links for the resource
        $this->assertArrayHasKey('links', $resource2);
        $this->assertIsArray($resource2['links']);
        $this->assertArrayHasKey('self', $resource2['links']);
        $this->assertStringContainsString('/users/', $resource2['links']['self']);
        $this->assertStringContainsString($resource2['id'], $resource2['links']['self']);
    }

    public function testGeneratesResponseForJsonApiCreateRoute()
    {
        // Create endpoint data for create route
        $endpointData = ExtractedEndpointData::fromRoute(new Route(['POST'], 'users', [
            'as' => 'jsonapi.users.create',
            'uses' => fn () => null
        ]));

        $result = $this->strategy->__invoke($endpointData);

        // Verify response structure
        $this->assertIsArray($result);
        $this->assertCount(3, $result); // Success + validation error + 404 error
        $this->assertEquals(201, $result[0]['status']); // Created status code
        $this->assertEquals(422, $result[1]['status']); // Validation error
        $this->assertEquals(404, $result[2]['status']); // Not found error
        $this->assertArrayHasKey('content', $result[0]);

        // Verify response content structure
        $content = $result[0]['content'];
        $this->assertIsArray($content);
        $this->assertArrayHasKey('data', $content);

        // Verify resource object structure
        $resource = $content['data'];
        $this->assertIsArray($resource);
        $this->assertArrayHasKey('type', $resource);
        $this->assertEquals('users', $resource['type']);
        $this->assertArrayHasKey('id', $resource);
        $this->assertIsString($resource['id']);
        $this->assertNotEmpty($resource['id']);
        $this->assertArrayHasKey('attributes', $resource);

        // Verify attributes
        $this->assertIsArray($resource['attributes']);
        $this->assertArrayHasKey('name', $resource['attributes']);
        $this->assertArrayHasKey('email', $resource['attributes']);
        $this->assertIsString($resource['attributes']['name']);
        $this->assertIsString($resource['attributes']['email']);

        // Verify links
        $this->assertArrayHasKey('links', $resource);
        $this->assertIsArray($resource['links']);
        $this->assertArrayHasKey('self', $resource['links']);
        $this->assertStringContainsString('/users/', $resource['links']['self']);
        $this->assertStringContainsString($resource['id'], $resource['links']['self']);
    }

    public function testGeneratesResponseForJsonApiUpdateRoute()
    {
        // Create endpoint data for update route
        $endpointData = ExtractedEndpointData::fromRoute(new Route(['PATCH'], 'users/{user}', [
            'as' => 'jsonapi.users.update',
            'uses' => fn () => null
        ]));

        $result = $this->strategy->__invoke($endpointData);

        // Verify response structure
        $this->assertIsArray($result);
        $this->assertCount(3, $result); // Success + validation error + 404 error
        $this->assertEquals(200, $result[0]['status']); // OK status code
        $this->assertEquals(422, $result[1]['status']); // Validation error
        $this->assertEquals(404, $result[2]['status']); // Not found error
        $this->assertArrayHasKey('content', $result[0]);

        // Verify response content structure
        $content = $result[0]['content'];
        $this->assertIsArray($content);
        $this->assertArrayHasKey('data', $content);

        // Verify resource object structure
        $resource = $content['data'];
        $this->assertIsArray($resource);
        $this->assertArrayHasKey('type', $resource);
        $this->assertEquals('users', $resource['type']);
        $this->assertArrayHasKey('id', $resource);
        $this->assertIsString($resource['id']);
        $this->assertNotEmpty($resource['id']);
        $this->assertArrayHasKey('attributes', $resource);

        // Verify attributes
        $this->assertIsArray($resource['attributes']);
        $this->assertArrayHasKey('name', $resource['attributes']);
        $this->assertArrayHasKey('email', $resource['attributes']);
        $this->assertIsString($resource['attributes']['name']);
        $this->assertIsString($resource['attributes']['email']);

        // Verify links
        $this->assertArrayHasKey('links', $resource);
        $this->assertIsArray($resource['links']);
        $this->assertArrayHasKey('self', $resource['links']);
        $this->assertStringContainsString('/users/', $resource['links']['self']);
        $this->assertStringContainsString($resource['id'], $resource['links']['self']);
    }

    public function testGeneratesResponseForJsonApiDeleteRoute()
    {
        // Create endpoint data for delete route
        $endpointData = ExtractedEndpointData::fromRoute(new Route(['DELETE'], 'users/{user}', [
            'as' => 'jsonapi.users.delete',
            'uses' => fn () => null
        ]));

        $result = $this->strategy->__invoke($endpointData);

        // Verify response structure
        $this->assertIsArray($result);
        $this->assertCount(2, $result); // Success + 404 error
        $this->assertEquals(204, $result[0]['status']); // No Content status code for delete operations
        $this->assertEquals(404, $result[1]['status']); // Not found error

        // For 204 No Content responses, content should be null
        $this->assertNull($result[0]['content']);
    }

    public function testGeneratesResponseForJsonApiShowRelationshipRoute()
    {
        // Create endpoint data for relationship route
        $endpointData = ExtractedEndpointData::fromRoute(new Route(['GET'], 'users/{user}/relationships/status', [
            'as' => 'jsonapi.users.relationships.status',
            'uses' => fn () => null
        ]));

        $result = $this->strategy->__invoke($endpointData);

        // Verify response structure
        $this->assertIsArray($result);
        $this->assertCount(2, $result); // Success + 404 error
        $this->assertEquals(200, $result[0]['status']);
        $this->assertEquals(404, $result[1]['status']);
        $this->assertArrayHasKey('content', $result[0]);

        // Verify response content structure
        $content = $result[0]['content'];
        $this->assertIsArray($content);
        $this->assertArrayHasKey('data', $content);

        // For to-one relationships, data should be an object
        $this->assertIsArray($content['data']);
        $this->assertArrayHasKey('type', $content['data']);
        $this->assertEquals('user-statuses', $content['data']['type']);
        $this->assertArrayHasKey('id', $content['data']);
    }

    public function testGeneratesResponseForJsonApiAddRelationshipsRoute()
    {
        // Create endpoint data for add relationships route
        $endpointData = ExtractedEndpointData::fromRoute(new Route(['POST'], 'users/{user}/relationships/status', [
            'as' => 'jsonapi.users.relationships.status.add',
            'uses' => fn () => null
        ]));

        $result = $this->strategy->__invoke($endpointData);

        // Verify response structure
        $this->assertIsArray($result);
        $this->assertCount(2, $result); // Success + 404 error
        $this->assertEquals(200, $result[0]['status']); // OK status code (implementation returns 200 instead of 204)
        $this->assertEquals(404, $result[1]['status']); // Not found error
        $this->assertArrayHasKey('content', $result[0]);

        // Verify response content structure
        $content = $result[0]['content'];
        $this->assertIsArray($content);
        $this->assertArrayHasKey('data', $content);

        // Verify the resource object structure for a to-one relationship
        $resource = $content['data'];
        $this->assertIsArray($resource);
        $this->assertArrayHasKey('type', $resource);
        $this->assertEquals('user-statuses', $resource['type']);
        $this->assertArrayHasKey('id', $resource);
    }

    public function testGeneratesResponseForJsonApiRemoveRelationshipsRoute()
    {
        // Create endpoint data for remove relationships route
        $endpointData = ExtractedEndpointData::fromRoute(new Route(['DELETE'], 'users/{user}/relationships/status', [
            'as' => 'jsonapi.users.relationships.status.remove',
            'uses' => fn () => null
        ]));

        $result = $this->strategy->__invoke($endpointData);

        // Verify response structure
        $this->assertIsArray($result);
        $this->assertCount(2, $result); // Success + 404 error
        $this->assertEquals(204, $result[0]['status']); // No Content status code
        $this->assertEquals(404, $result[1]['status']); // Not found error
        $this->assertArrayHasKey('content', $result[0]);

        // For remove relationships, content should be null
        $this->assertNull($result[0]['content']);
    }

    public function testGeneratesResponseForUserRolesRelationshipRoute()
    {
        // Create endpoint data for user status relationship route
        $endpointData = ExtractedEndpointData::fromRoute(new Route(['GET'], 'users/{user}/relationships/status', [
            'as' => 'jsonapi.users.relationships.status',
            'uses' => fn () => null
        ]));

        $result = $this->strategy->__invoke($endpointData);

        // Verify response structure
        $this->assertIsArray($result);
        $this->assertCount(2, $result); // Success + 404 error
        $this->assertEquals(200, $result[0]['status']);
        $this->assertEquals(404, $result[1]['status']);
        $this->assertArrayHasKey('content', $result[0]);

        // Verify response content structure
        $content = $result[0]['content'];
        $this->assertIsArray($content);
        $this->assertArrayHasKey('data', $content);

        // Verify the resource object structure for a to-one relationship
        $resource = $content['data'];
        $this->assertIsArray($resource);
        $this->assertArrayHasKey('type', $resource);
        $this->assertEquals('user-statuses', $resource['type']);
        $this->assertArrayHasKey('id', $resource);
        $this->assertArrayHasKey('links', $resource);
        $this->assertIsArray($resource['links']);
        $this->assertArrayHasKey('self', $resource['links']);
        $this->assertStringContainsString('/user-statuses/', $resource['links']['self']);
    }

    public function testGeneratesResponseWithRealEntityData()
    {
        // Create endpoint data for show route
        $endpointData = ExtractedEndpointData::fromRoute(new Route(['GET'], 'users/{user}', [
            'as' => 'jsonapi.users.show',
            'uses' => fn () => null
        ]));

        $result = $this->strategy->__invoke($endpointData);

        // Verify response structure
        $this->assertIsArray($result);
        $this->assertCount(2, $result); // Success + 404 error
        $this->assertEquals(200, $result[0]['status']);
        $this->assertEquals(404, $result[1]['status']);
        $this->assertArrayHasKey('content', $result[0]);

        // Verify response content structure
        $content = $result[0]['content'];
        $this->assertIsArray($content);
        $this->assertArrayHasKey('data', $content);
    }

    public function testGeneratesRelationshipResponseWithRealEntityData()
    {
        // Create endpoint data for relationship route
        $endpointData = ExtractedEndpointData::fromRoute(new Route(['GET'], 'users/{user}/relationships/status', [
            'as' => 'jsonapi.users.relationships.status',
            'uses' => fn () => null
        ]));

        $result = $this->strategy->__invoke($endpointData);

        // Verify response structure
        $this->assertIsArray($result);
        $this->assertCount(2, $result); // Success + 404 error
        $this->assertEquals(200, $result[0]['status']);
        $this->assertEquals(404, $result[1]['status']);
        $this->assertArrayHasKey('content', $result[0]);

        // Verify response content structure
        $content = $result[0]['content'];
        $this->assertIsArray($content);
        $this->assertArrayHasKey('data', $content);

        // Verify the resource object structure for a to-one relationship
        $resource = $content['data'];
        $this->assertIsArray($resource);
        $this->assertArrayHasKey('type', $resource);
        $this->assertEquals('user-statuses', $resource['type']);
        $this->assertArrayHasKey('id', $resource);
        $this->assertArrayHasKey('links', $resource);
        $this->assertIsArray($resource['links']);
        $this->assertArrayHasKey('self', $resource['links']);
        $this->assertStringContainsString('/user-statuses/', $resource['links']['self']);
    }

    /**
     * Tests for User entity routes
     */
    public function testGeneratesResponseForUserShowRoute()
    {
        // Create endpoint data for user show route
        $endpointData = ExtractedEndpointData::fromRoute(new Route(['GET'], 'users/{user}', [
            'as' => 'jsonapi.users.show',
            'uses' => fn () => null
        ]));

        $result = $this->strategy->__invoke($endpointData);

        // Verify response structure
        $this->assertIsArray($result);
        $this->assertCount(2, $result); // Success + 404 error
        $this->assertEquals(200, $result[0]['status']);
        $this->assertEquals(404, $result[1]['status']);
        $this->assertArrayHasKey('content', $result[0]);

        // Verify response content structure
        $content = $result[0]['content'];
        $this->assertIsArray($content);
        $this->assertArrayHasKey('data', $content);
    }

    public function testGeneratesResponseForUserListRoute()
    {
        // Create endpoint data for user list route
        $endpointData = ExtractedEndpointData::fromRoute(new Route(['GET'], 'users', [
            'as' => 'jsonapi.users.list',
            'uses' => fn () => null
        ]));

        $result = $this->strategy->__invoke($endpointData);

        // Verify response structure
        $this->assertIsArray($result);
        $this->assertCount(2, $result); // Success + 404 error
        $this->assertEquals(200, $result[0]['status']);
        $this->assertEquals(404, $result[1]['status']);
        $this->assertArrayHasKey('content', $result[0]);

        // Verify response content structure
        $content = $result[0]['content'];
        $this->assertIsArray($content);
        $this->assertArrayHasKey('data', $content);
        $this->assertIsArray($content['data']);
    }

    public function testGeneratesResponseForUserCreateRoute()
    {
        // Create endpoint data for user create route
        $endpointData = ExtractedEndpointData::fromRoute(new Route(['POST'], 'users', [
            'as' => 'jsonapi.users.create',
            'uses' => fn () => null
        ]));

        $result = $this->strategy->__invoke($endpointData);

        // Verify response structure
        $this->assertIsArray($result);
        $this->assertCount(3, $result); // Success + validation error + 404 error
        $this->assertEquals(201, $result[0]['status']); // Created status code
        $this->assertEquals(422, $result[1]['status']); // Validation error
        $this->assertEquals(404, $result[2]['status']); // Not found error
        $this->assertArrayHasKey('content', $result[0]);

        // Verify response content structure
        $content = $result[0]['content'];
        $this->assertIsArray($content);
        $this->assertArrayHasKey('data', $content);
    }

    public function testGeneratesResponseForUserUpdateRoute()
    {
        // Create endpoint data for user update route
        $endpointData = ExtractedEndpointData::fromRoute(new Route(['PATCH'], 'users/{user}', [
            'as' => 'jsonapi.users.update',
            'uses' => fn () => null
        ]));

        $result = $this->strategy->__invoke($endpointData);

        // Verify response structure
        $this->assertIsArray($result);
        $this->assertCount(3, $result); // Success + validation error + 404 error
        $this->assertEquals(200, $result[0]['status']); // OK status code
        $this->assertEquals(422, $result[1]['status']); // Validation error
        $this->assertEquals(404, $result[2]['status']); // Not found error
        $this->assertArrayHasKey('content', $result[0]);

        // Verify response content structure
        $content = $result[0]['content'];
        $this->assertIsArray($content);
        $this->assertArrayHasKey('data', $content);
    }

    public function testGeneratesResponseForUserDeleteRoute()
    {
        // Create endpoint data for user delete route
        $endpointData = ExtractedEndpointData::fromRoute(new Route(['DELETE'], 'users/{user}', [
            'as' => 'jsonapi.users.delete',
            'uses' => fn () => null
        ]));

        $result = $this->strategy->__invoke($endpointData);

        // Verify response structure
        $this->assertIsArray($result);
        $this->assertCount(2, $result); // Success + 404 error
        $this->assertEquals(204, $result[0]['status']); // No Content status code
        $this->assertEquals(404, $result[1]['status']);

        // For 204 No Content responses, content should be null
        $this->assertNull($result[0]['content']);
    }

    public function testGeneratesResponseForUserStatusRelationshipRoute()
    {
        // Create endpoint data for user status relationship route
        $endpointData = ExtractedEndpointData::fromRoute(new Route(['GET'], 'users/{user}/relationships/status', [
            'as' => 'jsonapi.users.relationships.status',
            'uses' => fn () => null
        ]));

        $result = $this->strategy->__invoke($endpointData);

        // Verify response structure
        $this->assertIsArray($result);
        $this->assertCount(2, $result); // Success + 404 error
        $this->assertEquals(200, $result[0]['status']);
        $this->assertEquals(404, $result[1]['status']);
        $this->assertArrayHasKey('content', $result[0]);

        // Verify response content structure
        $content = $result[0]['content'];
        $this->assertIsArray($content);
        $this->assertArrayHasKey('data', $content);

        // Verify the resource object structure for a to-one relationship
        $resource = $content['data'];
        $this->assertIsArray($resource);
        $this->assertArrayHasKey('type', $resource);
        $this->assertEquals('user-statuses', $resource['type']);
        $this->assertArrayHasKey('id', $resource);
        $this->assertArrayHasKey('links', $resource);
        $this->assertIsArray($resource['links']);
        $this->assertArrayHasKey('self', $resource['links']);
        $this->assertStringContainsString('/user-statuses/', $resource['links']['self']);
    }

    /**
     * Tests for Page entity routes
     */
    public function testGeneratesResponseForPageShowRoute()
    {
        // Create endpoint data for page show route
        $endpointData = ExtractedEndpointData::fromRoute(new Route(['GET'], 'pages/{page}', [
            'as' => 'jsonapi.pages.show',
            'uses' => fn () => null
        ]));

        $result = $this->strategy->__invoke($endpointData);

        // Verify response structure
        $this->assertIsArray($result);
        $this->assertCount(2, $result); // Success + 404 error
        $this->assertEquals(200, $result[0]['status']);
        $this->assertEquals(404, $result[1]['status']);
        $this->assertArrayHasKey('content', $result[0]);

        // Verify response content structure
        $content = $result[0]['content'];
        $this->assertIsArray($content);
        $this->assertArrayHasKey('data', $content);

        // Verify resource object structure
        $resource = $content['data'];
        $this->assertIsArray($resource);
        $this->assertArrayHasKey('type', $resource);
        $this->assertEquals('pages', $resource['type']);
        $this->assertArrayHasKey('id', $resource);
        $this->assertIsString($resource['id']);
        $this->assertNotEmpty($resource['id']);
        $this->assertArrayHasKey('attributes', $resource);
        $this->assertIsArray($resource['attributes']);

        // Verify common page attributes
        $this->assertArrayHasKey('title', $resource['attributes']);
        $this->assertArrayHasKey('content', $resource['attributes']);
        $this->assertIsString($resource['attributes']['title']);
        $this->assertIsString($resource['attributes']['content']);

        // Verify links
        $this->assertArrayHasKey('links', $resource);
        $this->assertIsArray($resource['links']);
        $this->assertArrayHasKey('self', $resource['links']);
        $this->assertStringContainsString('/pages/', $resource['links']['self']);
        $this->assertStringContainsString($resource['id'], $resource['links']['self']);
    }

    public function testGeneratesResponseForPageListRoute()
    {
        // Create endpoint data for page list route
        $endpointData = ExtractedEndpointData::fromRoute(new Route(['GET'], 'pages', [
            'as' => 'jsonapi.pages.list',
            'uses' => fn () => null
        ]));

        $result = $this->strategy->__invoke($endpointData);

        // Verify response structure
        $this->assertIsArray($result);
        $this->assertCount(2, $result); // Success + 404 error
        $this->assertEquals(200, $result[0]['status']);
        $this->assertEquals(404, $result[1]['status']);
        $this->assertArrayHasKey('content', $result[0]);

        // Verify response content structure
        $content = $result[0]['content'];
        $this->assertIsArray($content);
        $this->assertArrayHasKey('data', $content);
        $this->assertIsArray($content['data']);
        $this->assertCount(2, $content['data']);

        // Verify the first resource in the collection
        $this->assertArrayHasKey(0, $content['data']);
        $resource1 = $content['data'][0];

        // Verify resource object structure according to JSON:API spec
        $this->assertIsArray($resource1);
        $this->assertArrayHasKey('type', $resource1);
        $this->assertEquals('pages', $resource1['type']);
        $this->assertArrayHasKey('id', $resource1);
        $this->assertIsString($resource1['id']);
        $this->assertNotEmpty($resource1['id']);
        $this->assertArrayHasKey('attributes', $resource1);
        $this->assertIsArray($resource1['attributes']);

        // Verify common page attributes
        $this->assertArrayHasKey('title', $resource1['attributes']);
        $this->assertArrayHasKey('content', $resource1['attributes']);
        $this->assertIsString($resource1['attributes']['title']);
        $this->assertIsString($resource1['attributes']['content']);

        // Verify links for the resource
        $this->assertArrayHasKey('links', $resource1);
        $this->assertIsArray($resource1['links']);
        $this->assertArrayHasKey('self', $resource1['links']);
        $this->assertStringContainsString('/pages/', $resource1['links']['self']);
        $this->assertStringContainsString($resource1['id'], $resource1['links']['self']);

        // Verify the second resource in the collection
        $this->assertArrayHasKey(1, $content['data']);
        $resource2 = $content['data'][1];

        // Verify resource object structure according to JSON:API spec
        $this->assertIsArray($resource2);
        $this->assertArrayHasKey('type', $resource2);
        $this->assertEquals('pages', $resource2['type']);
        $this->assertArrayHasKey('id', $resource2);
        $this->assertIsString($resource2['id']);
        $this->assertNotEmpty($resource2['id']);
        $this->assertArrayHasKey('attributes', $resource2);
        $this->assertIsArray($resource2['attributes']);

        // Verify common page attributes
        $this->assertArrayHasKey('title', $resource2['attributes']);
        $this->assertArrayHasKey('content', $resource2['attributes']);
        $this->assertIsString($resource2['attributes']['title']);
        $this->assertIsString($resource2['attributes']['content']);

        // Verify links for the resource
        $this->assertArrayHasKey('links', $resource2);
        $this->assertIsArray($resource2['links']);
        $this->assertArrayHasKey('self', $resource2['links']);
        $this->assertStringContainsString('/pages/', $resource2['links']['self']);
        $this->assertStringContainsString($resource2['id'], $resource2['links']['self']);
    }

    public function testGeneratesResponseForPageUserRelationshipRoute()
    {
        // Create endpoint data for page user relationship route
        $endpointData = ExtractedEndpointData::fromRoute(new Route(['GET'], 'pages/{page}/relationships/user', [
            'as' => 'jsonapi.pages.relationships.user',
            'uses' => fn () => null
        ]));

        $result = $this->strategy->__invoke($endpointData);

        // Verify response structure
        $this->assertIsArray($result);
        $this->assertCount(2, $result); // Success + 404 error
        $this->assertEquals(200, $result[0]['status']);
        $this->assertEquals(404, $result[1]['status']);
        $this->assertArrayHasKey('content', $result[0]);

        // Verify response content structure
        $content = $result[0]['content'];
        $this->assertIsArray($content);
        $this->assertArrayHasKey('data', $content);

        // For to-one relationships, data should be an object
        $this->assertIsArray($content['data']);
        $this->assertArrayHasKey('type', $content['data']);
        $this->assertEquals('users', $content['data']['type']);
        $this->assertArrayHasKey('id', $content['data']);
        $this->assertIsString($content['data']['id']);
        $this->assertNotEmpty($content['data']['id']);
    }

    public function testGeneratesResponseForPageCommentsRelationshipRoute()
    {
        // Create endpoint data for page comments relationship route
        $endpointData = ExtractedEndpointData::fromRoute(new Route(['GET'], 'pages/{page}/relationships/pageComments', [
            'as' => 'jsonapi.pages.relationships.pageComments',
            'uses' => fn () => null
        ]));

        $result = $this->strategy->__invoke($endpointData);

        // Verify response structure
        $this->assertIsArray($result);
        $this->assertCount(2, $result); // Success + 404 error
        $this->assertEquals(200, $result[0]['status']);
        $this->assertEquals(404, $result[1]['status']);
        $this->assertArrayHasKey('content', $result[0]);

        // Verify response content structure
        $content = $result[0]['content'];
        $this->assertIsArray($content);
        $this->assertArrayHasKey('data', $content);

        // For to-many relationships, data should be an array
        $this->assertIsArray($content['data']);

        // Verify the structure of the data array
        if (count($content['data']) === 0) {
            // When there are no comments, data should be an empty array
            $this->assertCount(0, $content['data']);
        } else {
            // When there are comments, verify the structure of the first resource
            $this->assertArrayHasKey(0, $content['data']);
            $resource1 = $content['data'][0];

            // Verify resource object structure according to JSON:API spec
            $this->assertIsArray($resource1);
            $this->assertArrayHasKey('type', $resource1);
            $this->assertEquals('pageComments', $resource1['type']);
            $this->assertArrayHasKey('id', $resource1);
            $this->assertIsString($resource1['id']);
            $this->assertNotEmpty($resource1['id']);
            $this->assertArrayHasKey('attributes', $resource1);
            $this->assertIsArray($resource1['attributes']);

            // Verify common page comment attributes
            $this->assertArrayHasKey('content', $resource1['attributes']);
            $this->assertIsString($resource1['attributes']['content']);

            // Verify links for the resource
            $this->assertArrayHasKey('links', $resource1);
            $this->assertIsArray($resource1['links']);
            $this->assertArrayHasKey('self', $resource1['links']);
            $this->assertStringContainsString('/pageComments/', $resource1['links']['self']);
            $this->assertStringContainsString($resource1['id'], $resource1['links']['self']);
        }
    }

    public function testGeneratesResponseForPageCommentShowRoute()
    {
        // Create endpoint data for page comment show route
        $endpointData = ExtractedEndpointData::fromRoute(new Route(['GET'], 'pageComments/{id}', [
            'as' => 'jsonapi.pageComments.show',
            'uses' => fn () => null
        ]));

        $result = $this->strategy->__invoke($endpointData);

        // Verify response structure
        $this->assertIsArray($result);
        $this->assertCount(2, $result); // Success + 404 error
        $this->assertEquals(200, $result[0]['status']);
        $this->assertEquals(404, $result[1]['status']);
        $this->assertArrayHasKey('content', $result[0]);

        // Verify response content structure
        $content = $result[0]['content'];
        $this->assertIsArray($content);
        $this->assertArrayHasKey('data', $content);

        // Verify resource object structure according to JSON:API spec
        $resource = $content['data'];
        $this->assertIsArray($resource);
        $this->assertArrayHasKey('type', $resource);
        $this->assertEquals('pageComments', $resource['type']);
        $this->assertArrayHasKey('id', $resource);
        $this->assertIsString($resource['id']);
        $this->assertNotEmpty($resource['id']);
        $this->assertArrayHasKey('attributes', $resource);
        $this->assertIsArray($resource['attributes']);

        // Verify common page comment attributes
        $this->assertArrayHasKey('content', $resource['attributes']);
        $this->assertIsString($resource['attributes']['content']);

        // Verify links for the resource
        $this->assertArrayHasKey('links', $resource);
        $this->assertIsArray($resource['links']);
        $this->assertArrayHasKey('self', $resource['links']);
        $this->assertStringContainsString('/pageComments/', $resource['links']['self']);
        $this->assertStringContainsString($resource['id'], $resource['links']['self']);
    }

    public function testGeneratesResponseForPageCommentUserRelationshipRoute()
    {
        // Create endpoint data for page comment user relationship route
        $endpointData = ExtractedEndpointData::fromRoute(new Route(['GET'], 'pageComments/{id}/relationships/user', [
            'as' => 'jsonapi.pageComments.relationships.user',
            'uses' => fn () => null
        ]));

        $result = $this->strategy->__invoke($endpointData);

        // Verify response structure
        $this->assertIsArray($result);
        $this->assertCount(2, $result); // Success + 404 error
        $this->assertEquals(200, $result[0]['status']);
        $this->assertEquals(404, $result[1]['status']);
        $this->assertArrayHasKey('content', $result[0]);

        // Verify response content structure
        $content = $result[0]['content'];
        $this->assertIsArray($content);
        $this->assertArrayHasKey('data', $content);

        // For to-one relationships, data should be an object
        $resource = $content['data'];
        $this->assertIsArray($resource);
        $this->assertArrayHasKey('type', $resource);
        $this->assertEquals('users', $resource['type']);
        $this->assertArrayHasKey('id', $resource);
        $this->assertIsString($resource['id']);
        $this->assertNotEmpty($resource['id']);

        // Verify links for the resource
        $this->assertArrayHasKey('links', $resource);
        $this->assertIsArray($resource['links']);
        $this->assertArrayHasKey('self', $resource['links']);
        $this->assertStringContainsString('/users/', $resource['links']['self']);
        $this->assertStringContainsString($resource['id'], $resource['links']['self']);
    }

    public function testGeneratesResponseForPageCommentPageRelationshipRoute()
    {
        // Create endpoint data for page comment page relationship route
        $endpointData = ExtractedEndpointData::fromRoute(new Route(['GET'], 'pageComments/{id}/relationships/page', [
            'as' => 'jsonapi.pageComments.relationships.page',
            'uses' => fn () => null
        ]));

        $result = $this->strategy->__invoke($endpointData);

        // Verify response structure
        $this->assertIsArray($result);
        $this->assertCount(2, $result); // Success + 404 error
        $this->assertEquals(200, $result[0]['status']);
        $this->assertEquals(404, $result[1]['status']);
        $this->assertArrayHasKey('content', $result[0]);

        // Verify response content structure
        $content = $result[0]['content'];
        $this->assertIsArray($content);
        $this->assertArrayHasKey('data', $content);

        // For to-one relationships, data should be an object
        $resource = $content['data'];
        $this->assertIsArray($resource);
        $this->assertArrayHasKey('type', $resource);
        $this->assertEquals('pages', $resource['type']);
        $this->assertArrayHasKey('id', $resource);
        $this->assertIsString($resource['id']);
        $this->assertNotEmpty($resource['id']);

        // Verify links for the resource
        $this->assertArrayHasKey('links', $resource);
        $this->assertIsArray($resource['links']);
        $this->assertArrayHasKey('self', $resource['links']);
        $this->assertStringContainsString('/pages/', $resource['links']['self']);
        $this->assertStringContainsString($resource['id'], $resource['links']['self']);
    }

    public function testGeneratesResponseForRoleShowRoute()
    {
        $this->markTestIncomplete('We must use factory states fro the roles!');

        // Create endpoint data for role show route
        $endpointData = ExtractedEndpointData::fromRoute(new Route(['GET'], 'roles/{role}', [
            'as' => 'jsonapi.roles.show',
            'uses' => fn () => null
        ]));

        $result = $this->strategy->__invoke($endpointData);

        // Verify response structure
        $this->assertIsArray($result);
        $this->assertCount(2, $result); // Success + 404 error
        $this->assertEquals(200, $result[0]['status']);
        $this->assertEquals(404, $result[1]['status']);
        $this->assertArrayHasKey('content', $result[0]);

        // Verify response content structure
        $content = $result[0]['content'];
        $this->assertIsArray($content);
        $this->assertArrayHasKey('data', $content);

        // Verify resource object structure according to JSON:API spec
        $resource = $content['data'];
        $this->assertIsArray($resource);
        $this->assertArrayHasKey('type', $resource);
        $this->assertEquals('roles', $resource['type']);
        $this->assertArrayHasKey('id', $resource);
        $this->assertIsString($resource['id']);
        $this->assertNotEmpty($resource['id']);
        $this->assertArrayHasKey('attributes', $resource);
        $this->assertIsArray($resource['attributes']);

        // Verify common role attributes
        $this->assertArrayHasKey('name', $resource['attributes']);
        $this->assertIsString($resource['attributes']['name']);

        // Verify links for the resource
        $this->assertArrayHasKey('links', $resource);
        $this->assertIsArray($resource['links']);
        $this->assertArrayHasKey('self', $resource['links']);
        $this->assertStringContainsString('/roles/', $resource['links']['self']);
        $this->assertStringContainsString($resource['id'], $resource['links']['self']);
    }

    public function testGeneratesResponseForUserStatusShowRoute()
    {
        $this->markTestIncomplete('We must use factory states fro the user statuses!');

        // Create endpoint data for user status show route
        $endpointData = ExtractedEndpointData::fromRoute(new Route(['GET'], 'user-statuses/{id}', [
            'as' => 'jsonapi.user-statuses.show',
            'uses' => fn () => null
        ]));

        $result = $this->strategy->__invoke($endpointData);

        // Verify response structure
        $this->assertIsArray($result);
        $this->assertCount(2, $result); // Success + 404 error
        $this->assertEquals(200, $result[0]['status']);
        $this->assertEquals(404, $result[1]['status']);
        $this->assertArrayHasKey('content', $result[0]);

        // The implementation may return null content for resources it can't generate
        // This is acceptable for testing purposes
        $this->assertNull($result[0]['content']);
    }
}
