<?php

namespace Tests\FilterParsers;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Sowl\JsonApi\FilterParsers\BuilderChain\Chain;
use Sowl\JsonApi\FilterParsers\BuilderChain\MemberInterface;
use stdClass;

class ChainTest extends TestCase
{
    public function testChainProcess(): void
    {
        $object = new stdClass();

        $member1 = m::mock(MemberInterface::class)
            ->shouldReceive('__invoke')->with($object)->andReturn($object)
            ->getMock();
        $member2 = m::mock(MemberInterface::class)
            ->shouldReceive('__invoke')->with($object)->andReturn($object)
            ->getMock();
        $member3 = function ($qb) {
            return $qb;
        };

        $chain = new Chain([$member1, $member2, $member3]);

        $this->assertEquals($object, $chain->process($object));
    }
}
