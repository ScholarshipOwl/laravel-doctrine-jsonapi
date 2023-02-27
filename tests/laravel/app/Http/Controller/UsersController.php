<?php

namespace Tests\App\Http\Controller;

use Sowl\JsonApi\Action\Relationships\ToMany\CreateRelationships;
use Sowl\JsonApi\Action\Relationships\ToMany\RemoveRelationships;
use Sowl\JsonApi\Action\Relationships\ToMany\UpdateRelationships;
use Sowl\JsonApi\Action\Resource\UpdateResource;
use Sowl\JsonApi\AuthenticationAbilitiesInterface;
use Sowl\JsonApi\Controller;
use Sowl\JsonApi\Request;
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
        return UpdateResource::create()
            ->dispatch($request);
    }

    public function createUserRoles(CreateUserRolesRequest $request): Response
    {
        return (new CreateRelationships(User::relationships()->toMany()->get('roles')))
            ->dispatch($request);
    }

    public function updateUserRoles(UpdateUserRolesRequest $request): Response
    {
        return (new UpdateRelationships(User::relationships()->toMany()->get('roles')))
            ->dispatch($request);
    }

    public function removeUserRoles(RemoveUserRolesRequest $request): Response
    {
        return (new RemoveRelationships(User::relationships()->toMany()->get('roles')))
            ->dispatch($request);
    }

    protected function methodToAbilityMap(): array
    {
        $custom = [
            'create' => null,
            'createUserRoles' => AuthenticationAbilitiesInterface::CREATE_RELATIONSHIPS,
            'updateUserRoles' => AuthenticationAbilitiesInterface::UPDATE_RELATIONSHIPS,
            'removeUserRoles' => AuthenticationAbilitiesInterface::REMOVE_RELATIONSHIPS,
        ];

        return $custom + parent::methodToAbilityMap();
    }
}
