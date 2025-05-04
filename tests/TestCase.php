<?php

namespace Tests;

use App\Entities\User;
use Database\Seeders\SetUpSeeder;
use Knuckles\Scribe\Tools\ConsoleOutputUtils;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as TestbenchTestCase;
use Sowl\JsonApi\Testing\DoctrineRefreshDatabase;
use Sowl\JsonApi\Testing\InteractWithDoctrineDatabase;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\NullOutput;
use Tests\Helpers\WithEntityManagerTrait;

class TestCase extends TestbenchTestCase
{
    use DoctrineRefreshDatabase;
    use InteractWithDoctrineDatabase;
    use WithEntityManagerTrait;
    use WithWorkbench;

    protected function setUp(): void
    {
        parent::setUp();
        $this->refreshDoctrineDatabase();
        $this->interactsWithDoctrineDatabase();

        \Knuckles\Scribe\Tools\Globals::$shouldBeVerbose = true;
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // Reset Scribe debug output
        ConsoleOutputUtils::bootstrapOutput(new ConsoleOutput);
    }

    /**
     * Disable Scribe debug output, in tests where we expect debug output.
     */
    protected function noScribeDebugOutput(): void
    {
        ConsoleOutputUtils::bootstrapOutput(new NullOutput);
    }

    protected function seedDatabase(): void
    {
        $this->seed(SetUpSeeder::class);
    }

    protected function actingAsRoot(): User
    {
        $this->actingAs($user = $this->em()->find(User::class, User::ROOT_ID));

        return $user;
    }

    protected function actingAsUser(): User
    {
        $this->actingAs($user = $this->em()->find(User::class, User::USER_ID));

        return $user;
    }

    protected function actingAsModerator(): User
    {
        $this->actingAs($user = $this->em()->find(User::class, User::MODERATOR_ID));

        return $user;
    }
}
