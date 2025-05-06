<?php

namespace Tests\Action\Resource;

use App\Entities\Role;
use App\Entities\User;
use App\Entities\UserStatus;
use Sowl\JsonApi\Response;
use Tests\TestCase;

class UpdateResourceTest extends TestCase
{
    public function testAuthorizationPermissionsForNoLoggedIn(): void
    {
        $this->patch('/api/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b')
            ->assertStatus(Response::HTTP_FORBIDDEN);
        $this->patch('/api/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d')
            ->assertStatus(Response::HTTP_FORBIDDEN);
        $this->patch('/api/users/ccf660b9-3cf7-4f58-a5f7-22e53ad836f8')
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testAuthorizationPermissionsForUserRole(): void
    {
        $this->actingAsUser();
        $data = [
            'attributes' => [
                'name' => 'newname',
            ],
        ];

        $this->patch('/api/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b', ['data' => $data])
            ->assertStatus(Response::HTTP_OK);
        $this->patch('/api/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d', ['data' => $data])
            ->assertStatus(Response::HTTP_FORBIDDEN);
        $this->patch('/api/users/ccf660b9-3cf7-4f58-a5f7-22e53ad836f8', ['data' => $data])
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testAuthorizationPermissionsForRootRole(): void
    {
        $this->actingAsRoot();
        $data = [
            'attributes' => [
                'name' => 'newname',
            ],
        ];

        $this->patch('/api/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b', ['data' => $data])
            ->assertStatus(Response::HTTP_OK);
        $this->patch('/api/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d', ['data' => $data])
            ->assertStatus(Response::HTTP_OK);
        $this->patch('/api/users/ccf660b9-3cf7-4f58-a5f7-22e53ad836f8', ['data' => $data])
            ->assertStatus(Response::HTTP_OK);
    }

    public function testUpdateUser(): void
    {
        $this->actingAsUser();

        $response = $this->patch('/api/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b', [
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
                    'self' => '/api/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b',
                ],
            ],
        ]);

        $response = $this->patch('/api/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b?include=status', [
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
                            'related' => '/api/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b/status',
                            'self' => '/api/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b/relationships/status',
                        ],
                    ],
                ],
                'links' => [
                    'self' => '/api/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b',
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
                        'self' => '/api/userStatuses/' . UserStatus::INACTIVE,
                    ],
                ],
            ],
        ]);
    }

    public function testUpdateUserRoleRelationship(): void
    {
        $this->actingAsRoot();

        $response = $this->patch('/api/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b?include=roles', [
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
            ],
        ]);

        $this->em()->clear();
        $user = $this->em()->find(User::class, User::USER_ID);

        $this->assertCount(1, $user->getRoles()->toArray());
        $this->assertTrue($user->hasRole(Role::user()));

        $this->actingAsRoot();
        $response = $this->patch('/api/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b?include=roles', [
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

        $this->em()->clear();
        $user = $this->em()->find(User::class, User::USER_ID);

        $this->assertTrue($user->hasRole(Role::user()));
        $this->assertTrue($user->hasRole(Role::moderator()));
    }

    public function testUpdateUserValidation(): void
    {
        $this->actingAsUser();

        $response = $this->patch('/api/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b', [
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
