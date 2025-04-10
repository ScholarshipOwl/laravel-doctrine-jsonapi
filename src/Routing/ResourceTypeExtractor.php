<?php

namespace Sowl\JsonApi\Routing;

use Illuminate\Routing\Route;
use Illuminate\Support\Str;

/**
 * Extracts resource type from route.
 *
 * This class provides a way to extract the resource type from a given route.
 * It checks the route parameters and URI pattern to determine the resource type.
 */
class ResourceTypeExtractor
{
    /**
     * Extract resource type from route.
     *
     * @param Route $route The route to extract from.
     * @return string|null The extracted resource type or null if not found.
     */
    public function extract(Route $route): ?string
    {
        // Try to get from route parameter first
        try {
            $resourceType = $route->parameter('resourceType');
            if ($resourceType !== null) {
                return $resourceType;
            }
        } catch (\LogicException $e) {
            // Route is not bound, continue with pattern matching
        }

        // Try to extract from route URI pattern
        $uri = $route->uri();

        // Handle empty URI
        if (empty($uri)) {
            return null;
        }

        // Handle special case for {resourceType} parameter
        if (preg_match('/^\{resourceType\}/', $uri)) {
            return null;
        }

        // Handle dot notation (e.g., api.v1.users)
        if (!Str::contains($uri, '/') && Str::contains($uri, '.')) {
            return $uri;
        }

        // Get the path without prefix
        $pathWithoutPrefix = $this->pathWithoutPrefix($route);

        // Extract resource type using regex pattern
        $matches = [];
        if (preg_match('/^([^\/.]*)\/?.*$/', $pathWithoutPrefix, $matches)) {
            return $matches[1] ?: null;
        }

        return null;
    }

    /**
     * Remove any prefix from the URI.
     *
     * @param Route $route The route to get the URI from
     * @return string The URI without the prefix
     */
    private function pathWithoutPrefix(Route $route): string
    {
        $uri = $route->uri();
        $prefix = config('jsonapi.routing.rootPathPrefix', '');

        if ($prefix) {
            // Remove the prefix and trailing slash if present
            return Str::startsWith($uri, $prefix . '/')
                ? substr($uri, strlen($prefix) + 1)
                : $uri;
        }

        return $uri;
    }
}
