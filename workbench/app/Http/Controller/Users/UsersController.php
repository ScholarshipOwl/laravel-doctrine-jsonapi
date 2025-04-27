<?php

namespace App\Http\Controller\Users;

use Sowl\JsonApi\Action\Relationships\ToMany\CreateRelationshipsAction;
use Sowl\JsonApi\Action\Relationships\ToMany\RemoveRelationshipsAction;
use Sowl\JsonApi\Action\Relationships\ToMany\UpdateRelationshipsAction;
use Sowl\JsonApi\Action\Resource\UpdateResourceAction;
use Sowl\JsonApi\Controller;
use Sowl\JsonApi\Default\WithListTrait;
use Sowl\JsonApi\Response;
use Sowl\JsonApi\Scribe\Attributes\ResourceResponse;
use App\Entities\User;
use App\Http\Controller\Users\Relationships\AttachRolesToUserRequest;
use App\Http\Controller\Users\Relationships\DetachRolesFromUserRequest;
use App\Http\Controller\Users\Relationships\UpdateUserRolesRequest;

class UsersController extends Controller
{
    use WithListTrait;

    #[ResourceResponse(status: 201)]
    public function create(CreateUserRequest $request): Response
    {
        return CreateUserAction::create()
            ->dispatch($request);
    }

    #[ResourceResponse(status: 200)]
    public function update(UpdateUserRequest $request): Response
    {
        return UpdateResourceAction::create()
            ->dispatch($request);
    }

    public function createUserRoles(AttachRolesToUserRequest $request): Response
    {
        return (new CreateRelationshipsAction(User::relationships()->toMany()->get('roles')))
            ->dispatch($request);
    }

    public function updateUserRoles(UpdateUserRolesRequest $request): Response
    {
        return (new UpdateRelationshipsAction(User::relationships()->toMany()->get('roles')))
            ->dispatch($request);
    }

    public function removeUserRoles(DetachRolesFromUserRequest $request): Response
    {
        return (new RemoveRelationshipsAction(User::relationships()->toMany()->get('roles')))
            ->dispatch($request);
    }

    protected function noAuthMethods(): array
    {
        return [
            'create',
        ];
    }
}
