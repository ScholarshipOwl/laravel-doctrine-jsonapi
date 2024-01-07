<?php

namespace Tests\Action\Relationships\ToOne;

use Tests\App\Entities\User;
use Tests\TestCase;

class UpdateRelationshipTest extends TestCase
{
    public function testAuthorizationPermissionsForNoLoggedIn()
    {
        $data = ['data' => ['type' => 'users', 'id' => User::ROOT_ID]];

        $this->patch('/pages/1/relationships/user', $data)->assertStatus(403);
    }

    public function testAuthorizationPermissionsForUserRole()
    {
        $this->actingAsUser();
        $data = ['data' => ['type' => 'users', 'id' => User::ROOT_ID]];

        $this->patch('/pages/1/relationships/user', $data)->assertStatus(403);
    }

    public function testAuthorizationPermissionsForModeratorRole()
    {
        $this->actingAsModerator();
        $data = ['data' => ['type' => 'users', 'id' => User::ROOT_ID]];

        $this->patch('/pages/1/relationships/user', $data)->assertStatus(200);
    }

    public function testAuthorizationPermissionsForRootRole()
    {
        $this->actingAsRoot();
        $data = ['data' => ['type' => 'users', 'id' => User::ROOT_ID]];

        $this->patch('/pages/1/relationships/user', $data)->assertStatus(200);
    }

    public function testUpdatePageUserRelationshipResponse()
    {
        $this->actingAsModerator();
        $data = ['data' => ['type' => 'users', 'id' => User::ROOT_ID]];

        $response = $this->patch('/pages/1/relationships/user', $data);

        $response->assertExactJson([
            'data' => [
                'id' => User::ROOT_ID,
                'type' => 'users',
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
            ],
        ]);
    }
}
