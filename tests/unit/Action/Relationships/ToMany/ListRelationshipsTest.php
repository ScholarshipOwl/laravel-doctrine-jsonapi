<?php

namespace Tests\Action\Relationships\ToMany;

use Illuminate\Support\Facades\Route;
use Sowl\JsonApi\Action\Relationships\ToMany\ListRelationships;
use Sowl\JsonApi\Default;
use Sowl\JsonApi\Request;
use Sowl\JsonApi\Response;
use Tests\App\Actions\User\Relationships\ListUserRelationshipsRequest;
use Tests\App\Entities\Role;
use Tests\App\Transformers\RoleTransformer;
use Tests\TestCase;

class ListRelationshipsTest extends TestCase
{
    public function testAuthorizationPermissionsForNoLoggedIn()
    {
        $this->get('/users/1/relationships/roles')->assertStatus(Response::HTTP_FORBIDDEN);
        $this->get('/users/2/relationships/roles')->assertStatus(Response::HTTP_FORBIDDEN);
        $this->get('/users/3/relationships/roles')->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testAuthorizationPermissionsForUserRole()
    {
        $this->actingAsUser();

        $this->get('/users/1/relationships/roles')->assertStatus(Response::HTTP_OK);
        $this->get('/users/2/relationships/roles')->assertStatus(Response::HTTP_FORBIDDEN);
        $this->get('/users/3/relationships/roles')->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testAuthorizationPermissionsForRootRole()
    {
        $this->actingAsRoot();

        $this->get('/users/1/relationships/roles')->assertStatus(Response::HTTP_OK);
        $this->get('/users/2/relationships/roles')->assertStatus(Response::HTTP_OK);
        $this->get('/users/3/relationships/roles')->assertStatus(Response::HTTP_OK);
    }

    public function testListRelatedUserRolesResponse()
    {
        $user = $this->actingAsUser();

        $this->get('/users/'.$user->getId().'/relationships/roles')
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    [
                        'id' => '2',
                        'type' => 'roles',
                        'links' => [
                            'self' => '/roles/2'
                        ]
                    ],
                ]
            ]);

        $user->addRoles(Role::root());
        $this->em()->flush();

        $this->get('/users/'.$user->getId().'/relationships/roles')
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    [
                        'id' => '1',
                        'type' => 'roles',
                        'links' => [
                            'self' => '/roles/1'
                        ]
                    ],
                    [
                        'id' => '2',
                        'type' => 'roles',
                        'links' => [
                            'self' => '/roles/2'
                        ]
                    ],
                ]
            ]);
    }

    public function testListRelatedUserRolesPaginationAndSorting()
    {
        $user = $this->actingAsUser();
        $user->addRoles(Role::root());
        $user->addRoles(Role::moderator());

        $this->em()->flush();


        $this->get('/users/'.$user->getId().'/relationships/roles?sort=-id')
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    ['id' => '3'],
                    ['id' => '2'],
                    ['id' => '1'],
                ]
            ]);

        $this->get('/users/'.$user->getId().'/relationships/roles?page[number]=2&page[size]=1')
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    ['id' => '2'],
                ]
            ]);

        $this->get('/users/'.$user->getId().'/relationships/roles?page[offset]=2&page[limit]=1')
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    ['id' => '3'],
                ]
            ]);
    }
}
