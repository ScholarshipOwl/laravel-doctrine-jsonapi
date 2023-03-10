<?php

namespace Tests\Action\Relationships\ToMany;

use Sowl\JsonApi\Response;
use Tests\App\Entities\Role;
use Tests\TestCase;

class ListRelatedResourcesTest extends TestCase
{
    public function testAuthorizationPermissionsForNoLogedIn()
    {
        $this->get('/users/1/roles')->assertStatus(Response::HTTP_FORBIDDEN);
        $this->get('/users/2/roles')->assertStatus(Response::HTTP_FORBIDDEN);
        $this->get('/users/3/roles')->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testAuthorizationPermissionsForUserRole()
    {
        $this->actingAsUser();

        $this->get('/users/1/roles')->assertStatus(Response::HTTP_OK);
        $this->get('/users/2/roles')->assertStatus(Response::HTTP_FORBIDDEN);
        $this->get('/users/3/roles')->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testAuthorizationPermissionsForRootRole()
    {
        $this->actingAsRoot();

        $this->get('/users/1/roles')->assertStatus(Response::HTTP_OK);
        $this->get('/users/2/roles')->assertStatus(Response::HTTP_OK);
        $this->get('/users/3/roles')->assertStatus(Response::HTTP_OK);
    }

    public function testListRelatedUserRolesResponse()
    {
        $user = $this->actingAsUser();
        $roles = $user->getRoles()->toArray();

        $this->get('/users/'.$user->getId().'/roles')
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    [
                        'id' => '2',
                        'type' => 'roles',
                        'attributes' => [
                            'name' => 'User',
                        ],
                        'links' => [
                            'self' => '/roles/2'
                        ]
                    ],
                ]
            ]);

        $user->addRoles(Role::root());
        $this->em()->flush();

        $this->get('/users/'.$user->getId().'/roles')
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    [
                        'id' => '1',
                        'type' => 'roles',
                        'attributes' => [
                            'name' => 'Root',
                        ],
                        'links' => [
                            'self' => '/roles/1'
                        ]
                    ],
                    [
                        'id' => '2',
                        'type' => 'roles',
                        'attributes' => [
                            'name' => 'User',
                        ],
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


        $this->get('/users/'.$user->getId().'/roles?sort=-id')
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    ['id' => '3'],
                    ['id' => '2'],
                    ['id' => '1'],
                ]
            ]);

        $this->get('/users/'.$user->getId().'/roles?page[number]=2&page[size]=1')
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    ['id' => '2'],
                ]
            ]);

        $this->get('/users/'.$user->getId().'/roles?page[offset]=2&page[limit]=1')
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    ['id' => '3'],
                ]
            ]);
    }
}
