<?php

namespace Sowl\JsonApi\Controller;

use Illuminate\Support\Arr;

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
        $middleware = [];

        foreach ($this->methodToAbilityMap() as $method => $arguments) {
            if (empty($arguments)) {
                continue;
            }

            $middleware[sprintf('can:%s', implode(',', Arr::wrap($arguments)))][] = $method;
        }

        foreach ($middleware as $middlewareName => $methods) {
            $this->middleware($middlewareName)->only($methods);
        }
    }

}
