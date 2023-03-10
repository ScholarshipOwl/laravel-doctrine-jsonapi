# Resource Interface
To be used as JSON:API resources, Doctrine entities must implement the
[`Sowl\JsonApi\ResourceInterface`](/src/ResourceInterface.php).

The interface requires to implement the 4 following methods:

### getResourceType()
Defines the resource `type` as part of the JSON:API
[Resource Object](https://jsonapi.org/format/#document-resource-objects) standard.

```php
public static function getResourceType(): string
{
    return 'users';
}
```

### getId()
Returns the resource `id` as part of the JSON:API
[Resource Object](https://jsonapi.org/format/#document-resource-objects) stadard.

```php
public function getId(): int
{
    return $this->id;
}
```

### transformer()
Returns a new transformer instance.
```php
public static function transformer(): AbstractTransformer
{
    return new UserTransformer();
}
```

This Package uses [Fractal](https://fractal.thephpleague.com/) for serialization
of entities/resources as JSON:API responses.

[Fractal Transformers Documentation](https://fractal.thephpleague.com/transformers/)

Transformer classes should extend the
[`Sowl\JsonApi\AbstractTransformer`](/src/AbstractTransformer.php) class.

[User Transformer Example](./examples/UserTransformer.php)

### relationships()
Returns the resource relationships.

```php
public static function relationships(): RelationshipsCollection
{
    return new RelationshipsCollection();
}
```

This method must return an instance of
[`Sowl\JsonApi\Relationships\RelationshipsCollection`](src/Relationships/RelationshipsCollection.php).

Two types of relationships exist, `To-One` and `To-Many`.

#### To-One Relationship
Returns a single resource in the responses.

```php
// User Entity
public static function relationships(): RelationshipsCollection
{
    return new RelationshipsCollection([
        ToOneRelationship::create('country', Country::class)
    ]);
}
```

The `ToOneRelationship::create()` method accepts 3 parameters:

- `name` (required):

Name of the relationship that will be used in the endpoint:

i.e: `GET /user/1/{name}` or `GET /user/1/relationships/{name}`

i.e: `GET /user/1/country` or `GET /user/1/relationships/country`

- `class` (required):

The entity (resource) class being the relation.

- `property` (optional):

Allow to set a custom value for the relation `{name}` used in the endpoints.

For instance on the `User` entity, if the relationship is set as `protected Country $userCountry`,
by default the endpoint would be `GET /user/1/userCountry`.

```php
// User Entity
public static function relationships(): RelationshipsCollection
{
    return new RelationshipsCollection([
        ToOneRelationship::create('userCountry', Country::class)
    ]);
}
```

Set the 1st required parameter `name` as `country` and set the
3rd optional parameter `property` to `userCountry` to have a custom endpoint like `GET /user/1/country`.

```php
// User Entity
public static function relationships(): RelationshipsCollection
{
    return new RelationshipsCollection([
        ToOneRelationship::create('country', Country::class, 'userCountry')
    ]);
}
```

#### To-Many Relationship
Returns a list of resources in the responses.

```php
// User Entity
public static function relationships(): RelationshipsCollection
{
    return new RelationshipsCollection([
        ToManyRelationship::create('roles', Role::class, 'users')
    ]);
}
```

The `ToManyRelationship::create()` method accepts 4 parameters:

- `name` (required):

Name of the relationship that will be used in the endpoint:

i.e: `GET /user/1/{name}` or `GET /user/1/relationships/{name}`

i.e: `GET /user/1/roles` or `GET /user/1/relationships/roles`

- `class` (required):

The entity (resource) class being the relation.

- `mappedBy` (required):

Name of the association-field on the owning side of the relation.

- `property` (optional):

Allow to set a custom value for the relation `{name}` used in the endpoints.

For instance on the `User` entity, if the relationship is set as `protected Country $userRoles`,
by default the endpoint would be `GET /user/1/userRoles`.

```php
// User Entity
public static function relationships(): RelationshipsCollection
{
    return new RelationshipsCollection([
        ToManyRelationship::create('userRoles', Role::class)
    ]);
}
```

Set the 1st required parameter `name` as `roles` and set the
3rd optional parameter `property` to `userRoles` to have a custom endpoint like `GET /user/1/roles`.

```php
// User Entity
public static function relationships(): RelationshipsCollection
{
    return new RelationshipsCollection([
        ToManyRelationship::create('roles', Role::class, 'userRoles')
    ]);
}
```

#### Memoize Relationships
The memoize pattern allows to build the relationships list once and to save it in memory.

```php
public static function relationships(): RelationshipsCollection
{
    return static::memoizeRelationships(fn () => [
        ToOneRelationship::create('user', User::class),
        ToManyRelationship::create('roles', Role::class, 'users'),
    ]);
}
```

To use this pattern, Doctrine entities must implement the
[`Sowl\JsonApi\Relationships\MemoizeRelationshipsTrait`](/src/Relationships/MemoizeRelationshipsTrait.php) trait.
