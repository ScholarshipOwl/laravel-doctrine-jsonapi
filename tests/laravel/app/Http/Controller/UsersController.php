<?php

namespace Tests\App\Http\Controller;

use Sowl\JsonApi\Controller;
use Sowl\JsonApi\Action\Relationships\ToMany\CreateRelationshipsAction;
use Sowl\JsonApi\Action\Relationships\ToMany\RemoveRelationshipsAction;
use Sowl\JsonApi\Action\Relationships\ToMany\UpdateRelationshipsAction;
use Sowl\JsonApi\Action\Resource\UpdateResourceAction;
use Sowl\JsonApi\Default\WithListTrait;
use Sowl\JsonApi\Response;
use Tests\App\Actions\User\CreateUserAction;
use Tests\App\Actions\User\CreateUserRequest;
use Tests\App\Actions\User\Relationships\CreateUserRolesRequest;
use Tests\App\Actions\User\Relationships\RemoveUserRolesRequest;
use Tests\App\Actions\User\Relationships\UpdateUserRolesRequest;
use Tests\App\Actions\User\UpdateUserRequest;
use Tests\App\Entities\User;

class UsersController extends Controller
{
    use WithListTrait;

    public function searchProperty(): ?string
    {
        return 'email';
    }

    public function filterable(): array
    {
        return ['id', 'email', 'name'];
    }

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

    public function createUserRoles(CreateUserRolesRequest $request): Response
    {
        return (new CreateRelationshipsAction(User::relationships()->toMany()->get('roles')))
            ->dispatch($request);
    }

    public function updateUserRoles(UpdateUserRolesRequest $request): Response
    {
        return (new UpdateRelationshipsAction(User::relationships()->toMany()->get('roles')))
            ->dispatch($request);
    }

    public function removeUserRoles(RemoveUserRolesRequest $request): Response
    {
        return (new RemoveRelationshipsAction(User::relationships()->toMany()->get('roles')))
            ->dispatch($request);
    }

    protected function methodToAbilityMap(): array
    {
        return array_merge(parent::methodToAbilityMap(), [
            'create' => null
        ]);
    }
}
