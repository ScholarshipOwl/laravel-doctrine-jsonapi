<?php

namespace Tests\Action\Resource;

use Illuminate\Support\Facades\Route;
use Sowl\JsonApi\Action\Resource\RemoveResource;
use Sowl\JsonApi\AbstractRequest;
use Sowl\JsonApi\JsonApiResponse;
use Tests\App\Actions\User\RemoveUserRequest;
use Tests\App\Entities\User;
use Tests\App\Transformers\UserTransformer;
use Tests\TestCase;

class RemoveResourceTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Route::delete('/users/{id}', function (RemoveUserRequest $request) {
            return (new RemoveResource())
                ->dispatch($request);
        });
    }

    public function testAuthorizationPermissionsForNoLoggedIn()
    {
        $this->delete('/users/1')->assertStatus(JsonApiResponse::HTTP_FORBIDDEN);
        $this->delete('/users/2')->assertStatus(JsonApiResponse::HTTP_FORBIDDEN);
        $this->delete('/users/2')->assertStatus(JsonApiResponse::HTTP_FORBIDDEN);

        $this->em()->clear();
        $this->assertNotNull($this->em()->find(User::class, 1));
        $this->assertNotNull($this->em()->find(User::class, 2));
        $this->assertNotNull($this->em()->find(User::class, 3));
    }

    public function testAuthorizationPermissionsForUserRole()
    {
        $this->actingAsUser();

        $this->delete('/users/1')->assertStatus(JsonApiResponse::HTTP_NO_CONTENT);
        $this->delete('/users/2')->assertStatus(JsonApiResponse::HTTP_FORBIDDEN);
        $this->delete('/users/2')->assertStatus(JsonApiResponse::HTTP_FORBIDDEN);

        $this->em()->clear();
        $this->assertNull($this->em()->find(User::class, 1));
        $this->assertNotNull($this->em()->find(User::class, 2));
        $this->assertNotNull($this->em()->find(User::class, 3));
    }

    public function testAuthorizationPermissionsForModeratorRole()
    {
        $this->actingAsModerator();

        $this->delete('/users/1')->assertStatus(JsonApiResponse::HTTP_FORBIDDEN);
        $this->delete('/users/2')->assertStatus(JsonApiResponse::HTTP_FORBIDDEN);
        $this->delete('/users/3')->assertStatus(JsonApiResponse::HTTP_NO_CONTENT);

        $this->em()->clear();
        $this->assertNotNull($this->em()->find(User::class, 1));
        $this->assertNotNull($this->em()->find(User::class, 2));
        $this->assertNull($this->em()->find(User::class, 3));
    }

    public function testAuthorizationPermissionsForRootRole()
    {
        $this->actingAsRoot();

        $this->delete('/users/1')->assertStatus(JsonApiResponse::HTTP_NO_CONTENT);
        $this->delete('/users/2')->assertStatus(JsonApiResponse::HTTP_NO_CONTENT);
        $this->delete('/users/3')->assertStatus(JsonApiResponse::HTTP_NO_CONTENT);

        $this->em()->clear();
        $this->assertNull($this->em()->find(User::class, 1));
        $this->assertNull($this->em()->find(User::class, 2));
        $this->assertNull($this->em()->find(User::class, 3));
    }
}
