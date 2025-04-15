<?php

namespace Tests\Scribe\Strategies;

use Knuckles\Camel\Extraction\ExtractedEndpointData;
use Illuminate\Routing\Route;
use Knuckles\Scribe\Tools\DocumentationConfig;
use Mockery;
use Tests\TestCase;
use Sowl\JsonApi\ResourceManager;
use Sowl\JsonApi\Scribe\Strategies\QueryParameters\AddJsonApiQueryParametersStrategy;

class AddJsonApiQueryParametersStrategyTest extends TestCase
{
    private AddJsonApiQueryParametersStrategy $strategy;
    private DocumentationConfig $config;

    protected function setUp(): void
    {
        parent::setUp();

        $this->config = new DocumentationConfig([]);
        $this->strategy = new AddJsonApiQueryParametersStrategy(
            $this->config,
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
        $this->assertArrayHasKey('fields[users]', $result);
        $this->assertArrayHasKey('include', $result);

        // Verify the parameters don't include list-specific parameters
        $this->assertArrayNotHasKey('filter', $result);
        $this->assertArrayNotHasKey('sort', $result);
        $this->assertArrayNotHasKey('page', $result);

        // Verify fields parameter structure
        $this->assertIsArray($result['fields[users]']);
        $this->assertEquals([
            'type' => 'string',
            'required' => false,
            'description' =>
                'Sparse fieldsets - specify which fields to include in the response for each resource type.'
                . " ([Spec](https://jsonapi.org/format/#fetching-sparse-fieldsets))\n\n"
                . '**Available fields for users:** `name`, `email`',
            'example' => 'name,email'
        ], $result['fields[users]']);

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
        $this->assertArrayHasKey('fields[users]', $result);
        $this->assertArrayHasKey('meta[users]', $result);
        $this->assertArrayHasKey('filter', $result);
        $this->assertArrayHasKey('sort', $result);
        $this->assertArrayHasKey('page[number]', $result);
        $this->assertArrayHasKey('page[size]', $result);
        // $this->assertArrayHasKey('page[limit]', $result);
        // $this->assertArrayHasKey('page[offset]', $result);

        // Assert page parameter structure
        $this->assertEquals([
            'description' => 'Page number.'
                           . ' ([Spec](https://jsonapi.org/format/#fetching-pagination))',
            'required' => false,
            'type' => 'integer',
            'example' => 1,
        ], $result['page[number]']);

        $this->assertEquals([
            'description' => 'Number of results per page.'
                           . ' ([Spec](https://jsonapi.org/format/#fetching-pagination))',
            'required' => false,
            'type' => 'integer',
            'example' => 10,
        ], $result['page[size]']);

//        $this->assertEquals([
//            'description' => 'Maximum number of results to return.'
//                           . ' ([Spec](https://jsonapi.org/format/#fetching-pagination))',
//            'required' => false,
//            'type' => 'integer',
//            'example' => 10,
//        ], $result['page[limit]']);
//
//        $this->assertEquals([
//            'description' => 'Number of results to skip.'
//                           . ' ([Spec](https://jsonapi.org/format/#fetching-pagination))',
//            'required' => false,
//            'type' => 'integer',
//            'example' => 0,
//        ], $result['page[offset]']);

        // Assert filter parameter structure
        $this->assertArrayHasKey('filter', $result);
        $this->assertIsArray($result['filter']);

        // Assert sort parameter structure
        $this->assertIsArray($result['sort']);
        $this->assertEquals([
            'description' => 'Sort the results by attributes. Prefix with `-` for descending order.'
                           . " ([Spec](https://jsonapi.org/format/#fetching-sorting))\n\n"
                           . '**Available sort fields for users:** `name`, `email`',
            'required' => false,
            'example' => 'name,email'
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

        // Assert that only list-specific parameters applicable to relationships are returned
        $this->assertArrayHasKey('filter', $result);
        $this->assertArrayHasKey('sort', $result);
        $this->assertArrayHasKey('page[number]', $result);
        $this->assertArrayHasKey('page[size]', $result);
        // $this->assertArrayHasKey('page[limit]', $result);
        // $this->assertArrayHasKey('page[offset]', $result);

        // Assert page parameter structure
        $this->assertEquals([
            'description' => 'Page number.'
                           . ' ([Spec](https://jsonapi.org/format/#fetching-pagination))',
            'required' => false,
            'type' => 'integer',
            'example' => 1,
        ], $result['page[number]']);

        $this->assertEquals([
            'description' => 'Number of results per page.'
                           . ' ([Spec](https://jsonapi.org/format/#fetching-pagination))',
            'required' => false,
            'type' => 'integer',
            'example' => 10,
        ], $result['page[size]']);

//        $this->assertEquals([
//            'description' => 'Maximum number of results to return.'
//                           . ' ([Spec](https://jsonapi.org/format/#fetching-pagination))',
//            'required' => false,
//            'type' => 'integer',
//            'example' => 10,
//        ], $result['page[limit]']);
//
//        $this->assertEquals([
//            'description' => 'Number of results to skip.'
//                           . ' ([Spec](https://jsonapi.org/format/#fetching-pagination))',
//            'required' => false,
//            'type' => 'integer',
//            'example' => 0,
//        ], $result['page[offset]']);

        // Assert filter parameter structure
        $this->assertArrayHasKey('filter', $result);
        $this->assertIsArray($result['filter']);

        // Assert sort parameter structure
        $this->assertIsArray($result['sort']);
        $this->assertEquals([
            'description' => 'Sort the results by attributes. Prefix with `-` for descending order.'
                           . " ([Spec](https://jsonapi.org/format/#fetching-sorting))\n\n"
                           . '**Available sort fields for users:** `name`, `email`',
            'required' => false,
            'example' => 'name,email'
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
        $routeInfo = [
            'as' => 'jsonapi.users.show',
            'uses' => fn () => null
        ];
        $routePath = 'users/{user}';

        // Test methods expected to have query parameters (GET, PATCH, PUT)
        $methodsWithParams = ['GET', 'PATCH', 'PUT'];
        foreach ($methodsWithParams as $method) {
            $endpointData = ExtractedEndpointData::fromRoute(new Route([$method], $routePath, $routeInfo));
            $result = $this->strategy->__invoke($endpointData);
            $this->assertNotEmpty($result, "Expected non-empty result for HTTP method: {$method}");
            // Could add more specific assertions here if needed (e.g., assertArrayHasKey 'include')
            $this->assertArrayHasKey('include', $result); // Re-add common assertion
            $this->assertArrayHasKey('fields[users]', $result); // Re-add common assertion
            $this->assertArrayHasKey('meta[users]', $result); // Re-add common assertion
             // Assert keys not expected for detail route
            $this->assertArrayNotHasKey('page', $result);
            $this->assertArrayNotHasKey('sort', $result);
            $this->assertArrayNotHasKey('filter', $result);
        }

        // Test custom action POST method - Expect empty result
        $postMethod = 'POST';
        $endpointDataPost = ExtractedEndpointData::fromRoute(new Route([$postMethod], $routePath, $routeInfo));
        $resultPost = $this->strategy->__invoke($endpointDataPost);
        $this->assertEmpty($resultPost, "Expected empty result for custom action HTTP method: {$postMethod}");
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
        $this->assertStringContainsString(
            '([Spec](https://jsonapi.org/format/#fetching-includes))', $includeParam['description']
        );
        $this->assertStringContainsString('Available includes:', $includeParam['description']);
        $this->assertStringContainsString('`status`, `roles`', $includeParam['description']);
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
        $this->assertArrayHasKey('meta[users]', $result);
        $meta = $result['meta[users]'];

        $this->assertStringContainsString('Additional metadata', $meta['description']);
        $this->assertFalse($meta['required']);
        $this->assertEquals('string', $meta['type']);
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
        $this->assertArrayHasKey('fields[users]', $result);
        $fields = $result['fields[users]'];

        $this->assertStringContainsString('Sparse fieldsets', $fields['description']);
        $this->assertStringContainsString(
            'https://jsonapi.org/format/#fetching-sparse-fieldsets', $fields['description']
        );
        $this->assertFalse($fields['required']);
        $this->assertEquals('string', $fields['type']);
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
        $this->assertIsArray($result['filter']); // Simplified check
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
            'description' => 'Sort the results by attributes. Prefix with `-` for descending order.'
                           . " ([Spec](https://jsonapi.org/format/#fetching-sorting))\n\n"
                           . '**Available sort fields for users:** `name`, `email`',
            'required' => false,
            'example' => 'name,email'
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
        $this->assertEquals([
            'description' => 'Page number.'
                           . ' ([Spec](https://jsonapi.org/format/#fetching-pagination))',
            'required' => false,
            'type' => 'integer',
            'example' => 1,
        ], $result['page[number]']);

        $this->assertEquals([
            'description' => 'Number of results per page.'
                           . ' ([Spec](https://jsonapi.org/format/#fetching-pagination))',
            'required' => false,
            'type' => 'integer',
            'example' => 10,
        ], $result['page[size]']);
//
//        $this->assertEquals([
//            'description' => 'Maximum number of results to return.'
//                           . ' ([Spec](https://jsonapi.org/format/#fetching-pagination))',
//            'required' => false,
//            'type' => 'integer',
//            'example' => 10,
//        ], $result['page[limit]']);
//
//        $this->assertEquals([
//            'description' => 'Number of results to skip.'
//                           . ' ([Spec](https://jsonapi.org/format/#fetching-pagination))',
//            'required' => false,
//            'type' => 'integer',
//            'example' => 0,
//        ], $result['page[offset]']);
    }
}
