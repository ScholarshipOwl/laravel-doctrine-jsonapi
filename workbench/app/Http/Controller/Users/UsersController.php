<?php

namespace App\Http\Controller\Users;

use App\Entities\User;
use App\Http\Controller\Users\Relationships\AttachRolesToUserRequest;
use App\Http\Controller\Users\Relationships\DetachRolesFromUserRequest;
use App\Http\Controller\Users\Relationships\UpdateUserRolesRequest;
use Illuminate\Routing\Controller;
use Sowl\JsonApi\Action\Relationships\ToMany\CreateRelationshipsAction;
use Sowl\JsonApi\Action\Relationships\ToMany\RemoveRelationshipsAction;
use Sowl\JsonApi\Action\Relationships\ToMany\UpdateRelationshipsAction;
use Sowl\JsonApi\Action\Resource\UpdateResourceAction;
use Sowl\JsonApi\Default\WithListTrait;
use Sowl\JsonApi\Response;
use Sowl\JsonApi\Scribe\Attributes\ResourceRequest;
use Sowl\JsonApi\Scribe\Attributes\ResourceRequestCreate;
use Sowl\JsonApi\Scribe\Attributes\ResourceResponse;
use Sowl\JsonApi\Scribe\Attributes\ResourceResponseRelationships;

class UsersController extends Controller
{
    use WithListTrait;

    #[ResourceRequestCreate]
    #[ResourceResponse(status: 201)]
    public function create(CreateUserRequest $request): Response
    {
        return CreateUserAction::makeDispatch($request);
    }

    #[ResourceRequest]
    #[ResourceResponse(status: 200)]
    public function update(UpdateUserRequest $request): Response
    {
        return UpdateResourceAction::makeDispatch($request);
    }

    #[ResourceRequest]
    #[ResourceResponseRelationships(status: 200)]
    public function createUserRoles(AttachRolesToUserRequest $request): Response
    {
        return CreateRelationshipsAction::makeDispatch(User::relationships()->get('roles'), $request);
    }

    #[ResourceRequest]
    #[ResourceResponseRelationships(status: 200)]
    public function updateUserRoles(UpdateUserRolesRequest $request): Response
    {
        return UpdateRelationshipsAction::makeDispatch(User::relationships()->get('roles'), $request);
    }

    #[ResourceRequest]
    #[ResourceResponseRelationships(status: 200)]
    public function removeUserRoles(DetachRolesFromUserRequest $request): Response
    {
        return RemoveRelationshipsAction::makeDispatch(User::relationships()->get('roles'), $request);
    }
}
