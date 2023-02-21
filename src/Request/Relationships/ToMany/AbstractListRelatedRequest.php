<?php

namespace Sowl\JsonApi\Request\Relationships\ToMany;

use Sowl\JsonApi\AbstractRequest;
use Sowl\JsonApi\AuthenticationAbilitiesInterface;
use Sowl\JsonApi\Request\AuthorizeRelationshipsTrait;

abstract class AbstractListRelatedRequest extends AbstractRequest
{
    use AuthorizeRelationshipsTrait;

    public function authAbility(): string
    {
        return AuthenticationAbilitiesInterface::LIST_RELATIONSHIPS;
    }
}