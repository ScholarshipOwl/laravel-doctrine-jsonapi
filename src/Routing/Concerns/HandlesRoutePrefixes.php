<?php

namespace Sowl\JsonApi\Routing\Concerns;

use Illuminate\Routing\Route;
use Illuminate\Support\Str;

/**
 * Trait for handling route prefixes in JSON:API routes
 */
trait HandlesRoutePrefixes
{
    /**
     * Remove any prefix from the URI.
     *
     * @param Route $route The route to get the URI from
     * @return string The URI without the prefix
     */
    protected function pathWithoutPrefix(Route $route): string
    {
        $uri = $route->uri();

        // Then check for global JSON:API prefix from config
        $configPrefix = config('jsonapi.routing.rootPathPrefix', '');
        if ($configPrefix) {
            $uri = $this->removePrefix($uri, $configPrefix);
        }

        return $uri;
    }

    /**
     * Remove a specific prefix from a URI string
     *
     * @param string $uri The URI to process
     * @param string $prefix The prefix to remove
     * @return string The URI without the prefix
     */
    private function removePrefix(string $uri, string $prefix): string
    {
        // Remove the prefix and trailing slash if present
        return Str::startsWith($uri, $prefix . '/')
            ? substr($uri, strlen($prefix) + 1)
            : $uri;
    }
}
