<?php

namespace Sowl\JsonApi\Controller;

use Illuminate\Support\Arr;
use Sowl\JsonApi\Middleware\Authorize;

trait AuthorizesRequestsTrait
{
    abstract public function middleware($middleware, array $options = []);

    /**
     * Returns map of the method to ability.
     * @return array<string, string|array<string>>
     */
    abstract protected function methodToAbilityMap(): array;

    public function authorizeResource(): void
    {
        $middlewares = [];

        foreach ($this->methodToAbilityMap() as $method => $arguments) {
            if (empty($arguments)) {
                continue;
            }

            $middlewares[Authorize::class.':'.implode(',', Arr::wrap($arguments))][] = $method;
        }

        foreach ($middlewares as $middlewareName => $methods) {
            $this->middleware($middlewareName)->only($methods);
        }
    }
}
