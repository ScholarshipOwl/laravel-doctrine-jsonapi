<?php

namespace Tests\Action\Resource;

use App\Entities\Role;
use App\Entities\User;
use Sowl\JsonApi\Response;
use Tests\TestCase;

class ShowResourceTest extends TestCase
{
    public function testAuthorizationPermissionsForNoLoggedIn(): void
    {
        $this->get('/api/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b')->assertStatus(Response::HTTP_FORBIDDEN);
        $this->get('/api/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d')->assertStatus(Response::HTTP_FORBIDDEN);
        $this->get('/api/users/ccf660b9-3cf7-4f58-a5f7-22e53ad836f8')->assertStatus(Response::HTTP_FORBIDDEN);

        $this->get('/api/roles/1')->assertStatus(Response::HTTP_FORBIDDEN);
        $this->get('/api/roles/2')->assertStatus(Response::HTTP_FORBIDDEN);

        $this->get('/api/pages/1')->assertStatus(Response::HTTP_OK);

        $this->get('/api/pageComments/00000000-0000-0000-0000-000000000001')->assertStatus(Response::HTTP_OK);
        $this->get('/api/pageComments/00000000-0000-0000-0000-000000000002')->assertStatus(Response::HTTP_OK);
        $this->get('/api/pageComments/00000000-0000-0000-0000-000000000003')->assertStatus(Response::HTTP_OK);
    }

    public function testAuthorizationPermissionsForUserRole(): void
    {
        $this->actingAsUser();

        $this->get('/api/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b')->assertStatus(Response::HTTP_OK);
        $this->get('/api/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d')->assertStatus(Response::HTTP_OK);
        $this->get('/api/users/ccf660b9-3cf7-4f58-a5f7-22e53ad836f8')->assertStatus(Response::HTTP_OK);

        $this->get('/api/roles/1')->assertStatus(Response::HTTP_FORBIDDEN);
        $this->get('/api/roles/2')->assertStatus(Response::HTTP_OK);

        $this->get('/api/pages/1')->assertStatus(Response::HTTP_OK);

        $this->get('/api/pageComments/00000000-0000-0000-0000-000000000001')->assertStatus(Response::HTTP_OK);
        $this->get('/api/pageComments/00000000-0000-0000-0000-000000000002')->assertStatus(Response::HTTP_OK);
        $this->get('/api/pageComments/00000000-0000-0000-0000-000000000003')->assertStatus(Response::HTTP_OK);
    }

    public function testAuthorizationPermissionsForRootRole(): void
    {
        $this->actingAsRoot();

        $this->get('/api/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b')->assertStatus(Response::HTTP_OK);
        $this->get('/api/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d')->assertStatus(Response::HTTP_OK);
        $this->get('/api/users/ccf660b9-3cf7-4f58-a5f7-22e53ad836f8')->assertStatus(Response::HTTP_OK);

        $this->get('/api/roles/1')->assertStatus(Response::HTTP_OK);
        $this->get('/api/roles/2')->assertStatus(Response::HTTP_OK);

        $this->get('/api/pages/1')->assertStatus(Response::HTTP_OK);

        $this->get('/api/pageComments/00000000-0000-0000-0000-000000000001')->assertStatus(Response::HTTP_OK);
        $this->get('/api/pageComments/00000000-0000-0000-0000-000000000002')->assertStatus(Response::HTTP_OK);
        $this->get('/api/pageComments/00000000-0000-0000-0000-000000000003')->assertStatus(Response::HTTP_OK);
    }

