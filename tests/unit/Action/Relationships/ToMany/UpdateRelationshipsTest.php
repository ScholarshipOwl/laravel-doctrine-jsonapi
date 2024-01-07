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

        Route::patch('/{resourceType}/{id}/relationships/{relationship}',
            [Controller::class, 'updateRelationships']
        );
    }

    public function testAuthorizationPermissionsForNoLoggedIn()
    {
        $this->patch('/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b/relationships/roles')->assertStatus(403);
        $this->patch('/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d/relationships/roles')->assertStatus(403);
        $this->patch('/users/ccf660b9-3cf7-4f58-a5f7-22e53ad836f8/relationships/roles')->assertStatus(403);
    }

    public function testAuthorizationPermissionsForUserRole()
    {
        $this->actingAsUser();

        $this->patch('/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b/relationships/roles')->assertStatus(403);
        $this->patch('/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d/relationships/roles')->assertStatus(403);
        $this->patch('/users/ccf660b9-3cf7-4f58-a5f7-22e53ad836f8/relationships/roles')->assertStatus(403);
    }

    public function testAuthorizationPermissionsForRootRole()
    {
        $this->actingAsRoot();

        $data = [
            'data' => [
                ['type' => 'roles', 'id' => Role::moderator()],
            ]
        ];

        $this->patch('/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b/relationships/roles', $data)->assertStatus(200);
        $this->patch('/users/ccf660b9-3cf7-4f58-a5f7-22e53ad836f8/relationships/roles', $data)->assertStatus(200);

        // Do last as it's replaces root role
        $this->patch('/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d/relationships/roles', $data)->assertStatus(200);
    }

    public function testAssignANewRoleToUser()
    {
        $this->actingAsRoot();

        $response = $this->patch('/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b/relationships/roles', [
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
