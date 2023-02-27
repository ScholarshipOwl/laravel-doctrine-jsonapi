<?php

namespace Tests\App\Actions\User\Relationships;

use Sowl\JsonApi\Request\Relationships\ToMany\UpdateRelationshipsRequest;
use Sowl\JsonApi\Rules\ObjectIdentifierRule;
use Tests\App\Actions\User\Rules\UserRoleAssignRule;

class UpdateUserRolesRequest extends UpdateRelationshipsRequest
{
    protected function dataValidationRule(): ObjectIdentifierRule
    {
        return new UserRoleAssignRule($this);
    }
}
