# Laravel Doctrine JSON:API
Implement feature-rich [JSON:API](https://jsonapi.org/) compliant APIs
in your [Laravel](https://laravel.com/) applications using [Doctrine ORM](https://www.doctrine-project.org/).

## Features
- Built for Doctrine ORM
- Battle-tested
- Standardised, consistent APIs
- Fetch resources
- Fetch relationships
- Inclusion of related resources (compound documents)
- Sparse field sets
- Sorting
- Pagination
- Filtering
- Create resources
- Update resources
- Update relationships
- Delete resources

## Installation & Config
1. [Laravel](./Installation.md#laravel)
2. [Doctrine Setup](./Installation.md#doctrine-setup)
3. [Package Installation](./Installation.md#package-installation)
4. [Package Config](./Installation.md#package-config)

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
