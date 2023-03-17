<?php

namespace Sowl\JsonApi\Fractal;

use League\Fractal\Manager;
use League\Fractal\Resource\ResourceInterface;
use League\Fractal\Scope;
use League\Fractal\ScopeFactoryInterface;

/**
 * Class implements the League\Fractal\ScopeFactoryInterface and is responsible for creating instances of the custom
 * Sowl\JsonApi\Fractal\Scope class.
 *
 * By implementing the ScopeFactoryInterface, the ScopeFactory class can be used with the Fractal library to create
 * custom Scope instances, allowing you to leverage the additional functionality provided by the
 * Sowl\JsonApi\Fractal\Scope class when working with resources and transformations.
 */
class ScopeFactory implements ScopeFactoryInterface
{
    /**
     * This method creates a new Sowl\JsonApi\Fractal\Scope instance.
     */
    public function createScopeFor(
        Manager $manager,
        ResourceInterface $resource,
        ?string $scopeIdentifier = null
    ): Scope {
        return new \Sowl\JsonApi\Fractal\Scope($manager, $resource, $scopeIdentifier);
    }

    /**
     * This method creates a child scope for a given parent scope.
     */
    public function createChildScopeFor(
        Manager $manager,
        Scope $parentScope,
        ResourceInterface $resource,
        ?string $scopeIdentifier = null
    ): Scope {
        $scopeInstance = $this->createScopeFor($manager, $resource, $scopeIdentifier);

        $scopeArray = $parentScope->getParentScopes();
        $scopeArray[] = $parentScope->getScopeIdentifier();

        $scopeInstance->setParentScopes($scopeArray);

        return $scopeInstance;
    }
}