<?php

declare(strict_types=1);

namespace Tests\Scribe\Metadata;

use Illuminate\Routing\Route;
use Knuckles\Camel\Extraction\ExtractedEndpointData;
use Knuckles\Scribe\Extracting\ParamBag;
use Knuckles\Scribe\Tools\DocumentationConfig;
use Sowl\JsonApi\Action\JsonApiAction;
use Sowl\JsonApi\Scribe\Metadata\GetFromJsonApiRouteStrategy;
use Tests\TestCase;

/**
 * @group Scribe
 * @group ScribeMetadata
 */
final class GetFromJsonApiRouteStrategyTest extends TestCase
{
    private GetFromJsonApiRouteStrategy $strategy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->strategy = new GetFromJsonApiRouteStrategy(new DocumentationConfig([]));
    }

    public function testGeneratesMetadataForListAction(): void
    {
        $endpointData = ExtractedEndpointData::fromRoute(new Route(['GET'], 'users', [
            'as' => 'jsonapi.users.list',
            'uses' => fn () => null,
            ['prefix' => '/api']
        ]));

        $result = $this->strategy->__invoke($endpointData);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('groupName', $result);
        $this->assertArrayHasKey('title', $result);
        $this->assertArrayHasKey('description', $result);
        $this->assertArrayHasKey('subgroup', $result);

        $this->assertEquals('Users', $result['groupName']); // Expectation might change based on convertToDisplay
        $this->assertEquals('List Users', $result['title']); // Expectation might change based on convertToDisplay
        $this->assertEquals('Retrieve a list of Users.', $result['description']); // Expectation might change
        $this->assertNull($result['subgroup']);
    }
}
