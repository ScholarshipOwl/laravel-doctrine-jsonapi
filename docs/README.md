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
            Route::prefix(config('jsonapi.routing.rootPathPrefix', 'jsonapi'))
                ->group(base_path('routes/jsonapi.php'));
        },
    )->create();
```

- You may add or customize routes in `routes/jsonapi.php` as needed for your application.
- The route prefix is configurable via `config/jsonapi.php` using the `routing.rootPathPrefix` key.

Run `php artisan route:list` to view available JSON:API endpoints.

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
