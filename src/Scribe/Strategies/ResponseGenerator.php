<?php

namespace Sowl\JsonApi\Scribe\Strategies;

use LaravelDoctrine\ORM\Testing\Factory;
use Sowl\JsonApi\ResourceInterface;

class ResponseGenerator
{
    private static ?ResponseGenerator $instance = null;

    private array $cache = [];

    private function __construct()
    {
    }

    public static function instance(): ResponseGenerator
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Generate a single resource instance for the given resource class
     *
     * @template T of object
     *
     * @param  class-string<T>  $resourceClass
     * @return T
     */
    public function createSingleResource(string $resourceClass, string $name = 'default'): ?ResourceInterface
    {
        if (isset($this->cache[$resourceClass][$name])) {
            return $this->cache[$resourceClass][$name];
        }

        $factory = app(Factory::class);

        $this->cache[$resourceClass][$name] = $factory->of($resourceClass, $name)->create();

        return $this->cache[$resourceClass][$name];
    }
}
    