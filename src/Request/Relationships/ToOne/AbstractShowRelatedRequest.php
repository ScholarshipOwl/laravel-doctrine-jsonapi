<?php

namespace Sowl\JsonApi\Request\Relationships\ToOne;

use Sowl\JsonApi\AbstractRequest;
use Sowl\JsonApi\AuthenticationAbilitiesInterface;
use Sowl\JsonApi\Request\AuthorizeRelationshipsTrait;

abstract class AbstractShowRelatedRequest extends AbstractRequest
{
    use AuthorizeRelationshipsTrait;

    public function authAbility(): string
    {
        return AuthenticationAbilitiesInterface::SHOW_RELATIONSHIPS;
    }
}