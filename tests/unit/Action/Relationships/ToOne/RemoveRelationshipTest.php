<?php

namespace Tests\Action\Relationships\ToOne;

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

        $data = ['data' => ['type' => 'users', 'id' => '2']];

        $this->delete('/pages/1/relationships/user', $data)->assertStatus(204);
    }
}
