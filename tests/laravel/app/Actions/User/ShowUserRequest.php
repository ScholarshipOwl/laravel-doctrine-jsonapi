<?php

namespace Tests\App\Actions\User;

use Sowl\JsonApi\Request\Resource\AbstractShowRequest;

class ShowUserRequest extends AbstractShowRequest
{
    use HasUsersRepositoryTrait;
}