    public function testShowUserResponse(): void
    {
        $this->actingAsRoot();

        $this->get('/api/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d232')->assertStatus(404);

        $this->get('/api/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b')
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
                        'self' => '/api/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b',
                    ],
                ],
            ]);

        $this->get('/api/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d')
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
                        'self' => '/api/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d',
                    ],
                ],
            ]);

        $this->get('/api/users/ccf660b9-3cf7-4f58-a5f7-22e53ad836f8')
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
                        'self' => '/api/users/ccf660b9-3cf7-4f58-a5f7-22e53ad836f8',
                    ],
                ],
            ]);
    }

    public function testShowUserSelf(): void
    {
        $user = $this->actingAsUser();

        $this->get('/api/users/' . $user->getId())
            ->assertJson([
                'data' => [
                    'id' => '' . $user->getId(),
                    'type' => 'users',
                ],
            ]);
    }

    public function testShowRoleResponse(): void
    {
        $this->actingAsRoot();

        $this->get('/api/roles/2232')->assertStatus(404);

        $this->get('/api/roles/1')
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'id' => '1',
                    'type' => 'roles',
                    'attributes' => [
                        'name' => 'Root',
                    ],
                    'links' => [
                        'self' => '/api/roles/1',
                    ],
                ],
            ]);

        $this->actingAsUser();
        $this->get('/api/roles/2')
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'id' => '2',
                    'type' => 'roles',
                    'attributes' => [
                        'name' => 'User',
                    ],
                    'links' => [
                        'self' => '/api/roles/2',
                    ],
                ],
            ]);

        $this->actingAsModerator();
        $this->get('/api/roles/3')
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'id' => '3',
                    'type' => 'roles',
                    'attributes' => [
                        'name' => 'Moderator',
                    ],
                    'links' => [
                        'self' => '/api/roles/3',
                    ],
                ],
            ]);
    }

    public function testShowPageResponse(): void
    {
        $this->get('/api/pages/2232')->assertStatus(404);

        $this->get('/api/pages/1')
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
                        'self' => '/api/pages/1',
                    ],
                ],
            ]);
    }

    public function testShowPageCommentsResponse(): void
    {
        $this->get('/api/pageComments/00000000-0000-0000-0000-000000000002232')->assertStatus(404);

        $this->get('/api/pageComments/00000000-0000-0000-0000-000000000001')
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'id' => '00000000-0000-0000-0000-000000000001',
                    'type' => 'pageComments',
                    'attributes' => [
                        'content' => '<span>It is mine comment</span>',
                    ],
                    'links' => [
                        'self' => '/api/pageComments/00000000-0000-0000-0000-000000000001',
                    ],
                ],
            ]);
        $this->get('/api/pageComments/00000000-0000-0000-0000-000000000002')
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'id' => '00000000-0000-0000-0000-000000000002',
                    'type' => 'pageComments',
                    'attributes' => [
                        'content' => '<span>I know better</span>',
                    ],
                    'links' => [
                        'self' => '/api/pageComments/00000000-0000-0000-0000-000000000002',
                    ],
                ],
            ]);

        $this->get('/api/pageComments/00000000-0000-0000-0000-000000000003')
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'id' => '00000000-0000-0000-0000-000000000003',
                    'type' => 'pageComments',
                    'attributes' => [
                        'content' => '<span>I think he is right</span>',
                    ],
                    'links' => [
                        'self' => '/api/pageComments/00000000-0000-0000-0000-000000000003',
                    ],
                ],
            ]);
    }

    public function testIncludeUserRoles(): void
    {
        $user = $this->actingAsUser();
        $user->addRole(Role::moderator());
        $this->em()->flush();

        $this->get('/api/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b?include=roles')
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
                                'related' => '/api/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b/roles',
                                'self' => '/api/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b/relationships/roles',
                            ],
                        ],
                    ],
                    'links' => [
                        'self' => '/api/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b',
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
                            'self' => '/api/roles/2',
                        ],
                    ],
                    [
                        'id' => '3',
                        'type' => 'roles',
                        'attributes' => [
                            'name' => 'Moderator',
                        ],
                        'links' => [
                            'self' => '/api/roles/3',
                        ],
                    ],
                ],
            ]);
    }

    public function testIncludePageUserAndUserRoles(): void
    {
        $this->actingAsModerator();
        $this->get('/api/pages/1?include=user,user.roles')
            ->assertStatus(403)
            ->assertExactJson([
                'errors' => [],
            ]);

        $this->actingAsUser();
        $this->get('/api/pages/1?include=user,user.roles')
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
                                'related' => '/api/pages/1/user',
                                'self' => '/api/pages/1/relationships/user',
                            ],
                        ],
                    ],
                    'links' => [
                        'self' => '/api/pages/1',
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
                            'self' => '/api/roles/2',
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
                                    'related' => '/api/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b/roles',
                                    'self' => '/api/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b/relationships/roles',
                                ],
                            ],
                        ],
                        'links' => [
                            'self' => '/api/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b',
                        ],
                    ],
                ],
            ]);
    }

    public function testMetafieldInclude(): void
    {
        $this->actingAsUser();

        $this->get('/api/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b?meta[users]=random')
            ->assertJsonStructure([
                'data' => [
                    'meta' => [
                        'random',
                    ],
                ],
            ]);
    }
}
