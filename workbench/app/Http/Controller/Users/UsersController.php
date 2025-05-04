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
use Sowl\JsonApi\Scribe\Attributes\ResourceResponse;

class UsersController extends Controller
{
    use WithListTrait;

    #[ResourceResponse(status: 201)]
    public function create(CreateUserAction $action): Response
    {
        return $action->dispatch();
    }

    #[ResourceResponse(status: 200)]
    public function update(UpdateUserRequest $request): Response
    {
        return (new UpdateResourceAction($request))->dispatch();
    }

    public function createUserRoles(AttachRolesToUserRequest $request): Response
    {
        return (new CreateRelationshipsAction(User::relationships()->toMany()->get('roles'), $request))
            ->dispatch();
    }

    public function updateUserRoles(UpdateUserRolesRequest $request): Response
    {
        return (new UpdateRelationshipsAction(User::relationships()->toMany()->get('roles'), $request))
            ->dispatch();
    }

    public function removeUserRoles(DetachRolesFromUserRequest $request): Response
    {
        return (new RemoveRelationshipsAction(User::relationships()->toMany()->get('roles'), $request))
            ->dispatch();
    }
}
