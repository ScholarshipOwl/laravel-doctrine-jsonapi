<?php

declare(strict_types=1);

namespace Tests\Unit\Scribe;

use PHPUnit\Framework\TestCase;
use Sowl\JsonApi\Scribe\DeepObjectQueryHelper;

class DeepObjectQueryHelperTest extends TestCase
{
    public function testNoDeepObjects(): void
    {
        $query = ['foo' => 'bar'];
        $this->assertEquals(['foo' => 'bar'], DeepObjectQueryHelper::convert($query));
    }

    public function testSingleDeepObject(): void
    {
        $query = ['fields[pageComments]' => 'content'];
        $expected = ['fields' => ['pageComments' => 'content']];
        $this->assertEquals($expected, DeepObjectQueryHelper::convert($query));
    }

    public function testMultipleDeepObjects(): void
    {
        $query = [
            'fields[pageComments]' => 'content',
            'fields[pages]' => 'title',
            'foo' => 'bar',
        ];
        $expected = [
            'fields' => [
                'pageComments' => 'content',
                'pages' => 'title',
            ],
            'foo' => 'bar',
        ];
        $this->assertEquals($expected, DeepObjectQueryHelper::convert($query));
    }

    public function testMixedKeys(): void
    {
        $query = [
            'fields[pageComments]' => 'content',
            'fields[pages]' => 'title',
            'baz' => 'qux',
            'simple' => 'value',
        ];
        $expected = [
            'fields' => [
                'pageComments' => 'content',
                'pages' => 'title',
            ],
            'baz' => 'qux',
            'simple' => 'value',
        ];
        $this->assertEquals($expected, DeepObjectQueryHelper::convert($query));
    }

    public function testMalformedKeyIgnored(): void
    {
        $query = ['fields[' => 'broken', 'foo' => 'bar'];
        $expected = ['fields[' => 'broken', 'foo' => 'bar'];
        $this->assertEquals($expected, DeepObjectQueryHelper::convert($query));
    }
}
