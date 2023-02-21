<?php

namespace Sowl\JsonApi\Fractal;

use League\Fractal\Manager;
use League\Fractal\Resource\ResourceInterface;
use Sowl\JsonApi\AbstractRequest;

class Scope extends \League\Fractal\Scope
{
    public function __construct(
        protected AbstractRequest $request,
        Manager                   $manager,
        ResourceInterface         $resource,
                                  $scopeIdentifier = null
    ) {
        parent::__construct($manager, $resource, $scopeIdentifier);
    }

    public function request(): AbstractRequest
    {
        return $this->request;
    }
}
