<?php

namespace Tests\Action\Resource;

use Sowl\JsonApi\Response;
use Tests\App\Entities\User;
use Tests\TestCase;

class RemoveResourceTest extends TestCase
{
    public function testAuthorizationPermissionsForNoLoggedIn()
    {
        $this->delete('/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b')->assertStatus(Response::HTTP_FORBIDDEN);
        $this->delete('/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d')->assertStatus(Response::HTTP_FORBIDDEN);
        $this->delete('/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d')->assertStatus(Response::HTTP_FORBIDDEN);

        $this->em()->clear();
        $this->assertNotNull($this->em()->find(User::class, User::USER_ID));
        $this->assertNotNull($this->em()->find(User::class, User::ROOT_ID));
        $this->assertNotNull($this->em()->find(User::class, User::MODERATOR_ID));
    }

    public function testAuthorizationPermissionsForUserRole()
    {
        $this->actingAsUser();

        $this->delete('/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b')->assertStatus(Response::HTTP_NO_CONTENT);
        $this->delete('/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d')->assertStatus(Response::HTTP_FORBIDDEN);
        $this->delete('/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d')->assertStatus(Response::HTTP_FORBIDDEN);

        $this->em()->clear();
        $this->assertNull($this->em()->find(User::class, User::USER_ID));
        $this->assertNotNull($this->em()->find(User::class, User::ROOT_ID));
        $this->assertNotNull($this->em()->find(User::class, User::MODERATOR_ID));
    }

    public function testAuthorizationPermissionsForModeratorRole()
    {
        $this->actingAsModerator();

        $this->delete('/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b')->assertStatus(Response::HTTP_FORBIDDEN);
        $this->delete('/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d')->assertStatus(Response::HTTP_FORBIDDEN);
        $this->delete('/users/ccf660b9-3cf7-4f58-a5f7-22e53ad836f8')->assertStatus(Response::HTTP_NO_CONTENT);

        $this->em()->clear();
        $this->assertNotNull($this->em()->find(User::class, User::USER_ID));
        $this->assertNotNull($this->em()->find(User::class, User::ROOT_ID));
        $this->assertNull($this->em()->find(User::class, User::MODERATOR_ID));
    }

    public function testAuthorizationPermissionsForRootRole()
    {
        $this->actingAsRoot();

        $this->delete('/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b')->assertStatus(Response::HTTP_NO_CONTENT);
        $this->delete('/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d')->assertStatus(Response::HTTP_NO_CONTENT);
        $this->delete('/users/ccf660b9-3cf7-4f58-a5f7-22e53ad836f8')->assertStatus(Response::HTTP_NO_CONTENT);

        $this->em()->clear();
        $this->assertNull($this->em()->find(User::class, User::USER_ID));
        $this->assertNull($this->em()->find(User::class, User::ROOT_ID));
        $this->assertNull($this->em()->find(User::class, User::MODERATOR_ID));
    }
}
