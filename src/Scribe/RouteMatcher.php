<?php

namespace Sowl\JsonApi\Scribe;

use Sowl\JsonApi\Relationships\RelationshipInterface;
use Sowl\JsonApi\ResourceManager;
use Illuminate\Routing\Route;
use Knuckles\Scribe\Matching\MatchedRoute;
use Knuckles\Scribe\Matching\RouteMatcher as ScribeRouteMatcher;
use Sowl\JsonApi\Routing\ResourceTypeExtractor;

class RouteMatcher extends ScribeRouteMatcher
{
    protected ResourceTypeExtractor $resourceTypeExtractor;
    public function __construct(protected ResourceManager $rm)
    {
        $this->resourceTypeExtractor = new ResourceTypeExtractor();
    }

    public function getRoutes(array $routeRules = [], string $router = 'laravel'): array
    {
        $routes = parent::getRoutes($routeRules, $router);

        // Extend a resource type with resources
        foreach ($routes as $index => $route) {
            if ($this->isDynamicRoute($route)) {
                $dynamicToResourceTypeRoutes = $this->extendDynamicRoute($route);
                $dynamicToResourceTypeRoutes = $this->excludeExistingRoutes($routes, $dynamicToResourceTypeRoutes);

                // Exclude current route from the list
                $routes = array_filter($routes, fn ($r) => $r !== $route);

                // Merge with new routes
                $routes = array_merge($routes, $dynamicToResourceTypeRoutes);
            }
        }

        return $routes;
    }

    /**
     * @return MatchedRoute[]
     */
    protected function extendDynamicRoute(MatchedRoute $route): array
    {
        $uri = $route->getRoute()->uri();
        $routes = [];

        if (\Str::contains($uri, '{resourceType}')) {
            foreach ($this->rm->getResources() as $resourceType => $resourceClass) {
                $resourceRoute = new MatchedRoute(
                    new Route(
                        $route->getRoute()->methods(),
                        \Str::replace('{resourceType}', $resourceType, $uri),
                        $route->getRoute()->getAction()
                    ),
                    $route->getRules(),
                );

                if (\Str::contains($uri, '{relationship}')) {
                    $relationshipsRoutes = $this->buildRelationshipsRoutes($resourceType, $resourceRoute);

                    // It's empty only if we have no relationships.
                    // In this case we should remove the resource route.
                    if (empty($relationshipsRoutes)) {
                        continue;
                    }
                }


                // In case we build relationship routes we should use this list, otherwise singe resource route.
                $routes = array_merge(
                    $routes,
                    empty($relationshipsRoutes) ? [$resourceRoute] : $relationshipsRoutes
                );
            }
        } elseif (\Str::contains($uri, '{relationship}')) {
            $routes = $this->buildRelationshipsRoutes(
                $this->resourceTypeExtractor->extract($route->getRoute()),
                $route
            );
        }

        return $routes;
    }

    /**
     * @return MatchedRoute[]
     */
    protected function buildRelationshipsRoutes(string $resourceType, MatchedRoute $route): array
    {
        $uri = $route->getRoute()->uri();
        $method = $route->getRoute()->methods()[0];

        // Process relationship endpoints (e.g., /pages/{id}/relationships/user)
        $relationshipsRoutes = $this->rm->relationshipsByResourceType($resourceType)
            ->map(function ($relationship) use ($route, $uri, $method) {
                if ($this->isRelationshipsRoute($route)) {
                    if (!$this->isAllowedRelationshipsMethod($relationship, $method)) {
                        return null;
                    }
                }

                return new MatchedRoute(
                    new Route(
                        $route->getRoute()->methods(),
                        \Str::replace('{relationship}', $relationship->name(), $uri),
                        $route->getRoute()->getAction()
                    ),
                    $route->getRules(),
                );
            })
            ->filter()
            ->toArray();

        return array_values($relationshipsRoutes);
    }

    /**
     * We exclude existing routes to avoid duplicates.
     *
     * @param MatchedRoute[] $routes
     * @param MatchedRoute[] $dynamicToResourceTypeRoutes
     * @return array
     */
    protected function excludeExistingRoutes(array $routes, array $dynamicToResourceTypeRoutes): array
    {
        $filtered = array_filter($dynamicToResourceTypeRoutes, function (MatchedRoute $route) use ($routes) {
            foreach ($routes as $existingRoute) {
                $existingUri = $existingRoute->getRoute()->uri();
                $existingMethods = $existingRoute->getRoute()->methods();

                if (
                    $route->getRoute()->uri() === $existingUri &&
                    empty(array_diff($route->getRoute()->methods(), $existingMethods))
                ) {
                    return false;
                }
            }

            return true;
        });

        return $filtered;
    }

    protected function isAllowedRelationshipsMethod(RelationshipInterface $relationship, string $method): bool
    {
        /**
         * @see https://jsonapi.org/format/#crud-updating-to-one-relationships
         * @see https://jsonapi.org/format/#fetching-relationships
         */
        $toOneAllowedMethods = ['GET', 'PATCH'];

        /**
         * @see https://jsonapi.org/format/#crud-updating-to-many-relationships
         * @see https://jsonapi.org/format/#fetching-relationships
         */
        $toManyAllowedMethods = ['GET', 'POST', 'PATCH', 'DELETE'];

        return $relationship->isToOne()
            ? \in_array(strtoupper($method), $toOneAllowedMethods)
            : \in_array(strtoupper($method), $toManyAllowedMethods);
    }

    protected function isRelationshipsRoute(MatchedRoute $route): bool
    {
        return \Str::contains($route->getRoute()->uri(), '/relationships/');
    }

    protected function isDynamicRoute(MatchedRoute $route): bool
    {
        return \Str::contains($route->getRoute()->uri(), '{resourceType}') ||
            \Str::contains($route->getRoute()->uri(), '{relationship}');
    }
}
