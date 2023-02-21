<?php

namespace Sowl\JsonApi\Request\Resource;

use Sowl\JsonApi\AbstractRequest;
use Sowl\JsonApi\AuthenticationAbilitiesInterface;
use Sowl\JsonApi\Request\AuthorizeTrait;

abstract class AbstractRemoveRequest extends AbstractRequest
{
    use AuthorizeTrait;

    public function authAbility(): string
    {
        return AuthenticationAbilitiesInterface::REMOVE_RESOURCE;
    }
}