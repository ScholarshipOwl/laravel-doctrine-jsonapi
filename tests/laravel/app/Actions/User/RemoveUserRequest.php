<?php

namespace Tests\App\Actions\User;

use Sowl\JsonApi\Request\Resource\AbstractRemoveRequest;

class RemoveUserRequest extends AbstractRemoveRequest
{
    use HasUsersRepositoryTrait;
}