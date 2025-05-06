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

    public function testExtractsRelationshipNames(): void
    {
        // Resource without relationship
        $this->assertExtractsRelationshipName('users/{id}', null);

        // Basic relationships
        $this->assertExtractsRelationshipName('users/{id}/relationships/posts', 'posts');
        $this->assertExtractsRelationshipName('users/{id}/posts', 'posts');

        // Nested relationships
        $this->assertExtractsRelationshipName('users/{id}/relationships/posts/comments', 'posts');
        $this->assertExtractsRelationshipName('users/{id}/posts/comments/likes', 'posts');

        // Special cases
        $this->assertExtractsRelationshipName('users/{userId}/relationships/{relationshipName}', null);
        $this->assertExtractsRelationshipName('', null);

        // Different naming conventions
        $this->assertExtractsRelationshipName('users/{id}/relationships/blog-posts', 'blog-posts');
        $this->assertExtractsRelationshipName('users/{id}/relationships/user_profiles', 'user_profiles');
        $this->assertExtractsRelationshipName('users/{id}/relationships/posts2', 'posts2');
        $this->assertExtractsRelationshipName('users/{id}/relationships/_internal', '_internal');

        // Complex relationships
        $this->assertExtractsRelationshipName(
            'organizations/{orgId}/departments/{deptId}/employees/{empId}/relationships/tasks',
            'tasks'
        );
        $this->assertExtractsRelationshipName(
            'users/{id}/relationships/friends/relationships/posts',
            'friends'
        );

        // Relationship keyword in resource name
        $this->assertExtractsRelationshipName('relationships/{id}/settings', 'settings');

        // Routes with parameters
        $this->assertExtractsRelationshipName(
            'users/{id}/relationships/posts/{postId?}',
            'posts'
        );
        $this->assertExtractsRelationshipName(
            'users/{id}/relationships/comments/{commentId:[0-9]+}',
            'comments'
        );

        // Prefixed routes
        $this->assertExtractsRelationshipName(
            'users/{id}/relationships/posts',
            'posts',
            ['prefix' => 'api/v1']
        );
        $this->assertExtractsRelationshipName(
            'users/{id}/posts',
            'posts',
            ['prefix' => 'api/v1']
        );
        $this->assertExtractsRelationshipName(
            'users/{id}/relationships/posts',
            'posts',
            ['prefix' => 'api/v2/admin']
        );
    }

    private function assertExtractsRelationshipName(
        string $uri,
        ?string $expectedName,
        array $options = []
    ): void {
        $route = new Route('GET', $uri, $options);
        $actualName = $this->extractor->extract($route);
        $this->assertEquals($expectedName, $actualName);
    }

    public function testIdentifiesRelationshipsRoutes(): void
    {
        // Valid relationship routes
        $this->assertTrue($this->extractor->isRelationships(
            new Route(['GET'], 'users/{user}/relationships/roles', [])
        ));
        $this->assertTrue($this->extractor->isRelationships(
            new Route(['GET'], 'api/users/{user}/relationships/roles', [])
        ));
        $this->assertTrue($this->extractor->isRelationships(
            new Route(['GET'], 'users/{user}/relationships/roles/', [])
        ));
        $this->assertTrue($this->extractor->isRelationships(
            new Route(['GET'], 'users/{user}/relationships/{relation:[a-z]+}', [])
        ));
        $this->assertTrue($this->extractor->isRelationships(
            new Route(['GET'], 'users/{user}/relationships/{relation?}', [])
        ));

        // Non-relationship routes
        $this->assertFalse($this->extractor->isRelationships(
            new Route(['GET'], 'users/{user}/roles', [])
        ));
        $this->assertFalse($this->extractor->isRelationships(
            new Route(['GET'], '', [])
        ));
        $this->assertFalse($this->extractor->isRelationships(
            new Route(['GET'], '/', [])
        ));
        $this->assertFalse($this->extractor->isRelationships(
            new Route(['GET'], 'users', [])
        ));
        $this->assertFalse($this->extractor->isRelationships(
            new Route(['GET'], 'users/{user}', [])
        ));
        $this->assertFalse($this->extractor->isRelationships(
            new Route(['GET'], 'users/{user}/relationships', [])
        ));
        $this->assertFalse($this->extractor->isRelationships(
            new Route(['GET'], 'users/{user}/relationships/', [])
        ));
    }
}
