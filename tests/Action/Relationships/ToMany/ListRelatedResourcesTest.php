<?php

namespace Tests\Action\Relationships\ToMany;

use Sowl\JsonApi\Response;
use App\Entities\Role;
use Tests\TestCase;

class ListRelatedResourcesTest extends TestCase
{
    public function test_authorization_permissions_for_no_loged_in()
    {
        $this->get('/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b/roles')->assertStatus(Response::HTTP_FORBIDDEN);
        $this->get('/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d/roles')->assertStatus(Response::HTTP_FORBIDDEN);
        $this->get('/users/ccf660b9-3cf7-4f58-a5f7-22e53ad836f8/roles')->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_authorization_permissions_for_user_role()
    {
        $this->actingAsUser();

        $this->get('/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b/roles')->assertStatus(Response::HTTP_OK);
        $this->get('/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d/roles')->assertStatus(Response::HTTP_FORBIDDEN);
        $this->get('/users/ccf660b9-3cf7-4f58-a5f7-22e53ad836f8/roles')->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_authorization_permissions_for_root_role()
    {
        $this->actingAsRoot();

        $this->get('/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b/roles')->assertStatus(Response::HTTP_OK);
        $this->get('/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d/roles')->assertStatus(Response::HTTP_OK);
        $this->get('/users/ccf660b9-3cf7-4f58-a5f7-22e53ad836f8/roles')->assertStatus(Response::HTTP_OK);
    }

    public function test_not_found_relationship(): void
    {
        $this->get('/pageComments/00000000-0000-0000-0000-000000000001/relationships/notexists')->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_list_related_one_to_many_relationship(): void
    {
        $this->actingAsUser();

        $this->get('/pages/1/pageComments')
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

    public function test_list_related_user_roles_response()
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
                            'self' => '/roles/2',
                        ],
                    ],
                ],
            ]);

        $user->addRole(Role::root());
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
                            'self' => '/roles/1',
                        ],
                    ],
                    [
                        'id' => '2',
                        'type' => 'roles',
                        'attributes' => [
                            'name' => 'User',
                        ],
                        'links' => [
                            'self' => '/roles/2',
                        ],
                    ],
                ],
            ]);
    }

    public function test_list_related_user_roles_pagination_and_sorting()
    {
        $user = $this->actingAsUser();
        $user->addRole(Role::root());
        $user->addRole(Role::moderator());

        $this->em()->flush();

        $this->get('/users/'.$user->getId().'/roles?sort=-id')
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    ['id' => '3'],
                    ['id' => '2'],
                    ['id' => '1'],
                ],
            ]);

        $this->get('/users/'.$user->getId().'/roles?page[number]=2&page[size]=1')
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    ['id' => '2'],
                ],
            ]);

        $this->get('/users/'.$user->getId().'/roles?page[offset]=2&page[limit]=1')
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    ['id' => '3'],
                ],
            ]);
    }
}
