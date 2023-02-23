# Laravel Doctrine JSON:API
Set of tools for implementation JSON:API in Laravel framework using Doctrine ORM.

### Installation
Install using composer
```shell
composer require sowl/laravel-doctrine-jsonapi
```

### Development
Use `docker compose` for running PHPUnit tests even if your local PHP runtime version doesn't match library one.

To install dependencies and run the tests
```shell
docker compose run php
```

To get shell into Docker environment run
```shell
docker compose run php sh
```

### Testing
You can find all the testing documentation in the [./tests](./tests) folder.

### Roadmap
The list of proposed improvements to the library.
  - [ ] Create default global error handler or write down documentation how to create such one.
        How to handle missing route\endpoint 404 and internal 500 errors.
  - [x] Move authorization logic to the "FormRequest::authorize" action as this is the way Laravel want's to authorize requests.
  - [ ] Implement dynamic metadata import in the transformer.
  - [ ] Checkout option for adding include params like "include=roles:sort"  (TransformerAbstract.php:173)
  - [ ] Check the option to integrate the [Laravel Sanctum](https://laravel.com/docs/10.x/sanctum)
