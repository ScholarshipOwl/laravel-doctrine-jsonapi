<?php

namespace Tests\Action\Resource;

use Sowl\JsonApi\Response;
use Tests\App\Entities\Role;
use Tests\App\Entities\User;
use Tests\TestCase;

class UpdateResourceTest extends TestCase
{
    public function testAuthorizationPermissionsForNoLoggedIn()
    {
        $this->patch('/users/1')->assertStatus(Response::HTTP_FORBIDDEN);
        $this->patch('/users/2')->assertStatus(Response::HTTP_FORBIDDEN);
        $this->patch('/users/3')->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testAuthorizationPermissionsForUserRole()
    {
        $this->actingAsUser();
        $data = [
            'attributes' => [
                'name' => 'newname',
            ],
        ];

        $this->patch('/users/1', ['data' => $data])->assertStatus(Response::HTTP_OK);
        $this->patch('/users/2', ['data' => $data])->assertStatus(Response::HTTP_FORBIDDEN);
        $this->patch('/users/3', ['data' => $data])->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testAuthorizationPermissionsForRootRole()
    {
        $this->actingAsRoot();
        $data = [
            'attributes' => [
                'name' => 'newname',
            ],
        ];


        $this->patch('/users/1', ['data' => $data])->assertStatus(Response::HTTP_OK);
        $this->patch('/users/2', ['data' => $data])->assertStatus(Response::HTTP_OK);
        $this->patch('/users/3', ['data' => $data])->assertStatus(Response::HTTP_OK);
    }

    public function testUpdateUser()
    {
        $this->actingAsUser();

        $response = $this->patch('/users/1', [
            'data' => [
                'attributes' => [
                    'email' => 'newemail@gmail.com',
                    'name' => 'newname',
                    'password' => 'newsecret',
                ]
            ]
        ]);

        $response->assertExactJson([
            'data' => [
                'id' => '1',
                'type' => 'users',
                'attributes' => [
                    'email' => 'newemail@gmail.com',
                    'name' => 'newname',
                ],
                'relationships' => [
                    'roles' => [
                        'links' => [
                            'related' => '/users/1/roles',
                            'self' => '/users/1/relationships/roles'
                        ]
                    ]
                ],
                'links' => [
                    'self' => '/users/1'
                ]
            ]
        ]);
    }

    public function testUpdateUserRoleRelationship()
    {
        $this->actingAsRoot();

        $response = $this->patch('/users/1?include=roles', [
            'data' => [
                'relationships' => [
                    'roles' => [
                        'data' => []
                    ]
                ]
            ]
        ]);

        $response->assertExactJson([
            'data' => [
                'id' => '1',
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
                            'related' => '/users/1/roles',
                            'self' => '/users/1/relationships/roles'
                        ]
                    ]
                ],
                'links' => [
                    'self' => '/users/1'
                ]
            ],
            'included' => [
                [
                    'id' => '2',
                    'type' => 'roles',
                    'attributes' => [
                        'name' => 'User'
                    ],
                    'links' => [
                        'self' => '/roles/2'
                    ]
                ]
            ]
        ]);

        $this->em()->clear();
        $user = $this->em()->find(User::class, 1);

        $this->assertCount(1, $user->getRoles()->toArray());
        $this->assertTrue($user->hasRole(Role::user()));

        $this->actingAsRoot();
        $response = $this->patch('/users/1?include=roles', [
            'data' => [
                'relationships' => [
                    'roles' => [
                        'data' => [
                            ['type' => Role::getResourceType(), 'id' => Role::user()->getId()],
                            ['type' => Role::getResourceType(), 'id' => Role::moderator()->getId()],
                        ]
                    ]
                ]
            ]
        ]);

        $response->assertExactJson([
            'data' => [
                'id' => '1',
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
                            'related' => '/users/1/roles',
                            'self' => '/users/1/relationships/roles'
                        ]
                    ]
                ],
                'links' => [
                    'self' => '/users/1'
                ]
            ],
            'included' => [
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
                [
                    'id' => '3',
                    'type' => 'roles',
                    'attributes' => [
                        'name' => 'Moderator',
                    ],
                    'links' => [
                        'self' => '/roles/3'
                    ]
                ]
            ]
        ]);

        $this->em()->clear();
        $user = $this->em()->find(User::class, 1);

        $this->assertTrue($user->hasRole(Role::user()));
        $this->assertTrue($user->hasRole(Role::moderator()));
    }

    public function testUpdateUserValidation()
    {
        $this->actingAsUser();

        $response = $this->patch('/users/1', [
            'data' => [
                'attributes' => [
                    'email' => 'wrongemail',
                ]
            ]
        ]);

        $response->assertExactJson([
            'errors' => [
                [
                    'code' => 422,
                    'detail' => 'validation.email',
                    'source' => [
                        'pointer' => '/data/attributes/email'
                    ],
                ]
            ]
        ]);
    }
}
