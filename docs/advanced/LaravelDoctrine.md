# Laravel Doctrine
[Official Documentation](http://laraveldoctrine.org/docs/1.8/orm/installation)

Installation guide, config and prerequisites.

## Installation
Install the package:
```shell
composer require -W laravel-doctrine/orm:^1.8
```

Add the ServiceProvider to the providers array in `config/app.php`:
```PHP
LaravelDoctrine\ORM\DoctrineServiceProvider::class,
```

Publish the config files:
```shell
php artisan vendor:publish --tag="config" --provider="LaravelDoctrine\ORM\DoctrineServiceProvider"
```

## Recommended Dependencies

We recommend to install the following packages:

### Doctrine Migrations
[Official Documentation](http://laraveldoctrine.org/docs/1.8/migrations)

Install the package:
```shell
composer require -W laravel-doctrine/migrations
```

Add the ServiceProvider to the providers array in `config/app.php`:
```PHP
LaravelDoctrine\Migrations\MigrationsServiceProvider::class,
```

Publish the config files:
```shell
php artisan vendor:publish --tag="config" --provider="LaravelDoctrine\Migrations\MigrationsServiceProvider"
```

### Doctrine Extensions
[Official Documentation](http://www.laraveldoctrine.org/docs/1.8/extensions)

Install the packages:
```shell
composer require laravel-doctrine/extensions
composer require "gedmo/doctrine-extensions=^3.0"
composer require "beberlei/doctrineextensions=^1.0"
```

Add the two ServiceProvider to the providers array in `config/app.php`:
```PHP
LaravelDoctrine\Extensions\GedmoExtensionsServiceProvider::class,
LaravelDoctrine\Extensions\BeberleiExtensionsServiceProvider::class,
```

Enable the `TimestampableExtension` extension in `config/doctrine.php`:
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

## Default Settings

### Database Connection Server Version
Update your connection config and add a `serverVersion` key in `config/database.php`:
```php
'connections' => [
    'mysql' => [
        'serverVersion' => '8.0',
    ],
],
```

Replace the version number by the version you use.

### User Entity
Create a default `User` entity to be used for authentication.

- Delete the `app/Models` directory.
- Create a new `App/Entities` directory.
- Create a new `App/Entities/User.php` file.

[User Entity Example](./examples/User.php)

### Authentication
Update the driver and model config in `config/auth.php`:
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

Replace Laravel default `PasswordResetServiceProvider` in `config/app.php`:
```php
...
'providers' => [
    //Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
    LaravelDoctrine\ORM\Auth\Passwords\PasswordResetServiceProvider::class,
]
...
```

### Queue
Add the `FailedJobsServiceProvider` in `config/app.php`:
```php
...
'providers' => [
    LaravelDoctrine\ORM\Queue\FailedJobsServiceProvider::class,
]
...
```

### Doctrine Configuration
Review and update to your needs the doctrine configuration `config/doctrine.php` file.

### Migrations
Migrations config options are available at `config/migrations.php`.

Delete the default Laravel migrations files:
```shell
rm -rf database/migrations/*.php
```

#### Generate Migrations
Doctrine provides a command to generate a migration by comparing project current database to mapping information.
It generates migration classes by changing entity mappings instead of manually adding modifications to migration class.

Generate the migration:
```shell
php artisan doctrine:migrations:diff
```

A new migration file is generated in `database/migrations`.

#### Migrate Migrations
Run the migration(s):
```shell
php artisan doctrine:migrations:migrate
```

The command executes a migration to a specified version or the latest available version.

### Testing
When testing or demonstrating your application you may need to insert some dummy data into the database.
To help with this Laravel Doctrine provides Entity Factories, which are similar to Laravel's Model Factories.
These allow you to define values for each property of your Entities and quickly generate many of them.

[Official Documentation](http://laraveldoctrine.org/docs/1.8/orm/testing)

Install the package:
```shell
composer require fzaninotto/faker --dev
```

Delete the default factory and seeder files:
```shell
rm -rf database/factories/*.php
rm -rf database/seeders/*.php
```