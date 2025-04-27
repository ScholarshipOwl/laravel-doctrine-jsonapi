<?php

namespace Tests\Action\Resource;

use Ramsey\Uuid\Uuid;
use Tests\App\Entities\PageComment;
use Tests\App\Entities\Role;
use Tests\App\Entities\User;
use Tests\TestCase;

class CreateResourceTest extends TestCase
{
    public function test_create_new_user()
    {
        $response = $this->post('/users', [
            'data' => [
                'id' => Uuid::uuid4()->toString(),
                'type' => 'users',
                'attributes' => [
                    'name' => 'New user',
                    'email' => 'newuser@gmail.com',
                    'password' => 'secret',
                ],
            ],
        ]);

        $response->assertCreated();

        $this->em()->clear();
        $newUser = $this->em()->find(User::class, $response->json('data.id'));

        $this->assertTrue($newUser->hasRole(Role::user()));
    }

    public function test_cant_create_user_with_root_role()
    {
        $response = $this->post('/users', [
            'data' => [
                'attributes' => [
                    'name' => 'New user',
                    'email' => 'newuser@gmail.com',
                    'password' => 'secret',
                ],
                'relationships' => [
                    'roles' => [
                        'data' => [
                            ['type' => 'roles', 'id' => (string) Role::root()->getId()],
                        ],
                    ],
                ],
            ],
        ]);

        $response->assertCreated();

        $this->em()->clear();
        $newUser = $this->em()->find(User::class, $response->json('data.id'));

        $this->assertTrue($newUser->hasRole(Role::user()));
        $this->assertFalse($newUser->hasRole(Role::root()));
    }

    public function test_user_create_validation(): void
    {
        $response = $this->post('/users');
        $response->assertExactJson([
            'errors' => [
                [
                    'code' => 422,
                    'detail' => 'The name field is required.',
                    'source' => [
                        'pointer' => '/data/attributes/name',
                    ],
                ],
                [
                    'code' => 422,
                    'detail' => 'The password field is required.',
                    'source' => [
                        'pointer' => '/data/attributes/password',
                    ],
                ],
                [
                    'code' => 422,
                    'detail' => 'The email field is required.',
                    'source' => [
                        'pointer' => '/data/attributes/email',
                    ],
                ],
            ],
        ]);

        $response = $this->post('/users', [
            'data' => [
                'attributes' => [
                    'name' => 'New user',
                    'password' => 'secret',
                    'email' => 'not email',
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

    public function test_create_page_comment_with_user_provided_id()
    {
        $user = $this->actingAsUser();

        $response = $this->post('/pageComments', [
            'data' => [
                'id' => Uuid::uuid4()->toString(),
                'type' => 'pageComments',
                'attributes' => [
                    'content' => 'New comment',
                ],
                'relationships' => [
                    'page' => [
                        'data' => [
                            'type' => 'pages',
                            'id' => '1',
                        ],
                    ],
                    'user' => [
                        'data' => [
                            'type' => 'users',
                            'id' => $user->getId(),
                        ],
                    ],
                ],
            ],
        ]);

        $response->assertCreated();

        $this->em()->clear();
        $newComment = $this->em()->find(PageComment::class, $response->json('data.id'));

        $this->assertEquals('New comment', $newComment->getContent());
        $this->assertEquals(1, $newComment->getPage()->getId());
        $this->assertEquals($user->getId(), $newComment->getUser()->getId());
    }

    public function test_create_page_comment()
    {
        $user = $this->actingAsUser();

        $response = $this->post('/pageComments', [
            'data' => [
                'type' => 'pageComments',
                'attributes' => [
                    'content' => 'New comment',
                ],
                'relationships' => [
                    'page' => [
                        'data' => [
                            'type' => 'pages',
                            'id' => '1',
                        ],
                    ],
                    'user' => [
                        'data' => [
                            'type' => 'users',
                            'id' => $user->getId(),
                        ],
                    ],
                ],
            ],
        ]);

        $response->assertCreated();

        $this->em()->clear();
        $newComment = $this->em()->find(PageComment::class, $response->json('data.id'));

        $this->assertEquals('New comment', $newComment->getContent());
        $this->assertEquals(1, $newComment->getPage()->getId());
        $this->assertEquals($user->getId(), $newComment->getUser()->getId());
    }
}
