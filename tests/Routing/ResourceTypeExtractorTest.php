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

    public function testExtractsResourceTypes(): void
    {
        // Basic resource types
        $this->assertExtractsResourceType('users/specialCustomEndpoint', 'users');
        $this->assertExtractsResourceType('users', 'users');
        $this->assertExtractsResourceType('users/{id}', 'users');

        // Nested and relationship resources
        $this->assertExtractsResourceType('users/{id}/posts', 'users');
        $this->assertExtractsResourceType('users/{id}/relationships/posts', 'users');
        $this->assertExtractsResourceType('users/{userId}/posts/{postId}', 'users');

        // Special cases
        $this->assertExtractsResourceType('{resourceType}/{id}', null);
        $this->assertExtractsResourceType('/{dictionaryType}', null);
        $this->assertExtractsResourceType('', null);

        // Different naming conventions
        $this->assertExtractsResourceType('blog-posts', 'blog-posts');
        $this->assertExtractsResourceType('user_profiles', 'user_profiles');

        // Complex cases
        $this->assertExtractsResourceType(
            'organizations/{orgId}/departments/{deptId}/employees/{empId}/tasks',
            'organizations'
        );

        // Routes with parameters
        $this->assertExtractsResourceType('search/{query?}', 'search');
        $this->assertExtractsResourceType('articles/{id:[0-9]+}', 'articles');

        // Dot notation
        $this->assertExtractsResourceType('api.v1.users', 'api.v1.users');
    }

    private function assertExtractsResourceType(string $uri, ?string $expectedType): void
    {
        $route = new Route('GET', $uri, []);
        $actualType = $this->extractor->extract($route);
        $this->assertEquals($expectedType, $actualType);
    }
}
