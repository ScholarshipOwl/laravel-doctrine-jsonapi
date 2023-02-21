<?php

namespace Sowl\JsonApi\Request\Resource;

use Sowl\JsonApi\AbstractRequest;
use Sowl\JsonApi\AuthenticationAbilitiesInterface;
use Sowl\JsonApi\Request\AuthorizeTrait;

abstract class AbstractCreateRequest extends AbstractRequest
{
    use AuthorizeTrait;

    public function rules(): array
    {
        return parent::rules()
            + $this->dataRules();
    }

    public function authAbility(): string
    {
        return AuthenticationAbilitiesInterface::CREATE_RESOURCE;
    }

    public function authArguments(): array
    {
        return [$this->repository()->getClassName()];
    }
}