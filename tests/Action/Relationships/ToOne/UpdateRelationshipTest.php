<?php

namespace Tests\Action\Relationships\ToOne;

use App\Entities\User;
use Tests\TestCase;

class UpdateRelationshipTest extends TestCase
{
    public function test_authorization_permissions_for_no_logged_in()
    {
        $data = ['data' => ['type' => 'users', 'id' => User::ROOT_ID]];

        $this->patch('/pages/1/relationships/user', $data)->assertStatus(403);
    }

    public function test_authorization_permissions_for_user_role()
    {
        $this->actingAsUser();
        $data = ['data' => ['type' => 'users', 'id' => User::ROOT_ID]];

        $this->patch('/pages/1/relationships/user', $data)->assertStatus(403);
    }

    public function test_authorization_permissions_for_moderator_role()
    {
        $this->actingAsModerator();
        $data = ['data' => ['type' => 'users', 'id' => User::ROOT_ID]];

        $this->patch('/pages/1/relationships/user', $data)->assertStatus(200);
    }

    public function test_authorization_permissions_for_root_role()
    {
        $this->actingAsRoot();
        $data = ['data' => ['type' => 'users', 'id' => User::ROOT_ID]];

        $this->patch('/pages/1/relationships/user', $data)->assertStatus(200);
    }

    public function test_update_page_user_relationship_response()
    {
        $this->actingAsModerator();
        $data = ['data' => ['type' => 'users', 'id' => User::ROOT_ID]];

        $response = $this->patch('/pages/1/relationships/user', $data);

        $response->assertExactJson([
            'data' => [
                'id' => User::ROOT_ID,
                'type' => 'users',
                'links' => [
                    'self' => '/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d',
                ],
            ],
        ]);
    }
}
