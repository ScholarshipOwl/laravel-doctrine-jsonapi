<?php

namespace Tests\Scribe\QueryParameters;

use Knuckles\Camel\Extraction\ExtractedEndpointData;
use Illuminate\Routing\Route;
use Knuckles\Scribe\Tools\DocumentationConfig;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;
use Sowl\JsonApi\ResourceManager;
use Sowl\JsonApi\Scribe\QueryParameters\AddJsonApiQueryParametersStrategy;

class AddJsonApiQueryParametersStrategyTest extends TestCase
{
    private AddJsonApiQueryParametersStrategy $strategy;
    private DocumentationConfig|MockInterface $mockConfig;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockConfig = Mockery::mock(DocumentationConfig::class);
        $this->strategy = new AddJsonApiQueryParametersStrategy(
            $this->mockConfig,
            app(ResourceManager::class)
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testReturnsCommonQueryParametersForJsonApiRoutes()
    {
        $endpointData = ExtractedEndpointData::fromRoute(new Route(['GET'], 'users/{user}', [
            'as' => 'jsonapi.users.show',
            'uses' => fn () => null,
        ]));

        // Execute the strategy
        $result = $this->strategy->__invoke($endpointData);

        // Assert that the common query parameters are returned
        $this->assertArrayHasKey('fields', $result);
        $this->assertArrayHasKey('include', $result);

        // Verify the parameters don't include list-specific parameters
        $this->assertArrayNotHasKey('filter', $result);
        $this->assertArrayNotHasKey('sort', $result);
        $this->assertArrayNotHasKey('page', $result);

        // Verify fields parameter structure
        $this->assertIsArray($result['fields']);
        $this->assertEquals([
            'type' => 'object',
            'required' => false,
            'description' => 'Sparse fieldsets - specify which fields to include in the response for each resource type. ([Spec](https://jsonapi.org/format/#fetching-sparse-fieldsets))

**Available fields for users:** `name`, `email`',
            'test' => 'test',
            'example' => ['users' => 'name,email']
        ], $result['fields']);

        // Verify include parameter structure if available
        $this->assertIsArray($result['include']);
        $this->assertArrayHasKey('description', $result['include']);
        $this->assertArrayHasKey('required', $result['include']);
        $this->assertArrayHasKey('example', $result['include']);
        $this->assertFalse($result['include']['required']);
    }

    public function testReturnsEmptyArrayForNonJsonApiRoutes()
    {
        $endpointData = ExtractedEndpointData::fromRoute(new Route(['GET'], 'users', [
            'as' => 'users.list',
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
        $this->assertArrayHasKey('page', $result);

        // Assert page parameter structure
        $this->assertArrayHasKey('page', $result);
        $page = $result['page'];
        
        $this->assertStringContainsString('Pagination parameters', $page['description']);
        $this->assertStringContainsString('https://jsonapi.org/format/#fetching-pagination', $page['description']);
        $this->assertEquals('object', $page['type']);
        $this->assertEquals(['number' => 1, 'size' => 10], $page['example']);

        // Assert filter parameter structure
        $this->assertIsArray($result['filter']);
        $this->assertEquals([
            'description' => 'Filter the resources by attributes. ([Spec](https://jsonapi.org/format/#fetching-filtering))',
            'required' => false,
            'style' => 'deepObject',
            'explode' => true,
            'schema' => [
                'type' => 'object',
                'additionalProperties' => true
            ],
            'example' => ['name' => 'John']
        ], $result['filter']);

        // Assert sort parameter structure
        $this->assertIsArray($result['sort']);
        $this->assertEquals([
            'description' => 'Sort the results by attributes. Prefix with `-` for descending order. ([Spec](https://jsonapi.org/format/#fetching-sorting))

**Available sort fields for users:** `name`, `email`',
            'required' => false,
            'example' => 'name'
        ], $result['sort']);
    }

    public function testAddsListParametersForToManyRelationshipRoutes()
    {
        $endpointData = ExtractedEndpointData::fromRoute(new Route(['GET'], 'users/{user}/relationships/roles', [
            'as' => 'jsonapi.users.relationships.roles.list',
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
        $this->assertArrayHasKey('page', $result);

        // Assert page parameter structure
        $this->assertArrayHasKey('page', $result);
        $page = $result['page'];
        
        $this->assertStringContainsString('Pagination parameters', $page['description']);
        $this->assertStringContainsString('https://jsonapi.org/format/#fetching-pagination', $page['description']);
        $this->assertEquals('object', $page['type']);
        $this->assertEquals(['number' => 1, 'size' => 10], $page['example']);

        // Assert filter parameter structure
        $this->assertIsArray($result['filter']);
        $this->assertEquals([
            'description' => 'Filter the resources by attributes. ([Spec](https://jsonapi.org/format/#fetching-filtering))',
            'required' => false,
            'style' => 'deepObject',
            'explode' => true,
            'schema' => [
                'type' => 'object',
                'additionalProperties' => true
            ],
            'example' => ['name' => 'John']
        ], $result['filter']);

        // Assert sort parameter structure
        $this->assertIsArray($result['sort']);
        $this->assertEquals([
            'description' => 'Sort the results by attributes. Prefix with `-` for descending order. ([Spec](https://jsonapi.org/format/#fetching-sorting))

**Available sort fields for users:** `name`, `email`',
            'required' => false,
            'example' => 'name'
        ], $result['sort']);
    }

    public function testReturnsEmptyArrayForNonAllowedMethods()
    {
        $endpointData = ExtractedEndpointData::fromRoute(new Route(['DELETE'], 'users', [
            'as' => 'jsonapi.users.list',
            'uses' => fn () => null
        ]));

        // Execute the strategy
        $result = $this->strategy->__invoke($endpointData);

        // Assert that an empty array is returned for non-allowed HTTP methods
        $this->assertEquals([], $result);
    }

    public function testReturnsQueryParametersForAllowedMethods()
    {
        $allowedMethods = ['GET', 'POST', 'PATCH', 'PUT'];

        foreach ($allowedMethods as $method) {
            $endpointData = ExtractedEndpointData::fromRoute(new Route([$method], 'users/{user}', [
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

    public function testIncludeParameterWithAvailableIncludes()
    {
        $endpointData = ExtractedEndpointData::fromRoute(new Route(['GET'], 'users/{user}', [
            'as' => 'jsonapi.users.show',
            'uses' => fn () => null
        ]));

        // Execute the strategy
        $result = $this->strategy->__invoke($endpointData);

        // Assert include parameter structure
        $this->assertArrayHasKey('include', $result);
        $includeParam = $result['include'];
        $this->assertStringContainsString('Include related resources.', $includeParam['description']);
        $this->assertStringContainsString('([Spec](https://jsonapi.org/format/#fetching-includes))', $includeParam['description']);
        $this->assertStringContainsString('Available includes:', $includeParam['description']);
        $this->assertStringContainsString('`status`, `roles`', $includeParam['description']); // Check specific includes
        $this->assertEquals(false, $includeParam['required']);
        $this->assertEquals('status,roles', $includeParam['example']);
    }

    public function testMetaParameterWithAvailableMetas()
    {
        $endpointData = ExtractedEndpointData::fromRoute(new Route(['GET'], 'users/{user}', [
            'as' => 'jsonapi.users.show',
            'uses' => fn () => null
        ]));

        // Execute the strategy
        $result = $this->strategy->__invoke($endpointData);

        // Assert meta parameter structure
        $this->assertArrayHasKey('meta', $result);
        $meta = $result['meta'];

        $this->assertStringContainsString('Additional metadata', $meta['description']);
        $this->assertFalse($meta['required']);
        $this->assertEquals('object', $meta['type']);
        $this->assertArrayHasKey('example', $meta);
    }

    public function testFieldsParameterStructure()
    {
        $endpointData = ExtractedEndpointData::fromRoute(new Route(['GET'], 'users/{user}', [
            'as' => 'jsonapi.users.show',
            'uses' => fn () => null
        ]));

        // Execute the strategy
        $result = $this->strategy->__invoke($endpointData);

        // Assert fields parameter structure
        $this->assertArrayHasKey('fields', $result);
        $fields = $result['fields'];

        $this->assertStringContainsString('Sparse fieldsets', $fields['description']);
        $this->assertStringContainsString('https://jsonapi.org/format/#fetching-sparse-fieldsets', $fields['description']);
        $this->assertFalse($fields['required']);
        $this->assertEquals('object', $fields['type']);
    }

    public function testFilterParameterForCollectionRoute()
    {
        $endpointData = ExtractedEndpointData::fromRoute(new Route(['GET'], 'users', [
            'as' => 'jsonapi.users.index',
            'uses' => fn () => null
        ]));

        // Execute the strategy
        $result = $this->strategy->__invoke($endpointData);

        // Assert filter parameter structure
        $this->assertArrayHasKey('filter', $result);
        $this->assertEquals([
            'description' => 'Filter the resources by attributes. ([Spec](https://jsonapi.org/format/#fetching-filtering))',
            'required' => false,
            'style' => 'deepObject',
            'explode' => true,
            'schema' => [
                'type' => 'object',
                'additionalProperties' => true
            ],
            'example' => ['name' => 'John']
        ], $result['filter']);
    }

    public function testSortParameterForCollectionRoute()
    {
        $endpointData = ExtractedEndpointData::fromRoute(new Route(['GET'], 'users', [
            'as' => 'jsonapi.users.index',
            'uses' => fn () => null
        ]));

        // Execute the strategy
        $result = $this->strategy->__invoke($endpointData);

        // Assert sort parameter structure
        $this->assertArrayHasKey('sort', $result);
        $this->assertEquals([
            'description' => 'Sort the results by attributes. Prefix with `-` for descending order. ([Spec](https://jsonapi.org/format/#fetching-sorting))

**Available sort fields for users:** `name`, `email`',
            'required' => false,
            'example' => 'name'
        ], $result['sort']);
    }

    public function testPageParameterForCollectionRoute()
    {
        $endpointData = ExtractedEndpointData::fromRoute(new Route(['GET'], 'users', [
            'as' => 'jsonapi.users.index',
            'uses' => fn () => null
        ]));

        // Execute the strategy
        $result = $this->strategy->__invoke($endpointData);

        // Assert page parameter structure
        $this->assertArrayHasKey('page', $result);
        $page = $result['page'];

        $this->assertStringContainsString('Pagination parameters', $page['description']);
        $this->assertStringContainsString('https://jsonapi.org/format/#fetching-pagination', $page['description']);
        $this->assertEquals('object', $page['type']);
        $this->assertEquals(['number' => 1, 'size' => 10], $page['example']);
    }
}
