<?php

namespace Tests\Action\Relationships\ToMany;

use App\Entities\Role;
use Tests\TestCase;

class CreateRelationshipsTest extends TestCase
{
    public function testAuthorizationPermissionsForNoLoggedIn(): void
    {
        $this->post('/api/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b/relationships/roles', [
            'data' => [
                ['type' => 'roles', 'id' => Role::moderator()],
            ],
        ])->assertStatus(403);
        $this->post('/api/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d/relationships/roles', [
            'data' => [
                ['type' => 'roles', 'id' => Role::moderator()],
            ],
        ])->assertStatus(403);
        $this->post('/api/users/ccf660b9-3cf7-4f58-a5f7-22e53ad836f8/relationships/roles', [
            'data' => [
                ['type' => 'roles', 'id' => Role::moderator()],
            ],
        ])->assertStatus(403);
    }

    public function testAuthorizationPermissionsForUserRole(): void
    {
        $this->actingAsUser();

        $this->post('/api/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b/relationships/roles', [
            'data' => [
                ['type' => 'roles', 'id' => Role::moderator()],
            ],
        ])->assertStatus(403);
        $this->post('/api/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d/relationships/roles', [
            'data' => [
                ['type' => 'roles', 'id' => Role::moderator()],
            ],
        ])->assertStatus(403);
        $this->post('/api/users/ccf660b9-3cf7-4f58-a5f7-22e53ad836f8/relationships/roles', [
            'data' => [
                ['type' => 'roles', 'id' => Role::moderator()],
            ],
        ])->assertStatus(403);
    }

    public function testAuthorizationPermissionsForRootRole(): void
    {
        $this->actingAsRoot();

        $data = [
            'data' => [
                ['type' => 'roles', 'id' => Role::moderator()],
            ],
        ];

        $this->post('/api/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b/relationships/roles', $data)->assertStatus(200);
        $this->post('/api/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d/relationships/roles', $data)->assertStatus(200);
        $this->post('/api/users/ccf660b9-3cf7-4f58-a5f7-22e53ad836f8/relationships/roles', $data)->assertStatus(200);
    }

    public function testAssignANewRoleToUser(): void
    {
        $this->actingAsRoot();

        $response = $this->post('/api/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b/relationships/roles', [
            'data' => [
                ['type' => 'roles', 'id' => Role::moderator()],
            ],
        ]);

        $response->assertExactJson([
            'data' => [
                [
                    'id' => '2',
                    'type' => 'roles',
                    'links' => [
                        'self' => '/api/roles/2',
                    ],
                ],
                [
                    'id' => '3',
                    'type' => 'roles',
                    'links' => [
                        'self' => '/api/roles/3',
                    ],
                ],
            ],
        ]);
    }
}
