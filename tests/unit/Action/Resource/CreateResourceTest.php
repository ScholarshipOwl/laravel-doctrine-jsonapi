<?php

namespace Tests\Action\Resource;

use Tests\App\Entities\Role;
use Tests\App\Entities\User;
use Tests\TestCase;

class CreateResourceTest extends TestCase
{
    public function testCreateNewUser()
    {
        $response = $this->post('/users', [
            'data' => [
                'attributes' => [
                    'name' => 'New user',
                    'email' => 'newuser@gmail.com',
                    'password' => 'secret',
                ]
            ]
        ]);

        $response->assertCreated();

        $this->em()->clear();
        $newUser = $this->em()->find(User::class, $response->json('data.id'));

        $this->assertTrue($newUser->hasRole(Role::user()));
    }

    public function testCantCreateUserWithRootRole()
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
                        ]
                    ]
                ]
            ]
        ]);

        $response->assertCreated();

        $this->em()->clear();
        $newUser = $this->em()->find(User::class, $response->json('data.id'));

        $this->assertTrue($newUser->hasRole(Role::user()));
        $this->assertFalse($newUser->hasRole(Role::root()));
    }

    public function testUserCreateValidation(): void
    {
        $response = $this->post('/users');
        $response->assertExactJson([
            'errors' => [
                [
                    'code' => 422,
                    'detail' => 'The name field is required.',
                    'source' => [
                        'pointer' => '/data/attributes/name'
                    ],
                ],
                [
                    'code' => 422,
                    'detail' => 'The password field is required.',
                    'source' => [
                        'pointer' => '/data/attributes/password'
                    ],
                ],
                [
                    'code' => 422,
                    'detail' => 'The email field is required.',
                    'source' => [
                        'pointer' => '/data/attributes/email'
                    ],
                ],
            ]
        ]);

        $response = $this->post('/users', [
            'data' => [
                'attributes' => [
                    'name' => 'New user',
                    'password' => 'secret',
                    'email' => 'not email',
                ]
            ]
        ]);

        $response->assertExactJson([
            'errors' => [
                [
                    'code' => 422,
                    'detail' => 'The email field must be a valid email address.',
                    'source' => [
                        'pointer' => '/data/attributes/email'
                    ],
                ]
            ]
        ]);
    }
}
