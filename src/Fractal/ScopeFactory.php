<?php

namespace Sowl\JsonApi\Fractal;

use League\Fractal\Manager;
use League\Fractal\Resource\ResourceInterface;
use Sowl\JsonApi\AbstractRequest;

class ScopeFactory extends \League\Fractal\ScopeFactory
{
    public function __construct(protected AbstractRequest $request) {}

    public function request(): AbstractRequest
    {
        return $this->request;
    }

    public function createScopeFor(Manager $manager, ResourceInterface $resource, $scopeIdentifier = null): Scope
    {
        return new Scope($this->request, $manager, $resource, $scopeIdentifier);
    }
}
