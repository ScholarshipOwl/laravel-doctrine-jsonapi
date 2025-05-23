<?php

namespace Tests\Action\Relationships\ToMany;

use App\Entities\Role;
use Sowl\JsonApi\Response;
use Tests\TestCase;

class ListRelatedResourcesTest extends TestCase
{
    public function testAuthorizationPermissionsForNoLoggedIn(): void
    {
        $this->get('/api/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b/roles')
            ->assertStatus(Response::HTTP_FORBIDDEN);
        $this->get('/api/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d/roles')
            ->assertStatus(Response::HTTP_FORBIDDEN);
        $this->get('/api/users/ccf660b9-3cf7-4f58-a5f7-22e53ad836f8/roles')
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testAuthorizationPermissionsForUserRole(): void
    {
        $this->actingAsUser();

        $this->get('/api/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b/roles')
            ->assertStatus(Response::HTTP_OK);
        $this->get('/api/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d/roles')
            ->assertStatus(Response::HTTP_FORBIDDEN);
        $this->get('/api/users/ccf660b9-3cf7-4f58-a5f7-22e53ad836f8/roles')
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testAuthorizationPermissionsForRootRole(): void
    {
        $this->actingAsRoot();

        $this->get('/api/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b/roles')
            ->assertStatus(Response::HTTP_OK);
        $this->get('/api/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d/roles')
            ->assertStatus(Response::HTTP_OK);
        $this->get('/api/users/ccf660b9-3cf7-4f58-a5f7-22e53ad836f8/roles')
            ->assertStatus(Response::HTTP_OK);
    }

    public function testNotFoundRelationship(): void
    {
        $this->get('/api/pageComments/00000000-0000-0000-0000-000000000001/relationships/notexists')
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testListRelatedOneToManyRelationship(): void
    {
        $this->actingAsUser();

        $this->get('/api/pages/1/pageComments')
            ->assertJson([
                'data' => [
                    [
                        'type' => 'pageComments',
                        'id' => '00000000-0000-0000-0000-000000000001',
                    ],
                ],
            ])
            ->assertOk();
    }

    public function testListRelatedUserRolesResponse(): void
    {
        $user = $this->actingAsUser();
        $roles = $user->getRoles()->toArray();

        $this->get('/api/users/' . $user->getId() . '/roles')
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
                            'self' => '/api/roles/2',
                        ],
                    ],
                ],
            ]);

        $user->addRole(Role::root());
        $this->em()->flush();

        $this->get('/api/users/' . $user->getId() . '/roles')
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
                            'self' => '/api/roles/1',
                        ],
                    ],
                    [
                        'id' => '2',
                        'type' => 'roles',
                        'attributes' => [
                            'name' => 'User',
                        ],
                        'links' => [
                            'self' => '/api/roles/2',
                        ],
                    ],
                ],
            ]);
    }

    public function testListRelatedUserRolesPaginationAndSorting(): void
    {
        $user = $this->actingAsUser();
        $user->addRole(Role::root());
        $user->addRole(Role::moderator());

        $this->em()->flush();

        $this->get('/api/users/' . $user->getId() . '/roles?sort=-id')
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    ['id' => '3'],
                    ['id' => '2'],
                    ['id' => '1'],
                ],
            ]);

        $this->get('/api/users/' . $user->getId() . '/roles?page[number]=2&page[size]=1')
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    ['id' => '2'],
                ],
            ]);

        $this->get('/api/users/' . $user->getId() . '/roles?page[offset]=2&page[limit]=1')
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    ['id' => '3'],
                ],
            ]);
    }
}
