<?php

namespace Tests\Action\Relationships\ToMany;

use Sowl\JsonApi\Response;
use Tests\App\Entities\Page;
use Tests\App\Entities\Role;
use Tests\TestCase;

class ListRelatedResourcesTest extends TestCase
{
    public function testAuthorizationPermissionsForNoLogedIn()
    {
        $this->get('/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b/roles')->assertStatus(Response::HTTP_FORBIDDEN);
        $this->get('/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d/roles')->assertStatus(Response::HTTP_FORBIDDEN);
        $this->get('/users/ccf660b9-3cf7-4f58-a5f7-22e53ad836f8/roles')->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testAuthorizationPermissionsForUserRole()
    {
        $this->actingAsUser();

        $this->get('/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b/roles')->assertStatus(Response::HTTP_OK);
        $this->get('/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d/roles')->assertStatus(Response::HTTP_FORBIDDEN);
        $this->get('/users/ccf660b9-3cf7-4f58-a5f7-22e53ad836f8/roles')->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testAuthorizationPermissionsForRootRole()
    {
        $this->actingAsRoot();

        $this->get('/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b/roles')->assertStatus(Response::HTTP_OK);
        $this->get('/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d/roles')->assertStatus(Response::HTTP_OK);
        $this->get('/users/ccf660b9-3cf7-4f58-a5f7-22e53ad836f8/roles')->assertStatus(Response::HTTP_OK);
    }

    public function testNotFoundRelationship(): void
    {
        $this->get('/pageComments/00000000-0000-0000-0000-000000000001/relationships/notexists')->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testListRelatedOneToManyRelationship(): void
    {
        $this->actingAsUser();

        $this->get('/pages/1/pageComments')
            ->assertJson([
                'data' => [
                    [
                        'type' => 'pageComments',
                        'id' => '00000000-0000-0000-0000-000000000001'
                    ]
                ]
            ])
            ->assertOk();
    }

    public function testListRelatedUserRolesResponse()
    {
        $user = $this->actingAsUser();
        $roles = $user->getRoles()->toArray();

        $this->get('/users/' . $user->getId() . '/roles')
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

        $user->addRole(Role::root());
        $this->em()->flush();

        $this->get('/users/' . $user->getId() . '/roles')
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
        $user->addRole(Role::root());
        $user->addRole(Role::moderator());

        $this->em()->flush();


        $this->get('/users/' . $user->getId() . '/roles?sort=-id')
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    ['id' => '3'],
                    ['id' => '2'],
                    ['id' => '1'],
                ]
            ]);

        $this->get('/users/' . $user->getId() . '/roles?page[number]=2&page[size]=1')
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    ['id' => '2'],
                ]
            ]);

        $this->get('/users/' . $user->getId() . '/roles?page[offset]=2&page[limit]=1')
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    ['id' => '3'],
                ]
            ]);
    }
}
