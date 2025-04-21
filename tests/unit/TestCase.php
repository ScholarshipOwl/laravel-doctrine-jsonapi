<?php

namespace Tests;

use Database\Seeders\SetUpSeeder;
use Doctrine\ORM\EntityManager;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Console\Kernel;
use Illuminate\Foundation\Testing\TestCase as LaravelTestCase;
use LaravelDoctrine\Migrations\MigrationsServiceProvider;
use LaravelDoctrine\ORM\DoctrineServiceProvider;
use Sowl\JsonApi\Testing\DoctrineRefreshDatabase;
use Sowl\JsonApi\Testing\InteractWithDoctrineDatabase;
use Tests\App\Entities\User;
use Tests\Helpers\WithEntityManagerTrait;

class TestCase extends LaravelTestCase
{
    use WithEntityManagerTrait;
    use DoctrineRefreshDatabase;
    use InteractWithDoctrineDatabase;

    protected Kernel $kernel;

    /**
     * @return Application
     */
    public function createApplication()
    {
        /** @var Application $app */
        $app = require realpath(__DIR__) . '/../laravel/bootstrap/app.php';

        $this->kernel = $app->make(Kernel::class);
        $this->kernel->bootstrap();

        $app->register(DoctrineServiceProvider::class);
        $app->register(MigrationsServiceProvider::class);

        $this->kernel->call('doctrine:migrations:refresh --no-interaction');

        $this->em = $app->make(EntityManager::class);

        return $app;
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->refreshDoctrineDatabase();
        $this->interactsWithDoctrineDatabase();
    }

    protected function afterRefreshingDoctrineDatabase(): void
    {
        $this->seed(SetUpSeeder::class);
    }

    protected function actingAsRoot(): User
    {
        $this->actingAs($user = $this->em->find(User::class, User::ROOT_ID));
        return $user;
    }

    protected function actingAsUser(): User
    {
        $this->actingAs($user = $this->em->find(User::class, User::USER_ID));
        return $user;
    }

    protected function actingAsModerator(): User
    {
        $this->actingAs($user = $this->em->find(User::class, User::MODERATOR_ID));
        return $user;
    }
}
