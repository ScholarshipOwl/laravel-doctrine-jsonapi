# Laravel Doctrine JSON:API
Implement feature-rich [JSON:API](https://jsonapi.org/) compliant APIs
in your [Laravel](https://laravel.com/) applications using [Doctrine ORM](https://www.doctrine-project.org/).

## Installation & Config
Follow this guide to install this package in a new or existing laravel project.

### Laravel Installation
This package requires Laravel `>=9.0.0 <10.0.0`.

Install Laravel:
```shell
composer create-project laravel/laravel:^9.0 laravel-jsonapi
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

#### Middleware
Add a new application middleware group in the `app/Http/Kernel.php` file:
```php
protected $middlewareGroups = [
    'jsonapi' => [
        'throttle',
    ],
];
```

#### Route
Add a route configuration in the `app/Providers/RouteServiceProvider.php` file:
```php
public function boot()
{
    $this->routes(function () {
        Route::middleware('jsonapi')
            ->prefix('jsonapi')
            ->group(base_path('routes/jsonapi.php'));

        Route::middleware('web')
            ->group(base_path('routes/web.php'));
    });
}
```

Run `php artisan route:list` to get the list of available routes.

JSON:API routes should be included.

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
