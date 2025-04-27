<?php

namespace Tests\Action\Resource;

use Sowl\JsonApi\Response;
use Tests\App\Entities\Role;
use Tests\App\Entities\User;
use Tests\TestCase;

class ShowResourceTest extends TestCase
{
    public function test_authorization_permissions_for_no_logged_in()
    {
        $this->get('/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b')->assertStatus(Response::HTTP_FORBIDDEN);
        $this->get('/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d')->assertStatus(Response::HTTP_FORBIDDEN);
        $this->get('/users/ccf660b9-3cf7-4f58-a5f7-22e53ad836f8')->assertStatus(Response::HTTP_FORBIDDEN);

        $this->get('/roles/1')->assertStatus(Response::HTTP_FORBIDDEN);
        $this->get('/roles/2')->assertStatus(Response::HTTP_FORBIDDEN);

        $this->get('/pages/1')->assertStatus(Response::HTTP_OK);

        $this->get('/pageComments/00000000-0000-0000-0000-000000000001')->assertStatus(Response::HTTP_OK);
        $this->get('/pageComments/00000000-0000-0000-0000-000000000002')->assertStatus(Response::HTTP_OK);
        $this->get('/pageComments/00000000-0000-0000-0000-000000000003')->assertStatus(Response::HTTP_OK);
    }

    public function test_authorization_permissions_for_user_role()
    {
        $this->actingAsUser();

        $this->get('/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b')->assertStatus(Response::HTTP_OK);
        $this->get('/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d')->assertStatus(Response::HTTP_OK);
        $this->get('/users/ccf660b9-3cf7-4f58-a5f7-22e53ad836f8')->assertStatus(Response::HTTP_OK);

        $this->get('/roles/1')->assertStatus(Response::HTTP_FORBIDDEN);
        $this->get('/roles/2')->assertStatus(Response::HTTP_OK);

        $this->get('/pages/1')->assertStatus(Response::HTTP_OK);

        $this->get('/pageComments/00000000-0000-0000-0000-000000000001')->assertStatus(Response::HTTP_OK);
        $this->get('/pageComments/00000000-0000-0000-0000-000000000002')->assertStatus(Response::HTTP_OK);
        $this->get('/pageComments/00000000-0000-0000-0000-000000000003')->assertStatus(Response::HTTP_OK);
    }

    public function test_authorization_permissions_for_root_role()
    {
        $this->actingAsRoot();

        $this->get('/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b')->assertStatus(Response::HTTP_OK);
        $this->get('/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d')->assertStatus(Response::HTTP_OK);
        $this->get('/users/ccf660b9-3cf7-4f58-a5f7-22e53ad836f8')->assertStatus(Response::HTTP_OK);

        $this->get('/roles/1')->assertStatus(Response::HTTP_OK);
        $this->get('/roles/2')->assertStatus(Response::HTTP_OK);

        $this->get('/pages/1')->assertStatus(Response::HTTP_OK);

        $this->get('/pageComments/00000000-0000-0000-0000-000000000001')->assertStatus(Response::HTTP_OK);
        $this->get('/pageComments/00000000-0000-0000-0000-000000000002')->assertStatus(Response::HTTP_OK);
        $this->get('/pageComments/00000000-0000-0000-0000-000000000003')->assertStatus(Response::HTTP_OK);
    }

