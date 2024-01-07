<?php

namespace Tests\Action\Relationships\ToOne;

use Sowl\JsonApi\Response;
use Tests\App\Entities\User;
use Tests\TestCase;

class ShowRelatedTest extends TestCase
{
    public function testAuthorizationPermissionsAnyOneCanAccess()
    {
        $this->actingAsUser();
        $this->get('/pageComments/00000000-0000-0000-0000-000000000001/user')->assertStatus(Response::HTTP_OK);
        $this->get('/pageComments/00000000-0000-0000-0000-000000000001/page')->assertStatus(Response::HTTP_OK);
        $this->get('/pageComments/00000000-0000-0000-0000-000000000002/user')->assertStatus(Response::HTTP_OK);
        $this->get('/pageComments/00000000-0000-0000-0000-000000000002/page')->assertStatus(Response::HTTP_OK);
        $this->get('/pageComments/00000000-0000-0000-0000-000000000003/user')->assertStatus(Response::HTTP_OK);
        $this->get('/pageComments/00000000-0000-0000-0000-000000000003/page')->assertStatus(Response::HTTP_OK);
    }

    public function testNotFoundRelationship(): void
    {
        $this->get('/pageComments/00000000-0000-0000-0000-000000000001/notexists')->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testShowPageCommentsRelatedUserResponse()
    {
        $this->actingAsUser();
        $this->get('/pageComments/00000000-0000-0000-0000-000000000001/user')
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
                            'links' => [
                                'related' => '/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b/roles',
                                'self' => '/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b/relationships/roles'
                            ]
                        ]
                    ],
                    'links' => [
                        'self' => '/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b'
                    ]
                ]
            ]);

        $this->get('/pageComments/00000000-0000-0000-0000-000000000002/user')
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'id' => User::ROOT_ID,
                    'type' => 'users',
                    'attributes' => [
                        'email' => 'test2email@gmail.com',
                        'name' => 'testing user2',
                    ],
                    'relationships' => [
                        'roles' => [
                            'links' => [
                                'related' => '/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d/roles',
                                'self' => '/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d/relationships/roles'
                            ]
                        ]
                    ],
                    'links' => [
                        'self' => '/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d'
                    ]
                ]
            ]);

        $this->get('/pageComments/00000000-0000-0000-0000-000000000003/user')
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'id' => User::MODERATOR_ID,
                    'type' => 'users',
                    'attributes' => [
                        'email' => 'test3email@test.com',
                        'name' => 'testing user3',
                    ],
                    'relationships' => [
                        'roles' => [
                            'links' => [
                                'related' => '/users/ccf660b9-3cf7-4f58-a5f7-22e53ad836f8/roles',
                                'self' => '/users/ccf660b9-3cf7-4f58-a5f7-22e53ad836f8/relationships/roles'
                            ]
                        ]
                    ],
                    'links' => [
                        'self' => '/users/ccf660b9-3cf7-4f58-a5f7-22e53ad836f8'
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

        $this->get('/pageComments/00000000-0000-0000-0000-000000000001/page')
            ->assertStatus(200)
            ->assertExactJson($page1response);

        $this->get('/pageComments/00000000-0000-0000-0000-000000000002/page')
            ->assertStatus(200)
            ->assertExactJson($page1response);

        $this->get('/pageComments/00000000-0000-0000-0000-000000000003/page')
            ->assertStatus(200)
            ->assertExactJson($page1response);
    }
}
