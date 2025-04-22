# Laravel Doctrine JSON:API
Implement feature-rich [JSON:API](https://jsonapi.org/) compliant APIs
in your [Laravel](https://laravel.com/) applications using [Doctrine ORM](https://www.doctrine-project.org/).

## Installation & Config
Follow this guide to install this package in a new or existing laravel project.

### Laravel Installation
This package requires Laravel `>=9.0.0` but works with Laravel `^12.0`.

Install Laravel:
```shell
composer create-project laravel/laravel:^12.0 laravel-jsonapi
```

### Laravel Doctrine Installation
This package uses Doctrine ORM and the
[laravel-doctrine/orm](https://packagist.org/packages/laravel-doctrine/orm#1.8.x-dev) package
as default requirements.

[Laravel Doctrine Installation Guide](./LaravelDoctrine.md)

### Package Installation
Install the package:
```shell
composer require sowl/laravel-doctrine-jsonapi:dev-main
```

Add the ServiceProvider to the providers array in `config/app.php`:
```PHP
Sowl\JsonApi\JsonApiServiceProvider::class,
```

### Package Config
Publish the config files:
```shell
php artisan vendor:publish --provider="Sowl\JsonApi\JsonApiServiceProvider"
```

#### Route

This package publishes a ready-to-use route file at `routes/jsonapi.php`.

You are encouraged to add your own custom routes directly to this file. All default JSON:API routes are also defined here and handled by the package's controllers.

To enable JSON:API endpoints, register this file in your application's routing configuration. With Laravel 12.x, use the `withRouting` method in `bootstrap/app.php`:

```php
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::prefix(config('jsonapi.routing.rootPathPrefix', ''))
                ->name(config('jsonapi.routing.rootNamePrefix', 'jsonapi.'))
                ->middleware(config('jsonapi.routing.rootMiddleware'))
                ->group(base_path('routes/jsonapi.php'));
        },
    )->create();
```

- You may add or customize routes in `routes/jsonapi.php` as needed for your application.
- The route prefix is configurable via `config/jsonapi.php` using the `routing.rootPathPrefix` key.

Run `php artisan route:list` to view available JSON:API endpoints.

### Middleware Group

You must register the JSON:API middleware group before loading routes:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->group(
        'jsonapi',
        [
            // \Illuminate\Cookie\Middleware\EncryptCookies::class,
            // \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            // \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            // \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
            // \Illuminate\Routing\Middleware\SubstituteBindings::class,
            // 'auth.session',
        ]
    );
})
```

## Usage

### Entities/Resources
To be used as JSON:API resources, Doctrine entities must implement the [`Sowl\JsonApi\ResourceInterface`](/src/ResourceInterface.php).

[Interface Implementation Guide](./ResourceInterface.md)

List entities in the `resources` array in the `config/jsonapi.php` file.

```PHP
'resources' => [
    App\Entities\User::class,
]
```

### Policies
Entity policies are required to enforce API resources access through autorization.

[Policies Implementation Guide](./Policies.md)

### API Documentation

This package integrates with Scribe to generate API documentation automatically.

[Scribe Integration Guide](./Scribe.md)

### API Testing
Test your API with help of [Laravel HTTP Tests](https://laravel.com/docs/9.x/http-tests).

Feature test `Tests\Feature\UserControllerTest` example:
```php
public function test_view_user()
{
    $user = entity(User::class)->create();

    $this->json('get', '/jsonapi/users/'.$user->getId())->assertStatus(403);

    $this->actingAs($user);

    $this->json('get', '/jsonapi/users/'.$user->getId())
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => (string) $user->getId(),
                'type' => 'users'
            ]
        ]);
}

```

## Useful Traits for Doctrine Testing

This package provides several traits to simplify testing and database management when using Doctrine ORM in Laravel:

- **DoctrineRefreshDatabase**: Refreshes the database schema before each test using Doctrine migrations. Use this trait to ensure a clean schema for every test run.
- **DoctrineDatabaseTruncation**: Truncates all Doctrine-managed tables after each test. Useful for cleaning up data between tests without dropping the schema.
- **InteractWithDoctrineDatabase**: Syncs Doctrine's PDO connections with Laravel's database layer, enabling the use of Laravel's `assertDatabaseHas` and similar helpers with Doctrine entities.

To use these traits, simply add them to your test case classes as needed:

```php
use Sowl\JsonApi\Testing\DoctrineRefreshDatabase;
use Sowl\JsonApi\Testing\DoctrineDatabaseTruncation;
use Sowl\JsonApi\Testing\InteractWithDoctrineDatabase;

class MyTestCase extends TestCase
{
    use DoctrineRefreshDatabase;
    use DoctrineDatabaseTruncation;
    use InteractWithDoctrineDatabase;

    // ...
}
```

See the trait docblocks and source for more details on their capabilities and configuration.
