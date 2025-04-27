<?php

namespace Tests\App\Entities;

use Tests\TestCase;

class UserConfigTest extends TestCase
{
    public function test_user_config_properties_can_be_set_and_retrieved(): void
    {
        $user = entity(User::class)->make();
        $config = $user->getConfig();
        $this->assertSame($user, $config->getUser());
        $this->assertEquals('light', $config->getTheme());
        $this->assertTrue($config->isNotificationsEnabled());
        $this->assertEquals('en', $config->getLanguage());
    }

    public function test_user_config_resource_type_and_id(): void
    {
        $user = entity(User::class)->make(['id' => 'abc-123']);
        $config = $user->getConfig();
        $this->assertEquals('user-configs', $config::getResourceType());
        $this->assertEquals('abc-123', $config->getId());
    }
}
