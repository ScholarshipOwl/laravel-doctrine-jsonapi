<?php

namespace Sowl\JsonApi\Request\Resource;

use Sowl\JsonApi\AbstractRequest;
use Sowl\JsonApi\AuthenticationAbilitiesInterface;
use Sowl\JsonApi\Request\AuthorizeTrait;
use Sowl\JsonApi\Request\WithFilterParamsTrait;
use Sowl\JsonApi\Request\WithPaginationParamsTrait;

abstract class AbstractListRequest extends AbstractRequest
{
    use AuthorizeTrait;
    use WithPaginationParamsTrait;
    use WithFilterParamsTrait;

    public function authAbility(): string
    {
        return AuthenticationAbilitiesInterface::LIST_RESOURCES;
    }

    public function authArguments(): array
    {
        return [$this->repository()->getClassName()];
    }
}