<?php

namespace Tests\Routing;

use Illuminate\Routing\Route;
use Sowl\JsonApi\Routing\RelationshipNameExtractor;
use Tests\TestCase;

class RelationshipNameExtractorTest extends TestCase
{
    private RelationshipNameExtractor $extractor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->extractor = new RelationshipNameExtractor();
    }

    /**
     * @dataProvider provideRouteData
     */
    public function testExtractRelationshipName(string $uri, ?string $expectedRelationshipName, array $options = []): void
    {
        // Arrange
        $route = new Route('GET', $uri, $options);

        // Act
        $actualRelationshipName = $this->extractor->extract($route);

        // Assert
        $this->assertEquals($expectedRelationshipName, $actualRelationshipName);
    }

    /**
     * Provides test cases for relationship name extraction
     *
     * @return array<string, array{string, string|null, array}>
     */
    public function provideRouteData(): array
    {
        return [
            'resource without relationship' => [
                'users/{id}',
                null,
                []
            ],
            'resource with relationship' => [
                'users/{id}/relationships/posts',
                'posts',
                []
            ],
            'resource with related' => [
                'users/{id}/posts',
                'posts',
                []
            ],
            'nested relationship' => [
                'users/{id}/relationships/posts/comments',
                'posts',
                []
            ],
            'relationship with parameters' => [
                'users/{userId}/relationships/{relationshipName}',
                null,
                []
            ],
            // Additional creative test cases
            'kebab-case relationship' => [
                'users/{id}/relationships/blog-posts',
                'blog-posts',
                []
            ],
            'snake_case relationship' => [
                'users/{id}/relationships/user_profiles',
                'user_profiles',
                []
            ],
            'relationship with numeric suffix' => [
                'users/{id}/relationships/posts2',
                'posts2',
                []
            ],
            'relationship with underscore prefix' => [
                'users/{id}/relationships/_internal',
                '_internal',
                []
            ],
            'complex nested relationships' => [
                'organizations/{orgId}/departments/{deptId}/employees/{empId}/relationships/tasks',
                'tasks',
                []
            ],
            'relationship with query parameter' => [
                'users/{id}/relationships/posts/{postId?}',
                'posts',
                []
            ],
            'relationship with regex constraint' => [
                'users/{id}/relationships/comments/{commentId:[0-9]+}',
                'comments',
                []
            ],
            'multiple related resources' => [
                'users/{id}/posts/comments/likes',
                'posts',
                []
            ],
            'empty URI' => [
                '',
                null,
                []
            ],
            'relationship keyword in resource name' => [
                'relationships/{id}/settings',
                'settings',
                []
            ],
            'multiple relationship segments' => [
                'users/{id}/relationships/friends/relationships/posts',
                'friends',
                []
            ],
            'prefixed resource with relationship' => [
                'users/{id}/relationships/posts',
                'posts',
                ['prefix' => 'api/v1']
            ],
            'prefixed resource with related' => [
                'users/{id}/posts',
                'posts',
                ['prefix' => 'api/v1']
            ],
            'deeply nested prefixed relationship' => [
                'users/{id}/relationships/posts',
                'posts',
                ['prefix' => 'api/v2/admin']
            ]
        ];
    }

    /**
     * Test that extractIsRelationships correctly identifies relationships routes
     *
     * @dataProvider provideRelationshipRoutes
     */
    public function testExtractIsRelationships(string $uri, bool $expected)
    {
        $route = new Route(['GET'], $uri, []);
        $this->assertEquals($expected, $this->extractor->isRelationships($route));
    }

    public function provideRelationshipRoutes(): array
    {
        return [
            'relationships route' => ['users/{user}/relationships/roles', true],
            'relationships route with prefix' => ['api/users/{user}/relationships/roles', true],
            'relationships route with trailing slash' => ['users/{user}/relationships/roles/', true],
            'related resource route' => ['users/{user}/roles', false],
            'empty uri' => ['', false],
            'root path' => ['/', false],
            'simple resource' => ['users', false],
            'resource with id' => ['users/{user}', false],
            'relationships without relation' => ['users/{user}/relationships', false],
            'relationships with empty relation' => ['users/{user}/relationships/', false],
            'relationships with regex constraint' => ['users/{user}/relationships/{relation:[a-z]+}', true],
            'relationships with optional parameter' => ['users/{user}/relationships/{relation?}', true],
        ];
    }
}
