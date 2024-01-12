<?php

namespace Tests\App\Http\Controller\Users;

use Sowl\JsonApi\Controller;
use Sowl\JsonApi\Action\Relationships\ToMany\CreateRelationshipsAction;
use Sowl\JsonApi\Action\Relationships\ToMany\RemoveRelationshipsAction;
use Sowl\JsonApi\Action\Relationships\ToMany\UpdateRelationshipsAction;
use Sowl\JsonApi\Action\Resource\UpdateResourceAction;
use Sowl\JsonApi\Default\WithListTrait;
use Sowl\JsonApi\Response;
use Tests\App\Http\Controller\Users\CreateUserAction;
use Tests\App\Http\Controller\Users\CreateUserRequest;
use Tests\App\Http\Controller\Users\Relationships\AttachRolesToUserRequest;
use Tests\App\Http\Controller\Users\Relationships\DetachRolesFromUserRequest;
use Tests\App\Http\Controller\Users\Relationships\UpdateUserRolesRequest;
use Tests\App\Http\Controller\Users\UpdateUserRequest;
use Tests\App\Entities\User;

class UsersController extends Controller
{
    use WithListTrait;

    public function create(CreateUserRequest $request): Response
    {
        return CreateUserAction::create()
            ->dispatch($request);
    }

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
