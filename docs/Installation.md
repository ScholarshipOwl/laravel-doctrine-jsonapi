# Installation
Follow this guide if you want to install this package in new\existing laravel installation.

Follow next steps one after another:
1. [Laravel](#laravel)
2. [Doctrine Setup](#doctrine-setup)
3. [Package config](#package-config)
4. [Verify installation](#verify-installation)

## Laravel
You must have Laravel new or existing installation.

If you want to start a new installation you can use next command:
```shell
composer create-project laravel/laravel:^9.0 laravel-jsonapi
```

## Doctrine Setup
This package using Doctrine ORM as its basic requirement.
You can use any library for integration of Doctrine into Laravel.

We suggest [laravel-doctrine/orm](https://packagist.org/packages/laravel-doctrine/orm#1.8.x-dev) package for integration
of Doctrine ORM into your laravel installation.

Follow guide if proposed installation:

[Laravel Doctrine Guide](./LaravelDoctrine.md)

## Package config
Install the package using composer
```shell
composer require sowl/laravel-doctrine-jsonapi
```

Publish package config into current Laravel installation.
```shell
php artisan vendor:publish --provider="Sowl\JsonApi\JsonApiServiceProvider"
```

Set up base `jsonapi` middleware and add routes loading into `RouteServiceProvider`.

Create or replace the default `api` middleware group with `jsonapi` in the `App\Http\Kernel`:
```php
    protected $middlewareGroups = [
        ...,
        'jsonapi' => [
            'throttle',
        ],
    ];
```

Open `App\Providers\RoutesServiceProvider` and append or replace the `api` route loading in the `boot` method.
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

## Verify installation
Run `php artisan route:list` to get list of available routes, it must have the JSON:API routes.