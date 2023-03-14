<?php

namespace Tests\Action\Relationships\ToOne;

use Sowl\JsonApi\Response;
use Tests\TestCase;

class ShowRelationshipTest extends TestCase
{
    public function testAuthorizationPermissionsAnyOneCanAccess()
    {
        $this->actingAsUser();
        $this->get('/page-comments/1/relationships/user')->assertStatus(Response::HTTP_OK);
        $this->get('/page-comments/1/relationships/page')->assertStatus(Response::HTTP_OK);
        $this->get('/page-comments/2/relationships/user')->assertStatus(Response::HTTP_OK);
        $this->get('/page-comments/2/relationships/page')->assertStatus(Response::HTTP_OK);
        $this->get('/page-comments/3/relationships/user')->assertStatus(Response::HTTP_OK);
        $this->get('/page-comments/3/relationships/page')->assertStatus(Response::HTTP_OK);
    }

    public function testShowPageCommentsRelatedUserResponse()
    {
        $this->actingAsUser();
        $this->get('/page-comments/1/relationships/user')
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'id' => '1',
                    'type' => 'users',
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

        $this->get('/page-comments/2/relationships/user')
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'id' => '2',
                    'type' => 'users',
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

        $this->get('/page-comments/3/relationships/user')
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'id' => '3',
                    'type' => 'users',
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

        $this->get('/page-comments/1/relationships/page')
            ->assertStatus(200)
            ->assertExactJson($page1response);

        $this->get('/page-comments/2/relationships/page')
            ->assertStatus(200)
            ->assertExactJson($page1response);

        $this->get('/page-comments/3/relationships/page')
            ->assertStatus(200)
            ->assertExactJson($page1response);
    }
}
