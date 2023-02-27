<?php

namespace Sowl\JsonApi\Controller;

use Sowl\JsonApi\AuthenticationAbilitiesInterface;

trait AuthorizesRequestsTrait
{

    abstract public function middleware($middleware, array $options = []);

    /**
     * Returns map of the method to ability.
     * @return array<string, string>
     */
    abstract protected function methodToAbilityMap(): array;

    public function authorizeResource(): void
    {
        $middleware = [];

        foreach ($this->methodToAbilityMap() as $method => $ability) {
            if (empty($ability)) {
                continue;
            }

            $middleware["can:${ability}"][] = $method;
        }

        foreach ($middleware as $middlewareName => $methods) {
            $this->middleware($middlewareName)->only($methods);
        }
    }

}
