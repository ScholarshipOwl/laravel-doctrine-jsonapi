<?php

namespace Tests\Action\Resource;

use Sowl\JsonApi\Response;
use Tests\App\Entities\Role;
use Tests\App\Entities\User;
use Tests\TestCase;

class ListResourcesTest extends TestCase
{
    public function testAuthorizationPermissionsForNoLogedIn()
    {
        $this->get('/roles')->assertStatus(Response::HTTP_FORBIDDEN);
        $this->get('/users')->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testAuthorizationPermissionsForUserRole()
    {
        $this->actingAsUser();

        $this->get('/roles')->assertStatus(Response::HTTP_FORBIDDEN);
        $this->get('/users')->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testAuthorizationPermissionsForRootRole()
    {
        $this->actingAsRoot();

        $this->get('/roles')->assertStatus(Response::HTTP_OK);
        $this->get('/users')->assertStatus(Response::HTTP_OK);
    }

    public function testListRoleResponse()
    {
        $this->actingAsRoot();

        $this->get('/roles')
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
                    [
                        'id' => '3',
                        'type' => 'roles',
                        'attributes' => [
                            'name' => 'Moderator',
                        ],
                        'links' => [
                            'self' => '/roles/3'
                        ]
                    ],
                ]
            ]);
    }

    public function testListUsersResponse()
    {
        $this->actingAsRoot();

        $this->get('/users?include=roles')
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
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
                                    ['type' => 'roles', 'id' => Role::USER],
                                ],
                                'links' => [
                                    'related' => '/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b/roles',
                                    'self' => '/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b/relationships/roles'
                                ]
                            ]
                        ],
                        'links' => [
                            'self' => '/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b'
                        ],
                    ],
                    [
                        'id' => User::ROOT_ID,
                        'type' => 'users',
                        'attributes' => [
                            'email' => 'test2email@gmail.com',
                            'name' => 'testing user2',
                        ],
                        'relationships' => [
                            'roles' => [
                                'data' => [
                                    ['type' => 'roles', 'id' => Role::USER],
                                    ['type' => 'roles', 'id' => Role::ROOT],
                                ],
                                'links' => [
                                    'related' => '/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d/roles',
                                    'self' => '/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d/relationships/roles'
                                ]
                            ]
                        ],
                        'links' => [
                            'self' => '/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d'
                        ],
                    ],
                    [
                        'id' => User::MODERATOR_ID,
                        'type' => 'users',
                        'attributes' => [
                            'email' => 'test3email@test.com',
                            'name' => 'testing user3',
                        ],
                        'relationships' => [
                            'roles' => [
                                'data' => [
                                    ['type' => 'roles', 'id' => '2'],
                                    ['type' => 'roles', 'id' => '3'],
                                ],
                                'links' => [
                                    'related' => '/users/ccf660b9-3cf7-4f58-a5f7-22e53ad836f8/roles',
                                    'self' => '/users/ccf660b9-3cf7-4f58-a5f7-22e53ad836f8/relationships/roles'
                                ]
                            ]
                        ],
                        'links' => [
                            'self' => '/users/ccf660b9-3cf7-4f58-a5f7-22e53ad836f8'
                        ],
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
                            'self' => '/roles/2'
                        ]
                    ],
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
                        'id' => '3',
                        'type' => 'roles',
                        'attributes' => [
                            'name' => 'Moderator',
                        ],
                        'links' => [
                            'self' => '/roles/3'
                        ]
                    ],
                ]
            ]);
    }

    public function testListUsersPaginationAndSorting()
    {
        $this->actingAsRoot();

        $this->get('/users')
            ->assertSuccessful()
            ->assertJson([
                'data' => [
                    ['id' => User::USER_ID],
                    ['id' => User::ROOT_ID],
                    ['id' => User::MODERATOR_ID]],
            ]);

        $this->get('/users?page[limit]=1&page[offset]=2')
            ->assertSuccessful()
            ->assertJson([
                'data' => [
                    ['id' => User::MODERATOR_ID]
                ],
                'meta' => [
                    'pagination' => [
                        'total' => 3,
                        'count' => 1,
                        'per_page' => 1,
                        'current_page' => 3,
                        'total_pages' => 3,
                    ],
                ],
            ]);

        $this->get('/users?page[limit]=2&page[offset]=2')
            ->assertSuccessful()
            ->assertJson([
                'data' => [
                    [
                        'id' => User::MODERATOR_ID,
                        'attributes' => [
                            'name' => 'testing user3',
                            'email' => 'test3email@test.com',
                        ]
                    ]
                ],
                'meta' => [
                    'pagination' => [
                        'total' => 3,
                        'count' => 1,
                        'per_page' => 2,
                        'current_page' => 2,
                        'total_pages' => 2,
                    ],
                ],
            ]);

        $this->get('/users?sort=-id')
            ->assertSuccessful()
            ->assertJson([
                'data' => [
                    ['id' => User::ROOT_ID],
                    ['id' => User::MODERATOR_ID],
                    ['id' => User::USER_ID]
                ],
            ]);

        $response = $this->get('/users?filter=@test.com');
        $response
            ->assertSuccessful()
            ->assertJson([
                'data' => [
                    ['id' => User::USER_ID],
                    ['id' => User::MODERATOR_ID]
                ],
            ]);

        $this->get('/users?filter=@test.com&page[limit]=1')
            ->assertSuccessful()
            ->assertJson([
                'data' => [
                    ['id' => User::USER_ID]
                ],
                'meta' => [
                    'pagination' => [
                        'total' => 2,
                        'count' => 1,
                        'per_page' => 1,
                        'current_page' => 1,
                        'total_pages' => 2,
                    ],
                ],
                'links' => [],
            ]);

        $this->get('/users?filter=@test.com&page[number]=2&page[size]=1')
            ->assertSuccessful()
            ->assertJson([
                'data' => [['id' => User::MODERATOR_ID]],
                'meta' => [
                    'pagination' => [
                        'total' => 2,
                        'count' => 1,
                        'per_page' => 1,
                        'current_page' => 2,
                        'total_pages' => 2,
                    ],
                ],
                'links' => [],
            ]);
    }
}
