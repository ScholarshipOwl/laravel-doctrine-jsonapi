<?php

namespace Sowl\JsonApi\Request\Resource;

use Sowl\JsonApi\AbstractRequest;
use Sowl\JsonApi\AuthenticationAbilitiesInterface;
use Sowl\JsonApi\Request\AuthorizeTrait;

abstract class AbstractUpdateRequest extends AbstractRequest
{
    use AuthorizeTrait;

    public function authAbility(): string
    {
        return AuthenticationAbilitiesInterface::UPDATE_RESOURCE;
    }
}