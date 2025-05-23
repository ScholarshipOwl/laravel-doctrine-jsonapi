<?php

declare(strict_types=1);

namespace Tests\Scribe\Strategies\UrlParameters;

use Knuckles\Scribe\Tools\DocumentationConfig;
use Sowl\JsonApi\Scribe\Attributes\ResourceRequest;
use Sowl\JsonApi\Scribe\Strategies\UrlParameters\GetFromResourceRequestAttributes;
use Tests\ExtractedEndpointDataBuilder;
use Tests\TestCase;

class GetFromResourceRequestAttributesTest extends TestCase
{
    use ExtractedEndpointDataBuilder;

    protected GetFromResourceRequestAttributes $strategy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->strategy = new GetFromResourceRequestAttributes(new DocumentationConfig());
    }

    public function testReturnsIdParamWithMetadataDefaults(): void
    {
        $endpointData = $this->buildExtractedEndpointData(
            'GET',
            'users/{id}',
            [
                'as' => 'jsonapi.users.show',
                'uses' => new class () {
                    #[ResourceRequest]
                    public function __invoke(): array
                    {
                        return [];
                    }
                },
            ]
        );
        $params = $this->strategy->__invoke($endpointData);
        $expected = [
            'id' => [
                'description' => "The unique identifier of the 'users' resource",
                'required' => true,
                'type' => 'string',
                'example' => '12345678-1234-1234-1234-123456789012',
            ],
        ];
        $this->assertEquals($expected, $params);
    }

    public function testReturnsCustomIdParam(): void
    {
        $endpointData = $this->buildExtractedEndpointData(
            'GET',
            'users/{user}',
            [
                'as' => 'jsonapi.users.show',
                'uses' => new class () {
                    #[ResourceRequest(idParam: 'user')]
                    public function __invoke(): array
                    {
                        return [];
                    }
                },
            ]
        );
        $params = $this->strategy->__invoke($endpointData);
        $expected = [
            'user' => [
                'description' => "The unique identifier of the 'users' resource",
                'required' => true,
                'type' => 'string',
                'example' => '12345678-1234-1234-1234-123456789012',
            ],
        ];
        $this->assertEquals($expected, $params);
    }

    public function testReturnsIdTypeOverride(): void
    {
        $endpointData = $this->buildExtractedEndpointData(
            'GET',
            'users/{id}',
            [
                'as' => 'jsonapi.users.show',
                'uses' => new class () {
                    #[ResourceRequest(idType: 'number', idExample: 123)]
                    public function __invoke(): array
                    {
                        return [];
                    }
                },
            ]
        );
        $params = $this->strategy->__invoke($endpointData);
        $expected = [
            'id' => [
                'description' => "The unique identifier of the 'users' resource",
                'required' => true,
                'type' => 'number',
                'example' => 123,
            ],
        ];
        $this->assertEquals($expected, $params);
    }

    public function testReturnsIdExampleOverride(): void
    {
        $endpointData = $this->buildExtractedEndpointData(
            'GET',
            'users/{id}',
            [
                'as' => 'jsonapi.users.show',
                'uses' => new class () {
                    #[ResourceRequest(idExample: 'abc-123')]
                    public function __invoke(): array
                    {
                        return [];
                    }
                },
            ]
        );
        $params = $this->strategy->__invoke($endpointData);
        $expected = [
            'id' => [
                'description' => "The unique identifier of the 'users' resource",
                'required' => true,
                'type' => 'string',
                'example' => 'abc-123',
            ],
        ];
        $this->assertEquals($expected, $params);
    }

    public function testReturnsEmptyWhenNoIdParam(): void
    {
        $this->noScribeDebugOutput();

        $endpointData = $this->buildExtractedEndpointData(
            'GET',
            'users',
            [
                'as' => 'jsonapi.users.index',
                'uses' => new class () {
                    #[ResourceRequest]
                    public function __invoke(): array
                    {
                        return [];
                    }
                },
            ]
        );
        $params = $this->strategy->__invoke($endpointData);
        $this->assertEquals([], $params);
    }

    public function testReturnsEmptyWhenNoAttribute(): void
    {
        $endpointData = $this->buildExtractedEndpointData(
            'GET',
            'users/{id}',
            [
                'as' => 'jsonapi.users.show',
                'uses' => new class () {
                    public function __invoke(): array
                    {
                        return [];
                    }
                },
            ]
        );
        $params = $this->strategy->__invoke($endpointData);
        $this->assertEquals([], $params);
    }
}
