# Installation

Follow these steps to install Laravel Doctrine JSON:API in your Laravel project.

This documentation is suited for setting up a new project from scratch. Full setup from scratch including overrideing default Laravel models and controllers is not covered in this documentation.

We recommend starting from [Quickstart guide](quickstart.md) if you want to set up a new project quickly, without going into all the details of proper project setup.

## Requirements
- Laravel **12.0** recommended
- PHP **8.2** or higher

## 1. Install Laravel
If you don't have a Laravel project yet, create one:

```bash
composer create-project laravel/laravel:^12.0 laravel-jsonapi
```

## 2. Install Laravel Doctrine ORM
This package relies on [laravel-doctrine/orm](https://packagist.org/packages/laravel-doctrine/orm#1.8.x-dev) but it is possible to setup the Doctrine ORM integration manually, that's not recommended for begginers.

```bash
composer require laravel-doctrine/orm
```

We also recommend installing the following Laravel Doctrine packages to unlock advanced features:

- [laravel-doctrine/migrations](https://github.com/laravel-doctrine/migrations): Manage your database schema with Doctrine migrations.
- [laravel-doctrine/acl](https://github.com/laravel-doctrine/acl): Add RBAC (role-based access control) features to your application.
- [laravel-doctrine/extensions](https://github.com/laravel-doctrine/extensions): Provides additional features and extensions for Doctrine ORM.

Install them with:

```bash
composer require laravel-doctrine/migrations laravel-doctrine/acl laravel-doctrine/extensions
```

For detailed setup, see the [Laravel Doctrine Installation Guide](https://laravel-doctrine.github.io/docs/1.8/orm/installation.html).

## 3. Install Laravel Doctrine JSON:API
Add the package to your project:

```bash
composer require sowl/laravel-doctrine-jsonapi:dev-main
```

Publish the package configuration:

```bash
php artisan vendor:publish --provider="Sowl\JsonApi\JsonApiServiceProvider"
```

This will publish `config/jsonapi.php` and the ready-to-use route file at `routes/jsonapi.php`.

### Configure Routing
If you use Laravel 12, register the JSON:API routes in `bootstrap/app.php`:

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

## 4. Database & Entities
Configure your database connection in `.env` and `config/doctrine.php`. 

You will have to setup Doctrine entities, for example `User` that should implement many authentication needed interfaces according to the Laravel documentation.
Project skeleton have a good example of the authentication setup as well as basic project structure.

Implement your Doctrine entities and register them in `config/jsonapi.php` under the `resources` array.

### Migrate & Seed Database
Run migrations and seeders as needed:

```bash
php artisan doctrine:migrations:migrate
php artisan db:seed
```

## 5. Test Your API
- Run `php artisan route:list` to check available endpoints.
- Use tools like [Postman](https://www.postman.com/) or [HTTPie](https://httpie.io/) to interact with your JSON:API endpoints.

---

For advanced configuration, testing, and documentation generation, see the other guides in this documentation.