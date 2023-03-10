<?php

namespace Tests\Action\Relationships\ToMany;

use Illuminate\Support\Facades\Route;
use Tests\App\Entities\Role;
use Tests\TestCase;

class UpdateRelationshipsTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();


        Route::patch('/{resourceKey}/{id}/relationships/{relationship}', [Controller::class, 'updateRelationships']);
    }

    public function testAuthorizationPermissionsForNoLoggedIn()
    {
        $this->patch('/users/1/relationships/roles')->assertStatus(403);
        $this->patch('/users/2/relationships/roles')->assertStatus(403);
        $this->patch('/users/3/relationships/roles')->assertStatus(403);
    }

    public function testAuthorizationPermissionsForUserRole()
    {
        $this->actingAsUser();

        $this->patch('/users/1/relationships/roles')->assertStatus(403);
        $this->patch('/users/2/relationships/roles')->assertStatus(403);
        $this->patch('/users/3/relationships/roles')->assertStatus(403);
    }

    public function testAuthorizationPermissionsForRootRole()
    {
        $this->actingAsRoot();

        $data = [
            'data' => [
                ['type' => 'roles', 'id' => Role::moderator()],
            ]
        ];

        $this->patch('/users/1/relationships/roles', $data)->assertStatus(200);
        $this->patch('/users/3/relationships/roles', $data)->assertStatus(200);

        // Do last as it's replaces root role
        $this->patch('/users/2/relationships/roles', $data)->assertStatus(200);
    }

    public function testAssignANewRoleToUser()
    {
        $this->actingAsRoot();

        $response = $this->patch('/users/1/relationships/roles', [
            'data' => [
                ['type' => 'roles', 'id' => Role::moderator()],
            ]
        ]);

        $response->assertExactJson([
            'data' => [
                [
                    'id' => '2',
                    'type' => 'roles',
                    'links' => [
                        'self' => '/roles/2'
                    ],
                ],
                [
                    'id' => '3',
                    'type' => 'roles',
                    'links' => [
                        'self' => '/roles/3'
                    ],
                ],
            ]
        ]);
    }
}
