# PHPUnit

## Running
Run the tests in the docker environment by running next command:
```shell
docker compose run php phpunit
```

## Laravel
We install Laravel as composer dev dependency and putting all it's files into the [tests/laravel]() folder. Doctrine is set up in this laravel instance and some testing entities created.

## Artisan
Use next command and file `artisan` for running Laravel artisan for testign purposes.

```shell
php artisan ...
```

## Database
We are using SQLite in memory database. Each time we start the tests we apply migrations from the [./database/migrations](./database/migrations) folder. After change in the entities metadata we need to create a new schema in our migration folder.

Because we use in memory database for migrations only single file must stay in the migrations folder.

```shell
docker compose run -T --rm -u $(id -u) php rm -rf tests/laravel/database/migrations/Version*.php
docker compose run -T --rm -u $(id -u) php tests/artisan doctrine:migrations:diff --no-interaction
```

Command will generate full schema migration file please create an old one.

Congrats you can run your tests with a new schema.

### Seeders
You can find seeders in the [./database/seeders](./database/seeders) folder. You can run them in tests by using `seed` method. The initialization seeder is running on each test `setUp` after migrations refresh.

```php
$this->seed(\laravel\database\seeders\SetUpSeeder::class);
```

## Documentation
We can generate documentation for this test instance.

```shell
docker compose run php tests/artisan scribe:generate --no-upgrade-check --force
```

or 

```shell
composer run-script -d ../ test:scribe:generate
```


### Scalar
We suggest to render the Open API documentation with [Scalar](https://github.com/scalar/scalar).

For that you can open the [./scalar/index.html](./scalar/index.html) file using PHP Storm re-view feature.
