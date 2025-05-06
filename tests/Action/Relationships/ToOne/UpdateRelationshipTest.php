<?php

namespace Tests\Action\Relationships\ToOne;

use App\Entities\User;
use Tests\TestCase;

class UpdateRelationshipTest extends TestCase
{
    public function testAuthorizationPermissionsForNoLoggedIn(): void
    {
        $data = ['data' => ['type' => 'users', 'id' => User::ROOT_ID]];

        $this->patch('/api/pages/1/relationships/user', $data)->assertStatus(403);
    }

    public function testAuthorizationPermissionsForUserRole(): void
    {
        $this->actingAsUser();
        $data = ['data' => ['type' => 'users', 'id' => User::ROOT_ID]];

        $this->patch('/api/pages/1/relationships/user', $data)->assertStatus(403);
    }

    public function testAuthorizationPermissionsForModeratorRole(): void
    {
        $this->actingAsModerator();
        $data = ['data' => ['type' => 'users', 'id' => User::ROOT_ID]];

        $this->patch('/api/pages/1/relationships/user', $data)->assertStatus(200);
    }

    public function testAuthorizationPermissionsForRootRole(): void
    {
        $this->actingAsRoot();
        $data = ['data' => ['type' => 'users', 'id' => User::ROOT_ID]];

        $this->patch('/api/pages/1/relationships/user', $data)->assertStatus(200);
    }

    public function testUpdatePageUserRelationshipResponse(): void
    {
        $this->actingAsModerator();
        $data = ['data' => ['type' => 'users', 'id' => User::ROOT_ID]];

        $response = $this->patch('/api/pages/1/relationships/user', $data);

        $response->assertExactJson([
            'data' => [
                'id' => User::ROOT_ID,
                'type' => 'users',
                'links' => [
                    'self' => '/api/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d',
                ],
            ],
        ]);
    }
}
