<?php

namespace Tests\Entities;

use App\Entities\User;
use Tests\TestCase;

class UserConfigTest extends TestCase
{
    public function testUserConfigPropertiesCanBeSetAndRetrieved(): void
    {
        $user = entity(User::class)->create();
        $config = $user->getConfig();
        $this->assertSame($user, $config->getUser());
        $this->assertEquals('light', $config->getTheme());
        $this->assertTrue($config->isNotificationsEnabled());
        $this->assertEquals('en', $config->getLanguage());
    }

    public function testUserConfigResourceTypeAndId(): void
    {
        $user = entity(User::class)->create(['id' => 'abc-123']);
        $config = $user->getConfig();
        $this->assertEquals('userConfigs', $config::getResourceType());
        $this->assertEquals('abc-123', $config->getId());
    }
}
