<?php

namespace Tests\Action\Resource;

use App\Entities\User;
use Sowl\JsonApi\Response;
use Tests\TestCase;

class RemoveResourceTest extends TestCase
{
    public function test_authorization_permissions_for_no_logged_in()
    {
        $this->delete('/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b')->assertStatus(Response::HTTP_FORBIDDEN);
        $this->delete('/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d')->assertStatus(Response::HTTP_FORBIDDEN);
        $this->delete('/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d')->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseHas('users', ['id' => User::USER_ID]);
        $this->assertDatabaseHas('users', ['id' => User::ROOT_ID]);
        $this->assertDatabaseHas('users', ['id' => User::MODERATOR_ID]);
    }

    public function test_authorization_permissions_for_user_role()
    {
        $this->actingAsUser();

        $this->delete('/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b')->assertStatus(Response::HTTP_NO_CONTENT);
        $this->delete('/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d')->assertStatus(Response::HTTP_FORBIDDEN);
        $this->delete('/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d')->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseMissing('users', ['id' => User::USER_ID]);
        $this->assertDatabaseHas('users', ['id' => User::ROOT_ID]);
        $this->assertDatabaseHas('users', ['id' => User::MODERATOR_ID]);
    }

    public function test_authorization_permissions_for_moderator_role()
    {
        $this->actingAsModerator();

        $this->delete('/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b')->assertStatus(Response::HTTP_FORBIDDEN);
        $this->delete('/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d')->assertStatus(Response::HTTP_FORBIDDEN);
        $this->delete('/users/ccf660b9-3cf7-4f58-a5f7-22e53ad836f8')->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseHas('users', ['id' => User::USER_ID]);
        $this->assertDatabaseHas('users', ['id' => User::ROOT_ID]);
        $this->assertDatabaseMissing('users', ['id' => User::MODERATOR_ID]);
    }

    public function test_authorization_permissions_for_root_role()
    {
        $this->actingAsRoot();

        $this->delete('/users/'.User::USER_ID)->assertStatus(Response::HTTP_NO_CONTENT);
        $this->em()->clear();

        $this->delete('/users/'.User::ROOT_ID)->assertStatus(Response::HTTP_NO_CONTENT);
        $this->em()->clear();

        $this->delete('/users/'.User::MODERATOR_ID)->assertStatus(Response::HTTP_NO_CONTENT);
        $this->em()->clear();

        $this->assertDatabaseMissing('users', ['id' => User::USER_ID]);
        $this->assertDatabaseMissing('users', ['id' => User::ROOT_ID]);
        $this->assertDatabaseMissing('users', ['id' => User::MODERATOR_ID]);
    }
}
