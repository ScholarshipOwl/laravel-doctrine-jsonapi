<?php

namespace Tests\App\Actions\User\Relationships;

use Sowl\JsonApi\Request\Relationships\ToMany\CreateRelationshipsRequest;
use Sowl\JsonApi\Rules\ObjectIdentifierRule;
use Tests\App\Actions\User\Rules\UserRoleAssignRule;

class CreateUserRolesRequest extends CreateRelationshipsRequest
{
    protected function dataValidationRule(): ObjectIdentifierRule
    {
        return new UserRoleAssignRule($this);
    }
}
