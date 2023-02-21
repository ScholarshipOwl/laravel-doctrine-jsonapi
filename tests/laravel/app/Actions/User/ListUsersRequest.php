<?php

namespace Tests\App\Actions\User;

use Sowl\JsonApi\Request\Resource\AbstractListRequest;

class ListUsersRequest extends AbstractListRequest
{
    use HasUsersRepositoryTrait;
}