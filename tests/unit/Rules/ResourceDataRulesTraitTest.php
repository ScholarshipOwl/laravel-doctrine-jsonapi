<?php

namespace Sowl\JsonApi\Test\Unit\Rules;

use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\TestCase;
use Sowl\JsonApi\Default\Request\ResourceDataRulesTrait;
use Sowl\JsonApi\Relationships\RelationshipInterface;
use Sowl\JsonApi\Relationships\RelationshipsCollection;
use Sowl\JsonApi\ResourceManager;
use Sowl\JsonApi\ResourceRepository;

class ResourceDataRulesTraitTest extends TestCase
{
    /**
     * Test class that uses the ResourceDataRulesTrait
     */
    private $testRequest;

    /**
     * Mock repository
     */
    private $mockRepository;

    /**
     * Mock resource manager
     */
    private $mockResourceManager;

    /**
     * Mock class metadata
     */
    private $mockMetadata;

    /**
     * Mock relationships collection
     */
    private $mockRelationshipsCollection;

    protected function setUp(): void
    {
        parent::setUp();

        // Create mock repository
        $this->mockRepository = $this->createMock(ResourceRepository::class);

        // Create mock resource manager
        $this->mockResourceManager = $this->createMock(ResourceManager::class);

        // Create mock metadata
        $this->mockMetadata = $this->createMock(ClassMetadata::class);

        // Create mock relationships collection
        $this->mockRelationshipsCollection = $this->createMock(RelationshipsCollection::class);

        // Setup the test request class that uses the trait
        $this->testRequest = new class ($this->mockRepository, $this->mockResourceManager) {
            use ResourceDataRulesTrait;

            private $repo;
            private $resourceManager;
            private $type;

            public function __construct($repo, $resourceManager)
            {
                $this->repo = $repo;
                $this->resourceManager = $resourceManager;
                $this->type = 'test-resource';
            }

            public function repository(): ResourceRepository
            {
                return $this->repo;
            }

            public function rm(): ResourceManager
            {
                return $this->resourceManager;
            }

            public function resourceType(): string
            {
                return $this->type;
            }
        };
    }

    public function testDataRulesIncludesRequiredDataRule()
    {
        // Setup mocks
        $this->mockRepository->expects($this->once())
            ->method('metadata')
            ->willReturn($this->mockMetadata);

        $this->mockMetadata->fieldMappings = [];
        $this->mockMetadata->identifier = [];

        $this->mockResourceManager->expects($this->once())
            ->method('relationshipsByresourceType')
            ->willReturn($this->mockRelationshipsCollection);

        $this->mockRelationshipsCollection->expects($this->once())
            ->method('all')
            ->willReturn([]);

        // Call the method
        $rules = $this->testRequest->dataRules();

        // Assert that the 'data' rule is included and is required|array
        $this->assertArrayHasKey('data', $rules);
        $this->assertEquals('required|array', $rules['data']);
    }

    public function testAttributeRulesGeneratedCorrectly()
    {
        // Setup field mappings with different types
        $fieldMappings = [
            'name' => [
                'type' => 'string',
                'length' => 255,
                'nullable' => false
            ],
            'description' => [
                'type' => 'text',
                'nullable' => true
            ],
            'age' => [
                'type' => 'integer',
                'nullable' => false
            ],
            'isActive' => [
                'type' => 'boolean',
                'nullable' => false
            ],
            'price' => [
                'type' => 'decimal',
                'precision' => 10,
                'scale' => 2,
                'nullable' => false
            ],
            'createdAt' => [
                'type' => 'datetime',
                'nullable' => false
            ],
            'tags' => [
                'type' => 'json_array',
                'nullable' => true
            ],
            'uuid' => [
                'type' => 'guid',
                'nullable' => false
            ]
        ];

        // Setup mocks
        $this->mockRepository->expects($this->once())
            ->method('metadata')
            ->willReturn($this->mockMetadata);

        $this->mockMetadata->fieldMappings = $fieldMappings;
        $this->mockMetadata->identifier = ['id']; // Exclude 'id' from attributes

        $this->mockResourceManager->expects($this->once())
            ->method('relationshipsByresourceType')
            ->willReturn($this->mockRelationshipsCollection);

        $this->mockRelationshipsCollection->expects($this->once())
            ->method('all')
            ->willReturn([]);

        // Call the method
        $rules = $this->testRequest->dataRules();

        // Assert string type rules
        $this->assertArrayHasKey('data.attributes.name', $rules);
        $this->assertContains('string', $rules['data.attributes.name']);
        $this->assertContains('max:255', $rules['data.attributes.name']);

        // Assert text type rules
        $this->assertArrayHasKey('data.attributes.description', $rules);
        $this->assertContains('string', $rules['data.attributes.description']);
        $this->assertContains('nullable', $rules['data.attributes.description']);

        // Assert integer type rules
        $this->assertArrayHasKey('data.attributes.age', $rules);
        $this->assertContains('integer', $rules['data.attributes.age']);

        // Assert boolean type rules
        $this->assertArrayHasKey('data.attributes.isActive', $rules);
        $this->assertContains('boolean', $rules['data.attributes.isActive']);

        // Assert decimal type rules
        $this->assertArrayHasKey('data.attributes.price', $rules);
        $this->assertContains('numeric', $rules['data.attributes.price']);
        $this->assertContains('decimal:2', $rules['data.attributes.price']);

        // Assert datetime type rules
        $this->assertArrayHasKey('data.attributes.createdAt', $rules);
        $this->assertContains('date_format:Y-m-d\TH:i:s.u\Z', $rules['data.attributes.createdAt']);

        // Assert json_array type rules
        $this->assertArrayHasKey('data.attributes.tags', $rules);
        $this->assertContains('array', $rules['data.attributes.tags']);
        $this->assertContains('nullable', $rules['data.attributes.tags']);

        // Assert guid type rules
        $this->assertArrayHasKey('data.attributes.uuid', $rules);
        $this->assertContains('uuid', $rules['data.attributes.uuid']);
    }
}
