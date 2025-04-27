<?php

declare(strict_types=1);

namespace Tests\Unit\Scribe;

use PHPUnit\Framework\TestCase;
use Sowl\JsonApi\Scribe\DeepObjectQueryHelper;

class DeepObjectQueryHelperTest extends TestCase
{
    public function test_no_deep_objects(): void
    {
        $query = ['foo' => 'bar'];
        $this->assertEquals(['foo' => 'bar'], DeepObjectQueryHelper::convert($query));
    }

    public function test_single_deep_object(): void
    {
        $query = ['fields[pageComments]' => 'content'];
        $expected = ['fields' => ['pageComments' => 'content']];
        $this->assertEquals($expected, DeepObjectQueryHelper::convert($query));
    }

    public function test_multiple_deep_objects(): void
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

    public function test_mixed_keys(): void
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

    public function test_malformed_key_ignored(): void
    {
        $query = ['fields[' => 'broken', 'foo' => 'bar'];
        $expected = ['fields[' => 'broken', 'foo' => 'bar'];
        $this->assertEquals($expected, DeepObjectQueryHelper::convert($query));
    }
}
