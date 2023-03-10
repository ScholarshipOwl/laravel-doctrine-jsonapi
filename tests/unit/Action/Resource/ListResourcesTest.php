<?php

namespace Tests\Action\Resource;

use Illuminate\Support\Facades\Route;
use Sowl\JsonApi\Response;
use Tests\App\Http\Controller\RolesDefaultController;
use Tests\App\Http\Controller\UsersController;
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
                        ],
                    ],
                    [
                        'id' => '2',
                        'type' => 'users',
                        'attributes' => [
                            'email' => 'test2email@gmail.com',
                            'name' => 'testing user2',
                        ],
                        'relationships' => [
                            'roles' => [
                                'data' => [
                                    ['type' => 'roles', 'id' => '1'],
                                    ['type' => 'roles', 'id' => '2'],
                                ],
                                'links' => [
                                    'related' => '/users/2/roles',
                                    'self' => '/users/2/relationships/roles'
                                ]
                            ]
                        ],
                        'links' => [
                            'self' => '/users/2'
                        ],
                    ],
                    [
                        'id' => '3',
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
                                    'related' => '/users/3/roles',
                                    'self' => '/users/3/relationships/roles'
                                ]
                            ]
                        ],
                        'links' => [
                            'self' => '/users/3'
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
                    ['id' => 1],
                    ['id' => 2],
                    ['id' => 3]],
            ]);

        $this->get('/users?page[limit]=1&page[offset]=2')
            ->assertSuccessful()
            ->assertJson([
                'data' => [
                    ['id' => 3]
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
                        'id' => 3,
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
                    ['id' => 3],
                    ['id' => 2],
                    ['id' => 1]
                ],
            ]);

        $response = $this->get('/users?filter=@test.com');
        $response
            ->assertSuccessful()
            ->assertJson([
                'data' => [
                    ['id' => 1],
                    ['id' => 3]
                ],
            ]);

        $this->get('/users?filter=@test.com&page[limit]=1')
            ->assertSuccessful()
            ->assertJson([
                'data' => [
                    ['id' => 1]
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
                'data' => [['id' => 3]],
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

        $this->get('/users?page[limit]=1&sort=-id&filter[id][start]=1&filter[id][end]=2')
            ->assertHeader('Content-Type', 'application/vnd.api+json')
            ->assertSuccessful()
            ->assertJson([
                'data' => [['id' => 2]],
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
    }
}
