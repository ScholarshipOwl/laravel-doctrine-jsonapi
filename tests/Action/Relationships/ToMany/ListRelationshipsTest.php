<?php

namespace Tests\Action\Relationships\ToMany;

use Sowl\JsonApi\Response;
use App\Entities\Role;
use Tests\TestCase;

class ListRelationshipsTest extends TestCase
{
    public function test_authorization_permissions_for_no_logged_in()
    {
        $this->get('/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b/relationships/roles')->assertStatus(Response::HTTP_FORBIDDEN);
        $this->get('/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d/relationships/roles')->assertStatus(Response::HTTP_FORBIDDEN);
        $this->get('/users/ccf660b9-3cf7-4f58-a5f7-22e53ad836f8/relationships/roles')->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_authorization_permissions_for_user_role()
    {
        $this->actingAsUser();

        $this->get('/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b/relationships/roles')->assertStatus(Response::HTTP_OK);
        $this->get('/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d/relationships/roles')->assertStatus(Response::HTTP_FORBIDDEN);
        $this->get('/users/ccf660b9-3cf7-4f58-a5f7-22e53ad836f8/relationships/roles')->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_authorization_permissions_for_root_role()
    {
        $this->actingAsRoot();

        $this->get('/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b/relationships/roles')->assertStatus(Response::HTTP_OK);
        $this->get('/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d/relationships/roles')->assertStatus(Response::HTTP_OK);
        $this->get('/users/ccf660b9-3cf7-4f58-a5f7-22e53ad836f8/relationships/roles')->assertStatus(Response::HTTP_OK);
    }

    public function test_list_related_user_roles_response()
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
                            'self' => '/roles/2',
                        ],
                    ],
                ],
            ]);

        $user->addRole(Role::root());
        $this->em()->flush();

        $this->get('/users/'.$user->getId().'/relationships/roles')
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    [
                        'id' => '1',
                        'type' => 'roles',
                        'links' => [
                            'self' => '/roles/1',
                        ],
                    ],
                    [
                        'id' => '2',
                        'type' => 'roles',
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

        $this->get('/users/'.$user->getId().'/relationships/roles?sort=-id')
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    ['id' => '3'],
                    ['id' => '2'],
                    ['id' => '1'],
                ],
            ]);

        $this->get('/users/'.$user->getId().'/relationships/roles?page[number]=2&page[size]=1')
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    ['id' => '2'],
                ],
            ]);

        $this->get('/users/'.$user->getId().'/relationships/roles?page[offset]=2&page[limit]=1')
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    ['id' => '3'],
                ],
            ]);
    }
}
