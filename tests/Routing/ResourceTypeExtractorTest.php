<?php

namespace Tests\Routing;

use Illuminate\Routing\Route;
use PHPUnit\Framework\Attributes\DataProvider;
use Sowl\JsonApi\Routing\ResourceTypeExtractor;
use Tests\TestCase;

class ResourceTypeExtractorTest extends TestCase
{
    private ResourceTypeExtractor $extractor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->extractor = new ResourceTypeExtractor;
    }

    #[DataProvider('provideRouteData')]
    public function test_extract_resource_type(string $uri, ?string $expectedResourceType, array $options = []): void
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
    public static function provideRouteData(): array
    {
        return [
            'special route case' => [
                'users/specialCustomEndpoint',
                'users',
                [],
            ],
            'simple resource' => [
                'users',
                'users',
                [],
            ],
            'resource with id' => [
                'users/{id}',
                'users',
                [],
            ],
            'prefixed resource' => [
                'users',
                'users',
            ],
            'nested resource' => [
                'users/{id}/posts',
                'users',
                [],
            ],
            'relationship' => [
                'users/{id}/relationships/posts',
                'users',
                [],
            ],
            'multiple parameters' => [
                'users/{userId}/posts/{postId}',
                'users',
                [],
            ],
            'resource type parameter' => [
                '{resourceType}/{id}',
                null,
                [],
            ],
            'dictionary type parameter only' => [
                '/{dictionaryType}',
                null,
                [],
            ],
            // Additional creative test cases
            'kebab-case resource' => [
                'blog-posts',
                'blog-posts',
                [],
            ],
            'snake_case resource' => [
                'user_profiles',
                'user_profiles',
                [],
            ],
            'complex nested resources' => [
                'organizations/{orgId}/departments/{deptId}/employees/{empId}/tasks',
                'organizations',
                [],
            ],
            'resource with query parameter in route definition' => [
                'search/{query?}',
                'search',
                [],
            ],
            'resource with regex constraint' => [
                'articles/{id:[0-9]+}',
                'articles',
                [],
            ],
            'resource with dot notation' => [
                'api.v1.users',
                'api.v1.users',
                [],
            ],
            'empty URI' => [
                '',
                null,
                [],
            ],
        ];
    }
}
