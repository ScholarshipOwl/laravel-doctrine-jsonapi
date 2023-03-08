# Laravel Doctrine JSON:API
Package for the [Laravel](https://laravel.com/) that allows developers to create [JSON:API](https://jsonapi.org/)
endpoints using the [Doctrine ORM](https://www.doctrine-project.org/) for data persistence.

It provides an easy-to-use API for building JSON:API responses and supports various features such as resource filtering,
sorting, pagination, and relationships. With this library, Laravel developers can quickly implement a JSON:API compliant
backend for their web or mobile applications.

### Setup
Please follow the [instructions](./Installation.md) and set up the package in your Laravel installation.

## Usage
Doctrine entities must implement [ResourceInterface](../src/ResourceInterface.php) to be used as JSON:API resource in endpoints and responses.
Each resource class must be added into the `resources` list in the [config/jsonpai.php](../config/jsonapi.php).

Follow [resource interface](./ResourceInterface.md) guide on how properly implement the interface.

### Policies
We must set up entity [policies](https://laravel.com/docs/10.x/authorization#creating-policies) so that API client will be authorized to access the resource.

Follow the [guide](./Policies.md) on how to set up the policies.

## API Testing
You can test the API with help of [Laravel HTTP Tests](https://laravel.com/docs/10.x/http-tests).

Create a new test `Tests\Feature\UserControllerTest` and append next test:
```php
public function test_view_user()
{
    /** @var User $user */
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