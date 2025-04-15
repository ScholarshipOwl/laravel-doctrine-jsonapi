<?php

namespace Tests\Scribe\Strategies;

use Knuckles\Camel\Extraction\ExtractedEndpointData;
use Illuminate\Routing\Route;
use Knuckles\Scribe\Tools\DocumentationConfig;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;
use Sowl\JsonApi\ResourceManager;
use Sowl\JsonApi\Scribe\Strategies\Headers\AddJsonApiHeadersStrategy;

class AddJsonApiHeadersStrategyTest extends TestCase
{
    private ResourceManager|MockInterface $mockResourceManager;

    private AddJsonApiHeadersStrategy $strategy;

    private DocumentationConfig|MockInterface $mockConfig;


    protected function setUp(): void
    {
        parent::setUp();

        $this->mockResourceManager = Mockery::mock(ResourceManager::class);

        // Create a mock for DocumentationConfig
        $this->mockConfig = Mockery::mock(DocumentationConfig::class);

        // Create the strategy with the mocked config
        $this->strategy = new AddJsonApiHeadersStrategy($this->mockConfig, $this->mockResourceManager);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testReturnsJsonApiHeadersForJsonApiRoutes()
    {
        $endpointData = ExtractedEndpointData::fromRoute(new Route(['GET'], 'users', [
            'as' => 'jsonapi.users.list',
            'uses' => fn () => null,
            ['prefix' => '/api']
        ]));

        // Execute the strategy
        $result = $this->strategy->__invoke($endpointData);

        // Assert that the correct headers are returned
        $this->assertEquals([
            'Accept' => 'application/vnd.api+json',
            'Content-Type' => 'application/vnd.api+json',
        ], $result);
    }

    public function testReturnsEmptyArrayForNonJsonApiRoutes()
    {
        $endpointData = ExtractedEndpointData::fromRoute(new Route(['GET'], 'users', [
            'as' => 'api.users.list',
            'uses' => fn () => null
        ]));

        // Execute the strategy
        $result = $this->strategy->__invoke($endpointData);

        // Assert that an empty array is returned for non-JSON:API routes
        $this->assertEquals([], $result);
    }

    public function testHandlesVariousJsonApiRouteNames()
    {
        $jsonApiRouteNames = [
            'jsonapi.users.list',
            'jsonapi.users.show',
            'jsonapi.users.create',
            'jsonapi.users.update',
            'jsonapi.users.remove',
            'jsonapi.users.roles.showRelated',
            'jsonapi.users.roles.showRelationships',
            'jsonapi.users.roles.createRelationships',
            'jsonapi.users.roles.updateRelationships',
            'jsonapi.users.roles.removeRelationships',
        ];

        foreach ($jsonApiRouteNames as $routeName) {
            $endpointData = ExtractedEndpointData::fromRoute(new Route(['GET'], 'users', [
                'as' => $routeName,
                'uses' => fn () => null
            ]));

            // Execute the strategy
            $result = $this->strategy->__invoke($endpointData);

            // Assert that the correct headers are returned for all JSON:API routes
            $this->assertEquals([
                'Accept' => 'application/vnd.api+json',
                'Content-Type' => 'application/vnd.api+json',
            ], $result, "Failed for route name: $routeName");
        }
    }

    public function testHandlesSettingsParameter()
    {
        // Execute the strategy with custom settings
        $customSettings = ['custom' => 'setting'];

        $endpointData = ExtractedEndpointData::fromRoute(new Route(['GET'], 'users', [
            'as' => 'jsonapi.users.list',
            'uses' => fn () => null,
        ]));

        $result = $this->strategy->__invoke($endpointData, $customSettings);

        // Assert that the correct headers are returned regardless of settings
        $this->assertEquals([
            'Accept' => 'application/vnd.api+json',
            'Content-Type' => 'application/vnd.api+json',
        ], $result);
    }
}
