<?php

namespace Sowl\JsonApi\Request\Relationships\ToMany;

use Sowl\JsonApi\AbstractRequest;
use Sowl\JsonApi\AuthenticationAbilitiesInterface;
use Sowl\JsonApi\Request\AuthorizeRelationshipsTrait;

abstract class AbstractCreateRelationshipsRequest extends AbstractRequest
{
    use AuthorizeRelationshipsTrait;

    public function authAbility(): string
    {
        return AuthenticationAbilitiesInterface::CREATE_RELATIONSHIPS;
    }
}