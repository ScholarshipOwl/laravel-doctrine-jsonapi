<?php

namespace Tests\Scribe\QueryParameters;

use Knuckles\Camel\Extraction\ExtractedEndpointData;
use Illuminate\Routing\Route;
use Knuckles\Scribe\Tools\DocumentationConfig;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;
use Sowl\JsonApi\ResourceManager;
use Sowl\JsonApi\Relationships\ToManyRelationship;
use Sowl\JsonApi\Relationships\RelationshipsCollection;
use Sowl\JsonApi\Scribe\QueryParameters\AddJsonApiQueryParametersStrategy;

class AddJsonApiQueryParametersStrategyTest extends TestCase
{
    private ResourceManager|MockInterface $mockResourceManager;

    private AddJsonApiQueryParametersStrategy $strategy;

    private DocumentationConfig|MockInterface $mockConfig;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockResourceManager = Mockery::mock(ResourceManager::class);

        // Create a mock for DocumentationConfig
        $this->mockConfig = Mockery::mock(DocumentationConfig::class);

        // Create our test strategy with overridden methods
        $this->strategy = new AddJsonApiQueryParametersStrategy($this->mockConfig, $this->mockResourceManager);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testReturnsCommonQueryParametersForJsonApiRoutes()
    {
        $endpointData = ExtractedEndpointData::fromRoute(new Route(['GET'], 'api/users/{user}', [
            'as' => 'jsonapi.users.show',
            'uses' => fn () => null
        ]));

        // Execute the strategy
        $result = $this->strategy->__invoke($endpointData);

        // Assert that the common query parameters are returned
        $this->assertArrayHasKey('include', $result);
        $this->assertArrayHasKey('fields', $result);
        $this->assertArrayHasKey('meta', $result);

        // Verify the parameters don't include list-specific parameters
        $this->assertArrayNotHasKey('filter', $result);
        $this->assertArrayNotHasKey('sort', $result);
        $this->assertArrayNotHasKey('page[number]', $result);
    }

    public function testReturnsEmptyArrayForNonJsonApiRoutes()
    {
        $endpointData = ExtractedEndpointData::fromRoute(new Route(['GET'], 'api/users', [
            'as' => 'api.users.list',
            'uses' => fn () => null
        ]));

        // Execute the strategy
        $result = $this->strategy->__invoke($endpointData);

        // Assert that an empty array is returned for non-JSON:API routes
        $this->assertEquals([], $result);
    }

    public function testAddsListParametersForListRoutes()
    {
        $endpointData = ExtractedEndpointData::fromRoute(new Route(['GET'], 'users', [
            'as' => 'jsonapi.users.list',
            'uses' => fn () => null
        ]));

        // Execute the strategy
        $result = $this->strategy->__invoke($endpointData);

        // Assert that both common and list-specific parameters are returned
        $this->assertArrayHasKey('include', $result);
        $this->assertArrayHasKey('fields', $result);
        $this->assertArrayHasKey('meta', $result);
        $this->assertArrayHasKey('filter', $result);
        $this->assertArrayHasKey('sort', $result);
        $this->assertArrayHasKey('page[number]', $result);
        $this->assertArrayHasKey('page[size]', $result);
        $this->assertArrayHasKey('page[limit]', $result);
        $this->assertArrayHasKey('page[offset]', $result);
    }

    public function testAddsListParametersForToManyRelationshipRoutes()
    {
        $endpointData = ExtractedEndpointData::fromRoute(new Route(['GET'], 'users/{user}/roles', [
            'as' => 'jsonapi.users.roles.showRelated',
            'uses' => fn () => null
        ]));

        // Set up the resource manager expectations
        $this->mockResourceManager->shouldReceive('hasResourceType')->with('users')->andReturn(true);

        // Create a ToManyRelationship mock
        $mockRelationship = Mockery::mock(ToManyRelationship::class);

        // Create a relationships collection with the relationship
        $relationships = new RelationshipsCollection();

        // Use reflection to set the protected relationships property
        $reflection = new \ReflectionClass($relationships);
        $property = $reflection->getProperty('relationships');
        $property->setAccessible(true);
        $property->setValue($relationships, collect(['roles' => $mockRelationship]));

        // Set up the resource manager to return our collection
        $this->mockResourceManager->shouldReceive('relationshipsByResourceType')
            ->with('users')
            ->andReturn($relationships);

        // Execute the strategy
        $result = $this->strategy->__invoke($endpointData);

        // Assert that both common and list-specific parameters are returned for to-many relationships
        $this->assertArrayHasKey('include', $result);
        $this->assertArrayHasKey('fields', $result);
        $this->assertArrayHasKey('meta', $result);
        $this->assertArrayHasKey('filter', $result);
        $this->assertArrayHasKey('sort', $result);
        $this->assertArrayHasKey('page[number]', $result);
    }

    public function testReturnsEmptyArrayForNonAllowedMethods()
    {
        $endpointData = ExtractedEndpointData::fromRoute(new Route(['DELETE'], 'api/users', [
            'as' => 'jsonapi.users.list',
            'uses' => fn () => null
        ]));

        // Execute the strategy
        $result = $this->strategy->__invoke($endpointData);

        // Assert that an empty array is returned for non-allowed HTTP methods
        $this->assertEquals([], $result);
    }

    public function testHandlesAllAllowedHttpMethods()
    {
        $allowedMethods = ['GET', 'POST', 'PATCH', 'PUT'];

        foreach ($allowedMethods as $method) {
            $endpointData = ExtractedEndpointData::fromRoute(new Route([$method], 'api/users/{user}', [
                'as' => 'jsonapi.users.show',
                'uses' => fn () => null
            ]));

            // Execute the strategy
            $result = $this->strategy->__invoke($endpointData);

            // Assert that the query parameters are returned for allowed HTTP methods
            $this->assertNotEmpty($result, "Expected non-empty result for HTTP method: {$method}");
            $this->assertArrayHasKey('include', $result);
        }
    }
}