    public function test_show_user_response()
    {
        $this->actingAsRoot();

        $this->get('/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d232')->assertStatus(404);

        $this->get('/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b')
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'id' => User::USER_ID,
                    'type' => 'users',
                    'attributes' => [
                        'email' => 'test1email@test.com',
                        'name' => 'testing user1',
                    ],
                    'links' => [
                        'self' => '/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b',
                    ],
                ],
            ]);

        $this->get('/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d')
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'id' => User::ROOT_ID,
                    'type' => 'users',
                    'attributes' => [
                        'email' => 'test2email@gmail.com',
                        'name' => 'testing user2',
                    ],
                    'links' => [
                        'self' => '/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d',
                    ],
                ],
            ]);

        $this->get('/users/ccf660b9-3cf7-4f58-a5f7-22e53ad836f8')
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'id' => User::MODERATOR_ID,
                    'type' => 'users',
                    'attributes' => [
                        'email' => 'test3email@test.com',
                        'name' => 'testing user3',
                    ],
                    'links' => [
                        'self' => '/users/ccf660b9-3cf7-4f58-a5f7-22e53ad836f8',
                    ],
                ],
            ]);
    }

    public function test_show_user_self()
    {
        $user = $this->actingAsUser();

        $this->get('/users/'.$user->getId())
            ->assertJson([
                'data' => [
                    'id' => ''.$user->getId(),
                    'type' => 'users',
                ],
            ]);
    }

    public function test_show_role_response()
    {
        $this->actingAsRoot();

        $this->get('/roles/2232')->assertStatus(404);

        $this->get('/roles/1')
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'id' => '1',
                    'type' => 'roles',
                    'attributes' => [
                        'name' => 'Root',
                    ],
                    'links' => [
                        'self' => '/roles/1',
                    ],
                ],
            ]);

        $this->actingAsUser();
        $this->get('/roles/2')
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'id' => '2',
                    'type' => 'roles',
                    'attributes' => [
                        'name' => 'User',
                    ],
                    'links' => [
                        'self' => '/roles/2',
                    ],
                ],
            ]);

        $this->actingAsModerator();
        $this->get('/roles/3')
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'id' => '3',
                    'type' => 'roles',
                    'attributes' => [
                        'name' => 'Moderator',
                    ],
                    'links' => [
                        'self' => '/roles/3',
                    ],
                ],
            ]);
    }

    public function test_show_page_response()
    {
        $this->get('/pages/2232')->assertStatus(404);

        $this->get('/pages/1')
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'id' => '1',
                    'type' => 'pages',
                    'attributes' => [
                        'title' => 'JSON:API standard',
                        'content' => '<strong>JSON:API</strong>',
                    ],
                    'links' => [
                        'self' => '/pages/1',
                    ],
                ],
            ]);
    }

    public function test_show_page_comments_response()
    {
        $this->get('/pageComments/00000000-0000-0000-0000-000000000002232')->assertStatus(404);

        $this->get('/pageComments/00000000-0000-0000-0000-000000000001')
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'id' => '00000000-0000-0000-0000-000000000001',
                    'type' => 'pageComments',
                    'attributes' => [
                        'content' => '<span>It is mine comment</span>',
                    ],
                    'links' => [
                        'self' => '/pageComments/00000000-0000-0000-0000-000000000001',
                    ],
                ],
            ]);
        $this->get('/pageComments/00000000-0000-0000-0000-000000000002')
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'id' => '00000000-0000-0000-0000-000000000002',
                    'type' => 'pageComments',
                    'attributes' => [
                        'content' => '<span>I know better</span>',
                    ],
                    'links' => [
                        'self' => '/pageComments/00000000-0000-0000-0000-000000000002',
                    ],
                ],
            ]);

        $this->get('/pageComments/00000000-0000-0000-0000-000000000003')
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'id' => '00000000-0000-0000-0000-000000000003',
                    'type' => 'pageComments',
                    'attributes' => [
                        'content' => '<span>I think he is right</span>',
                    ],
                    'links' => [
                        'self' => '/pageComments/00000000-0000-0000-0000-000000000003',
                    ],
                ],
            ]);
    }

    public function test_include_user_roles()
    {
        $user = $this->actingAsUser();
        $user->addRole(Role::moderator());
        $this->em()->flush();

        $this->get('/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b?include=roles')
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'id' => User::USER_ID,
                    'type' => 'users',
                    'attributes' => [
                        'email' => 'test1email@test.com',
                        'name' => 'testing user1',
                    ],
                    'relationships' => [
                        'roles' => [
                            'data' => [
                                [
                                    'id' => '2',
                                    'type' => 'roles',
                                ],
                                [
                                    'id' => '3',
                                    'type' => 'roles',
                                ],
                            ],
                            'links' => [
                                'related' => '/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b/roles',
                                'self' => '/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b/relationships/roles',
                            ],
                        ],
                    ],
                    'links' => [
                        'self' => '/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b',
                    ],
                ],
                'included' => [
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
                    [
                        'id' => '3',
                        'type' => 'roles',
                        'attributes' => [
                            'name' => 'Moderator',
                        ],
                        'links' => [
                            'self' => '/roles/3',
                        ],
                    ],
                ],
            ]);
    }

    public function test_include_page_user_and_user_roles()
    {
        $this->actingAsModerator();
        $this->get('/pages/1?include=user,user.roles')
            ->assertStatus(403)
            ->assertExactJson([
                'errors' => [],
            ]);

        $this->actingAsUser();
        $this->get('/pages/1?include=user,user.roles')
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'id' => '1',
                    'type' => 'pages',
                    'attributes' => [
                        'title' => 'JSON:API standard',
                        'content' => '<strong>JSON:API</strong>',
                    ],
                    'relationships' => [
                        'user' => [
                            'data' => [
                                'id' => User::USER_ID,
                                'type' => 'users',
                            ],
                            'links' => [
                                'related' => '/pages/1/user',
                                'self' => '/pages/1/relationships/user',
                            ],
                        ],
                    ],
                    'links' => [
                        'self' => '/pages/1',
                    ],
                ],
                'included' => [
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
                    [
                        'id' => User::USER_ID,
                        'type' => 'users',
                        'attributes' => [
                            'email' => 'test1email@test.com',
                            'name' => 'testing user1',
                        ],
                        'relationships' => [
                            'roles' => [
                                'data' => [
                                    [
                                        'id' => '2',
                                        'type' => 'roles',
                                    ],
                                ],
                                'links' => [
                                    'related' => '/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b/roles',
                                    'self' => '/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b/relationships/roles',
                                ],
                            ],
                        ],
                        'links' => [
                            'self' => '/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b',
                        ],
                    ],
                ],
            ]);
    }

    public function test_metafield_include(): void
    {
        $this->actingAsUser();

        $this->get('/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b?meta[users]=random')
            ->assertJsonStructure([
                'data' => [
                    'meta' => [
                        'random',
                    ],
                ],
            ]);
    }
}
