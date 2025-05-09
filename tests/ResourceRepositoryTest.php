<?php

namespace Tests;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Mockery as m;
use Mockery\MockInterface;
use Sowl\JsonApi\ResourceInterface;
use Sowl\JsonApi\ResourceRepository;

class ResourceRepositoryTest extends TestCase
{
    public function testFindByIdentifierInvalidEntity(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage(sprintf('stdClass - not implements %s', ResourceInterface::class));

        $id = 777;

        /** @var EntityManager|MockInterface $emMock */
        $emMock = m::mock(EntityManager::class);
        $emMock->shouldReceive('find')
            ->withArgs([\stdClass::class, $id, null, null])
            ->andReturn(new \stdClass());

        /** @var ClassMetadata|MockInterface $classMetadata */
        $classMetadata = m::mock(ClassMetadata::class);
        $classMetadata->name = \stdClass::class;

        $repository = new ResourceRepository($emMock, $classMetadata);
        $this->assertEquals('', $repository->getResourceType());
        $repository->findById($id);
    }
}
