<?php

namespace Tests\Scribe;

use Sowl\JsonApi\Scribe\RouteMatcher;
use Tests\TestCase;

class RouteMatcherTest extends TestCase
{
    protected RouteMatcher $routeMatcher;

    protected array $routes;

    protected array $routesByMethodUri;

    protected function setUp(): void
    {
        parent::setUp();

        $this->routeMatcher = app(RouteMatcher::class);
        $this->routes = $this->routeMatcher->getRoutes([
            [
                'match' => [
                    'prefixes' => ['*'],
                    'domains' => ['*'],
                    'versions' => ['v1'],
                ],
                'include' => [],
                'exclude' => [],
            ],
        ]);

        // Create more usable route mappings
        $this->routesByMethodUri = [];
        foreach ($this->routes as $route) {
            $uri = $route->getRoute()->uri();
            $methods = $route->getRoute()->methods();
            foreach ($methods as $method) {
                if (! isset($this->routesByMethodUri[$method])) {
                    $this->routesByMethodUri[$method] = [];
                }
                $this->routesByMethodUri[$method][$uri] = $route;
            }
        }
    }
    /**
     * Test that routes with {resourceType} placeholders are properly expanded
     * into concrete resource types.
     */
    public function testExpandsResourceTypePlaceholders(): void
    {
        // 1. No route should contain an unexpanded {resourceType} placeholder
        $allUris = [];
        foreach ($this->routesByMethodUri as $routes) {
            $allUris = array_merge($allUris, array_keys($routes));
        }
        $allUris = array_unique($allUris);

        foreach ($allUris as $uri) {
            $this->assertStringNotContainsString(
                '{resourceType}',
                $uri,
                "Route contains unexpanded {resourceType}: $uri"
            );

            $this->assertStringNotContainsString(
                '{relationship}',
                $uri,
                "Route contains unexpanded {relationship}: $uri"
            );
        }
    }

    /**
     * Test that routes with {relationship} placeholders are properly expanded
     */
    public function testExpandsRelationshipPlaceholders(): void
    {
        // 1. No route should contain an unexpanded {relationship} placeholder
        $allUris = [];
        foreach ($this->routesByMethodUri as $routes) {
            $allUris = array_merge($allUris, array_keys($routes));
        }
        $allUris = array_unique($allUris);

        foreach ($allUris as $uri) {
            $this->assertStringNotContainsString(
                '{relationship}',
                $uri,
                "Route contains unexpanded {relationship}: $uri"
            );
        }
    }

    /**
     * Complete list of all endpoints that should be generated by the RouteMatcher
     */
    public function testCompleteEndpointList(): void
    {
        // Single resource endpoints
        $this->assertRouteExists('GET', 'api/pages');
        $this->assertRouteExists('POST', 'api/pages');
        $this->assertRouteExists('GET', 'api/pages/{id}');
        $this->assertRouteExists('PATCH', 'api/pages/{id}');
        $this->assertRouteExists('DELETE', 'api/pages/{id}');

        $this->assertRouteExists('GET', 'api/pageComments');
        $this->assertRouteExists('POST', 'api/pageComments');
        $this->assertRouteExists('GET', 'api/pageComments/{id}');
        $this->assertRouteExists('PATCH', 'api/pageComments/{id}');
        $this->assertRouteExists('DELETE', 'api/pageComments/{id}');

        $this->assertRouteExists('GET', 'api/users');
        $this->assertRouteExists('POST', 'api/users');
        $this->assertRouteExists('GET', 'api/users/{id}');
        $this->assertRouteExists('PATCH', 'api/users/{id}');
        $this->assertRouteExists('DELETE', 'api/users/{id}');

        $this->assertRouteExists('GET', 'api/roles');
        $this->assertRouteExists('POST', 'api/roles');
        $this->assertRouteExists('GET', 'api/roles/{id}');
        $this->assertRouteExists('PATCH', 'api/roles/{id}');
        $this->assertRouteExists('DELETE', 'api/roles/{id}');

        $this->assertRouteExists('GET', 'api/userStatuses');
        $this->assertRouteExists('POST', 'api/userStatuses');
        $this->assertRouteExists('GET', 'api/userStatuses/{id}');
        $this->assertRouteExists('PATCH', 'api/userStatuses/{id}');
        $this->assertRouteExists('DELETE', 'api/userStatuses/{id}');

        // Related resource endpoints
        $this->assertRouteExists('GET', 'api/pages/{id}/user');
        $this->assertRouteExists('GET', 'api/pages/{id}/pageComments');

        $this->assertRouteExists('GET', 'api/pageComments/{id}/user');
        $this->assertRouteExists('GET', 'api/pageComments/{id}/page');

        $this->assertRouteExists('GET', 'api/users/{id}/roles');
        $this->assertRouteExists('GET', 'api/users/{id}/status');

        // To-one relationship endpoints
        $this->assertRouteExists('GET', 'api/pages/{id}/relationships/user');
        $this->assertRouteExists('PATCH', 'api/pages/{id}/relationships/user');

        $this->assertRouteExists('GET', 'api/pageComments/{id}/relationships/user');
        $this->assertRouteExists('PATCH', 'api/pageComments/{id}/relationships/user');
        $this->assertRouteExists('GET', 'api/pageComments/{id}/relationships/page');
        $this->assertRouteExists('PATCH', 'api/pageComments/{id}/relationships/page');

        $this->assertRouteExists('GET', 'api/users/{id}/relationships/status');
        $this->assertRouteExists('PATCH', 'api/users/{id}/relationships/status');

        // To-many relationship endpoints
        $this->assertRouteExists('GET', 'api/pages/{id}/relationships/pageComments');
        $this->assertRouteExists('POST', 'api/pages/{id}/relationships/pageComments');
        $this->assertRouteExists('PATCH', 'api/pages/{id}/relationships/pageComments');
        $this->assertRouteExists('DELETE', 'api/pages/{id}/relationships/pageComments');

        $this->assertRouteExists('GET', 'api/users/{id}/relationships/roles');
        $this->assertRouteExists('POST', 'api/users/{id}/relationships/roles');
        $this->assertRouteExists('PATCH', 'api/users/{id}/relationships/roles');
        $this->assertRouteExists('DELETE', 'api/users/{id}/relationships/roles');
    }

    /**
     * Test that there are no duplicate routes in the route collection
     */
    public function testNoDuplicateRoutes(): void
    {
        // Get all routes as method + uri combinations
        $routeSignatures = [];
        foreach ($this->routes as $route) {
            $uri = $route->getRoute()->uri();
            $methods = $route->getRoute()->methods();

            foreach ($methods as $method) {
                $signature = "$method $uri";
                if (isset($routeSignatures[$signature])) {
                    $this->fail("Duplicate route found: $signature");
                }
                $routeSignatures[$signature] = true;
            }
        }

        // Count unique routes
        $uniqueRouteCount = count($routeSignatures);
        $totalRouteCount = 0;

        // Count total method + uri combinations
        foreach ($this->routes as $route) {
            $totalRouteCount += count($route->getRoute()->methods());
        }

        // Assert that the counts match
        $this->assertEquals(
            $uniqueRouteCount,
            $totalRouteCount,
            "Expected $uniqueRouteCount unique routes, but found $totalRouteCount total routes"
        );
    }

    /**
     * Helper method to assert that a route exists and supports a specific HTTP method
     */
    protected function assertRouteExists(string $method, string $uri): void
    {
        $this->assertTrue(isset($this->routesByMethodUri[$method][$uri]), "Route not found: $method $uri");
    }
}
