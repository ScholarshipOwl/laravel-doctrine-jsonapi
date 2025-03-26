<?php

namespace Tests\Routing;

use Illuminate\Routing\Route;
use Sowl\JsonApi\Routing\ResourceTypeExtractor;
use Tests\TestCase;

class ResourceTypeExtractorTest extends TestCase
{
    private ResourceTypeExtractor $extractor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->extractor = new ResourceTypeExtractor();
    }

    /**
     * @dataProvider provideRouteData
     */
    public function testExtractResourceType(string $uri, ?string $expectedResourceType, array $options = []): void
    {
        // Arrange
        $route = new Route('GET', $uri, $options);

        // Act
        $actualResourceType = $this->extractor->extract($route);

        // Assert
        $this->assertEquals($expectedResourceType, $actualResourceType);
    }

    /**
     * Provides test cases for resource type extraction
     *
     * @return array<string, array{string, string|null, array}>
     */
    public function provideRouteData(): array
    {
        return [
            'simple resource' => [
                'users',
                'users',
                []
            ],
            'resource with id' => [
                'users/{id}',
                'users',
                []
            ],
            'prefixed resource' => [
                'users',
                'users',
                ['prefix' => 'api/v1']
            ],
            'nested resource' => [
                'users/{id}/posts',
                'users',
                []
            ],
            'relationship' => [
                'users/{id}/relationships/posts',
                'users',
                []
            ],
            'multiple parameters' => [
                'users/{userId}/posts/{postId}',
                'users',
                []
            ],
            'resource type parameter' => [
                '{resourceType}/{id}',
                null,
                []
            ],
            // Additional creative test cases
            'kebab-case resource' => [
                'blog-posts',
                'blog-posts',
                []
            ],
            'snake_case resource' => [
                'user_profiles',
                'user_profiles',
                []
            ],
            'multiple prefixes' => [
                'admin/users',
                'admin',
                ['prefix' => 'api/v2']
            ],
            'complex nested resources' => [
                'organizations/{orgId}/departments/{deptId}/employees/{empId}/tasks',
                'organizations',
                []
            ],
            'resource with query parameter in route definition' => [
                'search/{query?}',
                'search',
                []
            ],
            'resource with regex constraint' => [
                'articles/{id:[0-9]+}',
                'articles',
                []
            ],
            'deeply nested API version' => [
                'beta/users',
                'beta',
                ['prefix' => 'api/v3.1']
            ],
            'resource with dot notation' => [
                'api.v1.users',
                'api.v1.users',
                []
            ],
            'empty URI' => [
                '',
                null,
                []
            ]
        ];
    }
}
