<?php namespace Sowl\JsonApi;

interface ResourceInterface
{
    /**
     * Get fractal resource key.
     * JSON API `type`
     */
    public static function getResourceKey(): string;

    public static function transformer(): AbstractTransformer;

    /**
     * JSON API `id`
     */
    public function getId(): null|string|int;
}
