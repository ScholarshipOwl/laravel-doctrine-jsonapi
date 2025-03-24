<?php

namespace Sowl\JsonApi\Scribe;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Sowl\JsonApi\ResourceInterface;
use Sowl\JsonApi\ResourceManager;

/**
 * Helper class to extract information from Doctrine entities for documentation
 */
class DoctrineEntityExtractor
{
    /**
     * Constructor
     */
    public function __construct(
        protected EntityManager $entityManager,
        protected ResourceManager $resourceManager
    ) {
    }

    /**
     * Extract schema information for a Doctrine entity that implements ResourceInterface
     *
     * @param string $entityClass The class name of the entity
     * @return array Schema information in OpenAPI compatible format
     */
    public function extractEntitySchema(string $entityClass): array
    {
        if (!is_subclass_of($entityClass, ResourceInterface::class)) {
            throw new \InvalidArgumentException("Entity class must implement ResourceInterface");
        }
        
        $metadata = $this->entityManager->getClassMetadata($entityClass);
        $resourceType = $entityClass::getResourceType();
        
        $properties = $this->extractProperties($metadata);
        $relationships = $this->extractRelationships($entityClass);
        
        return [
            'type' => 'object',
            'properties' => [
                'id' => [
                    'type' => 'string',
                    'description' => 'The resource ID',
                ],
                'type' => [
                    'type' => 'string',
                    'description' => 'The resource type',
                    'example' => $resourceType,
                ],
                'attributes' => [
                    'type' => 'object',
                    'properties' => $properties,
                ],
                'relationships' => [
                    'type' => 'object',
                    'properties' => $relationships,
                ],
            ],
        ];
    }

    /**
     * Extract entity properties (excluding relationships)
     *
     * @param ClassMetadata $metadata
     * @return array
     */
    protected function extractProperties(ClassMetadata $metadata): array
    {
        $properties = [];
        $idFieldNames = $metadata->getIdentifierFieldNames();
        
        foreach ($metadata->getFieldNames() as $fieldName) {
            // Skip identifier fields as they are part of the resource ID
            if (in_array($fieldName, $idFieldNames)) {
                continue;
            }
            
            $mapping = $metadata->getFieldMapping($fieldName);
            $properties[$fieldName] = $this->mapDoctrineTypeToOpenApiType($mapping['type']);
        }
        
        return $properties;
    }

    /**
     * Extract relationships for an entity
     *
     * @param string $entityClass The class name of the entity
     * @return array
     */
    protected function extractRelationships(string $entityClass): array
    {
        $relationships = [];
        
        if (method_exists($entityClass, 'relationships')) {
            $entityRelationships = $entityClass::relationships();
            
            foreach ($entityRelationships as $relationship) {
                $name = $relationship->getName();
                $isToMany = $relationship->isToMany();
                
                $relationships[$name] = [
                    'type' => 'object',
                    'properties' => [
                        'data' => [
                            'type' => $isToMany ? 'array' : 'object',
                            'description' => $isToMany 
                                ? "Array of related {$name} resources" 
                                : "Related {$name} resource",
                        ],
                        'links' => [
                            'type' => 'object',
                            'properties' => [
                                'self' => [
                                    'type' => 'string',
                                    'format' => 'uri',
                                ],
                                'related' => [
                                    'type' => 'string',
                                    'format' => 'uri',
                                ],
                            ],
                        ],
                    ],
                ];
                
                if ($isToMany) {
                    $relationships[$name]['properties']['data']['items'] = [
                        'type' => 'object',
                        'properties' => [
                            'id' => ['type' => 'string'],
                            'type' => ['type' => 'string'],
                        ],
                    ];
                } else {
                    $relationships[$name]['properties']['data']['properties'] = [
                        'id' => ['type' => 'string'],
                        'type' => ['type' => 'string'],
                    ];
                }
            }
        }
        
        return $relationships;
    }

    /**
     * Map Doctrine field types to OpenAPI types
     *
     * @param string $doctrineType
     * @return array
     */
    protected function mapDoctrineTypeToOpenApiType(string $doctrineType): array
    {
        $typeMap = [
            'string' => ['type' => 'string'],
            'text' => ['type' => 'string'],
            'guid' => ['type' => 'string', 'format' => 'uuid'],
            'integer' => ['type' => 'integer'],
            'smallint' => ['type' => 'integer'],
            'bigint' => ['type' => 'integer', 'format' => 'int64'],
            'decimal' => ['type' => 'number', 'format' => 'double'],
            'float' => ['type' => 'number', 'format' => 'float'],
            'boolean' => ['type' => 'boolean'],
            'date' => ['type' => 'string', 'format' => 'date'],
            'datetime' => ['type' => 'string', 'format' => 'date-time'],
            'datetimetz' => ['type' => 'string', 'format' => 'date-time'],
            'time' => ['type' => 'string', 'format' => 'time'],
            'json' => ['type' => 'object'],
            'json_array' => ['type' => 'array'],
            'simple_array' => ['type' => 'array', 'items' => ['type' => 'string']],
            'array' => ['type' => 'array', 'items' => ['type' => 'string']],
            'binary' => ['type' => 'string', 'format' => 'binary'],
            'blob' => ['type' => 'string', 'format' => 'binary'],
        ];
        
        return $typeMap[$doctrineType] ?? ['type' => 'string', 'description' => "Mapped from {$doctrineType}"];
    }
}