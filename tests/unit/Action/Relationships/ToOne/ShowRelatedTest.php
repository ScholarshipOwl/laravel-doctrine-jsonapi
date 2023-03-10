<?php

namespace Tests\Action\Relationships\ToOne;

use Sowl\JsonApi\Response;
use Tests\TestCase;

class ShowRelatedTest extends TestCase
{
    public function testAuthorizationPermissionsAnyOneCanAccess()
    {
        $this->actingAsUser();
        $this->get('/pageComments/1/user')->assertStatus(Response::HTTP_OK);
        $this->get('/pageComments/1/page')->assertStatus(Response::HTTP_OK);
        $this->get('/pageComments/2/user')->assertStatus(Response::HTTP_OK);
        $this->get('/pageComments/2/page')->assertStatus(Response::HTTP_OK);
        $this->get('/pageComments/3/user')->assertStatus(Response::HTTP_OK);
        $this->get('/pageComments/3/page')->assertStatus(Response::HTTP_OK);
    }

    public function testShowPageCommentsRelatedUserResponse()
    {
        $this->actingAsUser();
        $this->get('/pageComments/1/user')
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

        $this->get('/pageComments/2/user')
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

        $this->get('/pageComments/3/user')
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

    public function testShowPageCommentsRelatedPage()
    {
        $this->actingAsUser();
        $page1response = [
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
        ];

        $this->get('/pageComments/1/page')
            ->assertStatus(200)
            ->assertExactJson($page1response);

        $this->get('/pageComments/2/page')
            ->assertStatus(200)
            ->assertExactJson($page1response);

        $this->get('/pageComments/3/page')
            ->assertStatus(200)
            ->assertExactJson($page1response);
    }
}
