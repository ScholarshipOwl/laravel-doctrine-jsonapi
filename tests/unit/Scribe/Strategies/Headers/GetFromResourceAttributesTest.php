<?php

declare(strict_types=1);

namespace Tests\Scribe\Strategies\Headers;

use Knuckles\Scribe\Tools\DocumentationConfig;
use Sowl\JsonApi\Scribe\Attributes\ResourceRequest;
use Sowl\JsonApi\Scribe\Attributes\ResourceResponse;
use Sowl\JsonApi\Scribe\Strategies\Headers\GetFromResourceAttributes;
use Mockery;
use Tests\TestCase;
use Tests\ExtractedEndpointDataBuilder;

class GetFromResourceAttributesTest extends TestCase
{
    use ExtractedEndpointDataBuilder;

    private GetFromResourceAttributes $strategy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->strategy = new GetFromResourceAttributes(new DocumentationConfig());
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testReturnsJsonApiHeadersForJsonApiRoutes()
    {
        $endpointData = $this->buildExtractedEndpointData(
            'GET',
            'users',
            [
                'as' => 'jsonapi.users.list',
                'uses' => new class {
                    #[ResourceRequest]
                    #[ResourceResponse]
                    public function __invoke()
                    {
                        return null;
                    }
                },
            ]
        );

        $result = $this->strategy->__invoke($endpointData);

        $this->assertEquals([
            'Accept' => 'application/vnd.api+json',
            'Content-Type' => 'application/vnd.api+json',
        ], $result);
    }

    public function testReturnsEmptyArrayForNonJsonApiRoutes()
    {
        $endpointData = $this->buildExtractedEndpointData(
            'GET',
            'users',
            [
                'as' => 'api.users.list',
                'uses' => new class {
                    public function __invoke()
                    {
                        return null;
                    }
                },
            ],
            false
        );

        $result = $this->strategy->__invoke($endpointData);

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
            $endpointData = $this->buildExtractedEndpointData(
                'GET',
                'users',
                [
                    'as' => $routeName,
                    'uses' => new class {
                        #[ResourceRequest]
                        #[ResourceResponse]
                        public function __invoke()
                        {
                            return null;
                        }
                    },
                ]
            );

            $result = $this->strategy->__invoke($endpointData);

            $this->assertEquals([
                'Accept' => 'application/vnd.api+json',
                'Content-Type' => 'application/vnd.api+json',
            ], $result, "Failed for route name: $routeName");
        }
    }

    public function testHandlesSettingsParameter()
    {
        $customSettings = ['custom' => 'setting'];

        $endpointData = $this->buildExtractedEndpointData(
            'GET',
            'users',
            [
                'as' => 'jsonapi.users.list',
                'uses' => new class {
                    #[ResourceRequest]
                    #[ResourceResponse]
                    public function __invoke()
                    {
                        return null;
                    }
                },
            ]
        );

        $result = $this->strategy->__invoke($endpointData, $customSettings);

        $this->assertEquals([
            'Accept' => 'application/vnd.api+json',
            'Content-Type' => 'application/vnd.api+json',
        ], $result);
    }
}
