<?php

namespace Tests\Action\Relationships\ToMany;

use Tests\App\Entities\Role;
use Tests\TestCase;

class RemoveRelationshipsTest extends TestCase
{
    public function test_authorization_permissions_for_no_logged_in()
    {
        $data = ['data' => [
            ['type' => 'roles', 'id' => (string) Role::user()->getId()],
        ]];

        $this->delete('/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b/relationships/roles', $data)->assertStatus(403);
        $this->delete('/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d/relationships/roles', $data)->assertStatus(403);
        $this->delete('/users/ccf660b9-3cf7-4f58-a5f7-22e53ad836f8/relationships/roles', $data)->assertStatus(403);
    }

    public function test_remove_user_role_by_root_response()
    {
        $this->actingAsRoot();

        $data = [
            'data' => [
                ['type' => 'roles', 'id' => (string) Role::moderator()->getId()],
            ],
        ];

        $response = $this->delete('/users/ccf660b9-3cf7-4f58-a5f7-22e53ad836f8/relationships/roles', [
            'data' => [
                ['type' => 'roles', 'id' => (string) Role::root()->getId()],
            ],
        ]);

        $response
            ->assertStatus(422)
            ->assertExactJson([
                'errors' => [
                    [
                        'code' => 422,
                        'detail' => 'User don\'t have assigned role "Root"',
                        'source' => [
                            'pointer' => '/data/0',
                        ],
                    ],
                ],
            ]);

        $this->delete('/users/ccf660b9-3cf7-4f58-a5f7-22e53ad836f8/relationships/roles', $data)->assertStatus(204);
    }
}
