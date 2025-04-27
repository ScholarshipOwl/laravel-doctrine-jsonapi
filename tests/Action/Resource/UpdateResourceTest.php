<?php

namespace Tests\Action\Resource;

use Sowl\JsonApi\Response;
use App\Entities\Role;
use App\Entities\User;
use App\Entities\UserStatus;
use Tests\TestCase;

class UpdateResourceTest extends TestCase
{
    public function test_authorization_permissions_for_no_logged_in()
    {
        $this->patch('/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b')->assertStatus(Response::HTTP_FORBIDDEN);
        $this->patch('/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d')->assertStatus(Response::HTTP_FORBIDDEN);
        $this->patch('/users/ccf660b9-3cf7-4f58-a5f7-22e53ad836f8')->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_authorization_permissions_for_user_role()
    {
        $this->actingAsUser();
        $data = [
            'attributes' => [
                'name' => 'newname',
            ],
        ];

        $this->patch('/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b', ['data' => $data])->assertStatus(Response::HTTP_OK);
        $this->patch('/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d', ['data' => $data])->assertStatus(Response::HTTP_FORBIDDEN);
        $this->patch('/users/ccf660b9-3cf7-4f58-a5f7-22e53ad836f8', ['data' => $data])->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_authorization_permissions_for_root_role()
    {
        $this->actingAsRoot();
        $data = [
            'attributes' => [
                'name' => 'newname',
            ],
        ];

        $this->patch('/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b', ['data' => $data])->assertStatus(Response::HTTP_OK);
        $this->patch('/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d', ['data' => $data])->assertStatus(Response::HTTP_OK);
        $this->patch('/users/ccf660b9-3cf7-4f58-a5f7-22e53ad836f8', ['data' => $data])->assertStatus(Response::HTTP_OK);
    }

    public function test_update_user()
    {
        $this->actingAsUser();

        $response = $this->patch('/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b', [
            'data' => [
                'attributes' => [
                    'email' => 'newemail@gmail.com',
                    'name' => 'newname',
                    'password' => 'newsecret',
                ],
            ],
        ]);

        $response->assertExactJson([
            'data' => [
                'id' => User::USER_ID,
                'type' => 'users',
                'attributes' => [
                    'email' => 'newemail@gmail.com',
                    'name' => 'newname',
                ],
                'links' => [
                    'self' => '/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b',
                ],
            ],
        ]);

        $response = $this->patch('/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b?include=status', [
            'data' => [
                'relationships' => [
                    'status' => [
                        'data' => [
                            'type' => UserStatus::getResourceType(),
                            'id' => UserStatus::INACTIVE,
                        ],
                    ],
                ],
            ],
        ]);

        $response->assertExactJson([
            'data' => [
                'id' => User::USER_ID,
                'type' => 'users',
                'attributes' => [
                    'email' => 'newemail@gmail.com',
                    'name' => 'newname',
                ],
                'relationships' => [
                    'status' => [
                        'data' => [
                            'type' => UserStatus::getResourceType(),
                            'id' => UserStatus::INACTIVE,
                        ],
                        'links' => [
                            'related' => '/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b/status',
                            'self' => '/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b/relationships/status',
                        ],
                    ],
                ],
                'links' => [
                    'self' => '/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b',
                ],
            ],
            'included' => [
                [
                    'id' => UserStatus::INACTIVE,
                    'type' => 'userStatuses',
                    'attributes' => [
                        'name' => 'Inactive',
                    ],
                    'links' => [
                        'self' => '/userStatuses/'.UserStatus::INACTIVE,
                    ],
                ],
            ],
        ]);
    }

    public function test_update_user_role_relationship()
    {
        $this->actingAsRoot();

        $response = $this->patch('/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b?include=roles', [
            'data' => [
                'relationships' => [
                    'roles' => [
                        'data' => [],
                    ],
                ],
            ],
        ]);

        $response->assertExactJson([
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
                            ['type' => 'roles', 'id' => '2'],
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
            ],
        ]);

        $this->em()->clear();
        $user = $this->em()->find(User::class, User::USER_ID);

        $this->assertCount(1, $user->getRoles()->toArray());
        $this->assertTrue($user->hasRole(Role::user()));

        $this->actingAsRoot();
        $response = $this->patch('/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b?include=roles', [
            'data' => [
                'relationships' => [
                    'roles' => [
                        'data' => [
                            ['type' => Role::getResourceType(), 'id' => Role::user()->getId()],
                            ['type' => Role::getResourceType(), 'id' => Role::moderator()->getId()],
                        ],
                    ],
                ],
            ],
        ]);

        $response->assertExactJson([
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
                            ['type' => Role::getResourceType(), 'id' => (string) Role::user()->getId()],
                            ['type' => Role::getResourceType(), 'id' => (string) Role::moderator()->getId()],
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

        $this->em()->clear();
        $user = $this->em()->find(User::class, User::USER_ID);

        $this->assertTrue($user->hasRole(Role::user()));
        $this->assertTrue($user->hasRole(Role::moderator()));
    }

    public function test_update_user_validation()
    {
        $this->actingAsUser();

        $response = $this->patch('/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b', [
            'data' => [
                'attributes' => [
                    'email' => 'wrongemail',
                ],
            ],
        ]);

        $response->assertExactJson([
            'errors' => [
                [
                    'code' => 422,
                    'detail' => 'The email field must be a valid email address.',
                    'source' => [
                        'pointer' => '/data/attributes/email',
                    ],
                ],
            ],
        ]);
    }
}
