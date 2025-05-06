<?php

namespace Tests\Action\Resource;

use App\Entities\PageComment;
use App\Entities\Role;
use App\Entities\User;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class CreateResourceTest extends TestCase
{
    public function testCreateNewUser(): void
    {
        $response = $this->post('/api/users', [
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

    public function testCantCreateUserWithRootRole(): void
    {
        $response = $this->post('/api/users', [
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

    public function testUserCreateValidation(): void
    {
        $response = $this->post('/api/users');
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

        $response = $this->post('/api/users', [
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

    public function testCreatePageCommentWithUserProvidedId(): void
    {
        $user = $this->actingAsUser();

        $response = $this->post('/api/pageComments', [
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

    public function testCreatePageComment(): void
    {
        $user = $this->actingAsUser();

        $response = $this->post('/api/pageComments', [
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
