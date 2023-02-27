<?php

namespace Tests\Action\Resource;

use Illuminate\Support\Facades\Route;
use Sowl\JsonApi\Controller;
use Sowl\JsonApi\Response;
use Tests\App\Entities\Role;
use Tests\App\Http\Controller\PageCommentController;
use Tests\App\Http\Controller\PageController;
use Tests\App\Http\Controller\UsersController;
use Tests\TestCase;

class ShowResourceTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Route::get('/users/{id}', [UsersController::class, 'show']);
        Route::get('/pages/{id}', [PageController::class, 'show']);
        Route::get('/pageComments/{id}', [PageCommentController::class, 'show']);

        Route::get('/{resourceKey}/{id}', [Controller::class, 'show']);
    }

    public function testAuthorizationPermissionsForNoLoggedIn()
    {
        $this->get('/users/1')->assertStatus(Response::HTTP_FORBIDDEN);
        $this->get('/users/2')->assertStatus(Response::HTTP_FORBIDDEN);
        $this->get('/users/3')->assertStatus(Response::HTTP_FORBIDDEN);

        $this->get('/roles/1')->assertStatus(Response::HTTP_FORBIDDEN);
        $this->get('/roles/2')->assertStatus(Response::HTTP_FORBIDDEN);

        $this->get('/pages/1')->assertStatus(Response::HTTP_OK);

        $this->get('/pageComments/1')->assertStatus(Response::HTTP_OK);
        $this->get('/pageComments/2')->assertStatus(Response::HTTP_OK);
        $this->get('/pageComments/3')->assertStatus(Response::HTTP_OK);
    }

    public function testAuthorizationPermissionsForUserRole()
    {
        $this->actingAsUser();
        $this->get('/users/1')->assertStatus(Response::HTTP_OK);
        $this->get('/users/2')->assertStatus(Response::HTTP_OK);
        $this->get('/users/3')->assertStatus(Response::HTTP_OK);

        $this->get('/roles/1')->assertStatus(Response::HTTP_FORBIDDEN);
        $this->get('/roles/2')->assertStatus(Response::HTTP_OK);

        $this->get('/pages/1')->assertStatus(Response::HTTP_OK);

        $this->get('/pageComments/1')->assertStatus(Response::HTTP_OK);
        $this->get('/pageComments/2')->assertStatus(Response::HTTP_OK);
        $this->get('/pageComments/3')->assertStatus(Response::HTTP_OK);
    }

    public function testAuthorizationPermissionsForRootRole()
    {
        $this->actingAsRoot();
        $this->get('/users/1')->assertStatus(Response::HTTP_OK);
        $this->get('/users/2')->assertStatus(Response::HTTP_OK);
        $this->get('/users/3')->assertStatus(Response::HTTP_OK);

        $this->get('/roles/1')->assertStatus(Response::HTTP_OK);
        $this->get('/roles/2')->assertStatus(Response::HTTP_OK);

        $this->get('/pages/1')->assertStatus(Response::HTTP_OK);

        $this->get('/pageComments/1')->assertStatus(Response::HTTP_OK);
        $this->get('/pageComments/2')->assertStatus(Response::HTTP_OK);
        $this->get('/pageComments/3')->assertStatus(Response::HTTP_OK);
    }

    public function testShowUserResponse()
    {
        $this->actingAsRoot();

        $this->get('/users/2232')->assertStatus(404);

        $this->get('/users/1')
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'id' => '1',
                    'type' => 'users',
                    'attributes' => [
                        'email' => 'test1email@test.com',
                        'name' => 'testing user1',
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

        $this->get('/users/2')
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'id' => '2',
                    'type' => 'users',
                    'attributes' => [
                        'email' => 'test2email@gmail.com',
                        'name' => 'testing user2',
                    ],
                    'relationships' => [
                        'roles' => [
                            'links' => [
                                'related' => '/users/2/roles',
                                'self' => '/users/2/relationships/roles'
                            ]
                        ]
                    ],
                    'links' => [
                        'self' => '/users/2'
                    ]
                ]
            ]);

        $this->get('/users/3')
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'id' => '3',
                    'type' => 'users',
                    'attributes' => [
                        'email' => 'test3email@test.com',
                        'name' => 'testing user3',
                    ],
                    'relationships' => [
                        'roles' => [
                            'links' => [
                                'related' => '/users/3/roles',
                                'self' => '/users/3/relationships/roles'
                            ]
                        ]
                    ],
                    'links' => [
                        'self' => '/users/3'
                    ]
                ]
            ]);
    }

    public function testShowUserSelf()
    {
        $user = $this->actingAsUser();

        $this->get('/users/'.$user->getId())
            ->assertJson([
                'data' => [
                    'id' => ''.$user->getId(),
                    'type' => 'users',
                ]
            ]);
    }

    public function testShowRoleResponse()
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
                        'self' => '/roles/1'
                    ]
                ]
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
                        'self' => '/roles/2'
                    ]
                ]
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
                        'self' => '/roles/3'
                    ]
                ]
            ]);
    }

    public function testShowPageResponse()
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
                        'content' => '<strong>JSON:API</strong>'
                    ],
                    'relationships' => [
                        'user' => [
                            'links' => [
                                'related' => '/pages/1/user',
                                'self' => '/pages/1/relationships/user'
                            ]
                        ]
                    ],
                    'links' => [
                        'self' => '/pages/1'
                    ]
                ]
            ]);
    }

    public function testShowPageCommentsResponse()
    {
        $this->get('/pageComments/2232')->assertStatus(404);

        $this->get('/pageComments/1')
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'id' => '1',
                    'type' => 'pageComments',
                    'attributes' => [
                        'content' => '<span>It is mine comment</span>'
                    ],
                    'relationships' => [
                        'user' => [
                            'links' => [
                                'related' => '/pageComments/1/user',
                                'self' => '/pageComments/1/relationships/user'
                            ]
                        ],
                        'page' => [
                            'links' => [
                                'related' => '/pageComments/1/page',
                                'self' => '/pageComments/1/relationships/page'
                            ]
                        ],
                    ],
                    'links' => [
                        'self' => '/pageComments/1'
                    ]
                ]
            ]);
        $this->get('/pageComments/2')
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'id' => '2',
                    'type' => 'pageComments',
                    'attributes' => [
                        'content' => '<span>I know better</span>'
                    ],
                    'relationships' => [
                        'user' => [
                            'links' => [
                                'related' => '/pageComments/2/user',
                                'self' => '/pageComments/2/relationships/user'
                            ]
                        ],
                        'page' => [
                            'links' => [
                                'related' => '/pageComments/2/page',
                                'self' => '/pageComments/2/relationships/page'
                            ]
                        ],
                    ],
                    'links' => [
                        'self' => '/pageComments/2'
                    ]
                ]
            ]);

        $this->get('/pageComments/3')
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'id' => '3',
                    'type' => 'pageComments',
                    'attributes' => [
                        'content' => '<span>I think he is right</span>'
                    ],
                    'relationships' => [
                        'user' => [
                            'links' => [
                                'related' => '/pageComments/3/user',
                                'self' => '/pageComments/3/relationships/user'
                            ]
                        ],
                        'page' => [
                            'links' => [
                                'related' => '/pageComments/3/page',
                                'self' => '/pageComments/3/relationships/page'
                            ]
                        ],
                    ],
                    'links' => [
                        'self' => '/pageComments/3'
                    ]
                ]
            ]);
    }

    public function testIncludeUserRoles()
    {
        $user = $this->actingAsUser();
        $user->addRoles(Role::moderator());
        $this->em()->flush();

        $this->get('/users/1?include=roles')
            ->assertStatus(200)
            ->assertExactJson([
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
                            'self' => '/roles/2',
                        ]
                    ],
                    [
                        'id' => '3',
                        'type' => 'roles',
                        'attributes' => [
                            'name' => 'Moderator',
                        ],
                        'links' => [
                            'self' => '/roles/3',
                        ]
                    ],
                ]
            ]);
    }

    public function testIncludePageUserAndUserRoles()
    {
        $this->actingAsModerator();
        $this->get('/pages/1?include=user,user.roles')
            ->assertStatus(403)
            ->assertExactJson([
                'errors' => []
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
                        'content' => '<strong>JSON:API</strong>'
                    ],
                    'relationships' => [
                        'user' => [
                            'data' => [
                                'id' => '1',
                                'type' => 'users'
                            ],
                            'links' => [
                                'related' => '/pages/1/user',
                                'self' => '/pages/1/relationships/user'
                            ]
                        ]
                    ],
                    'links' => [
                        'self' => '/pages/1'
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
                            'self' => '/roles/2',
                        ]
                   ],
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
                                   [
                                       'id' => '2',
                                       'type' => 'roles',
                                   ],
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
                   ]
                ]
            ]);
    }

    public function testMetafieldInclude(): void
    {
        $this->actingAsUser();

        $this->get("/users/1?meta[users]=random")
            ->assertJsonStructure([
                'data' => [
                    'meta' => [
                        'random'
                    ]
                ]
            ]);
    }
}
