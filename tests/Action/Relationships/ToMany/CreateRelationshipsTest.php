<?php

namespace Tests\Action\Relationships\ToMany;

use App\Entities\Role;
use Tests\TestCase;

class CreateRelationshipsTest extends TestCase
{
    public function test_authorization_permissions_for_no_logged_in()
    {
        $this->post('/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b/relationships/roles')->assertStatus(403);
        $this->post('/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d/relationships/roles')->assertStatus(403);
        $this->post('/users/ccf660b9-3cf7-4f58-a5f7-22e53ad836f8/relationships/roles')->assertStatus(403);
    }

    public function test_authorization_permissions_for_user_role()
    {
        $this->actingAsUser();

        $this->post('/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b/relationships/roles')->assertStatus(403);
        $this->post('/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d/relationships/roles')->assertStatus(403);
        $this->post('/users/ccf660b9-3cf7-4f58-a5f7-22e53ad836f8/relationships/roles')->assertStatus(403);
    }

    public function test_authorization_permissions_for_root_role()
    {
        $this->actingAsRoot();

        $data = [
            'data' => [
                ['type' => 'roles', 'id' => Role::moderator()],
            ],
        ];

        $this->post('/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b/relationships/roles', $data)->assertStatus(200);
        $this->post('/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d/relationships/roles', $data)->assertStatus(200);
        $this->post('/users/ccf660b9-3cf7-4f58-a5f7-22e53ad836f8/relationships/roles', $data)->assertStatus(200);
    }

    public function test_assign_a_new_role_to_user()
    {
        $this->actingAsRoot();

        $response = $this->post('/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b/relationships/roles', [
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
                        'self' => '/roles/2',
                    ],
                ],
                [
                    'id' => '3',
                    'type' => 'roles',
                    'links' => [
                        'self' => '/roles/3',
                    ],
                ],
            ],
        ]);
    }
}
