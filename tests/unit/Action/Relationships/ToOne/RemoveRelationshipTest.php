<?php

namespace Tests\Action\Relationships\ToOne;

use Tests\App\Entities\Role;
use Tests\TestCase;

class RemoveRelationshipsTest extends TestCase
{
    public function testAuthorizationPermissionsForNoLoggedIn()
    {
        $data = ['data' => ['type' => 'users', 'id' => '2']];

        $this->delete('/pages/1/relationships/user', $data)->assertStatus(403);
    }

    public function testRemovePageUserByRootRole()
    {
        $this->actingAsRoot();

        $this->delete('/pages/1/relationships/user')->assertStatus(404);
    }

    public function testJean()
    {
        $user = $this->actingAsUser();
        $user->addRole(Role::moderator());
        $this->em()->persist($user);
        $this->em()->flush();

        /* dd($user->getRoles()->count()); */

        $res = $this->get('/users/1/roles');
        /* $res = $this->get('/users/1/relationships/roles'); */
        dd(json_decode($res->getContent()));
    }
}
