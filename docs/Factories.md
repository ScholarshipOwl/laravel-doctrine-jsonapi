# Entity Factories for JSON:API Documentation

This guide explains how to create and use entity factories for generating realistic documentation examples with Scribe in your JSON:API application.

## Introduction

Entity factories are essential for generating sample data for your API documentation. The JSON:API Scribe integration uses Laravel Doctrine's factory system to create realistic entities that are transformed into JSON:API responses.

> For comprehensive information about Laravel Doctrine's entity factories, refer to the [official documentation](https://laravel-doctrine-orm-official.readthedocs.io/en/latest/testing.html#entity-factories).

## Setting Up Entity Factories

### 1. Create Factory Files

Factory files should be placed in your application's `database/factories` directory. Each entity should have its own factory file.

Example factory file structure:
```
database/factories/
  ├── UserFactory.php
  ├── ArticleFactory.php
  ├── CommentFactory.php
  └── ...
```

### 2. Define Factory Structure

Each factory file should define how to create an instance of your entity with sample data:

```php
<?php
/** @var LaravelDoctrine\ORM\Testing\Factory $factory */

use App\Entities\User;
use App\Entities\Role;
use Doctrine\Common\Collections\ArrayCollection;

$factory->define(User::class, function (Faker\Generator $faker) {
    return [
        'id' => $faker->uuid,
        'name' => $faker->name,
        'email' => $faker->email,
        'password' => 'secret',
        // For entity relationships, use the entity() helper
        'status' => entity(UserStatus::class, 'active')->create(),
        // For collections, create ArrayCollection instances
        'roles' => new ArrayCollection([
            entity(Role::class)->create(),
        ]),
    ];
});

// You can also define named variants
$factory->defineAs(User::class, 'admin', function (Faker\Generator $faker) {
    return [
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'roles' => new ArrayCollection([
            entity(Role::class, 'admin')->create(),
        ]),
    ];
});
```

### 3. Using Factories in Seeders

Seeders can use factories to populate your database with test data:

```php
<?php

namespace Database\Seeders;

use Doctrine\ORM\EntityManager;
use App\Entities\User;
use App\Entities\Article;

class TestDataSeeder
{
    public function run(EntityManager $em): void
    {
        // Create a user with the default factory
        $user = entity(User::class)->create();
        
        // Create a user with a named factory variant
        $admin = entity(User::class, 'admin')->create();
        
        // Create an entity with custom attributes
        $article = entity(Article::class)->create([
            'title' => 'Custom Title',
            'author' => $user,
        ]);
        
        // Create multiple entities
        entity(Article::class, 5)->create([
            'author' => $admin,
        ]);
    }
}
```

## Best Practices for Documentation Factories

When creating factories for Scribe documentation, follow these best practices:

1. **Use realistic data**: The Faker library helps generate realistic data for your entities.

2. **Define relationships properly**: Ensure that relationships between entities are correctly defined.

3. **Create named variants**: Define named variants for common entity states (e.g., 'admin', 'regular', 'premium').

4. **Use consistent IDs**: For documentation, using predictable IDs can make examples easier to understand.

5. **Include all required fields**: Make sure all non-nullable fields are populated.

6. **Match validation rules**: The data generated should pass your application's validation rules.

## Real-World Examples

Here are examples from the test suite:

### User Factory

```php
<?php
/** @var LaravelDoctrine\ORM\Testing\Factory $factory */

use Tests\App\Entities\User;
use Tests\App\Entities\UserStatus;
use Tests\App\Entities\Role;
use Doctrine\Common\Collections\ArrayCollection;

$factory->define(User::class, function (Faker\Generator $faker) {
    return [
        'id' => $faker->uuid,
        'name' => $faker->name,
        'email' => $faker->email,
        'password' => 'secret',
        'status' => entity(UserStatus::class, 'active')->create(),
        'roles' => new ArrayCollection([
            Role::user(),
        ]),
    ];
});

$factory->defineAs(User::class, 'user', function (Faker\Generator $faker) {
    return [
        'id' => User::USER_ID,
        'name' => 'testing user1',
        'email' => 'test1email@test.com',
        'password' => 'secret',
        'status' => entity(UserStatus::class, 'active')->create(),
        'roles' => new ArrayCollection([
            Role::user(),
        ]),
    ];
});
```

### Page Factory

```php
<?php
/** @var LaravelDoctrine\ORM\Testing\Factory $factory */

use Tests\App\Entities\Page;
use Tests\App\Entities\User;

$factory->define(Page::class, function (Faker\Generator $faker) {
    return [
        'id' => $faker->uuid,
        'user' => entity(User::class)->create(),
        'title' => $faker->sentence,
        'content' => $faker->paragraphs(3, true),
    ];
});
```

## Integration with Scribe

The JSON:API Scribe strategies automatically use these factories to generate documentation examples:

1. The `UseJsonApiResourceResponseStrategy` creates entities using factories
2. These entities are transformed into JSON:API responses
3. The documentation shows realistic examples of your API's responses

No additional configuration is needed - just ensure your factories are properly defined.

## Additional Resources

- [Laravel Doctrine ORM Entity Factories Documentation](https://laravel-doctrine-orm-official.readthedocs.io/en/latest/testing.html#entity-factories)
- [Example factories in the test suite](../tests/laravel/database/factories)
- [Example seeders in the test suite](../tests/laravel/database/seeders)
