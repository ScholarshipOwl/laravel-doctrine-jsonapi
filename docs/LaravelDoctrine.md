# Laravel Doctrine
Guide for how to set up Laravel Doctrine to be ready to work with the Doctrine JSON:API package.

[http://laraveldoctrine.org/](http://laraveldoctrine.org/)

## Installation
You can see full documentation on the official Laravel Doctrine package.
[http://laraveldoctrine.org/docs/1.8/orm/installation](http://laraveldoctrine.org/docs/1.8/orm/installation)


For simple installation you need to run:
```shell
composer require -W laravel-doctrine/orm:^1.8
```

Publish the config file
```shell
php artisan vendor:publish --tag="config" --provider="LaravelDoctrine\ORM\DoctrineServiceProvider"
```

### Doctrine Migrations Package
We suggest to install Laravel Doctrine Migrations package for any migrations you need to run.

[http://laraveldoctrine.org/docs/1.8/migrations](http://laraveldoctrine.org/docs/1.8/migrations)

Install the package
```shell
composer require -W laravel-doctrine/migrations
```

To publish the migrations config use:
```shell
php artisan vendor:publish --tag="config" --provider="LaravelDoctrine\Migrations\MigrationsServiceProvider"
```

### Doctrine Extensions package
Install this package with composer:

[http://www.laraveldoctrine.org/docs/1.8/extensions](http://www.laraveldoctrine.org/docs/1.8/extensions)

```shell
composer require laravel-doctrine/extensions
```

To include Gedmo extensions install them:

```shell
composer require "gedmo/doctrine-extensions=^3.0"
```

Enable the `TimestampableExtension` feature in the `app/doctrine.php`.
```shell
'extensions' => [
    //LaravelDoctrine\ORM\Extensions\TablePrefix\TablePrefixExtension::class,
    LaravelDoctrine\Extensions\Timestamps\TimestampableExtension::class,
    //LaravelDoctrine\Extensions\SoftDeletes\SoftDeleteableExtension::class,
    //LaravelDoctrine\Extensions\Sluggable\SluggableExtension::class,
    //LaravelDoctrine\Extensions\Sortable\SortableExtension::class,
    //LaravelDoctrine\Extensions\Tree\TreeExtension::class,
    //LaravelDoctrine\Extensions\Loggable\LoggableExtension::class,
    //LaravelDoctrine\Extensions\Blameable\BlameableExtension::class,
    //LaravelDoctrine\Extensions\IpTraceable\IpTraceableExtension::class,
    //LaravelDoctrine\Extensions\Translatable\TranslatableExtension::class
],
```
## Setup
Lets setup basic `User` entity that going to be used for the authorization.

Create a new file `App/Entities/User.php` this will be our `User` entity.
The entity must replace the default `App\Models\User`.

See file example: [./examples/User.php](./examples/User.php)

Remove the folder `app/Models` you're not going to use it with Doctrine.
Our entities\models folder will be `app/Entities`.

### Setup auth
Change the driver and model for the authentication model in the `config/auth.php` file.

```php
...
'providers' => [
    'users' => [
        'driver' => 'doctrine',
        'model' => \App\Entities\User::class,
    ],
]
...
```

Replace Laravel's PasswordResetServiceProvider in `config/app.php` by `LaravelDoctrine\ORM\Auth\Passwords\PasswordResetServiceProvider`.

### Setup database
Please review and setup doctrine configuration file `config/doctrine.php` for DB connection.

#### Migrations
We set up the `laravel-doctrine/migrations` package in previous steps, now we need to set it up.

Please review the `config/migrations.php` configurations file, to be familiar with configuration options.

First lets delete the default Laravel migrations from the `database/migrations` folder.
```shell
rm -rf ./database/migrations/*.php
```

Automatic generation of the migrations by finding out the difference between current DB schema and entities metadata
will drastically simplify the development processes and DB maintainability.

You can automatically generate schema migrations by running next command:
```shell
php artisan doctrine:migrations:diff
```

New migration file in `database/migrations` will be generated.

Now you can run Doctrine migrations:
```shell
php artisan doctrine:migrations:migrate
```

#### Testing
Laravel Doctrine ORM provides helpers for entities generation in tests same as Factories in the default Laravel.

The factory depends on next package, install it if you want to use it:
```shell
composer require fzaninotto/faker --dev
```

Delete default factory and seeder from the `database/factories` and `database/seeders`.

Follow this documentation for creating new factories and seeders.
[http://laraveldoctrine.org/docs/1.8/orm/testing](http://laraveldoctrine.org/docs/1.8/orm/testing)

### Queue
Add `FailedJobsServiceProvider` to `config/app.php` providers so that we could set up "failed_jobs" table with doctrine.
```php
\LaravelDoctrine\ORM\Queue\FailedJobsServiceProvider::class
```