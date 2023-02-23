<?php

namespace Sowl\JsonApi\Fractal;

use League\Fractal\Scope;
use League\Fractal\Manager;
use League\Fractal\ScopeFactoryInterface;
use League\Fractal\Resource\ResourceInterface;

class ScopeFactory implements ScopeFactoryInterface
{
    public function createScopeFor(
        Manager $manager,
        ResourceInterface $resource,
        ?string $scopeIdentifier = null
    ): Scope {
        return new \Sowl\JsonApi\Fractal\Scope($manager, $resource, $scopeIdentifier);
    }

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