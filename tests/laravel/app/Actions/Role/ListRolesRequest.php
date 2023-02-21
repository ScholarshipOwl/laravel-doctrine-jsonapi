<?php

namespace Tests\App\Actions\Role;

use Sowl\JsonApi\Request\Resource\AbstractListRequest;
use Tests\App\Actions\Page\HasRolesRepositoryTrait;

class ListRolesRequest extends AbstractListRequest
{
    use HasRolesRepositoryTrait;
}