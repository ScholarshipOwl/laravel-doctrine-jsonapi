<?php

namespace Tests\App\Actions\User\Relationships;

use Sowl\JsonApi\Request\Relationships\ToMany\RemoveRelationshipsRequest;
use Sowl\JsonApi\Rules\ObjectIdentifierRule;
use Tests\App\Actions\User\Rules\UserRoleRemoveRule;

class RemoveUserRolesRequest extends RemoveRelationshipsRequest
{
    protected function dataValidationRule(): ObjectIdentifierRule
    {
        return new UserRoleRemoveRule($this);
    }
}
