<?php

namespace Tests\App\Entities;

use Tests\TestCase;
use Tests\App\Entities\User;
use Tests\App\Entities\UserConfig;
use Illuminate\Support\Facades\App;

class UserConfigTest extends TestCase
{
    public function testUserConfigPropertiesCanBeSetAndRetrieved(): void
    {
        $user = entity(User::class)->make();
        $config = $user->getConfig();
        $this->assertSame($user, $config->getUser());
        $this->assertEquals('light', $config->getTheme());
        $this->assertTrue($config->isNotificationsEnabled());
        $this->assertEquals('en', $config->getLanguage());
    }

    public function testUserConfigResourceTypeAndId(): void
    {
        $user = entity(User::class)->make(['id' => 'abc-123']);
        $config = $user->getConfig();
        $this->assertEquals('user-configs', $config::getResourceType());
        $this->assertEquals('abc-123', $config->getId());
    }
}
